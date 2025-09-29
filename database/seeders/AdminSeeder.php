<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Admin;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create super admin user
        $superAdmin = Admin::firstOrCreate([
            'email' => 'superadmin@test.com'
        ], [
            'name' => 'Super Admin',
            'email' => 'superadmin@test.com',
            'password' => Hash::make('password123'),
            'phone' => '+1234567890',
            'status' => true,
        ]);

        // Assign super_admin role
        $superAdminRole = Role::where('name', 'super_admin')
                             ->where('guard_name', 'admins')
                             ->first();

        if ($superAdminRole && !$superAdmin->hasRole('super_admin')) {
            $superAdmin->assignRole($superAdminRole);
        }

        // Create additional test admin users
        $admin = Admin::firstOrCreate([
            'email' => 'admin@test.com'
        ], [
            'name' => 'Test Admin',
            'email' => 'admin@test.com',
            'password' => Hash::make('password123'),
            'phone' => '+1234567891',
            'status' => true,
        ]);

        // Assign admin role
        $adminRole = Role::where('name', 'admin')
                        ->where('guard_name', 'admins')
                        ->first();

        if ($adminRole && !$admin->hasRole('admin')) {
            $admin->assignRole($adminRole);
        }

        // Create manager user
        $manager = Admin::firstOrCreate([
            'email' => 'manager@test.com'
        ], [
            'name' => 'Test Manager',
            'email' => 'manager@test.com',
            'password' => Hash::make('password123'),
            'phone' => '+1234567892',
            'status' => true,
        ]);

        // Assign manager role
        $managerRole = Role::where('name', 'manager')
                          ->where('guard_name', 'admins')
                          ->first();

        if ($managerRole && !$manager->hasRole('manager')) {
            $manager->assignRole($managerRole);
        }

        $this->command->info('Admin users created successfully with roles assigned!');
        $this->command->info('Super Admin: superadmin@test.com / password123');
        $this->command->info('Admin: admin@test.com / password123');
        $this->command->info('Manager: manager@test.com / password123');
    }
}
