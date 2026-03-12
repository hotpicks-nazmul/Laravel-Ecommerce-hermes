<?php

use App\Models\Store;

/**
 * Get the current store from session.
 * If no store is selected, returns the default store.
 */
function getCurrentStore()
{
    $storeId = session('current_store_id');
    
    if ($storeId) {
        $store = Store::active()->find($storeId);
        if ($store) {
            return $store;
        }
    }
    
    // Return default store if no store selected or store not found
    return Store::getDefault();
}

/**
 * Get the current store ID from session.
 */
function getCurrentStoreId()
{
    $store = getCurrentStore();
    return $store ? $store->id : null;
}

/**
 * Check if multi-store feature is active.
 * Returns true if there are multiple stores.
 */
function isMultiStoreEnabled()
{
    return Store::count() > 1;
}

/**
 * Get all active stores.
 */
function getActiveStores()
{
    return Store::getActiveStores();
}
