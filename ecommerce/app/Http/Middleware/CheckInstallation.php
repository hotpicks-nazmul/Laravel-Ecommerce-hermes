<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\File;

class CheckInstallation
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // Skip installation check for installation routes
        if ($request->is('install/*') || $request->is('install')) {
            // If already installed, redirect to home
            if ($this->isInstalled()) {
                return redirect()->route('home');
            }
            return $next($request);
        }

        // Check if application is installed
        if (!$this->isInstalled()) {
            return redirect()->route('install.welcome');
        }

        return $next($request);
    }

    /**
     * Check if the application is installed.
     *
     * @return bool
     */
    protected function isInstalled(): bool
    {
        // Check if install.lock file exists
        if (File::exists(storage_path('framework/install.lock'))) {
            return true;
        }

        // Check if .env file exists
        if (!File::exists(base_path('.env'))) {
            return false;
        }

        // Check if database connection is configured
        try {
            DB::connection()->getPdo();
            
            // Check if users table exists (basic check for installation)
            if (Schema::hasTable('users')) {
                return true;
            }
        } catch (\Exception $e) {
            return false;
        }

        return false;
    }
}
