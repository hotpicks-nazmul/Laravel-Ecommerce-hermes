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

        // Build attribute options from JSON column
        $attributeOptions = [];
        $productAttrsRaw = $product->attributes;
        // Handle both string (JSON) and array
        $productAttrs = is_string($productAttrsRaw) ? json_decode($productAttrsRaw, true) : ($productAttrsRaw ?? []);
        
        if (!empty($productAttrs) && is_array($productAttrs)) {
            // Get all attributes for reference
            $allAttributes = \App\Models\Attribute::all()->keyBy('id');
            
            foreach ($productAttrs as $attrId => $attrData) {
                // Use saved name, or fallback to attribute name from DB
                $attrName = $attrData['name'] ?? '';
                if (empty($attrName) && isset($allAttributes[$attrId])) {
                    $attrName = $allAttributes[$attrId]->name;
                }
                if (empty($attrName)) {
                    $attrName = 'Option ' . $attrId;
                }
                
                if (isset($attrData['values']) && is_array($attrData['values'])) {
                    foreach ($attrData['values'] as $valueId => $value) {
                        if (!isset($attributeOptions[$attrName])) {
                            $attributeOptions[$attrName] = [];
                        }
                        $exists = in_array($value['value_name'] ?? '', array_column($attributeOptions[$attrName], 'value'));
                        if (!$exists && !empty($value['value_name'])) {
                            $attributeOptions[$attrName][] = [
                                'id' => $valueId,
                                'value' => $value['value_name'] ?? '',
                                'price' => $value['price'] ?? 0,
                                'color_code' => $value['color_code'] ?? null,
                                'image' => isset($value['image']) ? preg_replace('/^\/storage\//', '', $value['image']) : null,
                            ];
                        }
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

        // Get product attributes grouped by attribute name (from JSON column - fallback)
        $attributes = [];

        // Get colors from JSON column (admin saves here, not in pivot)
        $colors = collect([]);
        $colorOptions = [];
        $productColorsRaw = $product->colors;
        $productColors = is_string($productColorsRaw) ? json_decode($productColorsRaw, true) : ($productColorsRaw ?? []);
        
        if (!empty($productColors) && is_array($productColors)) {
            foreach ($productColors as $colorItem) {
                // Handle nested 'values' structure from admin panel
                if (isset($colorItem['values']) && is_array($colorItem['values'])) {
                    foreach ($colorItem['values'] as $valueId => $valueData) {
                        $colorOptions[] = [
                            'id' => $valueId,
                            'name' => $valueData['value_name'] ?? '',
                            'hex_code' => $valueData['hex_code'] ?? '#000000',
                            'image' => isset($valueData['image']) ? preg_replace('/^\/storage\//', '', $valueData['image']) : null,
                            'price' => $valueData['price'] ?? 0,
                            'quantity' => $valueData['quantity'] ?? 0,
                            'sku' => $valueData['sku'] ?? null,
                        ];
                    }
                }
                // Handle flat structure (direct color_id)
                elseif (isset($colorItem['color_id'])) {
                    $colorId = $colorItem['color_id'];
                    $colorModel = \App\Models\Color::find($colorId);
                    if ($colorModel) {
                        $colors->push($colorModel);
                        $colorOptions[] = [
                            'id' => $colorModel->id,
                            'name' => $colorModel->name,
                            'hex_code' => $colorModel->hex_code,
                            'image' => $colorItem['image'] ?? null,
                            'price' => $colorItem['price'] ?? 0,
                            'quantity' => $colorItem['quantity'] ?? 0,
                            'sku' => $colorItem['sku'] ?? null,
                        ];
                    }
                }
            }
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
