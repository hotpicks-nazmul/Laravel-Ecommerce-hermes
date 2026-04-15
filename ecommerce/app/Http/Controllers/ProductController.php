<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;

class ProductController extends Controller
{
    /**
     * Display a listing of products.
     */
    public function index(Request $request)
    {
        $query = Product::where('is_active', true);

        // Filter by category
        if ($request->has('category')) {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }

        // Filter by price range
        if ($request->has('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }
        if ($request->has('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        // Sort products
        $sortBy = $request->get('sort', 'latest');
        switch ($sortBy) {
            case 'price_low':
                $query->orderBy('price', 'asc');
                break;
            case 'price_high':
                $query->orderBy('price', 'desc');
                break;
            case 'name':
                $query->orderBy('name', 'asc');
                break;
            default:
                $query->latest();
        }

        $products = $query->paginate(12);
        $categories = Category::where('status', 'active')->get();

        return view('themes.general.products.index', compact('products', 'categories'));
    }

    /**
     * Display a single product.
     */
    public function show($slug)
    {
        $product = Product::where('slug', $slug)
            ->where('is_active', true)
            ->with(['category', 'reviews.user', 'attributeValues.attribute', 'colors', 'relatedProducts', 'variants'])
            ->firstOrFail();

        // If this is a variant, redirect to parent or get parent info
        if ($product->parent_id) {
            $parentProduct = Product::find($product->parent_id);
            if ($parentProduct && $parentProduct->slug !== $slug) {
                return redirect()->route('product.show', $parentProduct->slug);
            }
        }

        // Get manual variants (children products)
        $variants = $product->variants()->where('is_active', true)->get();
        
        // Build variant options for frontend with price and image
        $variantOptions = [];
        $variantImages = [];
        
        foreach ($variants as $variant) {
            $variations = json_decode($variant->variations, true);
            if (!empty($variations) && is_array($variations)) {
                foreach ($variations as $var) {
                    $attrName = $var['attrName'] ?? '';
                    $valueName = $var['valueName'] ?? '';
                    
                    if (!isset($variantOptions[$attrName])) {
                        $variantOptions[$attrName] = [];
                    }
                    
                    if (!isset($variantOptions[$attrName][$valueName])) {
                        $variantOptions[$attrName][$valueName] = [
                            'value' => $valueName,
                            'variants' => []
                        ];
                    }
                    
                    $variantOptions[$attrName][$valueName]['variants'][] = [
                        'id' => $variant->id,
                        'sku' => $variant->sku,
                        'price' => $variant->price,
                        'sale_price' => $variant->sale_price,
                        'stock' => $variant->quantity,
                        'image' => $variant->featured_image
                    ];
                    
                    // Store variant image for main product image swap
                    $variantImages[$variant->id] = $variant->featured_image;
                }
            }
        }

        // Also build attribute options from attribute values with price and image from pivot table
        $attributeOptions = [];
        if ($product->attributeValues->count() > 0) {
            foreach ($product->attributeValues as $value) {
                if ($value->attribute) {
                    $attrName = $value->attribute->name;
                    if (!isset($attributeOptions[$attrName])) {
                        $attributeOptions[$attrName] = [];
                    }
                    
                    // Check if value already exists with same name
                    $exists = false;
                    foreach ($attributeOptions[$attrName] as $existing) {
                        if ($existing['value'] === $value->value) {
                            $exists = true;
                            break;
                        }
                    }
                    
                    if (!$exists) {
                        // Get price from pivot table (product_attribute_values)
                        $pivotPrice = $value->pivot->price ?? 0;
                        $attributeOptions[$attrName][] = [
                            'id' => $value->id,
                            'value' => $value->value,
                            'price' => $pivotPrice,
                            'color_code' => $value->color_code,
                            'image' => $value->pivot->image ?? null,
                            'quantity' => $value->pivot->quantity ?? 0,
                            'sku' => $value->pivot->sku ?? null,
                        ];
                    }
                }
            }
        }

        // Get manually configured related products first
        $relatedProducts = $product->relatedProducts()
            ->where('is_active', true)
            ->limit(8)
            ->get();

        // If not enough related products, supplement with products from same category
        if ($relatedProducts->count() < 4) {
            $categoryProducts = Product::where('category_id', $product->category_id)
                ->where('id', '!=', $product->id)
                ->where('is_active', true)
                ->whereNotIn('id', $relatedProducts->pluck('id'))
                ->limit(8 - $relatedProducts->count())
                ->get();
            
            $relatedProducts = $relatedProducts->merge($categoryProducts);
        }

        // Get approved reviews with pagination
        $reviews = $product->approvedReviews()->latest()->paginate(5);

        // Get product attributes grouped by attribute name
        $attributes = [];
        if ($product->attributeValues->count() > 0) {
            foreach ($product->attributeValues as $value) {
                if ($value->attribute) {
                    $attributes[$value->attribute->name][] = $value;
                }
            }
        }

        // Get product colors with price, quantity, sku from pivot
        $colors = $product->colors()->where('is_active', true)->orderBy('display_order')->get();
        
        // Build color options with price from pivot
        $colorOptions = [];
        foreach ($colors as $color) {
            $colorOptions[] = [
                'id' => $color->id,
                'name' => $color->name,
                'hex_code' => $color->hex_code,
                'image' => $color->pivot->image ?? null,
                'price' => $color->pivot->price_adjustment ?? 0,
                'quantity' => $color->pivot->quantity ?? 0,
                'sku' => $color->pivot->sku ?? null,
            ];
        }

        return view('themes.general.products.show', compact('product', 'relatedProducts', 'reviews', 'attributes', 'colors', 'colorOptions', 'variants', 'variantOptions', 'attributeOptions', 'variantImages'));
    }

    /**
     * Display products by category.
     */
    public function byCategory($slug)
    {
        $category = Category::where('slug', $slug)->firstOrFail();
        
        $products = Product::where('category_id', $category->id)
            ->where('is_active', true)
            ->paginate(12);

        return view('themes.general.products.category', compact('products', 'category'));
    }

    /**
     * Quick view for product.
     */
    public function quickView($id)
    {
        $product = Product::with('category')->findOrFail($id);
        
        return view('themes.general.components.product-card', compact('product'));
    }
}
