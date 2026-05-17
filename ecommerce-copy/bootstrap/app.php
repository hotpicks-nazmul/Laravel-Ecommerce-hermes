<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Models\Order;
use App\Models\Review;
use App\Models\Product;
use App\Models\User;
use App\Models\Refund;
use App\Models\Ticket;
use App\Models\TicketReply;
use App\Models\ProductQA;
use App\Models\SellerPayout;
use App\Observers\OrderObserver;
use App\Observers\ReviewObserver;
use App\Observers\ProductObserver;
use App\Observers\UserObserver;
use App\Observers\RefundObserver;
use App\Observers\TicketObserver;
use App\Observers\TicketReplyObserver;
use App\Observers\ProductQAObserver;
use App\Observers\SellerPayoutObserver;

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
            'granular_permission' => \App\Http\Middleware\CheckGranularPermission::class,
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

Order::observe(OrderObserver::class);
Review::observe(ReviewObserver::class);
Product::observe(ProductObserver::class);
User::observe(UserObserver::class);
Refund::observe(RefundObserver::class);
Ticket::observe(TicketObserver::class);
TicketReply::observe(TicketReplyObserver::class);
ProductQA::observe(ProductQAObserver::class);
SellerPayout::observe(SellerPayoutObserver::class);
