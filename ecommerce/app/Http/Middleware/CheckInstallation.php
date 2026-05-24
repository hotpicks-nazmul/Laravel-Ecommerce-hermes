<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Symfony\Component\HttpFoundation\Response;

class CheckInstallation
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Skip installation check for installation routes
        if ($request->routeIs('install.*') || $request->is('install/*') || $request->is('install')) {
            // If install.lock exists, redirect to home (already installed)
            if ($this->isInstalled()) {
                return redirect()->route('home');
            }
            return $next($request);
        }

        // For non-install routes: if installed, pass through
        if ($this->isInstalled()) {
            return $next($request);
        }

        // Not installed — redirect to installation wizard
        return redirect()->route('install.welcome');
    }

    /**
     * Check if the application is installed by verifying install.lock exists.
     * Only the existence of this file (created at Step 7 of the wizard)
     * determines installation state — NOT the presence of DB tables.
     */
    protected function isInstalled(): bool
    {
        return File::exists(storage_path('framework/install.lock'));
    }
}
