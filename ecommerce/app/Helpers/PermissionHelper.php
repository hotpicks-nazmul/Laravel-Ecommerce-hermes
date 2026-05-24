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
     * Get comprehensive module-to-action mapping.
     * Defines which CRUD and special actions each module supports.
     */
    public static function moduleActions(): array
    {
        return [
            'dashboard'  => ['view'],
            'analytics'  => ['view', 'export'],
            'products'   => ['view', 'create', 'edit', 'delete', 'export', 'import', 'view-cost', 'view-cost-price', 'view-wholesale-price', 'view-profit'],
            'inventory'  => ['view', 'create', 'edit', 'delete', 'export', 'import'],
            'orders'     => ['view', 'create', 'edit', 'delete', 'export', 'view-customer', 'view-pricing', 'view-pricing-detail', 'view-discount'],
            'delivery'   => ['view', 'create', 'edit', 'delete', 'export'],
            'refund'     => ['view', 'manage', 'view-customer'],
            'customers'  => ['view', 'create', 'edit', 'delete', 'export', 'import', 'view-financial', 'view-contact', 'view-address', 'view-orders', 'view-payments', 'view-activity', 'view-notes'],
            'sellers'    => ['view', 'create', 'edit', 'delete', 'export', 'view-financial'],
            'affiliate'  => ['view', 'create', 'edit', 'delete', 'view-financial'],
            'media'      => ['view', 'upload', 'delete'],
            'reports'    => ['view', 'export'],
            'marketing'  => ['view', 'create', 'edit', 'delete'],
            'support'    => ['view', 'create', 'edit', 'delete'],
            'otp'        => ['view', 'configure', 'credentials', 'templates'],
            'content'    => ['view', 'create', 'edit', 'delete'],
            'appearance' => ['view', 'create', 'edit', 'delete'],
            'settings'   => ['view', 'edit'],
            'locations'  => ['view', 'create', 'edit', 'delete'],
            'warehouse'  => ['view', 'create', 'edit', 'delete'],
            'staffs'     => ['view', 'create', 'edit', 'delete'],
            'system'     => ['view', 'update', 'logs'],
            'pos'        => ['view', 'create', 'edit', 'delete'],
            'addon'      => ['view', 'install', 'uninstall'],
            'multistore' => ['view', 'create', 'edit', 'delete'],
        ];
    }

    /**
     * Get list of all section-level permission actions (used to filter them out of module actions).
     */
    public static function sectionActions(): array
    {
        return [
            'view-customer', 'view-pricing', 'view-cost', 'view-financial', 'view-revenue', 'view-sales',
            // Customer micro permissions
            'view-contact', 'view-address', 'view-orders', 'view-payments', 'view-activity', 'view-notes',
            // Price micro permissions
            'view-pricing-detail', 'view-discount', 'view-cost-price', 'view-wholesale-price', 'view-profit',
        ];
    }

    /**
     * Get all submenus mapping (submenu_key => route_name => label).
     * Complete coverage of ALL sidebar navigation items.
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
            'inventory' => [
                'admin.inventory.index' => 'Inventory Overview',
                'admin.inventory.stock-alerts' => 'Stock Alerts',
                'admin.inventory.stock-history' => 'Stock History',
            ],
            'orders' => [
                'admin.orders.in-house' => 'In-House Orders',
                'admin.orders.seller' => 'Seller Orders',
                'admin.orders.pickup-point' => 'Pickup Point Orders',
                'admin.quotations.index' => 'Quotations',
                'admin.subscriptions.index' => 'Subscriptions',
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
            'customers' => [
                'admin.customers.index' => 'All Customers',
                'admin.customers.groups.index' => 'Customer Groups',
                'admin.customers.segmentation.index' => 'Customer Segmentation',
                'admin.customers.loyalty.index' => 'Loyalty Points',
                'admin.customers.membership.index' => 'Membership Plans',
                'admin.customers.wallet.index' => 'Customer Wallet',
            ],
            'sellers' => [
                'admin.sellers.index' => 'All Sellers',
                'admin.sellers.payouts' => 'Payouts',
                'admin.sellers.payout-requests' => 'Payout Requests',
                'admin.sellers.commission' => 'Seller Commission',
                'admin.sellers.verification' => 'Seller Verification',
            ],
            'affiliate' => [
                'admin.affiliate.users.index' => 'Affiliate Users',
                'admin.affiliate.configuration' => 'Affiliate Configuration',
                'admin.affiliate.payouts' => 'Affiliate Payouts',
                'admin.affiliate.requests' => 'Affiliate Requests',
                'admin.affiliate.categories.index' => 'Affiliate Categories',
                'admin.affiliate.products.index' => 'Affiliate Products',
                'admin.affiliate.links.index' => 'Affiliate Links',
                'admin.affiliate.banners.index' => 'Affiliate Banners',
                'admin.affiliate.reports' => 'Affiliate Reports',
            ],
            'media' => [
                'admin.media.index' => 'Media',
            ],
            'reports' => [
                'admin.reports.in-house-product-sale' => 'In-House Product Sale',
                'admin.reports.seller-sales' => 'Seller Products Sale',
                'admin.reports.inventory' => 'Products Stock',
                'admin.reports.wishlist' => 'Products Wishlist',
                'admin.reports.user-searches' => 'User Searches',
                'admin.reports.commission-history' => 'Commission History',
                'admin.reports.wallet-history' => 'Wallet Recharge History',
                'admin.jakat.index' => 'Jakat Calculator',
            ],
            'marketing' => [
                'admin.marketing.flash-deals.index' => 'Flash Deals',
                'admin.marketing.newsletters.index' => 'Newsletters',
                'admin.marketing.bulk-sms.index' => 'Bulk SMS',
                'admin.marketing.subscribers.index' => 'Subscribers',
                'admin.coupons.index' => 'Coupon',
                'admin.marketing.abandoned-cart.index' => 'Abandoned Cart Recovery',
                'admin.marketing.gift-cards.index' => 'Gift Cards',
                'admin.marketing.push-notifications.index' => 'Push Notifications',
                'admin.marketing.price-rules.index' => 'Price Rules',
            ],
            'support' => [
                'admin.support.tickets.index' => 'Ticket',
                'admin.chat.index' => 'Live Chat',
                'admin.chat.ai-settings.index' => 'AI Chatbot Settings',
                'admin.chat.widget-settings.index' => 'Chat Widget Settings',
                'admin.chat.predefined.index' => 'Quick Replies',
                'admin.settings.whatsapp' => 'WhatsApp Chat',
                'admin.support.product-queries.index' => 'Product Queries',
            ],
            'otp' => [
                'admin.otp.configuration' => 'OTP Configurations',
                'admin.otp.sms-templates' => 'SMS Templates',
                'admin.otp.credentials' => 'Set OTP Credentials',
            ],
            'content' => [
                'admin.pages.index' => 'Pages',
                'admin.blogs.index' => 'Blog Posts',
                'admin.blog-categories.index' => 'Blog Categories',
                'admin.blog-tags.index' => 'Blog Tags',
                'admin.form-builder.index' => 'Form Builder',
                'admin.faqs.index' => 'FAQs',
                'admin.content.widgets.index' => 'Widget Manager',
            ],
            'appearance' => [
                'admin.themes.index' => 'Themes',
                'admin.menus.index' => 'Menu Builder',
                'admin.sliders.index' => 'Sliders',
                'admin.banners.index' => 'Banners',
                'admin.hero.index' => 'Hero Section',
                'admin.homepage.index' => 'Home Page Settings',
            ],
            'settings' => [
                'admin.settings.general' => 'General Settings',
                'admin.settings.languages' => 'Languages',
                'admin.settings.currency' => 'Currency',
                'admin.settings.vat-tax' => 'VAT & Tax',
                'admin.settings.email' => 'SMTP Settings',
                'admin.payment-gateways.index' => 'Payment Methods',
                'admin.settings.order-configuration' => 'Order Configuration',
                'admin.settings.file-system' => 'File System & Cache',
                'admin.settings.social-login' => 'Social Media Logins',
                'admin.settings.shipping' => 'Shipping',
                'admin.seo.index' => 'SEO Settings',
                'admin.settings.footer' => 'Footer Settings',
                'admin.settings.email-templates.index' => 'Email Templates',
                'admin.settings.notification-settings' => 'Notification Settings',
                'admin.api-keys.index' => 'API Keys & Integrations',
                'admin.backup.index' => 'Backup & Restore',
            ],
            'locations' => [
                'admin.locations.countries.index' => 'Countries',
                'admin.locations.states.index' => 'States',
                'admin.locations.cities.index' => 'Cities',
                'admin.locations.areas.index' => 'Areas',
            ],
            'warehouse' => [
                'admin.warehouses.index' => 'All Warehouses',
                'admin.warehouses.create' => 'Add Warehouse',
            ],
            'staffs' => [
                'admin.staffs.index' => 'All Staffs',
                'admin.staffs.warehouse' => 'Warehouse Staffs',
                'admin.permissions.index' => 'Permission Keys',
            ],
            'system' => [
                'admin.system.update' => 'Update',
                'admin.system.server-status' => 'Server Status',
                'admin.system.logs' => 'Activity Logs',
                'admin.system.data-export' => 'Data Export/Import',
            ],
            'pos' => [
                'admin.pos.index' => 'POS Terminal',
                'admin.pos.cash-register' => 'Cash Register',
                'admin.pos.reports' => 'POS Reports',
            ],
            'addon' => [
                'admin.addons.index' => 'Addon Manager',
            ],
            'multistore' => [
                'admin.multi-store.index' => 'All Stores',
                'admin.multi-store.create' => 'Add Store',
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
            // ===== Orders =====
            'admin.orders.in-house'          => [],
            'admin.orders.seller'            => [],
            'admin.orders.pickup-point'      => ['view-customer', 'view-pricing', 'view-pricing-detail', 'view-discount'],
            'admin.orders.index'             => ['view-customer', 'view-pricing', 'view-pricing-detail', 'view-discount'],
            'admin.orders.show'              => ['view-customer', 'view-pricing', 'view-pricing-detail', 'view-discount'],

            // ===== Products =====
            'admin.products.edit'            => ['view-cost', 'view-cost-price', 'view-wholesale-price', 'view-profit'],
            'admin.products.create'          => ['view-cost', 'view-cost-price', 'view-wholesale-price', 'view-profit'],
            'admin.products.show'            => ['view-cost', 'view-cost-price', 'view-wholesale-price', 'view-profit'],
            'admin.products.index'           => ['view-cost', 'view-cost-price', 'view-wholesale-price', 'view-profit'],

            // ===== Customers =====
            'admin.customers.index'          => ['view-financial', 'view-contact', 'view-address', 'view-orders', 'view-payments', 'view-activity', 'view-notes'],
            'admin.customers.show'           => ['view-financial', 'view-contact', 'view-address', 'view-orders', 'view-payments', 'view-activity', 'view-notes'],
            'admin.customers.edit'           => ['view-financial', 'view-contact', 'view-address'],

            // ===== Sellers =====
            'admin.sellers.index'            => ['view-financial'],
            'admin.sellers.show'             => ['view-financial', 'view-revenue'],
            'admin.sellers.edit'             => ['view-financial'],
            'admin.sellers.payouts'          => ['view-financial'],
            'admin.sellers.payout-requests'  => ['view-financial'],
            'admin.sellers.commission'       => ['view-financial'],

            // ===== Refunds =====
            'admin.refunds.show'            => ['view-customer'],
            'admin.refunds.index'           => ['view-customer'],
            'admin.refunds.requests'        => ['view-customer'],
            'admin.refunds.approved'        => ['view-customer'],
            'admin.refunds.rejected'        => ['view-customer'],

            // ===== Affiliate =====
            'admin.affiliate.users.index'   => ['view-financial'],
            'admin.affiliate.payouts'       => ['view-financial'],
            'admin.affiliate.reports'       => ['view-financial'],

            // ===== Inventory =====
            'admin.inventory.index'         => ['view-cost'],
            'admin.inventory.show'          => ['view-cost'],

            // ===== Reports =====
            'admin.reports.in-house-product-sale' => ['view-revenue', 'view-sales'],
            'admin.reports.seller-sales'          => ['view-revenue', 'view-sales'],
            'admin.reports.commission-history'    => ['view-financial'],
            'admin.reports.wallet-history'        => ['view-financial'],

            // ===== Analytics =====
            'admin.analytics.index'         => ['view-revenue', 'view-sales'],

            // ===== Delivery =====
            'admin.delivery.reports'        => ['view-revenue'],
            'admin.delivery.index'          => ['view-revenue'],

            // ===== Dashboard =====
            'admin.dashboard'              => ['view-sales', 'view-revenue'],

            // ===== Customers Wallet/Loyalty =====
            'admin.customers.wallet.index'   => ['view-financial'],
            'admin.customers.loyalty.index'  => ['view-financial'],
        ];
    }

    /**
     * Page-level action permissions for every page (list AND detail).
     * Key = route name, value = flat list of permission names for that page.
     */
    public static function pageActions(): array
    {
        $flat = [];
        foreach (self::pageComponents() as $route => $page) {
            if (isset($page['items'])) {
                $flat[$route] = array_merge($flat[$route] ?? [], array_values($page['items']));
            }
            if (isset($page['groups'])) {
                foreach ($page['groups'] as $group) {
                    if (isset($group['items'])) {
                        $flat[$route] = array_merge($flat[$route] ?? [], array_values($group['items']));
                    }
                    if (isset($group['children'])) {
                        foreach ($group['children'] as $child) {
                            $childRoute = $child['route'] ?? '';
                            if ($childRoute && isset(self::pageComponents()[$childRoute]['items'])) {
                                $flat[$childRoute] = array_merge(
                                    $flat[$childRoute] ?? [],
                                    array_values(self::pageComponents()[$childRoute]['items'])
                                );
                            }
                        }
                    }
                }
            }
        }
        return $flat;
    }

    /**
     * Page tree: route → { items, groups }
     *
     * 'items' => ['Label' => 'permission.name']   — direct toggle pills
     * 'groups' => [
     *     'Group Label' => [
     *         'items'    => ['Label' => 'permission.name'],
     *         'children' => ['Item Label' => ['route' => 'child.route', 'label' => 'Child Label']],
     *     ],
     * ]
     */
    public static function pageComponents(): array
    {
        return [
            'admin.orders.in-house' => [
                'items' => [
                    'Create Order'  => 'orders.inhouse-create',
                    'Export'        => 'orders.inhouse-export',
                    'Summary Cards' => 'orders.inhouse-summary-cards',
                ],
                'groups' => [
                    'Table Columns' => [
                        'items' => [
                            'Customer'       => 'orders.view-customer',
                            'Pricing'        => 'orders.view-pricing',
                            'Pricing Detail' => 'orders.view-pricing-detail',
                            'Discount'       => 'orders.view-discount',
                        ],
                    ],
                    'Table Actions' => [
                        'items' => [
                            'View Details'   => 'orders.inhouse-view-details',
                            'Edit'           => 'orders.inhouse-edit',
                            'Delete'         => 'orders.inhouse-delete',
                        ],
                        'children' => [
                            'View Details' => [
                                'route' => 'admin.orders.show',
                                'label' => 'Order Detail',
                            ],
                        ],
                    ],
                ],
            ],
            'admin.orders.seller' => [
                'items' => [
                    'Export' => 'orders.seller-export',
                ],
                'groups' => [
                    'Table Actions' => [
                        'items' => [
                            'View Details'   => 'orders.seller-view-details',
                            'Edit'           => 'orders.seller-edit',
                            'Delete'         => 'orders.seller-delete',
                        ],
                        'children' => [
                            'View Details' => [
                                'route' => 'admin.orders.seller.show',
                                'label' => 'Seller Order Detail',
                            ],
                        ],
                    ],
                ],
            ],
            'admin.orders.seller.show' => [
                'items' => [
                    'Update Order Status'    => 'orders.seller-update-status',
                    'Invoice'               => 'orders.seller-invoice',
                    'Customer Info'         => 'orders.seller-customer-info',
                    'Billing Address'       => 'orders.seller-billing-address',
                    'Shipping Address'      => 'orders.seller-shipping-address',
                    'Payment Details'       => 'orders.seller-payment-details',
                    'Order Items'           => 'orders.seller-order-items',
                    'Timeline'              => 'orders.seller-timeline',
                ],
            ],
            'admin.orders.pickup-point' => [
                'items' => [
                    'Export' => 'orders.pickup-export',
                ],
                'groups' => [
                    'Table Columns' => [
                        'items' => [
                            'Customer'       => 'orders.view-customer',
                            'Pricing'        => 'orders.view-pricing',
                            'Pricing Detail' => 'orders.view-pricing-detail',
                            'Discount'       => 'orders.view-discount',
                        ],
                    ],
                    'Table Actions' => [
                        'items' => [
                            'View Details'   => 'orders.pickup-view-details',
                            'Mark Picked Up' => 'orders.pickup-mark-picked',
                        ],
                        'children' => [
                            'View Details' => [
                                'route' => 'admin.orders.pickup-point.show',
                                'label' => 'Pickup Order Detail',
                            ],
                        ],
                    ],
                ],
            ],
            'admin.orders.pickup-point.show' => [
                'items' => [
                    'Status Management'   => 'orders.pickup-update-status',
                    'Invoice'             => 'orders.pickup-invoice',
                    'Customer Info'       => 'orders.pickup-customer-info',
                    'Pickup Address'      => 'orders.pickup-address',
                    'Payment Details'     => 'orders.pickup-payment-details',
                    'Order Items'         => 'orders.pickup-order-items',
                    'Mark as Picked Up'   => 'orders.pickup-mark-picked-up',
                    'Timeline'            => 'orders.pickup-timeline',
                ],
            ],
            'admin.orders.show' => [
                'items' => [
                    'Update Order Status'   => 'orders.show-update-status',
                    'Update Payment Status'  => 'orders.show-update-payment',
                    'Invoice'               => 'orders.show-invoice',
                    'Customer Info'         => 'orders.show-customer-info',
                    'Billing Address'       => 'orders.show-billing-address',
                    'Shipping Address'      => 'orders.show-shipping-address',
                    'Payment Details'       => 'orders.show-payment-details',
                    'Order Items'           => 'orders.show-order-items',
                    'Timeline'              => 'orders.show-timeline',
                    'Ship Order'            => 'orders.show-ship-order',
                ],
            ],
            'admin.quotations.index' => [
                'items' => [
                    'Create Quotation' => 'quotations.create',
                    'Export'           => 'quotations.export',
                ],
                'groups' => [
                    'Table Actions' => [
                        'items' => [
                            'View Details'    => 'quotations.view-details',
                            'Edit'            => 'quotations.edit',
                            'Delete'          => 'quotations.delete',
                            'Convert to Order'=> 'quotations.convert-to-order',
                        ],
                        'children' => [
                            'View Details' => [
                                'route' => 'admin.quotations.show',
                                'label' => 'Quotation Detail',
                            ],
                        ],
                    ],
                ],
            ],
            'admin.quotations.show' => [
                'items' => [
                    'Status Management' => 'quotations.update-status',
                    'Convert to Order'  => 'quotations.convert-to-order',
                    'Customer Info'     => 'quotations.customer-info',
                    'Billing Address'   => 'quotations.billing-address',
                    'Shipping Address'  => 'quotations.shipping-address',
                    'Payment Details'   => 'quotations.payment-details',
                    'Items'             => 'quotations.items',
                    'Notes'             => 'quotations.notes',
                    'Timeline'          => 'quotations.timeline',
                ],
            ],
            'admin.subscriptions.index' => [
                'items' => [
                    'Create Subscription' => 'subscriptions.create',
                    'Export'              => 'subscriptions.export',
                ],
                'groups' => [
                    'Table Actions' => [
                        'items' => [
                            'View Details' => 'subscriptions.view-details',
                            'Edit'         => 'subscriptions.edit',
                            'Delete'       => 'subscriptions.delete',
                            'Activate'     => 'subscriptions.activate',
                            'Pause'        => 'subscriptions.pause',
                            'Cancel'       => 'subscriptions.cancel',
                        ],
                        'children' => [
                            'View Details' => [
                                'route' => 'admin.subscriptions.show',
                                'label' => 'Subscription Detail',
                            ],
                        ],
                    ],
                ],
            ],
            'admin.subscriptions.show' => [
                'items' => [
                    'Status Management'     => 'subscriptions.update-status',
                    'Customer Info'         => 'subscriptions.customer-info',
                    'Billing Address'       => 'subscriptions.billing-address',
                    'Payment Details'       => 'subscriptions.payment-details',
                    'Subscription Plan'     => 'subscriptions.plan',
                    'Billing History'       => 'subscriptions.billing-history',
                    'Timeline'              => 'subscriptions.timeline',
                    'Activate'              => 'subscriptions.activate',
                    'Pause'                 => 'subscriptions.pause',
                    'Cancel'                => 'subscriptions.cancel',
                    'Process Billing'       => 'subscriptions.process-billing',
                ],
            ],
        ];
    }

    /**
     * Map submenu parent routes to their child pages.
     * Returns: route → [ ['item_label' => ..., 'route' => ..., 'label' => ...] ]
     */
    public static function childPages(): array
    {
        $children = [];
        foreach (self::pageComponents() as $route => $page) {
            if (isset($page['groups'])) {
                foreach ($page['groups'] as $group) {
                    if (isset($group['children'])) {
                        foreach ($group['children'] as $itemLabel => $childInfo) {
                            $children[$route][] = [
                                'attached_to' => $itemLabel,
                                'route'       => $childInfo['route'],
                                'label'       => $childInfo['label'],
                            ];
                        }
                    }
                }
            }
        }
        return $children;
    }

    /**
     * Get all page action permission names flat list.
     */
    public static function allPageActionNames(): array
    {
        $names = [];
        foreach (self::pageActions() as $route => $actions) {
            foreach ($actions as $action) {
                $names[] = $action;
            }
        }
        return $names;
    }

    /**
     * Get page actions for a specific route.
     */
    public static function pageActionsForRoute(string $routeName): array
    {
        return self::pageActions()[$routeName] ?? [];
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

        $moduleActions = self::moduleActions();
        $modules = [];

        foreach ($moduleActions as $moduleKey => $actions) {
            $modules[$moduleKey] = [
                'key'     => $moduleKey,
                'label'   => self::humanizeModule($moduleKey),
                'icon'    => self::iconForModule($moduleKey),
                'actions' => $actions,
            ];
        }

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
