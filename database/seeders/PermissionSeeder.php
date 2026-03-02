<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // ─── Admin Permissions ───────────────────────────────────────────

        $adminPermissions = [

            // Admin User Management
            ['name' => 'admin-users.view',   'category' => 'Admin User Management'],
            ['name' => 'admin-users.create', 'category' => 'Admin User Management'],
            ['name' => 'admin-users.update', 'category' => 'Admin User Management'],
            ['name' => 'admin-users.delete', 'category' => 'Admin User Management'],
            ['name' => 'admin-users.roles',  'category' => 'Admin User Management'],

            // Regular User Management
            ['name' => 'users.view',   'category' => 'User Management'],
            ['name' => 'users.create', 'category' => 'User Management'],
            ['name' => 'users.update', 'category' => 'User Management'],
            ['name' => 'users.delete', 'category' => 'User Management'],

            // Delivery User Management
            ['name' => 'delivery-users.view',   'category' => 'Delivery User Management'],
            ['name' => 'delivery-users.create', 'category' => 'Delivery User Management'],
            ['name' => 'delivery-users.update', 'category' => 'Delivery User Management'],
            ['name' => 'delivery-users.delete', 'category' => 'Delivery User Management'],

            // Store Management
            ['name' => 'stores.view',    'category' => 'Store Management'],
            ['name' => 'stores.create',  'category' => 'Store Management'],
            ['name' => 'stores.update',  'category' => 'Store Management'],
            ['name' => 'stores.delete',  'category' => 'Store Management'],
            ['name' => 'stores.approve', 'category' => 'Store Management'],

            // Category & Module Management
            ['name' => 'categories.view',   'category' => 'Category Management'],
            ['name' => 'categories.create', 'category' => 'Category Management'],
            ['name' => 'categories.update', 'category' => 'Category Management'],
            ['name' => 'categories.delete', 'category' => 'Category Management'],

            // Order Management
            ['name' => 'orders.view',   'category' => 'Order Management'],
            ['name' => 'orders.create', 'category' => 'Order Management'],
            ['name' => 'orders.update', 'category' => 'Order Management'],
            ['name' => 'orders.delete', 'category' => 'Order Management'],

            // Reports
            ['name' => 'reports.view', 'category' => 'Reports'],

            // Dashboard
            ['name' => 'dashboard.view', 'category' => 'Dashboard'],

            // Activity Logs
            ['name' => 'activity-logs.view',   'category' => 'Activity Logs'],
            ['name' => 'activity-logs.delete', 'category' => 'Activity Logs'],

            // Vendor Invoices
            ['name' => 'vendor-invoices.view',   'category' => 'Vendor Invoices'],
            ['name' => 'vendor-invoices.update', 'category' => 'Vendor Invoices'],

            // Vouchers
            ['name' => 'vouchers.view',   'category' => 'Voucher Management'],
            ['name' => 'vouchers.create', 'category' => 'Voucher Management'],
            ['name' => 'vouchers.update', 'category' => 'Voucher Management'],
            ['name' => 'vouchers.delete', 'category' => 'Voucher Management'],

            // Delivery Invoices
            ['name' => 'delivery-invoices.view',   'category' => 'Delivery Invoices'],
            ['name' => 'delivery-invoices.update', 'category' => 'Delivery Invoices'],

            // App Settings
            ['name' => 'app-settings.view',   'category' => 'App Settings'],
            ['name' => 'app-settings.update', 'category' => 'App Settings'],

            // Home Ads
            ['name' => 'home-ads.view',   'category' => 'Home Ads'],
            ['name' => 'home-ads.create', 'category' => 'Home Ads'],
            ['name' => 'home-ads.update', 'category' => 'Home Ads'],
            ['name' => 'home-ads.delete', 'category' => 'Home Ads'],

            // Roles & Permissions
            ['name' => 'roles.view',   'category' => 'Roles & Permissions'],
            ['name' => 'roles.create', 'category' => 'Roles & Permissions'],
            ['name' => 'roles.update', 'category' => 'Roles & Permissions'],
            ['name' => 'roles.delete', 'category' => 'Roles & Permissions'],

            // Notification Management
            ['name' => 'notifications.view',   'category' => 'Notification Management'],
            ['name' => 'notifications.create', 'category' => 'Notification Management'],

            // System Settings
            ['name' => 'settings.view',       'category' => 'System Settings'],
            ['name' => 'settings.update',     'category' => 'System Settings'],
            ['name' => 'system.maintenance',  'category' => 'System Settings'],
        ];

        $createdPermissions = [];

        foreach ($adminPermissions as $permission) {
            $perm = Permission::firstOrCreate([
                'name' => $permission['name'],
                'guard_name' => 'admins',
            ]);
            $createdPermissions[] = $perm;
            $this->command->info("✓ Permission: {$permission['name']}");
        }

        // ─── Assign ALL admin permissions to super_admin ─────────────────

        $superAdmin = Role::where('name', 'super_admin')
                         ->where('guard_name', 'admins')
                         ->first();

        if ($superAdmin) {
            $allAdminPermissions = Permission::where('guard_name', 'admins')->get();
            $superAdmin->syncPermissions($allAdminPermissions);

            $this->command->newLine();
            $this->command->info("=== Summary ===");
            $this->command->info("Total admin permissions: " . $allAdminPermissions->count());
            $this->command->info("All permissions assigned to super_admin ✓");
        } else {
            $this->command->error("✗ super_admin role not found! Run RoleSeeder first.");
        }

        // ─── Vendor Permissions ──────────────────────────────────────────

        $vendorPermissions = [
            // Product Management
            ['name' => 'products.view',   'category' => 'Product Management'],
            ['name' => 'products.create', 'category' => 'Product Management'],
            ['name' => 'products.update', 'category' => 'Product Management'],
            ['name' => 'products.delete', 'category' => 'Product Management'],

            // Order Management
            ['name' => 'orders.view',   'category' => 'Order Management'],
            ['name' => 'orders.update', 'category' => 'Order Management'],
            ['name' => 'orders.cancel', 'category' => 'Order Management'],

            // Staff Management
            ['name' => 'staff.view',   'category' => 'Staff Management'],
            ['name' => 'staff.create', 'category' => 'Staff Management'],
            ['name' => 'staff.update', 'category' => 'Staff Management'],
            ['name' => 'staff.delete', 'category' => 'Staff Management'],

            // Vendor Settings
            ['name' => 'vendor-settings.view',   'category' => 'Vendor Settings'],
            ['name' => 'vendor-settings.update', 'category' => 'Vendor Settings'],
        ];

        foreach ($vendorPermissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission['name'],
                'guard_name' => 'vendors',
            ]);
        }

        $this->command->info("Vendor permissions seeded ✓");
    }
}
