<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use App\Models\Banner;
use App\Models\Blog;
use App\Services\ThemeService;

class HomeController extends Controller
{
    protected ThemeService $themeService;

    public function __construct(ThemeService $themeService)
    {
        $this->themeService = $themeService;
    }

    /**
     * Display the home page.
     */
    public function index()
    {
        $theme = $this->themeService->getActiveTheme();
        
        // Get featured products
        $featuredProducts = Product::where('is_featured', true)
            ->where('is_active', true)
            ->with('category')
            ->where('quantity', '>', 0)
            ->take(8)
            ->get();
        
        // Get latest products
        $latestProducts = Product::where('is_active', true)
            ->with('category')
            ->where('quantity', '>', 0)
            ->latest()
            ->take(8)
            ->get();
        
        // Get categories
        $categories = Category::where('status', 'active')
            ->whereNull('parent_id')
            ->take(6)
            ->get();
        
        // Get sale products
        $saleProducts = Product::where('is_active', true)
            ->whereNotNull('sale_price')
            ->where('sale_price', '>', 0)
            ->where('quantity', '>', 0)
            ->take(4)
            ->get();

        // Get latest blog posts
        $latestBlogs = Blog::where('status', 'published')
            ->where('published_at', '<=', now())
            ->latest('published_at')
            ->take(3)
            ->get();

        return view('themes.' . $theme . '.home.index', compact(
            'featuredProducts',
            'latestProducts',
            'categories',
            'saleProducts',
            'latestBlogs'
        ));
    }
}
