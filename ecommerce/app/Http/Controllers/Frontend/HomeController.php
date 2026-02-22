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
        // Get product count settings from database
        $featuredCount = (int) Setting::where('key', 'homepage_featured_products_count')->value('value') ?: 8;
        $newArrivalsCount = (int) Setting::where('key', 'homepage_new_arrivals_count')->value('value') ?: 8;
        $saleCount = (int) Setting::where('key', 'homepage_sale_products_count')->value('value') ?: 8;

        // Get featured products
        $featuredProducts = Product::active()
            ->featured()
            ->with('category')
            ->inStock()
            ->take($featuredCount)
            ->get();

        // Get latest products
        $latestProducts = Product::active()
            ->with('category')
            ->inStock()
            ->latest()
            ->take($newArrivalsCount)
            ->get();

        // Get categories based on settings
        $selectedCategoryIds = json_decode(Setting::where('key', 'homepage_selected_categories')->value('value') ?? '[]', true);
        
        if (!empty($selectedCategoryIds)) {
            // Get selected categories in the order they were saved
            $categories = Category::active()
                ->whereIn('id', $selectedCategoryIds)
                ->with(['children' => function ($query) {
                    $query->active()->ordered();
                }])
                ->get()
                ->sortBy(function($category) use ($selectedCategoryIds) {
                    return array_search($category->id, $selectedCategoryIds);
                });
        } else {
            // Fallback to default behavior
            $categories = Category::active()
                ->parents()
                ->ordered()
                ->with(['children' => function ($query) {
                    $query->active()->ordered();
                }])
                ->take(6)
                ->get();
        }

        // Get sale products
        $saleProducts = Product::active()
            ->whereNotNull('sale_price')
            ->where('sale_price', '<', \DB::raw('price'))
            ->inStock()
            ->take($saleCount)
            ->get();

        // Get latest blog posts
        $latestBlogs = Blog::published()
            ->latest()
            ->take(3)
            ->get();

        // Get section order from settings
        $sectionOrderSetting = Setting::where('key', 'homepage_section_order')->first();
        $sectionOrder = $sectionOrderSetting ? json_decode($sectionOrderSetting->value, true) : [
            'categories',
            'featured',
            'banner',
            'new_arrivals',
            'why_choose_us',
            'sale',
            'testimonials',
            'blog'
        ];

        // Get active theme
        $activeTheme = $this->theme->getActiveTheme();

        return view('themes.' . $activeTheme . '.home.index', compact(
            'featuredProducts',
            'latestProducts',
            'categories',
            'saleProducts',
            'latestBlogs',
            'sectionOrder'
        ));
    }
}
