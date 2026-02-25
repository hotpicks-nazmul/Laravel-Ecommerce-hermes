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
            ->with(['category', 'reviews.user', 'attributeValues.attribute', 'colors', 'relatedProducts'])
            ->firstOrFail();

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

        // Get product colors
        $colors = $product->colors()->where('is_active', true)->orderBy('display_order')->get();

        return view('themes.general.products.show', compact('product', 'relatedProducts', 'reviews', 'attributes', 'colors'));
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
