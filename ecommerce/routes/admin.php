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
use App\Http\Controllers\Admin\CustomerGroupController;

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

// Products Management - Custom routes (must be before resource routes to avoid conflicts)
Route::get('/products/in-house', [ProductController::class, 'inHouse'])->name('products.in-house');
Route::get('/products/in-house/export', [ProductController::class, 'exportInHouse'])->name('products.export-in-house');
Route::post('/products/in-house/bulk-action', [ProductController::class, 'inHouseBulkAction'])->name('products.in-house.bulk-action');
Route::post('/products/bulk-stock-update', [ProductController::class, 'bulkStockUpdate'])->name('products.bulk-stock-update');
Route::get('/products/low-stock-alerts', [ProductController::class, 'lowStockAlerts'])->name('products.low-stock-alerts');
Route::post('/products/bulk-action', [ProductController::class, 'bulkAction'])->name('products.bulk-action');
Route::get('/products-export', [ProductController::class, 'export'])->name('products.export');
Route::get('/products/{product}/duplicate', [ProductController::class, 'duplicate'])->name('products.duplicate');
Route::post('/products/{product}/images', [ProductController::class, 'uploadImages'])->name('products.images.upload');
Route::delete('/products/{product}/images/{image}', [ProductController::class, 'deleteImage'])->name('products.images.delete');
Route::post('/products/{product}/toggle-status', [ProductController::class, 'toggleStatus'])->name('products.toggle-status');
Route::post('/products/{product}/toggle-featured', [ProductController::class, 'toggleFeatured'])->name('products.toggle-featured');
Route::post('/products/{product}/quick-update', [ProductController::class, 'quickUpdate'])->name('products.quick-update');
Route::post('/products/{product}/update-stock', [ProductController::class, 'updateStock'])->name('products.update-stock');
Route::post('/products/{product}/update-low-stock-threshold', [ProductController::class, 'updateLowStockThreshold'])->name('products.update-low-stock-threshold');

// Bulk Import, Export, Discount Routes (must be before resource routes)
Route::get('/products/bulk-import', [ProductController::class, 'bulkImport'])->name('products.bulk-import');
Route::post('/products/bulk-import', [ProductController::class, 'processBulkImport'])->name('products.bulk-import.process');
Route::get('/products/bulk-export', [ProductController::class, 'bulkExport'])->name('products.bulk-export');
Route::get('/products/bulk-export/download', [ProductController::class, 'exportProducts'])->name('products.bulk-export.download');
Route::get('/products/bulk-discount', [ProductController::class, 'bulkDiscount'])->name('products.bulk-discount');
Route::post('/products/bulk-discount', [ProductController::class, 'applyBulkDiscount'])->name('products.bulk-discount.apply');
Route::post('/products/bulk-discount/remove', [ProductController::class, 'removeBulkDiscount'])->name('products.bulk-discount.remove');
Route::get('/products/bulk-discount/products', [ProductController::class, 'getProductsForSelection'])->name('products.bulk-discount.products');
Route::put('/products/{product}/discount', [ProductController::class, 'updateProductDiscount'])->name('products.discount.update');
Route::delete('/products/{product}/discount', [ProductController::class, 'removeProductDiscount'])->name('products.discount.remove');

// Related Products Management
Route::get('/products/{product}/related', [ProductController::class, 'relatedProducts'])->name('products.related');
Route::get('/products/{product}/related/search', [ProductController::class, 'searchRelatedProducts'])->name('products.related.search');
Route::post('/products/{product}/related', [ProductController::class, 'addRelatedProducts'])->name('products.related.add');
Route::delete('/products/{product}/related/{relatedId}', [ProductController::class, 'removeRelatedProduct'])->name('products.related.remove');
Route::post('/products/{product}/related/order', [ProductController::class, 'updateRelatedProductsOrder'])->name('products.related.order');
Route::post('/products/{product}/related/bulk-remove', [ProductController::class, 'bulkRemoveRelatedProducts'])->name('products.related.bulk-remove');
Route::get('/products/{product}/related/auto-suggest', [ProductController::class, 'autoSuggestRelatedProducts'])->name('products.related.auto-suggest');

// Digital Products Management (must be before resource routes)
Route::prefix('products/digital')->name('products.digital.')->group(function () {
    Route::get('/', [\App\Http\Controllers\Admin\DigitalProductController::class, 'index'])->name('index');
    Route::get('/create', [\App\Http\Controllers\Admin\DigitalProductController::class, 'create'])->name('create');
    Route::post('/', [\App\Http\Controllers\Admin\DigitalProductController::class, 'store'])->name('store');
    Route::get('/export', [\App\Http\Controllers\Admin\DigitalProductController::class, 'export'])->name('export');
    Route::post('/bulk-action', [\App\Http\Controllers\Admin\DigitalProductController::class, 'bulkAction'])->name('bulk-action');
    Route::get('/{id}/edit', [\App\Http\Controllers\Admin\DigitalProductController::class, 'edit'])->name('edit');
    Route::put('/{id}', [\App\Http\Controllers\Admin\DigitalProductController::class, 'update'])->name('update');
    Route::delete('/{id}', [\App\Http\Controllers\Admin\DigitalProductController::class, 'destroy'])->name('destroy');
    Route::post('/{id}/toggle-status', [\App\Http\Controllers\Admin\DigitalProductController::class, 'toggleStatus'])->name('toggle-status');
    Route::post('/{id}/toggle-featured', [\App\Http\Controllers\Admin\DigitalProductController::class, 'toggleFeatured'])->name('toggle-featured');
    
    // License Key Management
    Route::post('/{id}/generate-license-keys', [\App\Http\Controllers\Admin\DigitalProductController::class, 'generateLicenseKeys'])->name('generate-license-keys');
    Route::get('/{id}/license-keys', [\App\Http\Controllers\Admin\DigitalProductController::class, 'getLicenseKeys'])->name('license-keys');
    Route::get('/{id}/license-keys/export', [\App\Http\Controllers\Admin\DigitalProductController::class, 'exportLicenseKeys'])->name('export-license-keys');
    Route::delete('/{id}/license-keys/{keyId}', [\App\Http\Controllers\Admin\DigitalProductController::class, 'deleteLicenseKey'])->name('delete-license-key');
    Route::post('/{id}/license-keys/{keyId}/disable', [\App\Http\Controllers\Admin\DigitalProductController::class, 'disableLicenseKey'])->name('disable-license-key');
    
    // Additional Files Management
    Route::delete('/{id}/additional-files/{index}', [\App\Http\Controllers\Admin\DigitalProductController::class, 'deleteAdditionalFile'])->name('delete-additional-file');
    
    // Download Statistics
    Route::get('/{id}/download-stats', [\App\Http\Controllers\Admin\DigitalProductController::class, 'downloadStats'])->name('download-stats');
});

// Digital Categories Management
Route::resource('digital-categories', \App\Http\Controllers\Admin\DigitalCategoryController::class);
Route::post('/digital-categories/{digitalCategory}/toggle-status', [\App\Http\Controllers\Admin\DigitalCategoryController::class, 'toggleStatus'])->name('digital-categories.toggle-status');
Route::post('/digital-categories/update-order', [\App\Http\Controllers\Admin\DigitalCategoryController::class, 'updateOrder'])->name('digital-categories.update-order');
Route::get('/digital-categories/api/categories', [\App\Http\Controllers\Admin\DigitalCategoryController::class, 'getCategories'])->name('digital-categories.api.categories');

// Products Resource Routes
Route::resource('products', ProductController::class);

// Categories Management
Route::resource('categories', CategoryController::class);
Route::post('/categories/bulk-action', [CategoryController::class, 'bulkAction'])->name('categories.bulk-action');
Route::post('/categories/reorder', [CategoryController::class, 'reorder'])->name('categories.reorder');
Route::get('/categories/tree', [CategoryController::class, 'tree'])->name('categories.tree');
Route::get('/categories-export', [CategoryController::class, 'export'])->name('categories.export');
Route::post('/categories/{category}/toggle-status', [CategoryController::class, 'toggleStatus'])->name('categories.toggle-status');
Route::post('/categories/{category}/toggle-featured', [CategoryController::class, 'toggleFeatured'])->name('categories.toggle-featured');
Route::post('/categories/{category}/toggle-menu', [CategoryController::class, 'toggleMenu'])->name('categories.toggle-menu');
Route::post('/categories/{category}/toggle-homepage', [CategoryController::class, 'toggleHomepage'])->name('categories.toggle-homepage');
Route::get('/categories/{category}/products', [CategoryController::class, 'getProducts'])->name('categories.products');
Route::post('/categories/{category}/move-products', [CategoryController::class, 'moveProducts'])->name('categories.move-products');
Route::get('/categories-select-options', [CategoryController::class, 'getSelectOptions'])->name('categories.select-options');

 // Inhouse Orders - MUST be before any wildcard routes
Route::get('/orders/in-house', [OrderController::class, 'inHouse'])->name('orders.in-house');
Route::get('/orders/in-house/create', [OrderController::class, 'create'])->name('orders.in-house.create');
Route::post('/orders/in-house', [OrderController::class, 'store'])->name('orders.in-house.store');
Route::get('/orders/in-house/{order}', [OrderController::class, 'inHouseShow'])->name('orders.in-house.show');

// Seller Orders - MUST be before resource routes
Route::get('/orders/seller', [OrderController::class, 'seller'])->name('orders.seller');
Route::get('/orders/seller/{order}', [OrderController::class, 'sellerShow'])->name('orders.seller.show');

// Pickup Point Orders - MUST be before resource routes
Route::get('/orders/pickup-point', [OrderController::class, 'pickupPoint'])->name('orders.pickup-point');
Route::get('/orders/pickup-point/{order}', [OrderController::class, 'pickupPointShow'])->name('orders.pickup-point.show');
Route::post('/orders/pickup-point/{order}/picked-up', [OrderController::class, 'markAsPickedUp'])->name('orders.pickup-point.picked-up');

// Pickup Points Management (Delivery Section)
Route::resource('pickup-points', \App\Http\Controllers\Admin\PickupPointController::class);
Route::post('/pickup-points/{pickupPoint}/toggle-status', [\App\Http\Controllers\Admin\PickupPointController::class, 'toggleStatus'])->name('pickup-points.toggle-status');
Route::get('/api/pickup-points', [\App\Http\Controllers\Admin\PickupPointController::class, 'getPickupPoints'])->name('api.pickup-points');

// Orders Management - Resource route MUST be LAST
Route::resource('orders', OrderController::class)->only(['index', 'show', 'update']);

