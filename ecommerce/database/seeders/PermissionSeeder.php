<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $moduleActions = [
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

        $standalone = ['view-revenue', 'view-sales'];

        $permissionNames = [];
        foreach ($moduleActions as $module => $actions) {
            foreach ($actions as $action) {
                $permissionNames[] = $module . '.' . $action;
            }
        }
        $permissionNames = array_merge($permissionNames, $standalone);

        $created = 0;
        foreach ($permissionNames as $name) {
            Permission::firstOrCreate([
                'name' => $name,
                'guard_name' => 'web',
            ]);
            $created++;
        }

        $this->command->info("Created {$created} granular permissions across " . count($moduleActions) . " modules + " . count($standalone) . " standalone.");
    }
}
