<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class DeliveryUserPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Define the delivery user permissions
        $deliveryUserPermissions = [
            ['name' => 'delivery-users.view', 'category' => 'Delivery User Management'],
            ['name' => 'delivery-users.create', 'category' => 'Delivery User Management'],
            ['name' => 'delivery-users.update', 'category' => 'Delivery User Management'],
            ['name' => 'delivery-users.delete', 'category' => 'Delivery User Management'],
        ];

        // Create the permissions in the database
        $createdPermissions = [];
        foreach ($deliveryUserPermissions as $permission) {
            $perm = Permission::firstOrCreate([
                'name' => $permission['name'],
                'guard_name' => 'admins',
            ]);
            $createdPermissions[] = $perm;
            
            $this->command->info("✓ Permission created: {$permission['name']}");
        }

        // Find the super_admin role
        $superAdmin = Role::where('name', 'super_admin')
                         ->where('guard_name', 'admins')
                         ->first();

        if ($superAdmin) {
            // Assign all delivery user permissions to super_admin
            $superAdmin->givePermissionTo($createdPermissions);
            $this->command->info("✓ Assigned all delivery user permissions to super_admin role");
            
            // Display summary
            $this->command->newLine();
            $this->command->info("=== Summary ===");
            $this->command->info("Permissions created: " . count($createdPermissions));
            $this->command->info("Assigned to: super_admin");
            $this->command->info("Guard: admins");
            
            // Verify
            $assignedCount = $superAdmin->permissions()
                ->whereIn('name', collect($deliveryUserPermissions)->pluck('name'))
                ->count();
            
            $this->command->newLine();
            $this->command->info("✓ Verification: super_admin has {$assignedCount}/4 delivery user permissions");
            
            if ($assignedCount === 4) {
                $this->command->info("✓ SUCCESS: All delivery user permissions assigned to super_admin!");
            }
        } else {
            $this->command->error("✗ ERROR: super_admin role not found!");
            $this->command->error("Please run: php artisan db:seed --class=RoleSeeder first");
        }
    }
}
