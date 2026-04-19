---
name: route-ordering-404-fix
description: Fix 404 errors by defining specific routes before resource routes to prevent wildcard patterns from matching custom paths as IDs.
---

# 404 Errors Due to Route Ordering

**Problem:** When creating custom routes for specific order types (like `/admin/orders/in-house`), you may encounter 404 errors if routes are defined in the wrong order.

**Root Cause:** The resource route (which includes a wildcard pattern `/{order}`) is defined before the specific route. Laravel matches routes in the order they are defined, so the wildcard route matches `/in-house` as an order ID instead of recognizing it as a separate route.

**Incorrect Route Order:**

// ❌ INCORRECT - Resource route first
Route::resource('orders', OrderController::class)->only(['index', 'show', 'update']);

// This route will never be matched
Route::get('/orders/in-house', [OrderController::class, 'inHouse'])->name('orders.in-house');

**Correct Route Order:**

// ✅ CORRECT - Specific routes first
Route::get('/orders/in-house', [OrderController::class, 'inHouse'])->name('orders.in-house');
Route::get('/orders/in-house/create', [OrderController::class, 'create'])->name('orders.in-house.create');
Route::post('/orders/in-house', [OrderController::class, 'store'])->name('orders.in-house.store');
Route::get('/orders/in-house/{order}', [OrderController::class, 'inHouseShow'])->name('orders.in-house.show');

// Resource route comes after specific routes
Route::resource('orders', OrderController::class)->only(['index', 'show', 'update']);

**Best Practice:**
1. Define all custom routes with specific patterns before any resource or wildcard routes
2. Always place resource routes at the end of the route group
3. Remember that Laravel matches routes in the order they are defined
4. Test routes after making changes to route definitions to ensure they are working as expected and not causing 404 errors.