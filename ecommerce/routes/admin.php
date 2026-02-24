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

// Customer Groups
Route::prefix('customers/groups')->name('customers.groups.')->group(function () {
    Route::get('/', [\App\Http\Controllers\Admin\PlaceholderController::class, 'customerGroups'])->name('index');
    Route::get('/create', [\App\Http\Controllers\Admin\PlaceholderController::class, 'createCustomerGroup'])->name('create');
    Route::post('/', [\App\Http\Controllers\Admin\PlaceholderController::class, 'storeCustomerGroup'])->name('store');
    Route::get('/{id}/edit', [\App\Http\Controllers\Admin\PlaceholderController::class, 'editCustomerGroup'])->name('edit');
    Route::put('/{id}', [\App\Http\Controllers\Admin\PlaceholderController::class, 'updateCustomerGroup'])->name('update');
    Route::delete('/{id}', [\App\Http\Controllers\Admin\PlaceholderController::class, 'destroyCustomerGroup'])->name('destroy');
});

// Customer Segmentation
Route::prefix('customers/segmentation')->name('customers.segmentation.')->group(function () {
    Route::get('/', [\App\Http\Controllers\Admin\PlaceholderController::class, 'customerSegmentation'])->name('index');
    Route::get('/create', [\App\Http\Controllers\Admin\PlaceholderController::class, 'createSegment'])->name('create');
    Route::post('/', [\App\Http\Controllers\Admin\PlaceholderController::class, 'storeSegment'])->name('store');
    Route::delete('/{id}', [\App\Http\Controllers\Admin\PlaceholderController::class, 'destroySegment'])->name('destroy');
});

// Customer Loyalty Points
Route::prefix('customers/loyalty')->name('customers.loyalty.')->group(function () {
    Route::get('/', [\App\Http\Controllers\Admin\PlaceholderController::class, 'loyaltyPoints'])->name('index');
    Route::get('/settings', [\App\Http\Controllers\Admin\PlaceholderController::class, 'loyaltySettings'])->name('settings');
    Route::post('/settings', [\App\Http\Controllers\Admin\PlaceholderController::class, 'updateLoyaltySettings'])->name('settings.update');
});

// Customer Membership Plans
Route::prefix('customers/membership')->name('customers.membership.')->group(function () {
    Route::get('/', [\App\Http\Controllers\Admin\PlaceholderController::class, 'membershipPlans'])->name('index');
    Route::get('/create', [\App\Http\Controllers\Admin\PlaceholderController::class, 'createMembershipPlan'])->name('create');
    Route::post('/', [\App\Http\Controllers\Admin\PlaceholderController::class, 'storeMembershipPlan'])->name('store');
    Route::get('/{id}/edit', [\App\Http\Controllers\Admin\PlaceholderController::class, 'editMembershipPlan'])->name('edit');
    Route::put('/{id}', [\App\Http\Controllers\Admin\PlaceholderController::class, 'updateMembershipPlan'])->name('update');
    Route::delete('/{id}', [\App\Http\Controllers\Admin\PlaceholderController::class, 'destroyMembershipPlan'])->name('destroy');
});

// Customer Wallet
Route::prefix('customers/wallet')->name('customers.wallet.')->group(function () {
    Route::get('/', [\App\Http\Controllers\Admin\PlaceholderController::class, 'customerWallet'])->name('index');
    Route::get('/transactions', [\App\Http\Controllers\Admin\PlaceholderController::class, 'walletTransactions'])->name('transactions');
    Route::post('/add-balance', [\App\Http\Controllers\Admin\PlaceholderController::class, 'addWalletBalance'])->name('add-balance');
});

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
Route::delete('/media/{id}', [\App\Http\Controllers\Admin\MediaController::class, 'destroy'])->name('media.destroy');

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

/*
|--------------------------------------------------------------------------
| New Feature Routes (Placeholders)
|--------------------------------------------------------------------------
| These routes are placeholders for new features. Controllers will be created
| when implementing each feature.
*/

