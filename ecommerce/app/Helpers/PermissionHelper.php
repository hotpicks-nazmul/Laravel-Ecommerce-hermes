<?php

namespace App\Helpers;

use Illuminate\Support\Collection;
use Spatie\Permission\Models\Permission;
use App\Models\Setting;

class PermissionHelper
{
    private static ?Collection $cachedPermissions = null;
    private static ?array $cachedModules = null;
    private static ?array $hiddenSubmenus = null;
    private static ?array $hiddenModules = null;

    /**
     * Get list of submenu keys that are hidden from sidebar.
     */
    public static function hiddenSubmenus(): array
    {
        if (self::$hiddenSubmenus !== null) {
            return self::$hiddenSubmenus;
        }

        $raw = Setting::get('sidebar_hidden_submenus', '[]');
        $decoded = json_decode($raw, true);
        return self::$hiddenSubmenus = is_array($decoded) ? $decoded : [];
    }

    /**
     * Toggle a submenu's sidebar visibility.
     * Returns the new state (true = visible, false = hidden).
     */
    public static function toggleSubmenuVisibility(string $submenuKey): bool
    {
        $hidden = self::hiddenSubmenus();
        if (in_array($submenuKey, $hidden)) {
            $hidden = array_values(array_filter($hidden, fn($s) => $s !== $submenuKey));
            Setting::set('sidebar_hidden_submenus', json_encode($hidden));
            self::$hiddenSubmenus = $hidden;
            return true;
        } else {
            $hidden[] = $submenuKey;
            Setting::set('sidebar_hidden_submenus', json_encode($hidden));
            self::$hiddenSubmenus = $hidden;
            return false;
        }
    }

    /**
     * Check if a submenu is visible in the sidebar.
     */
    public static function isSubmenuVisible(string $submenuKey): bool
    {
        return !in_array($submenuKey, self::hiddenSubmenus());
    }

    /**
     * Check if user can see a submenu (global override + per-user permission).
     * Super admins and admins bypass per-user check.
     */
    public static function canUserSeeSubmenu(string $routeName): bool
    {
        // Global master override (admin hides from everyone)
        if (!self::isSubmenuVisible($routeName)) {
            return false;
        }

        $user = auth()->user();
        if (!$user) return false;
        
        // Super admin and admin bypass per-user check
        if ($user->role === 'super_admin' || $user->role === 'admin') {
            return true;
        }

        // Staff - check per-user submenu permission
        return $user->hasPermission('submenu:' . $routeName);
    }

