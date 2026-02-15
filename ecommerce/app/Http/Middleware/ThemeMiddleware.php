<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Services\ThemeService;

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

        return $next($request);
    }
}