// Products - Additional Routes
Route::get('/brands', [CategoryController::class, 'brands'])->name('brands.index');
Route::get('/attributes', [ProductController::class, 'attributes'])->name('attributes.index');
Route::get('/colors', [ProductController::class, 'colors'])->name('colors.index');

// Product Bundles
Route::prefix('product-bundles')->name('product-bundles.')->group(function () {
    Route::get('/', [\App\Http\Controllers\Admin\PlaceholderController::class, 'productBundles'])->name('index');
    Route::get('/create', [\App\Http\Controllers\Admin\PlaceholderController::class, 'createProductBundle'])->name('create');
    Route::post('/', [\App\Http\Controllers\Admin\PlaceholderController::class, 'storeProductBundle'])->name('store');
    Route::get('/{id}/edit', [\App\Http\Controllers\Admin\PlaceholderController::class, 'editProductBundle'])->name('edit');
    Route::put('/{id}', [\App\Http\Controllers\Admin\PlaceholderController::class, 'updateProductBundle'])->name('update');
    Route::delete('/{id}', [\App\Http\Controllers\Admin\PlaceholderController::class, 'destroyProductBundle'])->name('destroy');
});

// Related Products Management
Route::prefix('related-products')->name('related-products.')->group(function () {
    Route::get('/', [\App\Http\Controllers\Admin\PlaceholderController::class, 'relatedProducts'])->name('index');
    Route::get('/cross-sells', [\App\Http\Controllers\Admin\PlaceholderController::class, 'crossSells'])->name('cross-sells');
    Route::get('/up-sells', [\App\Http\Controllers\Admin\PlaceholderController::class, 'upSells'])->name('up-sells');
    Route::post('/save-rules', [\App\Http\Controllers\Admin\PlaceholderController::class, 'saveRelatedProductsRules'])->name('save-rules');
});

// Product Q&A
Route::prefix('product-qa')->name('product-qa.')->group(function () {
    Route::get('/', [\App\Http\Controllers\Admin\PlaceholderController::class, 'productQA'])->name('index');
    Route::get('/approved', [\App\Http\Controllers\Admin\PlaceholderController::class, 'approvedQuestions'])->name('approved');
    Route::get('/pending', [\App\Http\Controllers\Admin\PlaceholderController::class, 'pendingQuestions'])->name('pending');
    Route::post('/{id}/approve', [\App\Http\Controllers\Admin\PlaceholderController::class, 'approveQuestion'])->name('approve');
    Route::post('/{id}/answer', [\App\Http\Controllers\Admin\PlaceholderController::class, 'answerQuestion'])->name('answer');
    Route::delete('/{id}', [\App\Http\Controllers\Admin\PlaceholderController::class, 'deleteQuestion'])->name('destroy');
    Route::get('/templates', [\App\Http\Controllers\Admin\PlaceholderController::class, 'questionTemplates'])->name('templates');
});

// Wishlist Management
Route::prefix('wishlist-management')->name('wishlist-management.')->group(function () {
    Route::get('/', [\App\Http\Controllers\Admin\PlaceholderController::class, 'wishlistManagement'])->name('index');
    Route::get('/analytics', [\App\Http\Controllers\Admin\PlaceholderController::class, 'wishlistAnalytics'])->name('analytics');
    Route::get('/conversions', [\App\Http\Controllers\Admin\PlaceholderController::class, 'wishlistConversions'])->name('conversions');
});

