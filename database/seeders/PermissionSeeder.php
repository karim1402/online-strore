<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Admin permissions
        $adminPermissions = [
            // User Management
            ['name' => 'admin-users.view', 'category' => 'User Management'],
            ['name' => 'admin-users.create', 'category' => 'User Management'],
            ['name' => 'admin-users.update', 'category' => 'User Management'],
            ['name' => 'admin-users.delete', 'category' => 'User Management'],
            ['name' => 'admin-users.roles', 'category' => 'User Management'],
            
            // Store Management
            ['name' => 'stores.view', 'category' => 'Store Management'],
            ['name' => 'stores.create', 'category' => 'Store Management'],
            ['name' => 'stores.update', 'category' => 'Store Management'],
            ['name' => 'stores.delete', 'category' => 'Store Management'],
            ['name' => 'stores.approve', 'category' => 'Store Management'],
            
            // Category Management
            ['name' => 'categories.view', 'category' => 'Category Management'],
            ['name' => 'categories.create', 'category' => 'Category Management'],
            ['name' => 'categories.update', 'category' => 'Category Management'],
            ['name' => 'categories.delete', 'category' => 'Category Management'],
            
            // System Settings
            ['name' => 'settings.view', 'category' => 'System Settings'],
            ['name' => 'settings.update', 'category' => 'System Settings'],
            ['name' => 'system.maintenance', 'category' => 'System Settings'],
            
            // Delivery User Management
            ['name' => 'delivery-users.view', 'category' => 'Delivery User Management'],
            ['name' => 'delivery-users.create', 'category' => 'Delivery User Management'],
            ['name' => 'delivery-users.update', 'category' => 'Delivery User Management'],
            ['name' => 'delivery-users.delete', 'category' => 'Delivery User Management'],

            // Regular User Management
            ['name' => 'users.view', 'category' => 'User Management'],
            ['name' => 'users.create', 'category' => 'User Management'],
            ['name' => 'users.update', 'category' => 'User Management'],
            ['name' => 'users.delete', 'category' => 'User Management'],

            // Notification Management
            ['name' => 'notifications.view', 'category' => 'Notification Management'],
            ['name' => 'notifications.create', 'category' => 'Notification Management'],
        ];

        foreach ($adminPermissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission['name'],
                'guard_name' => 'admins',
            ]);
        }

        // Vendor permissions
        $vendorPermissions = [
            // Product Management
            ['name' => 'products.view', 'category' => 'Product Management'],
            ['name' => 'products.create', 'category' => 'Product Management'],
            ['name' => 'products.update', 'category' => 'Product Management'],
            ['name' => 'products.delete', 'category' => 'Product Management'],
            
            // Order Management
            ['name' => 'orders.view', 'category' => 'Order Management'],
            ['name' => 'orders.update', 'category' => 'Order Management'],
            ['name' => 'orders.cancel', 'category' => 'Order Management'],
            
            // Staff Management
            ['name' => 'staff.view', 'category' => 'Staff Management'],
            ['name' => 'staff.create', 'category' => 'Staff Management'],
            ['name' => 'staff.update', 'category' => 'Staff Management'],
            ['name' => 'staff.delete', 'category' => 'Staff Management'],
            
            // Vendor Settings
            ['name' => 'vendor-settings.view', 'category' => 'Vendor Settings'],
            ['name' => 'vendor-settings.update', 'category' => 'Vendor Settings'],
        ];

        foreach ($vendorPermissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission['name'],
                'guard_name' => 'vendors',
            ]);
        }
    }
}
