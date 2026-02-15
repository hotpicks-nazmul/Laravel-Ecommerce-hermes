<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WishlistController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\PageController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application.
|
*/

// Installation Routes (only accessible before installation)
Route::prefix('install')->group(base_path('routes/install.php'));

// Frontend Routes
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/about', [PageController::class, 'about'])->name('about');
Route::get('/contact', [PageController::class, 'contact'])->name('pages.contact');
Route::post('/contact', [PageController::class, 'sendContact'])->name('contact.send');
Route::get('/faq', [PageController::class, 'faq'])->name('faq');
Route::get('/terms', [PageController::class, 'terms'])->name('terms');
Route::get('/privacy', [PageController::class, 'privacy'])->name('privacy');
Route::get('/pages/{slug}', [PageController::class, 'show'])->name('pages.show');

// Blog Routes
Route::prefix('blogs')->name('blogs.')->group(function () {
    Route::get('/', [\App\Http\Controllers\Frontend\BlogController::class, 'index'])->name('index');
    Route::get('/{slug}', [\App\Http\Controllers\Frontend\BlogController::class, 'show'])->name('show');
});

// Live Chat Route
Route::get('/chat/live', [ChatController::class, 'live'])->name('chat.live');

// Search Routes
Route::get('/search', [SearchController::class, 'index'])->name('search.index');
Route::get('/search/suggestions', [SearchController::class, 'suggestions'])->name('search.suggestions');

// Product Routes
Route::prefix('products')->name('products.')->group(function () {
    Route::get('/', [ProductController::class, 'index'])->name('index');
    Route::get('/{slug}', [ProductController::class, 'show'])->name('show');
    Route::get('/category/{slug}', [ProductController::class, 'byCategory'])->name('category');
});

// Category Routes
Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
Route::get('/categories/{slug}', [CategoryController::class, 'show'])->name('categories.show');

// Cart Routes
Route::prefix('cart')->name('cart.')->group(function () {
    Route::get('/', [CartController::class, 'index'])->name('index');
    Route::post('/add', [CartController::class, 'add'])->name('add');
    Route::post('/update', [CartController::class, 'update'])->name('update');
    Route::post('/remove', [CartController::class, 'remove'])->name('remove');
    Route::post('/clear', [CartController::class, 'clear'])->name('clear');
    Route::get('/count', [CartController::class, 'count'])->name('count');
});

// API Routes
Route::prefix('api')->name('api.')->group(function () {
    Route::get('/cart/count', [CartController::class, 'count'])->name('cart.count');
    Route::get('/cart/items', [CartController::class, 'items'])->name('cart.items');
    Route::post('/cart/update', [CartController::class, 'update'])->name('cart.update');
    Route::post('/cart/remove', [CartController::class, 'remove'])->name('cart.remove');
    Route::post('/wishlist/toggle', [WishlistController::class, 'toggle'])->name('wishlist.toggle');
    Route::post('/chat/send', [ChatController::class, 'aiChat'])->name('chat.send');
    Route::get('/products/quick-view/{id}', [ProductController::class, 'quickView'])->name('products.quick-view');
});

// Wishlist Routes
Route::prefix('wishlist')->name('wishlist.')->group(function () {
    Route::get('/', [WishlistController::class, 'index'])->name('index');
    Route::post('/add', [WishlistController::class, 'add'])->name('add');
    Route::post('/remove', [WishlistController::class, 'remove'])->name('remove');
    Route::post('/toggle', [WishlistController::class, 'toggle'])->name('toggle');
});

// Checkout Routes
Route::prefix('checkout')->name('checkout.')->middleware('auth')->group(function () {
    Route::get('/', [CheckoutController::class, 'index'])->name('index');
    Route::post('/process', [CheckoutController::class, 'process'])->name('process');
    Route::get('/success/{order}', [CheckoutController::class, 'success'])->name('success');
    Route::get('/cancel', [CheckoutController::class, 'cancel'])->name('cancel');
});

// Payment Routes
Route::prefix('payment')->name('payment.')->group(function () {
    Route::post('/bkash/create', [PaymentController::class, 'bkashCreate'])->name('bkash.create');
    Route::post('/bkash/execute', [PaymentController::class, 'bkashExecute'])->name('bkash.execute');
    Route::get('/bkash/callback', [PaymentController::class, 'bkashCallback'])->name('bkash.callback');
    Route::post('/sslcommerz/create', [PaymentController::class, 'sslcommerzCreate'])->name('sslcommerz.create');
    Route::post('/sslcommerz/success', [PaymentController::class, 'sslcommerzSuccess'])->name('sslcommerz.success');
    Route::post('/sslcommerz/fail', [PaymentController::class, 'sslcommerzFail'])->name('sslcommerz.fail');
    Route::post('/sslcommerz/cancel', [PaymentController::class, 'sslcommerzCancel'])->name('sslcommerz.cancel');
    Route::post('/sslcommerz/ipn', [PaymentController::class, 'sslcommerzIpn'])->name('sslcommerz.ipn');
});