// Inventory Management
Route::prefix('inventory')->name('inventory.')->group(function () {
    Route::get('/', [\App\Http\Controllers\Admin\PlaceholderController::class, 'inventoryManagement'])->name('index');
    Route::get('/stock-alerts', [\App\Http\Controllers\Admin\PlaceholderController::class, 'stockAlerts'])->name('stock-alerts');
    Route::get('/low-stock', [\App\Http\Controllers\Admin\PlaceholderController::class, 'lowStockReports'])->name('low-stock');
    Route::get('/stock-history', [\App\Http\Controllers\Admin\PlaceholderController::class, 'stockHistory'])->name('stock-history');
    Route::get('/audits', [\App\Http\Controllers\Admin\PlaceholderController::class, 'inventoryAudits'])->name('audits');
    Route::get('/transfers', [\App\Http\Controllers\Admin\PlaceholderController::class, 'stockTransfers'])->name('transfers');
    Route::post('/adjust', [\App\Http\Controllers\Admin\PlaceholderController::class, 'adjustStock'])->name('adjust');
});

// Sales - Additional Routes
Route::get('/orders/in-house', [OrderController::class, 'inHouse'])->name('orders.in-house');
Route::get('/orders/seller', [OrderController::class, 'seller'])->name('orders.seller');
Route::get('/orders/pickup-point', [OrderController::class, 'pickupPoint'])->name('orders.pickup-point');

// Delivery Management
Route::prefix('delivery')->name('delivery.')->group(function () {
    Route::get('/', [\App\Http\Controllers\Admin\PlaceholderController::class, 'deliveryManagement'])->name('index');
    Route::get('/partners', [\App\Http\Controllers\Admin\PlaceholderController::class, 'deliveryPartners'])->name('partners');
    Route::get('/carriers', [\App\Http\Controllers\Admin\PlaceholderController::class, 'deliveryCarriers'])->name('carriers');
    Route::get('/tracking', [\App\Http\Controllers\Admin\PlaceholderController::class, 'shipmentTracking'])->name('tracking');
    Route::get('/zones', [\App\Http\Controllers\Admin\PlaceholderController::class, 'deliveryZones'])->name('zones');
    Route::get('/courier-integration', [\App\Http\Controllers\Admin\PlaceholderController::class, 'courierIntegration'])->name('courier-integration');
    Route::get('/delivery-boys', [\App\Http\Controllers\Admin\PlaceholderController::class, 'deliveryBoys'])->name('delivery-boys');
});

// Quotations
Route::prefix('quotations')->name('quotations.')->group(function () {
    Route::get('/', [\App\Http\Controllers\Admin\PlaceholderController::class, 'quotations'])->name('index');
    Route::get('/create', [\App\Http\Controllers\Admin\PlaceholderController::class, 'createQuotation'])->name('create');
    Route::post('/', [\App\Http\Controllers\Admin\PlaceholderController::class, 'storeQuotation'])->name('store');
    Route::get('/{id}', [\App\Http\Controllers\Admin\PlaceholderController::class, 'showQuotation'])->name('show');
    Route::post('/{id}/convert-to-order', [\App\Http\Controllers\Admin\PlaceholderController::class, 'convertQuotationToOrder'])->name('convert-to-order');
});

// Subscriptions
Route::prefix('subscriptions')->name('subscriptions.')->group(function () {
    Route::get('/', [\App\Http\Controllers\Admin\PlaceholderController::class, 'subscriptions'])->name('index');
    Route::get('/create', [\App\Http\Controllers\Admin\PlaceholderController::class, 'createSubscription'])->name('create');
    Route::post('/', [\App\Http\Controllers\Admin\PlaceholderController::class, 'storeSubscription'])->name('store');
    Route::get('/{id}', [\App\Http\Controllers\Admin\PlaceholderController::class, 'showSubscription'])->name('show');
    Route::post('/{id}/cancel', [\App\Http\Controllers\Admin\PlaceholderController::class, 'cancelSubscription'])->name('cancel');
});

