<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\Blog;
use App\Models\Setting;
use App\Services\ThemeService;

class HomeController extends Controller
{
    protected $theme;

    public function __construct(ThemeService $theme)
    {
        $this->theme = $theme;
    }

    /**
     * Display the home page.
     */
    public function index()
    {
        // Get featured products
        $featuredProducts = Product::active()
            ->featured()
            ->with('category')
            ->inStock()
            ->take(8)
            ->get();

        // Get latest products
        $latestProducts = Product::active()
            ->with('category')
            ->inStock()
            ->latest()
            ->take(8)
            ->get();

        // Get categories
        $categories = Category::active()
            ->parents()
            ->ordered()
            ->with(['children' => function ($query) {
                $query->active()->ordered();
            }])
            ->take(6)
            ->get();

        // Get sale products
        $saleProducts = Product::active()
            ->whereNotNull('sale_price')
            ->where('sale_price', '<', \DB::raw('price'))
            ->inStock()
            ->take(4)
            ->get();

        // Get latest blog posts
        $latestBlogs = Blog::published()
            ->latest()
            ->take(3)
            ->get();

        // Get active theme
        $activeTheme = $this->theme->getActiveTheme();

        return view('themes.' . $activeTheme . '.home.index', compact(
            'featuredProducts',
            'latestProducts',
            'categories',
            'saleProducts',
            'latestBlogs'
        ));
    }
}
