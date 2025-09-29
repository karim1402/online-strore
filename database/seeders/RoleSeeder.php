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

        // Store roles
        $storeOwner = Role::firstOrCreate([
            'name' => 'store_owner',
            'guard_name' => 'store_users',
        ]);
        $storeOwner->givePermissionTo(Permission::where('guard_name', 'store_users')->get());

        $storeManager = Role::firstOrCreate([
            'name' => 'store_manager',
            'guard_name' => 'store_users',
        ]);
        $storeManager->givePermissionTo([
            'products.view', 'products.create', 'products.update',
            'orders.view', 'orders.update',
            'staff.view',
            'store-settings.view'
        ]);

        $storeStaff = Role::firstOrCreate([
            'name' => 'store_staff',
            'guard_name' => 'store_users',
        ]);
        $storeStaff->givePermissionTo([
            'products.view',
            'orders.view', 'orders.update'
        ]);

        $storeViewer = Role::firstOrCreate([
            'name' => 'store_viewer',
            'guard_name' => 'store_users',
        ]);
        $storeViewer->givePermissionTo([
            'products.view',
            'orders.view',
            'store-settings.view'
        ]);
    }
}