// Refund Management
Route::prefix('refunds')->name('refunds.')->group(function () {
    Route::get('/', [\App\Http\Controllers\Admin\RefundController::class, 'index'])->name('index');
    Route::get('/requests', [\App\Http\Controllers\Admin\RefundController::class, 'requests'])->name('requests');
    Route::get('/approved', [\App\Http\Controllers\Admin\RefundController::class, 'approved'])->name('approved');
    Route::get('/rejected', [\App\Http\Controllers\Admin\RefundController::class, 'rejected'])->name('rejected');
    Route::get('/configuration', [\App\Http\Controllers\Admin\RefundController::class, 'configuration'])->name('configuration');
    Route::post('/configuration', [\App\Http\Controllers\Admin\RefundController::class, 'updateConfiguration'])->name('configuration.update');
    Route::post('/{id}/approve', [\App\Http\Controllers\Admin\RefundController::class, 'approve'])->name('approve');
    Route::post('/{id}/reject', [\App\Http\Controllers\Admin\RefundController::class, 'reject'])->name('reject');
});

// Sellers (B2B) Management
Route::prefix('sellers')->name('sellers.')->group(function () {
    Route::get('/', [\App\Http\Controllers\Admin\SellerController::class, 'index'])->name('index');
    Route::get('/create', [\App\Http\Controllers\Admin\SellerController::class, 'create'])->name('create');
    Route::post('/', [\App\Http\Controllers\Admin\SellerController::class, 'store'])->name('store');
    Route::get('/{id}', [\App\Http\Controllers\Admin\SellerController::class, 'show'])->name('show');
    Route::get('/{id}/edit', [\App\Http\Controllers\Admin\SellerController::class, 'edit'])->name('edit');
    Route::put('/{id}', [\App\Http\Controllers\Admin\SellerController::class, 'update'])->name('update');
    Route::delete('/{id}', [\App\Http\Controllers\Admin\SellerController::class, 'destroy'])->name('destroy');
    Route::post('/{id}/approve', [\App\Http\Controllers\Admin\SellerController::class, 'approve'])->name('approve');
    Route::post('/{id}/suspend', [\App\Http\Controllers\Admin\SellerController::class, 'suspend'])->name('suspend');
    Route::get('/payouts', [\App\Http\Controllers\Admin\SellerController::class, 'payouts'])->name('payouts');
    Route::get('/payout-requests', [\App\Http\Controllers\Admin\SellerController::class, 'payoutRequests'])->name('payout-requests');
    Route::post('/payout-requests/{id}/approve', [\App\Http\Controllers\Admin\SellerController::class, 'approvePayout'])->name('payout-requests.approve');
    Route::get('/commission', [\App\Http\Controllers\Admin\SellerController::class, 'commission'])->name('commission');
    Route::post('/commission', [\App\Http\Controllers\Admin\SellerController::class, 'updateCommission'])->name('commission.update');
    Route::get('/verification', [\App\Http\Controllers\Admin\SellerController::class, 'verification'])->name('verification');
    Route::post('/verification/{id}', [\App\Http\Controllers\Admin\SellerController::class, 'processVerification'])->name('verification.process');
});

// Reports - Additional Routes
Route::prefix('reports')->name('reports.')->group(function () {
    Route::get('/seller-sales', [ReportController::class, 'sellerSales'])->name('seller-sales');
    Route::get('/wishlist', [ReportController::class, 'wishlist'])->name('wishlist');
    Route::get('/user-searches', [ReportController::class, 'userSearches'])->name('user-searches');
    Route::get('/commission-history', [ReportController::class, 'commissionHistory'])->name('commission-history');
    Route::get('/wallet-history', [ReportController::class, 'walletHistory'])->name('wallet-history');
});