Route::post('/orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.status');
Route::post('/orders/{order}/payment-status', [OrderController::class, 'updatePaymentStatus'])->name('orders.payment-status');
Route::get('/orders/{order}/invoice', [OrderController::class, 'invoice'])->name('orders.invoice');
Route::post('/orders/{order}/ship', [OrderController::class, 'ship'])->name('orders.ship');

// Customer Groups - Must be defined BEFORE the customers resource route
Route::prefix('customers/groups')->name('customers.groups.')->group(function () {
    Route::get('/', [CustomerGroupController::class, 'index'])->name('index');
    Route::get('/create', [CustomerGroupController::class, 'create'])->name('create');
    Route::post('/', [CustomerGroupController::class, 'store'])->name('store');
    Route::get('/{customerGroup}/edit', [CustomerGroupController::class, 'edit'])->name('edit');
    Route::put('/{customerGroup}', [CustomerGroupController::class, 'update'])->name('update');
    Route::delete('/{customerGroup}', [CustomerGroupController::class, 'destroy'])->name('destroy');
    Route::post('/{customerGroup}/toggle-status', [CustomerGroupController::class, 'toggleStatus'])->name('toggle-status');
});

// Customer Segmentation - MUST be defined BEFORE customers resource route
Route::prefix('customers/segmentation')->name('customers.segmentation.')->group(function () {
    Route::get('/', [\App\Http\Controllers\Admin\CustomerSegmentationController::class, 'index'])->name('index');
    Route::get('/create', [\App\Http\Controllers\Admin\CustomerSegmentationController::class, 'create'])->name('create');
    Route::post('/', [\App\Http\Controllers\Admin\CustomerSegmentationController::class, 'store'])->name('store');
    Route::get('/preview', [\App\Http\Controllers\Admin\CustomerSegmentationController::class, 'preview'])->name('preview');
    Route::get('/{segment}/edit', [\App\Http\Controllers\Admin\CustomerSegmentationController::class, 'edit'])->name('edit');
    Route::put('/{segment}', [\App\Http\Controllers\Admin\CustomerSegmentationController::class, 'update'])->name('update');
    Route::delete('/{segment}', [\App\Http\Controllers\Admin\CustomerSegmentationController::class, 'destroy'])->name('destroy');
    Route::post('/{segment}/toggle-status', [\App\Http\Controllers\Admin\CustomerSegmentationController::class, 'toggleStatus'])->name('toggle-status');
    Route::get('/{segment}/preview', [\App\Http\Controllers\Admin\CustomerSegmentationController::class, 'preview'])->name('preview.single');
    Route::get('/{segment}/export', [\App\Http\Controllers\Admin\CustomerSegmentationController::class, 'export'])->name('export');
    Route::get('/{segment}', [\App\Http\Controllers\Admin\CustomerSegmentationController::class, 'show'])->name('show');
});

// Loyalty Points routes - MUST be defined BEFORE the resource route to avoid 404 errors
Route::prefix('customers/loyalty')->name('customers.loyalty.')->group(function () {
    Route::get('/', [\App\Http\Controllers\Admin\LoyaltyPointsController::class, 'index'])->name('index');
    Route::get('/settings', [\App\Http\Controllers\Admin\LoyaltyPointsController::class, 'settings'])->name('settings');
    Route::post('/settings', [\App\Http\Controllers\Admin\LoyaltyPointsController::class, 'updateSettings'])->name('settings.update');
    Route::get('/export', [\App\Http\Controllers\Admin\LoyaltyPointsController::class, 'export'])->name('export');
    Route::get('/{customerId}', [\App\Http\Controllers\Admin\LoyaltyPointsController::class, 'show'])->name('show');
    Route::get('/{customerId}/transactions', [\App\Http\Controllers\Admin\LoyaltyPointsController::class, 'transactions'])->name('transactions');
    Route::post('/add-points', [\App\Http\Controllers\Admin\LoyaltyPointsController::class, 'addPoints'])->name('addPoints');
    Route::post('/deduct-points', [\App\Http\Controllers\Admin\LoyaltyPointsController::class, 'deductPoints'])->name('deductPoints');
    Route::post('/rewards', [\App\Http\Controllers\Admin\LoyaltyPointsController::class, 'createReward'])->name('createReward');
    Route::put('/rewards/{rewardId}', [\App\Http\Controllers\Admin\LoyaltyPointsController::class, 'updateReward'])->name('updateReward');
    Route::delete('/rewards/{rewardId}', [\App\Http\Controllers\Admin\LoyaltyPointsController::class, 'deleteReward'])->name('deleteReward');
    Route::post('/rewards/{rewardId}/toggle', [\App\Http\Controllers\Admin\LoyaltyPointsController::class, 'toggleReward'])->name('toggleReward');
});

// Customer Membership Plans - MUST be defined BEFORE the customers resource route to avoid 404 errors
Route::prefix('customers/membership')->name('customers.membership.')->group(function () {
    Route::get('/', [\App\Http\Controllers\Admin\MembershipPlanController::class, 'index'])->name('index');
    Route::get('/create', [\App\Http\Controllers\Admin\MembershipPlanController::class, 'create'])->name('create');
    Route::post('/', [\App\Http\Controllers\Admin\MembershipPlanController::class, 'store'])->name('store');
    Route::get('/{membershipPlan}/edit', [\App\Http\Controllers\Admin\MembershipPlanController::class, 'edit'])->name('edit');
    Route::put('/{membershipPlan}', [\App\Http\Controllers\Admin\MembershipPlanController::class, 'update'])->name('update');
    Route::delete('/{membershipPlan}', [\App\Http\Controllers\Admin\MembershipPlanController::class, 'destroy'])->name('destroy');
    Route::post('/{membershipPlan}/toggle-status', [\App\Http\Controllers\Admin\MembershipPlanController::class, 'toggleStatus'])->name('toggle-status');
    Route::post('/{membershipPlan}/toggle-featured', [\App\Http\Controllers\Admin\MembershipPlanController::class, 'toggleFeatured'])->name('toggle-featured');
});

// Customer Wallet - MUST be defined BEFORE customers resource route to avoid 404 errors
Route::prefix('customers/wallet')->name('customers.wallet.')->group(function () {
    Route::get('/', [\App\Http\Controllers\Admin\CustomerWalletController::class, 'index'])->name('index');
    Route::get('/transactions', [\App\Http\Controllers\Admin\CustomerWalletController::class, 'transactions'])->name('transactions');
    Route::get('/{customerId}', [\App\Http\Controllers\Admin\CustomerWalletController::class, 'show'])->name('show');
    Route::post('/add-balance', [\App\Http\Controllers\Admin\CustomerWalletController::class, 'addBalance'])->name('add-balance');
    Route::post('/deduct-balance', [\App\Http\Controllers\Admin\CustomerWalletController::class, 'deductBalance'])->name('deduct-balance');
    Route::get('/search-customers', [\App\Http\Controllers\Admin\CustomerWalletController::class, 'searchCustomers'])->name('search-customers');
});

// Customers Management
Route::resource('customers', CustomerController::class)->only(['index', 'show', 'update', 'destroy']);
Route::get('/customers/{customer}/orders', [CustomerController::class, 'orders'])->name('customers.orders');
Route::post('/customers/{customer}/login-as', [CustomerController::class, 'loginAs'])->name('customers.login-as');

// Coupons Management
Route::post('/coupons/{coupon}/toggle', [CouponController::class, 'toggle'])->name('coupons.toggle');
Route::resource('coupons', CouponController::class);

// Reviews Management
Route::resource('reviews', ReviewController::class)->only(['index', 'update', 'destroy']);
Route::post('/reviews/{review}/approve', [ReviewController::class, 'approve'])->name('reviews.approve');

// Wishlist Management
Route::prefix('wishlists')->name('wishlists.')->group(function () {
    Route::get('/', [\App\Http\Controllers\Admin\WishlistController::class, 'index'])->name('index');
    Route::delete('/delete', [\App\Http\Controllers\Admin\WishlistController::class, 'destroy'])->name('destroy');
    Route::delete('/{id}', [\App\Http\Controllers\Admin\WishlistController::class, 'destroySingle'])->name('destroy-single');
    Route::post('/bulk-action', [\App\Http\Controllers\Admin\WishlistController::class, 'bulkAction'])->name('bulk-action');
    Route::get('/export', [\App\Http\Controllers\Admin\WishlistController::class, 'export'])->name('export');
    Route::get('/product/{productId}', [\App\Http\Controllers\Admin\WishlistController::class, 'productWishlist'])->name('product');
    Route::get('/user/{userId}', [\App\Http\Controllers\Admin\WishlistController::class, 'userWishlist'])->name('user');
});
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
    Route::post('/section-order', [HomePageController::class, 'updateSectionOrder'])->name('section-order');
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
Route::delete('/media', [\App\Http\Controllers\Admin\MediaController::class, 'destroy'])->name('media.destroy');
Route::post('/media/bulk-delete', [\App\Http\Controllers\Admin\MediaController::class, 'bulkDelete'])->name('media.bulk-delete');
Route::get('/media/show', [\App\Http\Controllers\Admin\MediaController::class, 'show'])->name('media.show');
Route::post('/media/folder', [\App\Http\Controllers\Admin\MediaController::class, 'createFolder'])->name('media.folder.create');
Route::delete('/media/folder', [\App\Http\Controllers\Admin\MediaController::class, 'deleteFolder'])->name('media.folder.delete');
Route::get('/media/stats', [\App\Http\Controllers\Admin\MediaController::class, 'stats'])->name('media.stats');

