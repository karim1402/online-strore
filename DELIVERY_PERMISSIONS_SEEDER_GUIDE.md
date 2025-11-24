# Delivery User Permissions Seeder

## Overview

This seeder creates the 4 delivery user permissions and assigns them to the super_admin role.

## Seeder File

📁 **Location:** `database/seeders/DeliveryUserPermissionsSeeder.php`

## Permissions Created

The seeder creates these 4 permissions:

1. ✅ `delivery-users.view` - View delivery users list and details
2. ✅ `delivery-users.create` - Create new delivery users
3. ✅ `delivery-users.update` - Update delivery users and toggle status
4. ✅ `delivery-users.delete` - Delete delivery users

**Guard:** `admins`  
**Category:** Delivery User Management

## How to Run

### Run the Seeder

```bash
php artisan db:seed --class=DeliveryUserPermissionsSeeder
```

### Expected Output

```
INFO  Seeding database.

✓ Permission created: delivery-users.view
✓ Permission created: delivery-users.create
✓ Permission created: delivery-users.update
✓ Permission created: delivery-users.delete
✓ Assigned all delivery user permissions to super_admin role

=== Summary ===
Permissions created: 4
Assigned to: super_admin
Guard: admins

✓ Verification: super_admin has 4/4 delivery user permissions
✓ SUCCESS: All delivery user permissions assigned to super_admin!
```

## What the Seeder Does

1. **Creates Permissions:** Creates 4 delivery user permissions in the database (if they don't exist)
2. **Finds Super Admin:** Locates the `super_admin` role in the admins guard
3. **Assigns Permissions:** Gives all 4 permissions to the super_admin role
4. **Verifies:** Confirms all permissions were assigned successfully
5. **Reports:** Shows detailed output of what was done

## Features

- ✅ **Idempotent:** Safe to run multiple times (uses `firstOrCreate`)
- ✅ **Auto-verification:** Checks if permissions were assigned correctly
- ✅ **Detailed output:** Shows exactly what's happening
- ✅ **Error handling:** Warns if super_admin role doesn't exist
- ✅ **Standalone:** Can run independently without affecting other permissions

## Prerequisites

The `super_admin` role must exist before running this seeder. If it doesn't exist, run:

```bash
php artisan db:seed --class=RoleSeeder
```

## Verify Permissions

After running the seeder, you can verify the permissions were assigned:

### Using Tinker

```bash
php artisan tinker
```

```php
$role = Spatie\Permission\Models\Role::where('name', 'super_admin')
    ->where('guard_name', 'admins')
    ->first();

// Get all delivery user permissions
$permissions = $role->permissions
    ->filter(fn($p) => str_contains($p->name, 'delivery-users'));

echo $permissions->pluck('name')->implode(', ');
// Output: delivery-users.view, delivery-users.create, delivery-users.update, delivery-users.delete
```

### Using Database Query

```sql
SELECT p.name 
FROM permissions p
INNER JOIN role_has_permissions rhp ON p.id = rhp.permission_id
INNER JOIN roles r ON rhp.role_id = r.id
WHERE r.name = 'super_admin' 
AND p.guard_name = 'admins'
AND p.name LIKE 'delivery-users.%';
```

## Troubleshooting

### Error: "super_admin role not found!"

**Problem:** The super_admin role doesn't exist in the database.

**Solution:** Run the RoleSeeder first:
```bash
php artisan db:seed --class=RoleSeeder
```

Then run the DeliveryUserPermissionsSeeder again:
```bash
php artisan db:seed --class=DeliveryUserPermissionsSeeder
```

### Permissions Already Exist

This is normal! The seeder uses `firstOrCreate`, so if permissions already exist, it will just assign them to super_admin without creating duplicates.

### Permissions Not Working

If you've assigned permissions but they're not working, clear the cache:
```bash
php artisan cache:clear
php artisan permission:cache-reset
```

## Integration with Main Seeders

This seeder is standalone, but you can also include it in your main `DatabaseSeeder`:

```php
// database/seeders/DatabaseSeeder.php
public function run()
{
    $this->call([
        PermissionSeeder::class,
        RoleSeeder::class,
        DeliveryUserPermissionsSeeder::class, // Add this
        // ... other seeders
    ]);
}
```

Then run:
```bash
php artisan db:seed
```

## Testing Super Admin Access

After running the seeder, test that super_admin can access delivery user endpoints:

```bash
# Login as super admin
curl -X POST http://localhost/api/admin/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "superadmin@example.com",
    "password": "password"
  }'

# Use the token to access delivery users
curl -X GET http://localhost/api/admin/delivery-users \
  -H "Authorization: Bearer {token}" \
  -H "Accept: application/json"
```

You should get a successful response without any 403 Forbidden errors.

## Summary

✅ **Seeder Created:** DeliveryUserPermissionsSeeder.php  
✅ **Permissions Added:** 4 delivery user permissions  
✅ **Assigned to:** super_admin role  
✅ **Verified:** All permissions assigned successfully  
✅ **Ready to Use:** Super admin can now manage delivery users  

---

**Command to Run:**
```bash
php artisan db:seed --class=DeliveryUserPermissionsSeeder
```
