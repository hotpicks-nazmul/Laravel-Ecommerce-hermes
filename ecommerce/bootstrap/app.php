<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->web(append: [
            \App\Http\Middleware\CheckInstallation::class,
            \App\Http\Middleware\ThemeMiddleware::class,
            \App\Http\Middleware\LanguageMiddleware::class,
            \App\Http\Middleware\SeoRedirectMiddleware::class,
        ]);
        
        $middleware->alias([
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
            'super_admin' => \App\Http\Middleware\SuperAdminMiddleware::class,
            'staff' => \App\Http\Middleware\StaffMiddleware::class,
            'permission' => \App\Http\Middleware\CheckPermission::class,
            'theme' => \App\Http\Middleware\ThemeMiddleware::class,
            'installed' => \App\Http\Middleware\CheckInstallation::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })
    ->withProviders([
        \App\Providers\MailServiceProvider::class,
    ])
    ->create();