// Marketing
Route::prefix('marketing')->name('marketing.')->group(function () {
    Route::get('/flash-deals', [\App\Http\Controllers\Admin\FlashDealController::class, 'index'])->name('flash-deals.index');
    Route::get('/flash-deals/create', [\App\Http\Controllers\Admin\FlashDealController::class, 'create'])->name('flash-deals.create');
    Route::post('/flash-deals', [\App\Http\Controllers\Admin\FlashDealController::class, 'store'])->name('flash-deals.store');
    Route::get('/flash-deals/{id}/edit', [\App\Http\Controllers\Admin\FlashDealController::class, 'edit'])->name('flash-deals.edit');
    Route::put('/flash-deals/{id}', [\App\Http\Controllers\Admin\FlashDealController::class, 'update'])->name('flash-deals.update');
    Route::delete('/flash-deals/{id}', [\App\Http\Controllers\Admin\FlashDealController::class, 'destroy'])->name('flash-deals.destroy');
    
    Route::get('/newsletters', [\App\Http\Controllers\Admin\NewsletterController::class, 'index'])->name('newsletters.index');
    Route::post('/newsletters/send', [\App\Http\Controllers\Admin\NewsletterController::class, 'send'])->name('newsletters.send');
    
    Route::get('/bulk-sms', [\App\Http\Controllers\Admin\SmsController::class, 'bulkSms'])->name('bulk-sms.index');
    Route::post('/bulk-sms/send', [\App\Http\Controllers\Admin\SmsController::class, 'sendBulkSms'])->name('bulk-sms.send');
    
    Route::get('/subscribers', [\App\Http\Controllers\Admin\SubscriberController::class, 'index'])->name('subscribers.index');
    Route::delete('/subscribers/{id}', [\App\Http\Controllers\Admin\SubscriberController::class, 'destroy'])->name('subscribers.destroy');
    Route::post('/subscribers/export', [\App\Http\Controllers\Admin\SubscriberController::class, 'export'])->name('subscribers.export');
    
    // Abandoned Cart Recovery
    Route::prefix('abandoned-cart')->name('abandoned-cart.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\PlaceholderController::class, 'abandonedCart'])->name('index');
        Route::get('/{id}', [\App\Http\Controllers\Admin\PlaceholderController::class, 'abandonedCartDetail'])->name('show');
        Route::post('/{id}/send-reminder', [\App\Http\Controllers\Admin\PlaceholderController::class, 'sendCartReminder'])->name('send-reminder');
        Route::get('/email-templates', [\App\Http\Controllers\Admin\PlaceholderController::class, 'recoveryEmailTemplates'])->name('email-templates');
        Route::get('/settings', [\App\Http\Controllers\Admin\PlaceholderController::class, 'recoverySettings'])->name('settings');
        Route::post('/settings', [\App\Http\Controllers\Admin\PlaceholderController::class, 'updateRecoverySettings'])->name('settings.update');
        Route::get('/conversion-tracking', [\App\Http\Controllers\Admin\PlaceholderController::class, 'conversionTracking'])->name('conversion-tracking');
    });
    
    // Gift Cards
    Route::prefix('gift-cards')->name('gift-cards.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\PlaceholderController::class, 'giftCards'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Admin\PlaceholderController::class, 'createGiftCard'])->name('create');
        Route::post('/', [\App\Http\Controllers\Admin\PlaceholderController::class, 'storeGiftCard'])->name('store');
        Route::get('/{id}/edit', [\App\Http\Controllers\Admin\PlaceholderController::class, 'editGiftCard'])->name('edit');
        Route::put('/{id}', [\App\Http\Controllers\Admin\PlaceholderController::class, 'updateGiftCard'])->name('update');
        Route::delete('/{id}', [\App\Http\Controllers\Admin\PlaceholderController::class, 'destroyGiftCard'])->name('destroy');
        Route::get('/templates', [\App\Http\Controllers\Admin\PlaceholderController::class, 'giftCardTemplates'])->name('templates');
        Route::get('/balance-tracking', [\App\Http\Controllers\Admin\PlaceholderController::class, 'giftCardBalanceTracking'])->name('balance-tracking');
        Route::get('/redemption-history', [\App\Http\Controllers\Admin\PlaceholderController::class, 'giftCardRedemptionHistory'])->name('redemption-history');
    });
    
    // Push Notifications
    Route::prefix('push-notifications')->name('push-notifications.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\PlaceholderController::class, 'pushNotifications'])->name('index');
        Route::post('/send', [\App\Http\Controllers\Admin\PlaceholderController::class, 'sendPushNotification'])->name('send');
        Route::get('/campaigns', [\App\Http\Controllers\Admin\PlaceholderController::class, 'pushCampaigns'])->name('campaigns');
        Route::get('/browser-settings', [\App\Http\Controllers\Admin\PlaceholderController::class, 'browserPushSettings'])->name('browser-settings');
        Route::get('/mobile-integration', [\App\Http\Controllers\Admin\PlaceholderController::class, 'mobilePushIntegration'])->name('mobile-integration');
    });
    
    // Price Rules
    Route::prefix('price-rules')->name('price-rules.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Admin\PlaceholderController::class, 'priceRules'])->name('index');
        Route::get('/create', [\App\Http\Controllers\Admin\PlaceholderController::class, 'createPriceRule'])->name('create');
        Route::post('/', [\App\Http\Controllers\Admin\PlaceholderController::class, 'storePriceRule'])->name('store');
        Route::get('/{id}/edit', [\App\Http\Controllers\Admin\PlaceholderController::class, 'editPriceRule'])->name('edit');
        Route::put('/{id}', [\App\Http\Controllers\Admin\PlaceholderController::class, 'updatePriceRule'])->name('update');
        Route::delete('/{id}', [\App\Http\Controllers\Admin\PlaceholderController::class, 'destroyPriceRule'])->name('destroy');
    });
});