    /**
     * Get all submenus mapping (submenu_key => route_name => label).
     */
    public static function submenus(): array
    {
        return [
            'products' => [
                'admin.products.create' => 'Add New Product',
                'admin.products.index' => 'All Products',
                'admin.categories.index' => 'Category',
                'admin.products.in-house' => 'In-House Products',
                'admin.products.digital.index' => 'Digital Products',
                'admin.digital-categories.index' => 'Digital Categories',
                'admin.products.bulk-import' => 'Bulk Import',
                'admin.products.bulk-export' => 'Bulk Export',
                'admin.products.bulk-discount' => 'Bulk Discount',
                'admin.brands.index' => 'Brand',
                'admin.attributes.index' => 'Attribute',
                'admin.colors.index' => 'Colors',
                'admin.reviews.index' => 'Product Reviews',
                'admin.product-qa.index' => 'Product Q&A',
                'admin.wishlists.index' => 'Wishlist Management',
            ],
            'orders' => [
                'admin.orders.in-house' => 'In-House Orders',
                'admin.orders.seller' => 'Seller Orders',
                'admin.orders.pickup-point' => 'Pickup Point Orders',
                'admin.orders.refund-requests' => 'Refund Requests',
            ],
            'customers' => [
                'admin.customers.index' => 'Customer List',
                'admin.customers.wallets' => 'Customer Wallets',
                'admin.customers.loyalty-points' => 'Loyalty Points',
            ],
            'sellers' => [
                'admin.sellers.index' => 'Seller List',
                'admin.seller-verify-requests.index' => 'Verify Requests',
                'admin.seller-warehouse.index' => 'Seller Products',
            ],
            'marketing' => [
                'admin.marketing.flash-deals.index' => 'Flash Deals',
                'admin.marketing.coupons.index' => 'Coupons',
                'admin.marketing.newsletters.index' => 'Newsletters',
                'admin.marketing.sms.index' => 'SMS',
            ],
            'content' => [
                'admin.blogs.index' => 'Blog',
                'admin.pages.index' => 'Pages',
            ],
            'appearance' => [
                'admin.appearance.index' => 'Theme',
                'admin.menus.index' => 'Menu',
                'admin.widgets.index' => 'Widgets',
                'admin.media.index' => 'Media',
            ],
            'settings' => [
                'admin.settings.index' => 'General',
                'admin.settings.payment' => 'Payment',
                'admin.settings.shipping' => 'Shipping',
                'admin.settings.otp' => 'OTP',
                'admin.settings.email' => 'Email',
            ],
            'support' => [
                'admin.support.tickets.index' => 'Tickets',
                'admin.support.faqs.index' => 'FAQs',
            ],
            'reports' => [
                'admin.reports.index' => 'Reports',
                'admin.reviews.index' => 'Reviews',
            ],
            'inventory' => [
                'admin.inventory.index' => 'Stock',
                'admin.inventory.add' => 'Add Stock',
            ],
            'delivery' => [
                'admin.delivery.index' => 'Delivery Dashboard',
                'admin.delivery.partners.index' => 'Delivery Partners',
                'admin.delivery.carriers.index' => 'Carriers',
                'admin.delivery.tracking' => 'Shipment Tracking',
                'admin.delivery.zones.index' => 'Delivery Zones',
                'admin.delivery.courier-integration' => 'Courier Integration',
                'admin.delivery.delivery-boys.index' => 'Delivery Boys',
                'admin.pickup-points.index' => 'Pick-up Points',
                'admin.delivery.schedules.index' => 'Delivery Schedules',
                'admin.delivery.reports' => 'Delivery Reports',
            ],
            'refund' => [
                'admin.refunds.index' => 'All Refunds',
                'admin.refunds.requests' => 'Refund Requests',
                'admin.refunds.approved' => 'Approved Refunds',
                'admin.refunds.rejected' => 'Rejected Refunds',
                'admin.refunds.configuration' => 'Refund Configuration',
            ],
            'affiliate' => [
                'admin.affiliate.index' => 'Affiliate',
                'admin.affiliate.config' => 'Configuration',
            ],
            'media' => [
                'admin.media.index' => 'Media',
            ],
            'warehouse' => [
                'admin.warehouses.index' => 'Warehouse',
            ],
            'staffs' => [
                'admin.staffs.index' => 'Staff',
                'admin.staffs.roles' => 'Roles',
                'admin.staffs.permissions' => 'Permissions',
                'admin.permissions.index' => 'Permission Keys',
            ],
            'system' => [
                'admin.system.update' => 'Updates',
                'admin.system.logs' => 'Logs',
            ],
            'pos' => [
                'admin.pos.index' => 'POS',
            ],
            'locations' => [
                'admin.locations.countries.index' => 'Countries',
                'admin.locations.states.index' => 'States',
                'admin.locations.cities.index' => 'Cities',
                'admin.locations.areas.index' => 'Areas',
            ],
            'otp' => [
                'admin.otp.configuration' => 'OTP Config',
            ],
            'addon' => [
                'admin.addons.index' => 'Addons',
            ],
            'multistore' => [
                'admin.multi-store.index' => 'Multi-Store',
            ],
            'analytics' => [
                'admin.analytics.index' => 'Analytics',
            ],
            'dashboard' => [
                'admin.dashboard' => 'Dashboard',
            ],
        ];
    }