// Blog Management
Route::resource('blogs', \App\Http\Controllers\Admin\BlogController::class);
Route::prefix('blog-settings')->name('blog-settings.')->group(function () {
    Route::get('/', [\App\Http\Controllers\Admin\BlogController::class, 'settings'])->name('index');
    Route::post('/', [\App\Http\Controllers\Admin\BlogController::class, 'updateSettings'])->name('update');
});

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
    
    // AJAX Routes - specific routes before parameterized routes to avoid 404
    Route::get('/conversations', [ChatController::class, 'conversations'])->name('conversations');
    Route::get('/conversation/{id}', [ChatController::class, 'conversation'])->name('conversation');
    Route::post('/send', [ChatController::class, 'send'])->name('send');
    Route::post('/typing', [ChatController::class, 'typing'])->name('typing');
    Route::get('/check-typing', [ChatController::class, 'checkTyping'])->name('check-typing');
    Route::post('/conversation/{id}/status', [ChatController::class, 'updateStatus'])->name('update-status');
    Route::post('/conversation/{id}/close', [ChatController::class, 'close'])->name('close');
    Route::post('/conversation/{id}/mark-unread', [ChatController::class, 'markAsUnread'])->name('mark-unread');
    Route::post('/conversation/{id}/mark-read', [ChatController::class, 'markAsRead'])->name('mark-read');
    Route::delete('/conversation/{id}', [ChatController::class, 'destroy'])->name('destroy');
    Route::get('/online-users', [ChatController::class, 'getOnlineUsers'])->name('online-users');
    
    // AI Settings
    Route::post('/ai-settings', [ChatController::class, 'aiSettings'])->name('ai-settings');
    
    // Chat Widget Settings
    Route::post('/widget-settings', [ChatController::class, 'widgetSettings'])->name('widget-settings');
    
    // Predefined Messages - CRUD
    Route::get('/predefined', [ChatController::class, 'predefinedMessages'])->name('predefined.index');
    Route::get('/predefined/messages', [ChatController::class, 'getPredefinedMessages'])->name('predefined.messages');
    Route::post('/predefined', [ChatController::class, 'storePredefinedMessage'])->name('predefined.store');
    Route::get('/predefined/{id}/edit', [ChatController::class, 'editPredefinedMessage'])->name('predefined.edit');
    Route::put('/predefined/{id}', [ChatController::class, 'updatePredefinedMessage'])->name('predefined.update');
    Route::delete('/predefined/{id}', [ChatController::class, 'destroyPredefinedMessage'])->name('predefined.destroy');
    Route::post('/predefined/toggle/{id}', [ChatController::class, 'togglePredefinedMessage'])->name('predefined.toggle');
    Route::post('/predefined/reorder', [ChatController::class, 'reorderPredefinedMessages'])->name('predefined.reorder');
    
    // Delete conversation
    Route::delete('/{id}', [ChatController::class, 'destroy'])->name('destroy');
});