// Support - Additional Routes
Route::prefix('support')->name('support.')->group(function () {
    Route::get('/tickets', [\App\Http\Controllers\Admin\SupportController::class, 'tickets'])->name('tickets.index');
    Route::get('/tickets/{id}', [\App\Http\Controllers\Admin\SupportController::class, 'showTicket'])->name('tickets.show');
    Route::post('/tickets/{id}/reply', [\App\Http\Controllers\Admin\SupportController::class, 'replyTicket'])->name('tickets.reply');
    Route::post('/tickets/{id}/close', [\App\Http\Controllers\Admin\SupportController::class, 'closeTicket'])->name('tickets.close');
    Route::get('/product-queries', [\App\Http\Controllers\Admin\SupportController::class, 'productQueries'])->name('product-queries.index');
    Route::post('/product-queries/{id}/reply', [\App\Http\Controllers\Admin\SupportController::class, 'replyQuery'])->name('product-queries.reply');
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

// Delivery - Additional Routes for robust delivery management
Route::prefix('delivery')->name('delivery.')->group(function () {
    Route::get('/pickup-points', [\App\Http\Controllers\Admin\PlaceholderController::class, 'pickupPoints'])->name('pickup-points');
    Route::post('/pickup-points', [\App\Http\Controllers\Admin\PlaceholderController::class, 'storePickupPoint'])->name('pickup-points.store');
    Route::put('/pickup-points/{id}', [\App\Http\Controllers\Admin\PlaceholderController::class, 'updatePickupPoint'])->name('pickup-points.update');
    Route::delete('/pickup-points/{id}', [\App\Http\Controllers\Admin\PlaceholderController::class, 'destroyPickupPoint'])->name('pickup-points.destroy');
    Route::get('/schedules', [\App\Http\Controllers\Admin\PlaceholderController::class, 'deliverySchedules'])->name('schedules');
    Route::post('/schedules', [\App\Http\Controllers\Admin\PlaceholderController::class, 'storeDeliverySchedule'])->name('schedules.store');
    Route::put('/schedules/{id}', [\App\Http\Controllers\Admin\PlaceholderController::class, 'updateDeliverySchedule'])->name('schedules.update');
    Route::delete('/schedules/{id}', [\App\Http\Controllers\Admin\PlaceholderController::class, 'destroyDeliverySchedule'])->name('schedules.destroy');
    Route::get('/reports', [\App\Http\Controllers\Admin\PlaceholderController::class, 'deliveryReports'])->name('reports');
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
