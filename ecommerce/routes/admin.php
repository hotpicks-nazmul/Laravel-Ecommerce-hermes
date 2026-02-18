<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\ThemeController;
use App\Http\Controllers\Admin\PaymentController;
use App\Http\Controllers\Admin\CouponController;
use App\Http\Controllers\Admin\ReviewController;
use App\Http\Controllers\Admin\PageController;
use App\Http\Controllers\Admin\SeoController;
use App\Http\Controllers\Admin\ChatController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\SliderController;
use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Admin\HeroController;
use App\Http\Controllers\Admin\HomePageController;

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
|
| Here is where you can register admin routes for your application.
|
*/

// Admin Dashboard
Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
Route::get('/analytics', [DashboardController::class, 'analytics'])->name('analytics');
Route::get('/sales-chart', [DashboardController::class, 'salesChart'])->name('sales-chart');

// Products Management
Route::resource('products', ProductController::class);
Route::post('/products/bulk-action', [ProductController::class, 'bulkAction'])->name('products.bulk-action');
Route::get('/products/{product}/duplicate', [ProductController::class, 'duplicate'])->name('products.duplicate');
Route::post('/products/{product}/images', [ProductController::class, 'uploadImages'])->name('products.images.upload');
Route::delete('/products/{product}/images/{image}', [ProductController::class, 'deleteImage'])->name('products.images.delete');

// Categories Management
Route::resource('categories', CategoryController::class);
Route::post('/categories/reorder', [CategoryController::class, 'reorder'])->name('categories.reorder');
Route::get('/categories/tree', [CategoryController::class, 'tree'])->name('categories.tree');

// Orders Management
Route::resource('orders', OrderController::class)->only(['index', 'show', 'update']);
Route::post('/orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.status');
Route::post('/orders/{order}/payment-status', [OrderController::class, 'updatePaymentStatus'])->name('orders.payment-status');
Route::get('/orders/{order}/invoice', [OrderController::class, 'invoice'])->name('orders.invoice');
Route::post('/orders/{order}/ship', [OrderController::class, 'ship'])->name('orders.ship');

// Customers Management
Route::resource('customers', CustomerController::class)->only(['index', 'show', 'update', 'destroy']);
Route::get('/customers/{customer}/orders', [CustomerController::class, 'orders'])->name('customers.orders');
Route::post('/customers/{customer}/login-as', [CustomerController::class, 'loginAs'])->name('customers.login-as');

// Coupons Management
Route::resource('coupons', CouponController::class);
Route::post('/coupons/{coupon}/toggle', [CouponController::class, 'toggle'])->name('coupons.toggle');

// Reviews Management
Route::resource('reviews', ReviewController::class)->only(['index', 'update', 'destroy']);
Route::post('/reviews/{review}/approve', [ReviewController::class, 'approve'])->name('reviews.approve');
Route::post('/reviews/bulk-action', [ReviewController::class, 'bulkAction'])->name('reviews.bulk-action');

// Pages Management
Route::resource('pages', PageController::class);
Route::post('/pages/{page}/toggle', [PageController::class, 'toggle'])->name('pages.toggle');

// Sliders Management
Route::resource('sliders', SliderController::class);
Route::post('/sliders/reorder', [SliderController::class, 'reorder'])->name('sliders.reorder');

// Banners Management
Route::resource('banners', BannerController::class);
Route::post('/banners/{banner}/toggle', [BannerController::class, 'toggle'])->name('banners.toggle');

// Hero Section Settings
Route::prefix('hero')->name('hero.')->group(function () {
    Route::get('/', [HeroController::class, 'index'])->name('index');
    Route::put('/', [HeroController::class, 'update'])->name('update');
    Route::post('/type', [HeroController::class, 'updateType'])->name('update-type');
});

// Home Page Settings
Route::prefix('homepage')->name('homepage.')->group(function () {
    Route::get('/', [HomePageController::class, 'index'])->name('index');
    Route::put('/', [HomePageController::class, 'update'])->name('update');
});

// Theme Management
Route::prefix('theme')->name('theme.')->group(function () {
    Route::get('/', [ThemeController::class, 'index'])->name('index');
    Route::post('/activate', [ThemeController::class, 'activate'])->name('activate');
    Route::get('/settings', [ThemeController::class, 'settings'])->name('settings');
    Route::post('/settings', [ThemeController::class, 'updateSettings'])->name('settings.update');
    Route::post('/reset', [ThemeController::class, 'reset'])->name('reset');
});

// Themes alias (for menu compatibility)
Route::get('/themes', [ThemeController::class, 'index'])->name('themes.index');

// Media Management
Route::get('/media', [\App\Http\Controllers\Admin\MediaController::class, 'index'])->name('media.index');
Route::post('/media/upload', [\App\Http\Controllers\Admin\MediaController::class, 'upload'])->name('media.upload');
Route::delete('/media/{id}', [\App\Http\Controllers\Admin\MediaController::class, 'destroy'])->name('media.destroy');

// Blog Management
Route::resource('blogs', \App\Http\Controllers\Admin\BlogController::class);

