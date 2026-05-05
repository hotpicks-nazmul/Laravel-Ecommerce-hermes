<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            // Products
            'products' => ['view', 'create', 'edit', 'delete', 'import', 'export'],
            
            // Orders
            'orders' => ['view', 'create', 'edit', 'delete', 'export'],
            
            // Customers
            'customers' => ['view', 'create', 'edit', 'delete', 'export'],
            
            // Marketing
            'marketing' => ['view', 'create', 'edit', 'delete'],
            
            // Content
            'content' => ['view', 'create', 'edit', 'delete'],
            
            // Appearance
            'appearance' => ['view', 'create', 'edit', 'delete'],
            
            // Settings
            'settings' => ['view', 'create', 'edit', 'delete'],
            
            // Support
            'support' => ['view', 'create', 'edit', 'delete'],
            
            // Reports
            'reports' => ['view', 'export'],
            
            // Inventory
            'inventory' => ['view', 'create', 'edit', 'delete', 'export'],
            
            // Delivery
            'delivery' => ['view', 'create', 'edit', 'delete'],
            
            // Refund
            'refund' => ['view', 'manage'],
            
            // Sellers
            'sellers' => ['view', 'create', 'edit', 'delete'],
            
            // Warehouse
            'warehouse' => ['view', 'create', 'edit', 'delete'],
            
            // Staffs
            'staffs' => ['all', 'permissions', 'roles'],
            
            // Locations
            'locations' => ['states', 'cities', 'areas', 'settings'],
            
            // System
            'system' => ['view', 'update', 'logs'],
            
            // OTP
            'otp' => ['view', 'configure', 'credentials', 'templates'],
            
            // POS
            'pos' => ['view', 'create', 'edit', 'delete'],
            
            // Affiliate
            'affiliate' => ['view', 'create', 'edit', 'delete'],
            
            // Media
            'media' => ['view', 'upload', 'delete'],
            
            // Multi-Store
            'multistore' => ['view', 'create', 'edit', 'delete'],
            
            // Addon
            'addon' => ['view', 'install', 'uninstall'],
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