    /**
     * Map submenu pages to their section-level permission actions.
     * Key = route name, value = permission actions that apply to this page.
     */
    public static function pageSectionPermissions(): array
    {
        return [
            // Order pages - customer info and pricing
            'admin.orders.in-house' => ['view-customer', 'view-pricing'],
            'admin.orders.seller' => ['view-customer', 'view-pricing'],
            'admin.orders.pickup-point' => ['view-customer', 'view-pricing'],
            'admin.orders.index' => ['view-customer', 'view-pricing'],

            // Product - cost price
            'admin.products.edit' => ['view-cost'],
            'admin.products.create' => ['view-cost'],

            // Customer - financial data
            'admin.customers.index' => ['view-financial'],
            'admin.customers.show' => ['view-financial'],

            // Refund - customer info
            'admin.refunds.show' => ['view-customer'],
            'admin.refunds.index' => ['view-customer'],
            'admin.refunds.requests' => ['view-customer'],
            'admin.refunds.approved' => ['view-customer'],
            'admin.refunds.rejected' => ['view-customer'],
        ];
    }

    /**
     * Get list of module keys that are hidden from sidebar.
     */
    public static function hiddenModules(): array
    {
        if (self::$hiddenModules !== null) {
            return self::$hiddenModules;
        }

        $raw = Setting::get('sidebar_hidden_modules', '[]');
        $decoded = json_decode($raw, true);
        return self::$hiddenModules = is_array($decoded) ? $decoded : [];
    }

    /**
     * Toggle a module's sidebar visibility.
     * Returns the new state (true = visible, false = hidden).
     */
    public static function toggleModuleVisibility(string $moduleKey): bool
    {
        $hidden = self::hiddenModules();
        if (in_array($moduleKey, $hidden)) {
            $hidden = array_values(array_filter($hidden, fn($m) => $m !== $moduleKey));
            Setting::set('sidebar_hidden_modules', json_encode($hidden));
            self::$hiddenModules = $hidden;
            return true;
        } else {
            $hidden[] = $moduleKey;
            Setting::set('sidebar_hidden_modules', json_encode($hidden));
            self::$hiddenModules = $hidden;
            return false;
        }
    }

    /**
     * Check if a module is visible in the sidebar.
     */
    public static function isModuleVisible(string $module): bool
    {
        return !in_array($module, self::hiddenModules());
    }

    /**
     * Get all permissions grouped by module prefix, with computed metadata.
     * Returns: ['products' => ['key' => 'products', 'label' => 'Products', 'actions' => ['view','create',...]]]
     */
    public static function modules(): array
    {
        if (self::$cachedModules !== null) {
            return self::$cachedModules;
        }

        $permissions = self::allPermissions();
        $modules = [];

        foreach ($permissions->groupBy('module') as $moduleKey => $perms) {
            $actions = $perms->pluck('action')->unique()->sort()->values()->toArray();

            $modules[$moduleKey] = [
                'key'     => $moduleKey,
                'label'   => self::humanizeModule($moduleKey),
                'icon'    => self::iconForModule($moduleKey),
                'actions' => $actions,
            ];
        }

        // Sort modules alphabetically
        ksort($modules);

        // Ensure common modules are at the top
        $priorityOrder = ['dashboard', 'analytics', 'products', 'inventory', 'orders', 'delivery',
            'refund', 'customers', 'sellers', 'affiliate', 'media', 'reports', 'marketing',
            'support', 'otp', 'content', 'appearance', 'settings', 'locations', 'warehouse',
            'staffs', 'system', 'pos', 'addon', 'multistore'];

        $sorted = [];
        foreach ($priorityOrder as $key) {
            if (isset($modules[$key])) {
                $sorted[$key] = $modules[$key];
                unset($modules[$key]);
            }
        }
        // Append remaining modules alphabetically
        foreach ($modules as $key => $mod) {
            $sorted[$key] = $mod;
        }

        return self::$cachedModules = $sorted;
    }

    /**
     * Get all legacy module-level keys (just the prefix names).
     */
    public static function legacyKeys(): array
    {
        return array_keys(static::modules());
    }

    /**
     * Get all granular permission names from the database.
     */
    public static function allGranular(): Collection
    {
        return self::allPermissions()->pluck('name');
    }

    /**
     * Get all permissions as a collection with module/action parsed.
     */
    private static function allPermissions(): Collection
    {
        if (self::$cachedPermissions !== null) {
            return self::$cachedPermissions;
        }

        return self::$cachedPermissions = Permission::where('guard_name', 'web')
            ->orderBy('name')
            ->get()
            ->map(function ($perm) {
                $parts = explode('.', $perm->name);
                $perm->module = $parts[0] ?? $perm->name;
                $perm->action = $parts[1] ?? 'view';
                return $perm;
            });
    }