// Settings Management
Route::prefix('settings')->name('settings.')->group(function () {
    Route::get('/', [SettingController::class, 'general'])->name('index');
    Route::get('/general', [SettingController::class, 'general'])->name('general');
    Route::post('/general', [SettingController::class, 'updateGeneral'])->name('general.update');
    Route::get('/store', [SettingController::class, 'store'])->name('store');
    Route::post('/store', [SettingController::class, 'updateStore'])->name('store.update');
    Route::get('/email', [SettingController::class, 'email'])->name('email');
    Route::post('/email', [SettingController::class, 'updateEmail'])->name('email.update');
    Route::post('/email/test', [SettingController::class, 'testEmail'])->name('email.test');
    Route::get('/seo', [SettingController::class, 'seo'])->name('seo');
    Route::post('/seo', [SettingController::class, 'updateSeo'])->name('seo.update');
    Route::get('/social', [SettingController::class, 'social'])->name('social');
    Route::post('/social', [SettingController::class, 'updateSocial'])->name('social.update');
    Route::get('/social-login', [SettingController::class, 'socialLogin'])->name('social-login');
    Route::put('/social-login', [SettingController::class, 'updateSocialLogin'])->name('social-login.update');
    Route::get('/whatsapp', [SettingController::class, 'whatsapp'])->name('whatsapp');
    Route::post('/whatsapp', [SettingController::class, 'updateWhatsapp'])->name('whatsapp.update');
    Route::get('/footer', [SettingController::class, 'footer'])->name('footer');
    Route::post('/footer', [SettingController::class, 'updateFooter'])->name('footer.update');
    Route::get('/maintenance', [SettingController::class, 'maintenance'])->name('maintenance');
    Route::post('/maintenance', [SettingController::class, 'updateMaintenance'])->name('maintenance.update');
});

// Payment Settings
Route::prefix('payment')->name('payment.')->group(function () {
    Route::get('/', [PaymentController::class, 'index'])->name('index');
    Route::post('/bkash', [PaymentController::class, 'updateBkash'])->name('bkash.update');
    Route::post('/sslcommerz', [PaymentController::class, 'updateSslcommerz'])->name('sslcommerz.update');
    Route::post('/nagad', [PaymentController::class, 'updateNagad'])->name('nagad.update');
    Route::post('/rocket', [PaymentController::class, 'updateRocket'])->name('rocket.update');
    Route::post('/cod', [PaymentController::class, 'updateCod'])->name('cod.update');
    Route::post('/toggle/{gateway}', [PaymentController::class, 'toggle'])->name('toggle');
});

// Payment Gateways alias (for menu compatibility)
Route::get('/payment-gateways', [PaymentController::class, 'index'])->name('payment-gateways.index');

// SEO Management
Route::prefix('seo')->name('seo.')->group(function () {
    Route::get('/', [SeoController::class, 'index'])->name('index');
    Route::post('/meta', [SeoController::class, 'updateMeta'])->name('meta.update');
    Route::post('/sitemap/generate', [SeoController::class, 'generateSitemap'])->name('sitemap.generate');
    Route::get('/redirects', [SeoController::class, 'redirects'])->name('redirects');
    Route::post('/redirects', [SeoController::class, 'storeRedirect'])->name('redirects.store');
    Route::delete('/redirects/{id}', [SeoController::class, 'deleteRedirect'])->name('redirects.destroy');
});

// Chat Management
Route::prefix('chat')->name('chat.')->group(function () {
    Route::get('/', [ChatController::class, 'index'])->name('index');
    Route::get('/conversations', [ChatController::class, 'conversations'])->name('conversations');
    Route::get('/conversation/{id}', [ChatController::class, 'conversation'])->name('conversation');
    Route::post('/send', [ChatController::class, 'send'])->name('send');
    Route::post('/ai-settings', [ChatController::class, 'aiSettings'])->name('ai-settings');
});

// Reports
Route::prefix('reports')->name('reports.')->group(function () {
    Route::get('/sales', [ReportController::class, 'sales'])->name('sales');
    Route::get('/products', [ReportController::class, 'products'])->name('products');
    Route::get('/customers', [ReportController::class, 'customers'])->name('customers');
    Route::get('/inventory', [ReportController::class, 'inventory'])->name('inventory');
    Route::get('/export/{type}', [ReportController::class, 'export'])->name('export');
});

// Profile
Route::get('/profile', [DashboardController::class, 'profile'])->name('profile');
Route::post('/profile', [DashboardController::class, 'updateProfile'])->name('profile.update');
Route::post('/password', [DashboardController::class, 'updatePassword'])->name('password.update');

// Backup & Restore
Route::get('/backup', [SettingController::class, 'backup'])->name('backup');
Route::post('/backup/create', [SettingController::class, 'createBackup'])->name('backup.create');
Route::get('/backup/download/{file}', [SettingController::class, 'downloadBackup'])->name('backup.download');
Route::post('/backup/restore', [SettingController::class, 'restoreBackup'])->name('backup.restore');
