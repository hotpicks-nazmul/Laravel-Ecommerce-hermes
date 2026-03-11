<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Services\ThemeService;
use App\Models\Category;
use App\Models\Setting;

class ThemeMiddleware
{
    protected ThemeService $themeService;

    public function __construct(ThemeService $themeService)
    {
        $this->themeService = $themeService;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Skip theme middleware for admin routes
        if ($request->is('admin/*')) {
            return $next($request);
        }

        // Skip for install routes
        if ($request->is('install/*')) {
            return $next($request);
        }

        // Get active theme
        $activeTheme = $this->themeService->getActiveTheme();

        // Share theme data with all views
        view()->share('activeTheme', $activeTheme);
        view()->share('themeSettings', $this->themeService->getThemeSettings());
        
        // Share categories with all views for header navigation
        $categories = Category::withCount('products')->orderBy('name')->get();
        view()->share('categories', $categories);

        // Share SEO settings with all views
        $seoSettings = Setting::whereIn('key', [
            'site_meta_title',
            'site_meta_description',
            'site_meta_keywords',
            'google_analytics_id',
            'google_search_console',
            'facebook_pixel_id',
            'og_title',
            'og_description',
            'og_image',
            'twitter_card_type',
        ])->pluck('value', 'key');
        view()->share('seoSettings', $seoSettings);

        return $next($request);
    }
}