    /**
     * Map a permission key to a default redirect route.
     */
    public static function permissionToRoute(string $permission): ?string
    {
        $modules = self::modules();
        if (!isset($modules[$permission])) {
            return null;
        }

        // Route naming convention: admin.{module}.index
        // Fall back to known mappings for special cases
        $specialRoutes = [
            'dashboard'  => 'admin.dashboard',
            'products'   => 'admin.products.in-house',
            'marketing'  => 'admin.marketing.flash-deals.index',
            'content'    => 'admin.blogs.index',
            'appearance' => 'admin.appearance.index',
            'system'     => 'admin.system.update',
            'otp'        => 'admin.otp.configuration',
            'locations'  => 'admin.locations.cities.index',
            'warehouse'  => 'admin.warehouses.index',
            'staffs'     => 'admin.staffs.index',
            'multistore' => 'admin.multi-store.index',
            'addon'      => 'admin.addons.index',
            'media'      => 'admin.media.index',
        ];

        if (isset($specialRoutes[$permission])) {
            return $specialRoutes[$permission];
        }

        $routeName = 'admin.' . $permission . '.index';
        // We can't validate routes at runtime easily, just return the convention
        return $routeName;
    }

    /**
     * Check if a permission key is a module-level key (no dot) vs granular (has dot).
     */
    public static function isModuleKey(string $permission): bool
    {
        return !str_contains($permission, '.');
    }

    /**
     * Get the module prefix from a permission name.
     */
    public static function moduleFromPermission(string $permission): string
    {
        return explode('.', $permission)[0];
    }

    /**
     * Convert a module key to a human-readable label.
     */
    private static function humanizeModule(string $key): string
    {
        $labels = [
            'multistore' => 'Multi-Store',
            'otp'        => 'OTP Management',
            'pos'        => 'POS Management',
            'addon'      => 'Addon Manager',
            'staffs'     => 'Staff Management',
            'orders'     => 'Sales Management',
        ];

        if (isset($labels[$key])) {
            return $labels[$key];
        }

        return ucwords(str_replace('-', ' ', $key)) . ' Management';
    }

    /**
     * Get a Bootstrap icon class for a module.
     */
    private static function iconForModule(string $key): string
    {
        $icons = [
            'dashboard'  => 'bi-speedometer2',
            'analytics'  => 'bi-graph-up-arrow',
            'products'   => 'bi-box',
            'inventory'  => 'bi-boxes',
            'orders'     => 'bi-cart-check',
            'sales'      => 'bi-cart-check',
            'delivery'   => 'bi-truck',
            'refund'     => 'bi-arrow-return-left',
            'customers'  => 'bi-people',
            'sellers'    => 'bi-shop-window',
            'affiliate'  => 'bi-link-45deg',
            'media'      => 'bi-images',
            'reports'    => 'bi-graph-up',
            'marketing'  => 'bi-megaphone',
            'support'    => 'bi-headset',
            'otp'        => 'bi-shield-lock',
            'content'    => 'bi-file-earmark-text',
            'appearance' => 'bi-palette2',
            'settings'   => 'bi-gear-fill',
            'locations'  => 'bi-geo-alt',
            'warehouse'  => 'bi-building',
            'staffs'     => 'bi-person-badge',
            'system'     => 'bi-cpu',
            'pos'        => 'bi-calculator',
            'multistore' => 'bi-shop',
            'addon'      => 'bi-puzzle',
        ];

        return $icons[$key] ?? 'bi-gear';
    }

    /**
     * Flush cached permissions (call after creating/deleting permissions).
     */
    public static function flushCache(): void
    {
        self::$cachedPermissions = null;
        self::$cachedModules = null;
        self::$hiddenSubmenus = null;
        self::$hiddenModules = null;
    }

    /**
     * Get submenu key from route name (e.g., 'admin.products.index' => 'admin.products.index').
     */
    public static function submenuKeyFromRoute(string $routeName): ?string
    {
        foreach (self::submenus() as $module => $items) {
            if (isset($items[$routeName])) {
                return $routeName;
            }
        }
        return null;
    }
}