// Reports
Route::prefix('reports')->name('reports.')->group(function () {
    // Products Wishlist - MUST be before resource routes to avoid 404
    Route::get('/wishlist', [ReportController::class, 'productsWishlist'])->name('wishlist');
    Route::get('/wishlist/export', [ReportController::class, 'productsWishlistExport'])->name('wishlist.export');
    
    // In-House Product Sale - MUST be before resource routes to avoid 404
    Route::get('/in-house-product-sale', [ReportController::class, 'inHouseProductSale'])->name('in-house-product-sale');
    Route::get('/in-house-product-sale/export', [ReportController::class, 'inHouseProductSaleExport'])->name('in-house-product-sale.export');
    
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

/*
|--------------------------------------------------------------------------
| New Feature Routes (Placeholders)
|--------------------------------------------------------------------------
| These routes are placeholders for new features. Controllers will be created
| when implementing each feature.
*/

// Attributes Management
Route::resource('attributes', \App\Http\Controllers\Admin\AttributeController::class);
Route::post('/attributes/{attribute}/toggle-status', [\App\Http\Controllers\Admin\AttributeController::class, 'toggleStatus'])->name('attributes.toggle-status');
Route::post('/attributes/{attribute}/toggle-filterable', [\App\Http\Controllers\Admin\AttributeController::class, 'toggleFilterable'])->name('attributes.toggle-filterable');
Route::post('/attributes/bulk-action', [\App\Http\Controllers\Admin\AttributeController::class, 'bulkAction'])->name('attributes.bulk-action');
Route::get('/attributes-export', [\App\Http\Controllers\Admin\AttributeController::class, 'export'])->name('attributes.export');
Route::post('/attributes/{attribute}/values', [\App\Http\Controllers\Admin\AttributeController::class, 'storeValue'])->name('attributes.values.store');
Route::put('/attributes/{attribute}/values/{value}', [\App\Http\Controllers\Admin\AttributeController::class, 'updateValue'])->name('attributes.values.update');
Route::delete('/attributes/{attribute}/values/{value}', [\App\Http\Controllers\Admin\AttributeController::class, 'destroyValue'])->name('attributes.values.destroy');
Route::post('/attributes/{attribute}/values/{value}/toggle-status', [\App\Http\Controllers\Admin\AttributeController::class, 'toggleValueStatus'])->name('attributes.values.toggle-status');
Route::get('/attributes/{attribute}/values', [\App\Http\Controllers\Admin\AttributeController::class, 'getValues'])->name('attributes.values.list');

// Colors Management
Route::resource('colors', \App\Http\Controllers\Admin\ColorController::class);
Route::post('/colors/{color}/toggle-status', [\App\Http\Controllers\Admin\ColorController::class, 'toggleStatus'])->name('colors.toggle-status');
Route::post('/colors/bulk-action', [\App\Http\Controllers\Admin\ColorController::class, 'bulkAction'])->name('colors.bulk-action');
Route::get('/colors-export', [\App\Http\Controllers\Admin\ColorController::class, 'export'])->name('colors.export');
Route::get('/colors/api/list', [\App\Http\Controllers\Admin\ColorController::class, 'getColors'])->name('colors.api.list');

// Brands Management
Route::resource('brands', \App\Http\Controllers\Admin\BrandController::class);
Route::post('/brands/{brand}/toggle-status', [\App\Http\Controllers\Admin\BrandController::class, 'toggleStatus'])->name('brands.toggle-status');
Route::post('/brands/{brand}/toggle-featured', [\App\Http\Controllers\Admin\BrandController::class, 'toggleFeatured'])->name('brands.toggle-featured');
Route::post('/brands/bulk-action', [\App\Http\Controllers\Admin\BrandController::class, 'bulkAction'])->name('brands.bulk-action');
Route::get('/brands-export', [\App\Http\Controllers\Admin\BrandController::class, 'export'])->name('brands.export');
Route::get('/brands/api/list', [\App\Http\Controllers\Admin\BrandController::class, 'getBrands'])->name('brands.api.list');

// Product Bundles
Route::resource('product-bundles', \App\Http\Controllers\Admin\ProductBundleController::class);
Route::post('/product-bundles/{productBundle}/toggle-status', [\App\Http\Controllers\Admin\ProductBundleController::class, 'toggleStatus'])->name('product-bundles.toggle-status');
Route::post('/product-bundles/{productBundle}/toggle-featured', [\App\Http\Controllers\Admin\ProductBundleController::class, 'toggleFeatured'])->name('product-bundles.toggle-featured');
Route::post('/product-bundles/bulk-action', [\App\Http\Controllers\Admin\ProductBundleController::class, 'bulkAction'])->name('product-bundles.bulk-action');
Route::get('/product-bundles-export', [\App\Http\Controllers\Admin\ProductBundleController::class, 'export'])->name('product-bundles.export');
Route::get('/product-bundles/api/products', [\App\Http\Controllers\Admin\ProductBundleController::class, 'getProducts'])->name('product-bundles.api.products');

// Product Q&A
Route::resource('product-qa', \App\Http\Controllers\Admin\ProductQAController::class);
Route::post('/product-qa/bulk-action', [\App\Http\Controllers\Admin\ProductQAController::class, 'bulkAction'])->name('product-qa.bulk-action');
Route::post('/product-qa/{product_qa}/toggle-featured', [\App\Http\Controllers\Admin\ProductQAController::class, 'toggleFeatured'])->name('product-qa.toggle-featured');
Route::post('/product-qa/{product_qa}/quick-answer', [\App\Http\Controllers\Admin\ProductQAController::class, 'quickAnswer'])->name('product-qa.quick-answer');
Route::post('/product-qa/{product_qa}/update-status', [\App\Http\Controllers\Admin\ProductQAController::class, 'updateStatus'])->name('product-qa.update-status');

// Wishlist Management
Route::prefix('wishlist-management')->name('wishlist-management.')->group(function () {
    Route::get('/', [\App\Http\Controllers\Admin\PlaceholderController::class, 'wishlistManagement'])->name('index');
    Route::get('/analytics', [\App\Http\Controllers\Admin\PlaceholderController::class, 'wishlistAnalytics'])->name('analytics');
    Route::get('/conversions', [\App\Http\Controllers\Admin\PlaceholderController::class, 'wishlistConversions'])->name('conversions');
});

// Inventory Management
Route::prefix('inventory')->name('inventory.')->group(function () {
    Route::get('/', [\App\Http\Controllers\Admin\InventoryController::class, 'index'])->name('index');
    Route::get('/stock-alerts', [\App\Http\Controllers\Admin\InventoryController::class, 'stockAlerts'])->name('stock-alerts');
    Route::get('/stock-history', [\App\Http\Controllers\Admin\InventoryController::class, 'stockHistory'])->name('stock-history');
    Route::post('/adjust', [\App\Http\Controllers\Admin\InventoryController::class, 'adjustStock'])->name('adjust');
    Route::post('/bulk-adjust', [\App\Http\Controllers\Admin\InventoryController::class, 'bulkAdjust'])->name('bulk-adjust');
    Route::get('/product/{id}', [\App\Http\Controllers\Admin\InventoryController::class, 'getProduct'])->name('product');
    Route::post('/threshold', [\App\Http\Controllers\Admin\InventoryController::class, 'updateThreshold'])->name('threshold');
});

// Delivery Management
Route::prefix('delivery')->name('delivery.')->group(function () {
    Route::get('/', [\App\Http\Controllers\Admin\DeliveryController::class, 'index'])->name('index');
    Route::post('/quick-ship', [\App\Http\Controllers\Admin\DeliveryController::class, 'quickShip'])->name('quick-ship');
    Route::post('/mark-delivered', [\App\Http\Controllers\Admin\DeliveryController::class, 'markDelivered'])->name('mark-delivered');
    Route::get('/orders', [\App\Http\Controllers\Admin\DeliveryController::class, 'getOrders'])->name('orders');
    Route::get('/partners', [\App\Http\Controllers\Admin\DeliveryController::class, 'partners'])->name('partners.index');
    Route::get('/partners/create', [\App\Http\Controllers\Admin\DeliveryController::class, 'createPartner'])->name('partners.create');
    Route::post('/partners', [\App\Http\Controllers\Admin\DeliveryController::class, 'storePartner'])->name('partners.store');
    Route::get('/partners/{partner}/edit', [\App\Http\Controllers\Admin\DeliveryController::class, 'editPartner'])->name('partners.edit');
    Route::put('/partners/{partner}', [\App\Http\Controllers\Admin\DeliveryController::class, 'updatePartner'])->name('partners.update');
    Route::delete('/partners/{partner}', [\App\Http\Controllers\Admin\DeliveryController::class, 'destroyPartner'])->name('partners.destroy');
    Route::post('/partners/{partner}/toggle-status', [\App\Http\Controllers\Admin\DeliveryController::class, 'togglePartnerStatus'])->name('partners.toggle-status');
    Route::post('/partners/{partner}/toggle-featured', [\App\Http\Controllers\Admin\DeliveryController::class, 'togglePartnerFeatured'])->name('partners.toggle-featured');
    Route::post('/partners/bulk-action', [\App\Http\Controllers\Admin\DeliveryController::class, 'bulkPartnerAction'])->name('partners.bulk-action');
    Route::get('/carriers', [\App\Http\Controllers\Admin\DeliveryController::class, 'carriers'])->name('carriers.index');
    Route::get('/carriers/create', [\App\Http\Controllers\Admin\DeliveryController::class, 'createCarrier'])->name('carriers.create');
    Route::post('/carriers', [\App\Http\Controllers\Admin\DeliveryController::class, 'storeCarrier'])->name('carriers.store');
    Route::get('/carriers/{carrier}/edit', [\App\Http\Controllers\Admin\DeliveryController::class, 'editCarrier'])->name('carriers.edit');
    Route::put('/carriers/{carrier}', [\App\Http\Controllers\Admin\DeliveryController::class, 'updateCarrier'])->name('carriers.update');
    Route::delete('/carriers/{carrier}', [\App\Http\Controllers\Admin\DeliveryController::class, 'destroyCarrier'])->name('carriers.destroy');
    Route::post('/carriers/{carrier}/toggle-status', [\App\Http\Controllers\Admin\DeliveryController::class, 'toggleCarrierStatus'])->name('carriers.toggle-status');
    Route::post('/carriers/{carrier}/toggle-featured', [\App\Http\Controllers\Admin\DeliveryController::class, 'toggleCarrierFeatured'])->name('carriers.toggle-featured');
    Route::post('/carriers/bulk-action', [\App\Http\Controllers\Admin\DeliveryController::class, 'bulkCarrierAction'])->name('carriers.bulk-action');
    Route::get('/tracking', [\App\Http\Controllers\Admin\DeliveryController::class, 'tracking'])->name('tracking');
    Route::post('/tracking/{order}/update-status', [\App\Http\Controllers\Admin\DeliveryController::class, 'updateTrackingStatus'])->name('tracking.update-status');
    Route::post('/tracking/{order}/generate-number', [\App\Http\Controllers\Admin\DeliveryController::class, 'generateTrackingNumber'])->name('tracking.generate-number');
    Route::post('/tracking/bulk-action', [\App\Http\Controllers\Admin\DeliveryController::class, 'bulkTrackingAction'])->name('bulk-tracking-action');
    Route::get('/zones', [\App\Http\Controllers\Admin\DeliveryController::class, 'zones'])->name('zones.index');
    Route::get('/zones/create', [\App\Http\Controllers\Admin\DeliveryController::class, 'createZone'])->name('zones.create');
    Route::post('/zones', [\App\Http\Controllers\Admin\DeliveryController::class, 'storeZone'])->name('zones.store');
    Route::get('/zones/{zone}/edit', [\App\Http\Controllers\Admin\DeliveryController::class, 'editZone'])->name('zones.edit');
    Route::put('/zones/{zone}', [\App\Http\Controllers\Admin\DeliveryController::class, 'updateZone'])->name('zones.update');
    Route::delete('/zones/{zone}', [\App\Http\Controllers\Admin\DeliveryController::class, 'destroyZone'])->name('zones.destroy');
    Route::post('/zones/{zone}/toggle-status', [\App\Http\Controllers\Admin\DeliveryController::class, 'toggleZoneStatus'])->name('zones.toggle-status');
    Route::post('/zones/{zone}/toggle-default', [\App\Http\Controllers\Admin\DeliveryController::class, 'toggleZoneDefault'])->name('zones.toggle-default');
    Route::post('/zones/bulk-action', [\App\Http\Controllers\Admin\DeliveryController::class, 'bulkZoneAction'])->name('zones.bulk-action');
    Route::get('/courier-integration', [\App\Http\Controllers\Admin\DeliveryController::class, 'courierIntegration'])->name('courier-integration');
    Route::post('/courier-integration/add', [\App\Http\Controllers\Admin\DeliveryController::class, 'addCourierFromTemplate'])->name('courier-integration.add');
    Route::get('/delivery-boys', [\App\Http\Controllers\Admin\DeliveryController::class, 'deliveryBoys'])->name('delivery-boys.index');
    Route::get('/delivery-boys/create', [\App\Http\Controllers\Admin\DeliveryController::class, 'createDeliveryBoy'])->name('delivery-boys.create');
    Route::post('/delivery-boys', [\App\Http\Controllers\Admin\DeliveryController::class, 'storeDeliveryBoy'])->name('delivery-boys.store');
    Route::get('/delivery-boys/{deliveryBoy}/edit', [\App\Http\Controllers\Admin\DeliveryController::class, 'editDeliveryBoy'])->name('delivery-boys.edit');
    Route::put('/delivery-boys/{deliveryBoy}', [\App\Http\Controllers\Admin\DeliveryController::class, 'updateDeliveryBoy'])->name('delivery-boys.update');
    Route::delete('/delivery-boys/{deliveryBoy}', [\App\Http\Controllers\Admin\DeliveryController::class, 'destroyDeliveryBoy'])->name('delivery-boys.destroy');
    Route::post('/delivery-boys/{deliveryBoy}/toggle-status', [\App\Http\Controllers\Admin\DeliveryController::class, 'toggleDeliveryBoyStatus'])->name('delivery-boys.toggle-status');
    Route::post('/delivery-boys/{deliveryBoy}/toggle-availability', [\App\Http\Controllers\Admin\DeliveryController::class, 'toggleDeliveryBoyAvailability'])->name('delivery-boys.toggle-availability');
    Route::post('/delivery-boys/bulk-action', [\App\Http\Controllers\Admin\DeliveryController::class, 'bulkDeliveryBoyAction'])->name('delivery-boys.bulk-action');
    Route::get('/schedules', [\App\Http\Controllers\Admin\DeliveryController::class, 'schedules'])->name('schedules.index');
    Route::get('/schedules/create', [\App\Http\Controllers\Admin\DeliveryController::class, 'createSchedule'])->name('schedules.create');
    Route::post('/schedules', [\App\Http\Controllers\Admin\DeliveryController::class, 'storeSchedule'])->name('schedules.store');
    Route::get('/schedules/{id}/edit', [\App\Http\Controllers\Admin\DeliveryController::class, 'editSchedule'])->name('schedules.edit');
    Route::put('/schedules/{id}', [\App\Http\Controllers\Admin\DeliveryController::class, 'updateSchedule'])->name('schedules.update');
    Route::delete('/schedules/{id}', [\App\Http\Controllers\Admin\DeliveryController::class, 'destroySchedule'])->name('schedules.destroy');
    Route::post('/schedules/{id}/toggle', [\App\Http\Controllers\Admin\DeliveryController::class, 'toggleScheduleStatus'])->name('schedules.toggle');
    Route::post('/schedules/bulk-action', [\App\Http\Controllers\Admin\DeliveryController::class, 'bulkScheduleAction'])->name('schedules.bulk-action');
    Route::get('/reports', [\App\Http\Controllers\Admin\DeliveryController::class, 'reports'])->name('reports');
    Route::get('/reports/export', [\App\Http\Controllers\Admin\DeliveryController::class, 'exportReports'])->name('reports.export');
});

// Quotations
Route::prefix('quotations')->name('quotations.')->group(function () {
    Route::get('/', [\App\Http\Controllers\Admin\QuotationController::class, 'index'])->name('index');
    Route::get('/create', [\App\Http\Controllers\Admin\QuotationController::class, 'create'])->name('create');
    Route::post('/', [\App\Http\Controllers\Admin\QuotationController::class, 'store'])->name('store');
    Route::get('/{quotation}', [\App\Http\Controllers\Admin\QuotationController::class, 'show'])->name('show');
    Route::get('/{quotation}/edit', [\App\Http\Controllers\Admin\QuotationController::class, 'edit'])->name('edit');
    Route::put('/{quotation}', [\App\Http\Controllers\Admin\QuotationController::class, 'update'])->name('update');
    Route::delete('/{quotation}', [\App\Http\Controllers\Admin\QuotationController::class, 'destroy'])->name('destroy');
    Route::post('/{quotation}/send', [\App\Http\Controllers\Admin\QuotationController::class, 'send'])->name('send');
    Route::post('/{quotation}/convert-to-order', [\App\Http\Controllers\Admin\QuotationController::class, 'convertToOrder'])->name('convert-to-order');
    Route::post('/{quotation}/status', [\App\Http\Controllers\Admin\QuotationController::class, 'updateStatus'])->name('status');
    Route::get('/{quotation}/print', [\App\Http\Controllers\Admin\QuotationController::class, 'print'])->name('print');
    Route::get('/{quotation}/download', [\App\Http\Controllers\Admin\QuotationController::class, 'download'])->name('download');
    Route::post('/bulk-action', [\App\Http\Controllers\Admin\QuotationController::class, 'bulkAction'])->name('bulk-action');
    Route::get('/api/products/search', [\App\Http\Controllers\Admin\QuotationController::class, 'searchProducts'])->name('api.products.search');
    Route::get('/api/product', [\App\Http\Controllers\Admin\QuotationController::class, 'getProduct'])->name('api.product');
});

// Subscriptions
Route::prefix('subscriptions')->name('subscriptions.')->group(function () {
    Route::get('/', [\App\Http\Controllers\Admin\SubscriptionController::class, 'index'])->name('index');
    Route::get('/create', [\App\Http\Controllers\Admin\SubscriptionController::class, 'create'])->name('create');
    Route::post('/', [\App\Http\Controllers\Admin\SubscriptionController::class, 'store'])->name('store');
    Route::get('/{subscription}', [\App\Http\Controllers\Admin\SubscriptionController::class, 'show'])->name('show');
    Route::get('/{subscription}/edit', [\App\Http\Controllers\Admin\SubscriptionController::class, 'edit'])->name('edit');
    Route::put('/{subscription}', [\App\Http\Controllers\Admin\SubscriptionController::class, 'update'])->name('update');
    Route::delete('/{subscription}', [\App\Http\Controllers\Admin\SubscriptionController::class, 'destroy'])->name('destroy');
    
    // Actions
    Route::post('/{subscription}/activate', [\App\Http\Controllers\Admin\SubscriptionController::class, 'activate'])->name('activate');
    Route::post('/{subscription}/pause', [\App\Http\Controllers\Admin\SubscriptionController::class, 'pause'])->name('pause');
    Route::post('/{subscription}/cancel', [\App\Http\Controllers\Admin\SubscriptionController::class, 'cancel'])->name('cancel');
    Route::post('/{subscription}/process-billing', [\App\Http\Controllers\Admin\SubscriptionController::class, 'processBilling'])->name('process-billing');
    
    // Bulk Actions
    Route::post('/bulk-action', [\App\Http\Controllers\Admin\SubscriptionController::class, 'bulkAction'])->name('bulk-action');
    
    // API Routes
    Route::get('/api/customer/{user}', [\App\Http\Controllers\Admin\SubscriptionController::class, 'getCustomerDetails'])->name('customer-details');
    Route::get('/api/product/{product}', [\App\Http\Controllers\Admin\SubscriptionController::class, 'getProductDetails'])->name('product-details');
});

// Refund Management
Route::prefix('refunds')->name('refunds.')->group(function () {
    Route::get('/', [\App\Http\Controllers\Admin\RefundController::class, 'index'])->name('index');
    Route::get('/requests', [\App\Http\Controllers\Admin\RefundController::class, 'requests'])->name('requests');
    Route::get('/approved', [\App\Http\Controllers\Admin\RefundController::class, 'approved'])->name('approved');
    Route::get('/rejected', [\App\Http\Controllers\Admin\RefundController::class, 'rejected'])->name('rejected');
    Route::get('/configuration', [\App\Http\Controllers\Admin\RefundController::class, 'configuration'])->name('configuration');
    Route::post('/configuration', [\App\Http\Controllers\Admin\RefundController::class, 'updateConfiguration'])->name('configuration.update');
    Route::get('/{id}', [\App\Http\Controllers\Admin\RefundController::class, 'show'])->name('show');
    Route::post('/{id}/approve', [\App\Http\Controllers\Admin\RefundController::class, 'approve'])->name('approve');
    Route::post('/{id}/reject', [\App\Http\Controllers\Admin\RefundController::class, 'reject'])->name('reject');
    Route::post('/{id}/process', [\App\Http\Controllers\Admin\RefundController::class, 'process'])->name('process');
});

// Sellers (B2B) Management
Route::prefix('sellers')->name('sellers.')->group(function () {
    // Specific routes MUST come before resource routes to avoid 404 errors
    
    // Payout Routes - must come before /{id} route
    Route::get('/payouts', [\App\Http\Controllers\Admin\SellerController::class, 'payouts'])->name('payouts');
    Route::get('/payouts/create/{id}', [\App\Http\Controllers\Admin\SellerController::class, 'createPayout'])->name('payouts.create');
    Route::post('/payouts/create/{id}', [\App\Http\Controllers\Admin\SellerController::class, 'storePayout'])->name('payouts.store');
    Route::get('/payouts/{id}', [\App\Http\Controllers\Admin\SellerController::class, 'showPayout'])->name('payouts.show');
    
    // Payout Requests Routes
    Route::get('/payout-requests', [\App\Http\Controllers\Admin\SellerController::class, 'payoutRequests'])->name('payout-requests');
    Route::post('/payout-requests/{id}/approve', [\App\Http\Controllers\Admin\SellerController::class, 'approvePayout'])->name('payout-requests.approve');
    Route::post('/payout-requests/{id}/reject', [\App\Http\Controllers\Admin\SellerController::class, 'rejectPayout'])->name('payout-requests.reject');
    
    // Commission Routes
    Route::get('/commission', [\App\Http\Controllers\Admin\SellerController::class, 'commission'])->name('commission');
    Route::post('/commission', [\App\Http\Controllers\Admin\SellerController::class, 'updateCommission'])->name('commission.update');
    
    // Verification Routes
    Route::get('/verification', [\App\Http\Controllers\Admin\SellerController::class, 'verification'])->name('verification');
    Route::post('/verification/{id}', [\App\Http\Controllers\Admin\SellerController::class, 'processVerification'])->name('verification.process');
    
    // Seller CRUD Routes - these use {id} so must come after specific routes
    Route::get('/', [\App\Http\Controllers\Admin\SellerController::class, 'index'])->name('index');
    Route::get('/create', [\App\Http\Controllers\Admin\SellerController::class, 'create'])->name('create');
    Route::post('/', [\App\Http\Controllers\Admin\SellerController::class, 'store'])->name('store');
    Route::get('/{id}', [\App\Http\Controllers\Admin\SellerController::class, 'show'])->name('show');
    Route::get('/{id}/edit', [\App\Http\Controllers\Admin\SellerController::class, 'edit'])->name('edit');
    Route::put('/{id}', [\App\Http\Controllers\Admin\SellerController::class, 'update'])->name('update');
    Route::delete('/{id}', [\App\Http\Controllers\Admin\SellerController::class, 'destroy'])->name('destroy');
    
    // Status management
    Route::post('/{id}/approve', [\App\Http\Controllers\Admin\SellerController::class, 'approve'])->name('approve');
    Route::post('/{id}/reject', [\App\Http\Controllers\Admin\SellerController::class, 'reject'])->name('reject');
    Route::post('/{id}/suspend', [\App\Http\Controllers\Admin\SellerController::class, 'suspend'])->name('suspend');
    Route::post('/{id}/activate', [\App\Http\Controllers\Admin\SellerController::class, 'activate'])->name('activate');
    
    // Bulk actions
    Route::post('/bulk-update', [\App\Http\Controllers\Admin\SellerController::class, 'bulkUpdateStatus'])->name('bulk-update');
    Route::post('/bulk-delete', [\App\Http\Controllers\Admin\SellerController::class, 'bulkDelete'])->name('bulk-delete');
});

// Reports - Additional Routes
Route::prefix('reports')->name('reports.')->group(function () {
    Route::get('/seller-sales', [ReportController::class, 'sellerSales'])->name('seller-sales');
    Route::get('/seller-sales/export', [ReportController::class, 'sellerSalesExport'])->name('seller-sales.export');
    Route::get('/user-searches', [ReportController::class, 'userSearches'])->name('user-searches');
    Route::get('/user-searches/export', [ReportController::class, 'userSearchesExport'])->name('user-searches.export');
    Route::get('/commission-history', [ReportController::class, 'commissionHistory'])->name('commission-history');
    Route::get('/commission-history/export', [ReportController::class, 'commissionHistoryExport'])->name('commission-history.export');
    Route::get('/wallet-history', [ReportController::class, 'walletHistory'])->name('wallet-history');
    Route::get('/wallet-history/export', [ReportController::class, 'walletHistoryExport'])->name('wallet-history.export');
});

// Marketing
Route::prefix('marketing')->name('marketing.')->group(function () {
    // Flash Deals - must be before wildcard routes
    Route::get('/flash-deals/create', [\App\Http\Controllers\Admin\FlashDealController::class, 'create'])->name('flash-deals.create');
    Route::post('/flash-deals', [\App\Http\Controllers\Admin\FlashDealController::class, 'store'])->name('flash-deals.store');
    Route::get('/flash-deals/{id}/edit', [\App\Http\Controllers\Admin\FlashDealController::class, 'edit'])->name('flash-deals.edit');
    Route::put('/flash-deals/{id}', [\App\Http\Controllers\Admin\FlashDealController::class, 'update'])->name('flash-deals.update');
    Route::delete('/flash-deals/{id}', [\App\Http\Controllers\Admin\FlashDealController::class, 'destroy'])->name('flash-deals.destroy');
    Route::post('/flash-deals/{id}/toggle-status', [\App\Http\Controllers\Admin\FlashDealController::class, 'toggleStatus'])->name('flash-deals.toggle-status');
    Route::get('/flash-deals/{id}/products', [\App\Http\Controllers\Admin\FlashDealController::class, 'products'])->name('flash-deals.products');
    Route::put('/flash-deals/{id}/products', [\App\Http\Controllers\Admin\FlashDealController::class, 'updateProducts'])->name('flash-deals.products.update');
    Route::post('/flash-deals/{id}/products', [\App\Http\Controllers\Admin\FlashDealController::class, 'addProducts'])->name('flash-deals.add-products');
    Route::delete('/flash-deals/{id}/products/{productId}', [\App\Http\Controllers\Admin\FlashDealController::class, 'removeProduct'])->name('flash-deals.remove-product');
    Route::put('/flash-deals/{id}/products/{productId}', [\App\Http\Controllers\Admin\FlashDealController::class, 'updateProduct'])->name('flash-deals.update-product');
    Route::get('/flash-deals', [\App\Http\Controllers\Admin\FlashDealController::class, 'index'])->name('flash-deals.index');
    
    Route::get('/newsletters', [\App\Http\Controllers\Admin\NewsletterController::class, 'index'])->name('newsletters.index');
    Route::get('/newsletters/create', [\App\Http\Controllers\Admin\NewsletterController::class, 'create'])->name('newsletters.create');
    Route::post('/newsletters', [\App\Http\Controllers\Admin\NewsletterController::class, 'store'])->name('newsletters.store');
    Route::get('/newsletters/{newsletter}/edit', [\App\Http\Controllers\Admin\NewsletterController::class, 'edit'])->name('newsletters.edit');
    Route::put('/newsletters/{newsletter}', [\App\Http\Controllers\Admin\NewsletterController::class, 'update'])->name('newsletters.update');
    Route::delete('/newsletters/{newsletter}', [\App\Http\Controllers\Admin\NewsletterController::class, 'destroy'])->name('newsletters.destroy');
    Route::post('/newsletters/{newsletter}/send', [\App\Http\Controllers\Admin\NewsletterController::class, 'send'])->name('newsletters.send');
    Route::get('/newsletters/{newsletter}/preview', [\App\Http\Controllers\Admin\NewsletterController::class, 'preview'])->name('newsletters.preview');
    Route::post('/newsletters/{newsletter}/duplicate', [\App\Http\Controllers\Admin\NewsletterController::class, 'duplicate'])->name('newsletters.duplicate');
    Route::get('/newsletters/recipient-count', [\App\Http\Controllers\Admin\NewsletterController::class, 'getRecipientCount'])->name('newsletters.recipient-count');
    
    Route::get('/bulk-sms', [\App\Http\Controllers\Admin\SmsController::class, 'index'])->name('bulk-sms.index');
    Route::post('/bulk-sms/send', [\App\Http\Controllers\Admin\SmsController::class, 'send'])->name('bulk-sms.send');
    Route::get('/bulk-sms/recipient-count', [\App\Http\Controllers\Admin\SmsController::class, 'getRecipientCount'])->name('bulk-sms.recipient-count');
    Route::delete('/bulk-sms/{id}', [\App\Http\Controllers\Admin\SmsController::class, 'destroy'])->name('bulk-sms.destroy');
    
    Route::get('/subscribers', [\App\Http\Controllers\Admin\SubscriberController::class, 'index'])->name('subscribers.index');
    Route::post('/subscribers', [\App\Http\Controllers\Admin\SubscriberController::class, 'store'])->name('subscribers.store');
    Route::post('/subscribers/export', [\App\Http\Controllers\Admin\SubscriberController::class, 'export'])->name('subscribers.export');
    Route::get('/subscribers/count', [\App\Http\Controllers\Admin\SubscriberController::class, 'getCount'])->name('subscribers.count');
    Route::delete('/subscribers/{subscriber}', [\App\Http\Controllers\Admin\SubscriberController::class, 'destroy'])->name('subscribers.destroy');
    Route::post('/subscribers/{subscriber}/unsubscribe', [\App\Http\Controllers\Admin\SubscriberController::class, 'unsubscribe'])->name('subscribers.unsubscribe');
    Route::post('/subscribers/{subscriber}/resubscribe', [\App\Http\Controllers\Admin\SubscriberController::class, 'resubscribe'])->name('subscribers.resubscribe');
    
    // Abandoned Cart Recovery
    Route::prefix('abandoned-cart')->name('abandoned-cart.')->group(function () {
        // Index - must be first
        Route::get('/', [\App\Http\Controllers\Admin\AbandonedCartController::class, 'index'])->name('index');
        // Specific routes BEFORE wildcard
        Route::get('/settings', [\App\Http\Controllers\Admin\AbandonedCartController::class, 'settings'])->name('settings');
        Route::post('/settings', [\App\Http\Controllers\Admin\AbandonedCartController::class, 'updateSettings'])->name('settings.update');
        Route::get('/conversion-tracking', [\App\Http\Controllers\Admin\AbandonedCartController::class, 'conversionTracking'])->name('conversion-tracking');
        // Wildcard routes AFTER specific routes
        Route::get('/{id}', [\App\Http\Controllers\Admin\AbandonedCartController::class, 'show'])->name('show');
        Route::post('/{id}/send-reminder', [\App\Http\Controllers\Admin\AbandonedCartController::class, 'sendReminder'])->name('send-reminder');
        Route::post('/{id}/mark-recovered', [\App\Http\Controllers\Admin\AbandonedCartController::class, 'markRecovered'])->name('mark-recovered');
        Route::delete('/{id}', [\App\Http\Controllers\Admin\AbandonedCartController::class, 'destroy'])->name('destroy');
    });
    
    // Gift Cards - Specific routes before wildcard routes (404 solution)
    Route::prefix('gift-cards')->name('gift-cards.')->group(function () {
        // Index - must be first
        Route::get('/', [\App\Http\Controllers\Admin\GiftCardController::class, 'index'])->name('index');
        // Specific routes BEFORE wildcard
        Route::get('/create', [\App\Http\Controllers\Admin\GiftCardController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\Admin\GiftCardController::class, 'store'])->name('store');
        Route::get('/generate-code', [\App\Http\Controllers\Admin\GiftCardController::class, 'generateCode'])->name('generate-code');
        // Wildcard routes AFTER specific routes
        Route::get('/{id}/edit', [\App\Http\Controllers\Admin\GiftCardController::class, 'edit'])->name('edit');
        Route::put('/{id}', [\App\Http\Controllers\Admin\GiftCardController::class, 'update'])->name('update');
        Route::delete('/{id}', [\App\Http\Controllers\Admin\GiftCardController::class, 'destroy'])->name('destroy');
        Route::post('/{id}/toggle-status', [\App\Http\Controllers\Admin\GiftCardController::class, 'toggleStatus'])->name('toggle-status');
    });
    
    // Push Notifications - Specific routes before wildcard routes (404 solution)
    Route::prefix('push-notifications')->name('push-notifications.')->group(function () {
        // Index - must be first
        Route::get('/', [\App\Http\Controllers\Admin\PushNotificationController::class, 'index'])->name('index');
        // Specific routes BEFORE wildcard
        Route::get('/create', [\App\Http\Controllers\Admin\PushNotificationController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\Admin\PushNotificationController::class, 'store'])->name('store');
        Route::get('/recipient-count', [\App\Http\Controllers\Admin\PushNotificationController::class, 'getRecipientCount'])->name('recipient-count');
        // Wildcard routes AFTER specific routes
        Route::get('/{pushNotification}/edit', [\App\Http\Controllers\Admin\PushNotificationController::class, 'edit'])->name('edit');
        Route::put('/{pushNotification}', [\App\Http\Controllers\Admin\PushNotificationController::class, 'update'])->name('update');
        Route::delete('/{pushNotification}', [\App\Http\Controllers\Admin\PushNotificationController::class, 'destroy'])->name('destroy');
        Route::post('/{pushNotification}/send', [\App\Http\Controllers\Admin\PushNotificationController::class, 'send'])->name('send');
        Route::post('/{pushNotification}/duplicate', [\App\Http\Controllers\Admin\PushNotificationController::class, 'duplicate'])->name('duplicate');
    });
    
    // Price Rules - Specific routes before wildcard routes (404 solution)
    Route::prefix('price-rules')->name('price-rules.')->group(function () {
        // Index - must be first
        Route::get('/', [\App\Http\Controllers\Admin\PriceRuleController::class, 'index'])->name('index');
        // Specific routes BEFORE wildcard
        Route::get('/create', [\App\Http\Controllers\Admin\PriceRuleController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\Admin\PriceRuleController::class, 'store'])->name('store');
        Route::get('/products/{id}', [\App\Http\Controllers\Admin\PriceRuleController::class, 'products'])->name('products');
        Route::put('/products/{id}', [\App\Http\Controllers\Admin\PriceRuleController::class, 'updateProducts'])->name('products.update');
        Route::post('/products/{id}', [\App\Http\Controllers\Admin\PriceRuleController::class, 'addProducts'])->name('products.add');
        Route::delete('/products/{id}/products/{productId}', [\App\Http\Controllers\Admin\PriceRuleController::class, 'removeProduct'])->name('products.remove');
        Route::post('/{id}/toggle-status', [\App\Http\Controllers\Admin\PriceRuleController::class, 'toggleStatus'])->name('toggle-status');
        Route::get('/get-products', [\App\Http\Controllers\Admin\PriceRuleController::class, 'getProducts'])->name('get-products');
        // Wildcard routes AFTER specific routes
        Route::get('/{id}/edit', [\App\Http\Controllers\Admin\PriceRuleController::class, 'edit'])->name('edit');
        Route::put('/{id}', [\App\Http\Controllers\Admin\PriceRuleController::class, 'update'])->name('update');
        Route::delete('/{id}', [\App\Http\Controllers\Admin\PriceRuleController::class, 'destroy'])->name('destroy');
    });
});

// Support - Additional Routes
// IMPORTANT: Specific routes must come before parameterized routes to avoid 404 errors
Route::prefix('support')->name('support.')->group(function () {
    // Product Queries (must come before /tickets/{id})
    Route::get('/product-queries', [\App\Http\Controllers\Admin\SupportController::class, 'productQueries'])->name('product-queries.index');
    Route::post('/product-queries/{id}/reply', [\App\Http\Controllers\Admin\SupportController::class, 'replyQuery'])->name('product-queries.reply');
    
    // Tickets - List
    Route::get('/tickets', [\App\Http\Controllers\Admin\SupportController::class, 'tickets'])->name('tickets.index');
    
    // Tickets - Bulk Actions (before parameterized routes)
    Route::post('/tickets/bulk-action', [\App\Http\Controllers\Admin\SupportController::class, 'bulkAction'])->name('tickets.bulk-action');
    
    // Tickets - Specific routes (before wildcard {id})
    Route::post('/tickets/{id}/reply', [\App\Http\Controllers\Admin\SupportController::class, 'replyTicket'])->name('tickets.reply');
    Route::post('/tickets/{id}/close', [\App\Http\Controllers\Admin\SupportController::class, 'closeTicket'])->name('tickets.close');
    Route::get('/tickets/{id}/reopen', [\App\Http\Controllers\Admin\SupportController::class, 'reopenTicket'])->name('tickets.reopen');
    Route::post('/tickets/{id}/update-status', [\App\Http\Controllers\Admin\SupportController::class, 'updateTicketStatus'])->name('tickets.update-status');
    Route::post('/tickets/{id}/assign', [\App\Http\Controllers\Admin\SupportController::class, 'assignTicket'])->name('tickets.assign');
    
    // Tickets - Show and Delete (wildcard {id} at the end)
    Route::get('/tickets/{id}', [\App\Http\Controllers\Admin\SupportController::class, 'showTicket'])->name('tickets.show');
    Route::delete('/tickets/{id}', [\App\Http\Controllers\Admin\SupportController::class, 'destroyTicket'])->name('tickets.destroy');
});

// OTP System
Route::prefix('otp')->name('otp.')->group(function () {
    Route::get('/configuration', [\App\Http\Controllers\Admin\OtpController::class, 'configuration'])->name('configuration');
    Route::post('/configuration', [\App\Http\Controllers\Admin\OtpController::class, 'updateConfiguration'])->name('configuration.update');
    Route::get('/sms-templates', [\App\Http\Controllers\Admin\OtpController::class, 'smsTemplates'])->name('sms-templates');
    Route::post('/sms-templates', [\App\Http\Controllers\Admin\OtpController::class, 'updateSmsTemplates'])->name('sms-templates.update');
    Route::get('/credentials', [\App\Http\Controllers\Admin\OtpController::class, 'credentials'])->name('credentials');
    Route::post('/credentials', [\App\Http\Controllers\Admin\OtpController::class, 'updateCredentials'])->name('credentials.update');
});

// Settings - Additional Routes
Route::prefix('settings')->name('settings.')->group(function () {
    Route::get('/features', [SettingController::class, 'features'])->name('features');
    Route::post('/features', [SettingController::class, 'updateFeatures'])->name('features.update');
    Route::get('/languages', [SettingController::class, 'languages'])->name('languages');
    Route::post('/languages', [SettingController::class, 'storeLanguage'])->name('languages.store');
    Route::put('/languages/{id}', [SettingController::class, 'updateLanguage'])->name('languages.update');
    Route::delete('/languages/{id}', [SettingController::class, 'destroyLanguage'])->name('languages.destroy');
    Route::get('/currency', [SettingController::class, 'currency'])->name('currency');
    Route::post('/currency', [SettingController::class, 'updateCurrency'])->name('currency.update');
    Route::get('/vat-tax', [SettingController::class, 'vatTax'])->name('vat-tax');
    Route::post('/vat-tax', [SettingController::class, 'updateVatTax'])->name('vat-tax.update');
    Route::get('/order-configuration', [SettingController::class, 'orderConfiguration'])->name('order-configuration');
    Route::post('/order-configuration', [SettingController::class, 'updateOrderConfiguration'])->name('order-configuration.update');
    Route::get('/file-system', [SettingController::class, 'fileSystem'])->name('file-system');
    Route::post('/file-system/clear-cache', [SettingController::class, 'clearCache'])->name('file-system.clear-cache');
    Route::get('/shipping', [SettingController::class, 'shipping'])->name('shipping');
    Route::post('/shipping', [SettingController::class, 'updateShipping'])->name('shipping.update');
    
    // Email Templates
    Route::prefix('email-templates')->name('email-templates.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\PlaceholderController::class, 'emailTemplates'])->name('index');
        Route::get('/{id}/edit', [\App\Http\Controllers\Admin\PlaceholderController::class, 'editEmailTemplate'])->name('edit');
        Route::put('/{id}', [\App\Http\Controllers\Admin\PlaceholderController::class, 'updateEmailTemplate'])->name('update');
    });
    
    // Notification Settings
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\PlaceholderController::class, 'notificationSettings'])->name('index');
        Route::post('/', [\App\Http\Controllers\Admin\PlaceholderController::class, 'updateNotificationSettings'])->name('update');
    });
});

