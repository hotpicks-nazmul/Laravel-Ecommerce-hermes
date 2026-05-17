<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class CheckInstallation
{
    public function handle(Request $request, Closure $next)
    {
        $isInstallRoute = $request->is('install/*') || $request->is('install');

        if ($isInstallRoute) {
            if ($this->isInstalled() && !$request->is('install/complete') && !$request->is('install/install/process')) {
                return redirect()->route('home');
            }
            return $next($request);
        }

        if (!$this->isInstalled()) {
            return redirect()->route('install.welcome');
        }

        return $next($request);
    }

    protected function isInstalled(): bool
    {
        return File::exists(storage_path('installed'));
    }
}
