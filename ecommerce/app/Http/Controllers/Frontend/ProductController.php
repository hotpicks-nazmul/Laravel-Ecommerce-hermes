<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Services\ThemeService;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    protected $theme;

    public function __construct(ThemeService $theme)
    {
        $this->theme = $theme;
    }

    /**
     * Display products listing.
     */
    public function index(Request $request)
    {
        $query = Product::active()
            ->with('category')
            ->inStock();

        // Filter by category
        if ($request->category) {
            $category = Category::where('slug', $request->category)->first();
            if ($category) {
                $query->where('category_id', $category->id);
            }
        }

        // Filter by price range
        if ($request->min_price) {
            $query->where('price', '>=', $request->min_price);
        }
        if ($request->max_price) {
            $query->where('price', '<=', $request->max_price);
        }

        // Filter by search
        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                    ->orWhere('short_description', 'like', "%{$request->search}%");
            });
        }

        // Sort products
        $sortBy = $request->sort ?? 'latest';
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
        $categories = Category::active()->parents()->ordered()->get();

        return $this->theme->view('products.index', compact('products', 'categories'));
    }

    /**
     * Display single product.
     */
    public function show($slug)
    {
        $product = Product::active()
            ->with(['category', 'reviews.user'])
            ->where('slug', $slug)
            ->firstOrFail();

        // Get related products
        $relatedProducts = Product::active()
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->inStock()
            ->take(4)
            ->get();

        // Get approved reviews
        $reviews = $product->approvedReviews()->latest()->paginate(5);

        return $this->theme->view('products.show', compact('product', 'relatedProducts', 'reviews'));
    }

    /**
     * Display products by category.
     */
    public function category($slug)
    {
        $category = Category::active()
            ->where('slug', $slug)
            ->firstOrFail();

        $products = Product::active()
            ->where('category_id', $category->id)
            ->with('category')
            ->inStock()
            ->paginate(12);

        return $this->theme->view('products.category', compact('category', 'products'));
    }

    /**
     * Get featured products (API).
     */
    public function featured()
    {
        $products = Product::active()
            ->featured()
            ->inStock()
            ->take(8)
            ->get();

        return response()->json($products);
    }

    /**
     * Get related products (API).
     */
    public function related($productId)
    {
        $product = Product::findOrFail($productId);
        
        $products = Product::active()
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->inStock()
            ->take(4)
            ->get();

        return response()->json($products);
    }
}
