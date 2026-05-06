<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            // View-only modules
            'dashboard' => ['view'],
            'analytics' => ['view'],
            'products' => ['view'],
            'customers' => ['view'],
            'marketing' => ['view'],
            'content' => ['view'],
            'appearance' => ['view'],
            'settings' => ['view'],
            'support' => ['view'],
            'reports' => ['view'],
            'inventory' => ['view'],
            'delivery' => ['view'],
            'sellers' => ['view'],
            'warehouse' => ['view'],
            'staffs' => ['view'],
            'pos' => ['view'],
            'affiliate' => ['view'],
            'multistore' => ['view'],

            // Orders - with section-level permissions
            'orders' => ['view', 'view-customer', 'view-pricing'],

            // Products - with cost view permission
            'products' => ['view', 'view-cost'],

            // Customers - with financial view permission
            'customers' => ['view', 'view-financial'],

            // Refund - with customer view permission
            'refund' => ['view', 'view-customer'],

            // Non-CRUD: domain-specific actions
            'media' => ['view', 'upload', 'delete'],
            'addon' => ['view', 'install', 'uninstall'],
            'system' => ['view', 'update', 'logs'],
            'otp' => ['view', 'configure', 'credentials', 'templates'],
            'locations' => ['states', 'cities', 'areas', 'settings'],
        ];

        $created = 0;
        
        foreach ($permissions as $module => $actions) {
            foreach ($actions as $action) {
                $permissionName = $module . '.' . $action;
                Permission::firstOrCreate([
                    'name' => $permissionName,
                    'guard_name' => 'web',
                ]);
                $created++;
            }
        }

        $this->command->info("Created {$created} granular permissions.");
    }
}