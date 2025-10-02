<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Admin roles
        $superAdmin = Role::firstOrCreate([
            'name' => 'super_admin',
            'guard_name' => 'admins',
        ]);
        $superAdmin->givePermissionTo(Permission::where('guard_name', 'admins')->get());

        $admin = Role::firstOrCreate([
            'name' => 'admin',
            'guard_name' => 'admins',
        ]);
        $admin->givePermissionTo([
            'admin-users.view',
            'stores.view', 'stores.update', 'stores.approve',
            'categories.view', 'categories.create', 'categories.update',
            'settings.view'
        ]);

        $manager = Role::firstOrCreate([
            'name' => 'manager',
            'guard_name' => 'admins',
        ]);
        $manager->givePermissionTo([
            'stores.view', 'stores.update',
            'categories.view', 'categories.update',
        ]);

        $moderator = Role::firstOrCreate([
            'name' => 'moderator',
            'guard_name' => 'admins',
        ]);
        $moderator->givePermissionTo([
            'categories.view',
            'stores.view'
        ]);

        // Vendor roles
        $vendorOwner = Role::firstOrCreate([
            'name' => 'vendor_owner',
            'guard_name' => 'vendors',
        ]);
        $vendorOwner->givePermissionTo(Permission::where('guard_name', 'vendors')->get());

        $vendorManager = Role::firstOrCreate([
            'name' => 'vendor_manager',
            'guard_name' => 'vendors',
        ]);
        $vendorManager->givePermissionTo([
            'products.view', 'products.create', 'products.update',
            'orders.view', 'orders.update',
            'staff.view',
            'vendor-settings.view'
        ]);

        $vendorStaff = Role::firstOrCreate([
            'name' => 'vendor_staff',
            'guard_name' => 'vendors',
        ]);
        $vendorStaff->givePermissionTo([
            'products.view',
            'orders.view', 'orders.update'
        ]);

        $vendorViewer = Role::firstOrCreate([
            'name' => 'vendor_viewer',
            'guard_name' => 'vendors',
        ]);
        $vendorViewer->givePermissionTo([
            'products.view',
            'orders.view',
            'vendor-settings.view'
        ]);
    }
}