// Order Routes
Route::prefix('orders')->name('orders.')->middleware('auth')->group(function () {
    Route::get('/', [OrderController::class, 'index'])->name('index');
    Route::get('/{order}', [OrderController::class, 'show'])->name('show');
    Route::post('/{order}/cancel', [OrderController::class, 'cancel'])->name('cancel');
    Route::get('/track/{order}', [OrderController::class, 'track'])->name('track');
});

// Wishlist Routes
Route::prefix('wishlist')->name('wishlist.')->middleware('auth')->group(function () {
    Route::get('/', [WishlistController::class, 'index'])->name('index');
    Route::post('/add', [WishlistController::class, 'add'])->name('add');
    Route::post('/remove', [WishlistController::class, 'remove'])->name('remove');
});

// Review Routes
Route::prefix('reviews')->name('reviews.')->middleware('auth')->group(function () {
    Route::post('/', [ReviewController::class, 'store'])->name('store');
    Route::put('/{review}', [ReviewController::class, 'update'])->name('update');
    Route::delete('/{review}', [ReviewController::class, 'destroy'])->name('destroy');
});

// Chat Routes
Route::prefix('chat')->name('chat.')->group(function () {
    Route::get('/', [ChatController::class, 'index'])->name('index');
    Route::post('/send', [ChatController::class, 'send'])->name('send');
    Route::get('/messages', [ChatController::class, 'messages'])->name('messages');
    Route::post('/ai', [ChatController::class, 'aiChat'])->name('ai');
});

// User Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [UserController::class, 'showLogin'])->name('login');
    Route::post('/login', [UserController::class, 'login'])->name('login.post');
    Route::get('/register', [UserController::class, 'showRegister'])->name('register');
    Route::post('/register', [UserController::class, 'register'])->name('register.post');
    Route::get('/forgot-password', [UserController::class, 'showForgotPassword'])->name('password.request');
    Route::post('/forgot-password', [UserController::class, 'forgotPassword'])->name('password.email');
    Route::get('/reset-password/{token}', [UserController::class, 'showResetPassword'])->name('password.reset');
    Route::post('/reset-password', [UserController::class, 'resetPassword'])->name('password.update');
});

// Social Login Routes
Route::get('/login/{provider}', [UserController::class, 'redirectToProvider'])->name('social.login');
Route::get('/login/{provider}/callback', [UserController::class, 'handleProviderCallback'])->name('social.callback');

// Authenticated User Routes
Route::middleware('auth')->group(function () {
    Route::post('/logout', [UserController::class, 'logout'])->name('logout');
    
    // Account Routes
    Route::prefix('account')->name('account.')->group(function () {
        Route::get('/profile', [UserController::class, 'profile'])->name('profile');
        Route::put('/profile', [UserController::class, 'updateProfile'])->name('profile.update');
        Route::get('/orders', [OrderController::class, 'index'])->name('orders');
        Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
        Route::post('/orders/{order}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');
        Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist');
        Route::get('/addresses', [UserController::class, 'addresses'])->name('addresses');
        Route::post('/addresses', [UserController::class, 'storeAddress'])->name('addresses.store');
        Route::put('/addresses/{address}', [UserController::class, 'updateAddress'])->name('addresses.update');
        Route::delete('/addresses/{address}', [UserController::class, 'deleteAddress'])->name('addresses.destroy');
    });
    
    Route::prefix('dashboard')->name('dashboard.')->group(function () {
        Route::get('/', [UserController::class, 'dashboard'])->name('index');
        Route::get('/profile', [UserController::class, 'profile'])->name('profile');
        Route::put('/profile', [UserController::class, 'updateProfile'])->name('profile.update');
        Route::put('/password', [UserController::class, 'updatePassword'])->name('password.update');
        Route::get('/addresses', [UserController::class, 'addresses'])->name('addresses');
        Route::post('/addresses', [UserController::class, 'storeAddress'])->name('addresses.store');
        Route::put('/addresses/{address}', [UserController::class, 'updateAddress'])->name('addresses.update');
        Route::delete('/addresses/{address}', [UserController::class, 'deleteAddress'])->name('addresses.destroy');
        Route::get('/orders', [OrderController::class, 'index'])->name('orders');
        Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
        Route::post('/orders/{order}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');
        Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist');
    });
});

// Admin Authentication Routes
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/login', [\App\Http\Controllers\Admin\AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [\App\Http\Controllers\Admin\AuthController::class, 'login'])->name('login.post');
    Route::post('/logout', [\App\Http\Controllers\Admin\AuthController::class, 'logout'])->name('logout');
});

// Admin Routes
Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(base_path('routes/admin.php'));

// Sitemap Route
Route::get('/sitemap.xml', function () {
    return response()->file(storage_path('app/sitemap.xml'), ['Content-Type' => 'application/xml']);
})->name('sitemap');