// Warehouse Management
Route::prefix('warehouses')->name('warehouses.')->group(function () {
    Route::get('/', [\App\Http\Controllers\Admin\WarehouseController::class, 'index'])->name('index');
    Route::get('/create', [\App\Http\Controllers\Admin\WarehouseController::class, 'create'])->name('create');
    Route::post('/', [\App\Http\Controllers\Admin\WarehouseController::class, 'store'])->name('store');
    Route::get('/{id}', [\App\Http\Controllers\Admin\WarehouseController::class, 'show'])->name('show');
    Route::get('/{id}/edit', [\App\Http\Controllers\Admin\WarehouseController::class, 'edit'])->name('edit');
    Route::put('/{id}', [\App\Http\Controllers\Admin\WarehouseController::class, 'update'])->name('update');
    Route::delete('/{id}', [\App\Http\Controllers\Admin\WarehouseController::class, 'destroy'])->name('destroy');
    Route::get('/{id}/inventory', [\App\Http\Controllers\Admin\WarehouseController::class, 'inventory'])->name('inventory');
});

// Staff Management
Route::prefix('staffs')->name('staffs.')->group(function () {
    Route::get('/', [\App\Http\Controllers\Admin\StaffController::class, 'index'])->name('index');
    Route::get('/create', [\App\Http\Controllers\Admin\StaffController::class, 'create'])->name('create');
    Route::post('/', [\App\Http\Controllers\Admin\StaffController::class, 'store'])->name('store');
    Route::get('/{id}/edit', [\App\Http\Controllers\Admin\StaffController::class, 'edit'])->name('edit');
    Route::put('/{id}', [\App\Http\Controllers\Admin\StaffController::class, 'update'])->name('update');
    Route::delete('/{id}', [\App\Http\Controllers\Admin\StaffController::class, 'destroy'])->name('destroy');
    Route::get('/warehouse', [\App\Http\Controllers\Admin\StaffController::class, 'warehouse'])->name('warehouse');
    Route::get('/permissions', [\App\Http\Controllers\Admin\StaffController::class, 'permissions'])->name('permissions');
    Route::post('/permissions', [\App\Http\Controllers\Admin\StaffController::class, 'updatePermissions'])->name('permissions.update');
});

// System Management
Route::prefix('system')->name('system.')->group(function () {
    Route::get('/update', [\App\Http\Controllers\Admin\SystemController::class, 'update'])->name('update');
    Route::post('/update', [\App\Http\Controllers\Admin\SystemController::class, 'performUpdate'])->name('update.perform');
    Route::get('/server-status', [\App\Http\Controllers\Admin\SystemController::class, 'serverStatus'])->name('server-status');
    
    // Activity Logs
    Route::prefix('activity-logs')->name('activity-logs.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\PlaceholderController::class, 'activityLogs'])->name('index');
        Route::get('/admin', [\App\Http\Controllers\Admin\PlaceholderController::class, 'adminActivityLogs'])->name('admin');
        Route::get('/customer', [\App\Http\Controllers\Admin\PlaceholderController::class, 'customerActivityLogs'])->name('customer');
    });
    
    // Data Export/Import
    Route::prefix('data-export')->name('data-export.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\PlaceholderController::class, 'dataExportImport'])->name('index');
        Route::post('/export', [\App\Http\Controllers\Admin\PlaceholderController::class, 'exportData'])->name('export');
        Route::post('/import', [\App\Http\Controllers\Admin\PlaceholderController::class, 'importData'])->name('import');
    });
});

// POS Management
Route::prefix('pos')->name('pos.')->group(function () {
    Route::get('/terminal', [\App\Http\Controllers\Admin\PlaceholderController::class, 'posTerminal'])->name('terminal');
    Route::get('/cash-register', [\App\Http\Controllers\Admin\PlaceholderController::class, 'cashRegister'])->name('cash-register');
    Route::get('/reports', [\App\Http\Controllers\Admin\PlaceholderController::class, 'posReports'])->name('reports');
});

// Multi-Store Management
Route::prefix('multi-store')->name('multi-store.')->group(function () {
    Route::get('/locations', [\App\Http\Controllers\Admin\PlaceholderController::class, 'storeLocations'])->name('locations');
    Route::get('/settings', [\App\Http\Controllers\Admin\PlaceholderController::class, 'storeSettings'])->name('settings');
    Route::get('/inventory', [\App\Http\Controllers\Admin\PlaceholderController::class, 'storeInventory'])->name('inventory');
    Route::post('/locations/create', [\App\Http\Controllers\Admin\PlaceholderController::class, 'createStoreLocation'])->name('locations.create');
    Route::put('/locations/{id}', [\App\Http\Controllers\Admin\PlaceholderController::class, 'updateStoreLocation'])->name('locations.update');
    Route::delete('/locations/{id}', [\App\Http\Controllers\Admin\PlaceholderController::class, 'deleteStoreLocation'])->name('locations.destroy');
});

// Addon Manager
Route::prefix('addons')->name('addons.')->group(function () {
    Route::get('/', [\App\Http\Controllers\Admin\AddonController::class, 'index'])->name('index');
    Route::get('/install', [\App\Http\Controllers\Admin\AddonController::class, 'install'])->name('install');
    Route::post('/install', [\App\Http\Controllers\Admin\AddonController::class, 'processInstall'])->name('install.process');
    Route::post('/{id}/activate', [\App\Http\Controllers\Admin\AddonController::class, 'activate'])->name('activate');
    Route::post('/{id}/deactivate', [\App\Http\Controllers\Admin\AddonController::class, 'deactivate'])->name('deactivate');
    Route::delete('/{id}', [\App\Http\Controllers\Admin\AddonController::class, 'destroy'])->name('destroy');
});

// Affiliate Management
Route::prefix('affiliate')->name('affiliate.')->group(function () {
    // Affiliate Users
    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\AffiliateController::class, 'users'])->name('index');
        Route::get('/{id}', [\App\Http\Controllers\Admin\AffiliateController::class, 'showUser'])->name('show');
        Route::post('/{id}/approve', [\App\Http\Controllers\Admin\AffiliateController::class, 'approveUser'])->name('approve');
        Route::post('/{id}/suspend', [\App\Http\Controllers\Admin\AffiliateController::class, 'suspendUser'])->name('suspend');
        Route::delete('/{id}', [\App\Http\Controllers\Admin\AffiliateController::class, 'destroyUser'])->name('destroy');
    });

    // Affiliate Configuration
    Route::get('/configuration', [\App\Http\Controllers\Admin\AffiliateController::class, 'configuration'])->name('configuration');
    Route::post('/configuration', [\App\Http\Controllers\Admin\AffiliateController::class, 'updateConfiguration'])->name('configuration.update');

    // Affiliate Payouts
    Route::get('/payouts', [\App\Http\Controllers\Admin\AffiliateController::class, 'payouts'])->name('payouts');
    Route::post('/payouts/{id}/approve', [\App\Http\Controllers\Admin\AffiliateController::class, 'approvePayout'])->name('payouts.approve');
    Route::post('/payouts/{id}/reject', [\App\Http\Controllers\Admin\AffiliateController::class, 'rejectPayout'])->name('payouts.reject');

    // Affiliate Requests
    Route::get('/requests', [\App\Http\Controllers\Admin\AffiliateController::class, 'requests'])->name('requests');
    Route::post('/requests/{id}/approve', [\App\Http\Controllers\Admin\AffiliateController::class, 'approveRequest'])->name('requests.approve');
    Route::post('/requests/{id}/reject', [\App\Http\Controllers\Admin\AffiliateController::class, 'rejectRequest'])->name('requests.reject');

    // Affiliate Categories
    Route::prefix('categories')->name('categories.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\AffiliateCategoryController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Admin\AffiliateCategoryController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\Admin\AffiliateCategoryController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [\App\Http\Controllers\Admin\AffiliateCategoryController::class, 'edit'])->name('edit');
        Route::put('/{id}', [\App\Http\Controllers\Admin\AffiliateCategoryController::class, 'update'])->name('update');
        Route::delete('/{id}', [\App\Http\Controllers\Admin\AffiliateCategoryController::class, 'destroy'])->name('destroy');
    });

    // Affiliate Products
    Route::prefix('products')->name('products.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\AffiliateProductController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Admin\AffiliateProductController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\Admin\AffiliateProductController::class, 'store'])->name('store');
        Route::get('/{id}', [\App\Http\Controllers\Admin\AffiliateProductController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [\App\Http\Controllers\Admin\AffiliateProductController::class, 'edit'])->name('edit');
        Route::put('/{id}', [\App\Http\Controllers\Admin\AffiliateProductController::class, 'update'])->name('update');
        Route::delete('/{id}', [\App\Http\Controllers\Admin\AffiliateProductController::class, 'destroy'])->name('destroy');
    });

    // Affiliate Links
    Route::prefix('links')->name('links.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\AffiliateLinkController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Admin\AffiliateLinkController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\Admin\AffiliateLinkController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [\App\Http\Controllers\Admin\AffiliateLinkController::class, 'edit'])->name('edit');
        Route::put('/{id}', [\App\Http\Controllers\Admin\AffiliateLinkController::class, 'update'])->name('update');
        Route::delete('/{id}', [\App\Http\Controllers\Admin\AffiliateLinkController::class, 'destroy'])->name('destroy');
    });

    // Affiliate Banners
    Route::prefix('banners')->name('banners.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\AffiliateBannerController::class, 'index'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Admin\AffiliateBannerController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\Admin\AffiliateBannerController::class, 'store'])->name('store');
        Route::get('/{id}/edit', [\App\Http\Controllers\Admin\AffiliateBannerController::class, 'edit'])->name('edit');
        Route::put('/{id}', [\App\Http\Controllers\Admin\AffiliateBannerController::class, 'update'])->name('update');
        Route::delete('/{id}', [\App\Http\Controllers\Admin\AffiliateBannerController::class, 'destroy'])->name('destroy');
    });

    // Affiliate Reports
    Route::get('/reports', [\App\Http\Controllers\Admin\AffiliateController::class, 'reports'])->name('reports');
    Route::get('/reports/export', [\App\Http\Controllers\Admin\AffiliateController::class, 'exportReports'])->name('reports.export');

    // Withdrawal Requests
    Route::prefix('withdrawals')->name('withdrawals.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\AffiliateWithdrawalController::class, 'index'])->name('index');
        Route::get('/{id}', [\App\Http\Controllers\Admin\AffiliateWithdrawalController::class, 'show'])->name('show');
        Route::post('/{id}/approve', [\App\Http\Controllers\Admin\AffiliateWithdrawalController::class, 'approve'])->name('approve');
        Route::post('/{id}/reject', [\App\Http\Controllers\Admin\AffiliateWithdrawalController::class, 'reject'])->name('reject');
    });
});

// Blog Categories
Route::prefix('blog-categories')->name('blog-categories.')->group(function () {
    Route::get('/', [\App\Http\Controllers\Admin\PlaceholderController::class, 'blogCategories'])->name('index');
    Route::get('/create', [\App\Http\Controllers\Admin\PlaceholderController::class, 'createBlogCategory'])->name('create');
    Route::post('/', [\App\Http\Controllers\Admin\PlaceholderController::class, 'storeBlogCategory'])->name('store');
    Route::get('/{id}/edit', [\App\Http\Controllers\Admin\PlaceholderController::class, 'editBlogCategory'])->name('edit');
    Route::put('/{id}', [\App\Http\Controllers\Admin\PlaceholderController::class, 'updateBlogCategory'])->name('update');
    Route::delete('/{id}', [\App\Http\Controllers\Admin\PlaceholderController::class, 'destroyBlogCategory'])->name('destroy');
});

// Blog Tags
Route::prefix('blog-tags')->name('blog-tags.')->group(function () {
    Route::get('/', [\App\Http\Controllers\Admin\PlaceholderController::class, 'blogTags'])->name('index');
    Route::get('/create', [\App\Http\Controllers\Admin\PlaceholderController::class, 'createBlogTag'])->name('create');
    Route::post('/', [\App\Http\Controllers\Admin\PlaceholderController::class, 'storeBlogTag'])->name('store');
    Route::get('/{id}/edit', [\App\Http\Controllers\Admin\PlaceholderController::class, 'editBlogTag'])->name('edit');
    Route::put('/{id}', [\App\Http\Controllers\Admin\PlaceholderController::class, 'updateBlogTag'])->name('update');
    Route::delete('/{id}', [\App\Http\Controllers\Admin\PlaceholderController::class, 'destroyBlogTag'])->name('destroy');
});

// FAQs Management
Route::prefix('faqs')->name('faqs.')->group(function () {
    Route::get('/', [\App\Http\Controllers\Admin\PlaceholderController::class, 'faqs'])->name('index');
    Route::get('/create', [\App\Http\Controllers\Admin\PlaceholderController::class, 'createFaq'])->name('create');
    Route::post('/', [\App\Http\Controllers\Admin\PlaceholderController::class, 'storeFaq'])->name('store');
    Route::get('/{id}/edit', [\App\Http\Controllers\Admin\PlaceholderController::class, 'editFaq'])->name('edit');
    Route::put('/{id}', [\App\Http\Controllers\Admin\PlaceholderController::class, 'updateFaq'])->name('update');
    Route::delete('/{id}', [\App\Http\Controllers\Admin\PlaceholderController::class, 'destroyFaq'])->name('destroy');
    Route::post('/reorder', [\App\Http\Controllers\Admin\PlaceholderController::class, 'reorderFaqs'])->name('reorder');
});

// Form Builder
Route::prefix('form-builder')->name('form-builder.')->group(function () {
    Route::get('/', [\App\Http\Controllers\Admin\PlaceholderController::class, 'formBuilder'])->name('index');
    Route::get('/create', [\App\Http\Controllers\Admin\PlaceholderController::class, 'createForm'])->name('create');
    Route::post('/', [\App\Http\Controllers\Admin\PlaceholderController::class, 'storeForm'])->name('store');
    Route::get('/{id}', [\App\Http\Controllers\Admin\PlaceholderController::class, 'showForm'])->name('show');
    Route::get('/{id}/edit', [\App\Http\Controllers\Admin\PlaceholderController::class, 'editForm'])->name('edit');
    Route::put('/{id}', [\App\Http\Controllers\Admin\PlaceholderController::class, 'updateForm'])->name('update');
    Route::delete('/{id}', [\App\Http\Controllers\Admin\PlaceholderController::class, 'destroyForm'])->name('destroy');
    Route::get('/{id}/submissions', [\App\Http\Controllers\Admin\PlaceholderController::class, 'formSubmissions'])->name('submissions');
    Route::get('/{id}/submissions/{submissionId}', [\App\Http\Controllers\Admin\PlaceholderController::class, 'showFormSubmission'])->name('submissions.show');
});

// Menu Builder
Route::prefix('menus')->name('menus.')->group(function () {
    Route::get('/', [\App\Http\Controllers\Admin\PlaceholderController::class, 'menus'])->name('index');
    Route::get('/create', [\App\Http\Controllers\Admin\PlaceholderController::class, 'createMenu'])->name('create');
    Route::post('/', [\App\Http\Controllers\Admin\PlaceholderController::class, 'storeMenu'])->name('store');
    Route::get('/{id}/edit', [\App\Http\Controllers\Admin\PlaceholderController::class, 'editMenu'])->name('edit');
    Route::put('/{id}', [\App\Http\Controllers\Admin\PlaceholderController::class, 'updateMenu'])->name('update');
    Route::delete('/{id}', [\App\Http\Controllers\Admin\PlaceholderController::class, 'destroyMenu'])->name('destroy');
    Route::post('/{id}/items', [\App\Http\Controllers\Admin\PlaceholderController::class, 'addMenuItem'])->name('items.store');
    Route::put('/{id}/items/{itemId}', [\App\Http\Controllers\Admin\PlaceholderController::class, 'updateMenuItem'])->name('items.update');
    Route::delete('/{id}/items/{itemId}', [\App\Http\Controllers\Admin\PlaceholderController::class, 'destroyMenuItem'])->name('items.destroy');
    Route::post('/{id}/items/reorder', [\App\Http\Controllers\Admin\PlaceholderController::class, 'reorderMenuItems'])->name('items.reorder');
});

// Widget Manager
Route::prefix('widgets')->name('widgets.')->group(function () {
    Route::get('/', [\App\Http\Controllers\Admin\PlaceholderController::class, 'widgets'])->name('index');
    Route::get('/create', [\App\Http\Controllers\Admin\PlaceholderController::class, 'createWidget'])->name('create');
    Route::post('/', [\App\Http\Controllers\Admin\PlaceholderController::class, 'storeWidget'])->name('store');
    Route::get('/{id}/edit', [\App\Http\Controllers\Admin\PlaceholderController::class, 'editWidget'])->name('edit');
    Route::put('/{id}', [\App\Http\Controllers\Admin\PlaceholderController::class, 'updateWidget'])->name('update');
    Route::delete('/{id}', [\App\Http\Controllers\Admin\PlaceholderController::class, 'destroyWidget'])->name('destroy');
    Route::post('/reorder', [\App\Http\Controllers\Admin\PlaceholderController::class, 'reorderWidgets'])->name('reorder');
});

// API Keys & Integrations
Route::prefix('api-keys')->name('api-keys.')->group(function () {
    Route::get('/', [\App\Http\Controllers\Admin\PlaceholderController::class, 'apiKeys'])->name('index');
    Route::post('/', [\App\Http\Controllers\Admin\PlaceholderController::class, 'storeApiKey'])->name('store');
    Route::put('/{id}', [\App\Http\Controllers\Admin\PlaceholderController::class, 'updateApiKey'])->name('update');
    Route::delete('/{id}', [\App\Http\Controllers\Admin\PlaceholderController::class, 'destroyApiKey'])->name('destroy');
    Route::post('/{id}/regenerate', [\App\Http\Controllers\Admin\PlaceholderController::class, 'regenerateApiKey'])->name('regenerate');
    Route::get('/webhooks', [\App\Http\Controllers\Admin\PlaceholderController::class, 'webhooks'])->name('webhooks');
    Route::post('/webhooks', [\App\Http\Controllers\Admin\PlaceholderController::class, 'storeWebhook'])->name('webhooks.store');
    Route::delete('/webhooks/{id}', [\App\Http\Controllers\Admin\PlaceholderController::class, 'destroyWebhook'])->name('webhooks.destroy');
});

// Settings - Additional Routes
Route::prefix('settings')->name('settings.')->group(function () {
    Route::get('/security', [\App\Http\Controllers\Admin\PlaceholderController::class, 'securitySettings'])->name('security');
    Route::post('/security', [\App\Http\Controllers\Admin\PlaceholderController::class, 'updateSecuritySettings'])->name('security.update');
    Route::get('/gdpr', [\App\Http\Controllers\Admin\PlaceholderController::class, 'gdprSettings'])->name('gdpr');
    Route::post('/gdpr', [\App\Http\Controllers\Admin\PlaceholderController::class, 'updateGdprSettings'])->name('gdpr.update');
    Route::get('/tax-classes', [\App\Http\Controllers\Admin\PlaceholderController::class, 'taxClasses'])->name('tax-classes');
    Route::post('/tax-classes', [\App\Http\Controllers\Admin\PlaceholderController::class, 'storeTaxClass'])->name('tax-classes.store');
    Route::put('/tax-classes/{id}', [\App\Http\Controllers\Admin\PlaceholderController::class, 'updateTaxClass'])->name('tax-classes.update');
    Route::delete('/tax-classes/{id}', [\App\Http\Controllers\Admin\PlaceholderController::class, 'destroyTaxClass'])->name('tax-classes.destroy');
});
