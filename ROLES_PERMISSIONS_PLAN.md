# 📋 **Roles & Permissions System Implementation Plan**

## 🎯 **Overview**
Implement a comprehensive roles and permissions system using **Spatie Laravel Permission** package for both Admin and Store guards, allowing fine-grained access control.

---

## 📦 **Phase 1: Package Installation & Setup**

### **1.1 Install Spatie Laravel Permission**
```bash
composer require spatie/laravel-permission
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
php artisan migrate
```

### **1.2 Configuration**
```php
// config/permission.php
'guards' => ['web', 'admins', 'store_users', 'deliveries'],
```

---

## 🔐 **Phase 2: Model Updates**

### **2.1 Admin Model Enhancement**
```php
// app/Models/Admin.php
use Spatie\Permission\Traits\HasRoles;

class Admin extends Authenticatable implements JWTSubject
{
    use HasRoles;
    
    protected $guard_name = 'admins';
    
    public function getJWTCustomClaims()
    {
        return [
            'guard' => 'admins',
            'permissions' => $this->getAllPermissions()->pluck('name')->toArray(),
            'roles' => $this->getRoleNames()->toArray(),
        ];
    }
}
```

### **2.2 StoreUser Model Enhancement**
```php
// app/Models/StoreUser.php
use Spatie\Permission\Traits\HasRoles;

class StoreUser extends Authenticatable implements JWTSubject
{
    use HasRoles;
    
    protected $guard_name = 'store_users';
    
    public function getJWTCustomClaims()
    {
        return [
            'guard' => 'store_users',
            'permissions' => $this->getAllPermissions()->pluck('name')->toArray(),
            'roles' => $this->getRoleNames()->toArray(),
        ];
    }
}
```

---

## 👑 **Phase 3: Roles Definition**

### **3.1 Admin Guard Roles**
- `super_admin` - Full system access
- `admin` - Standard admin access
- `manager` - Limited management access
- `moderator` - Content moderation only

### **3.2 Store Guard Roles**
- `store_owner` - Full store control
- `store_manager` - Store operations
- `store_staff` - Limited store access
- `store_viewer` - Read-only access

---

## 🛡️ **Phase 4: Permissions Structure**

### **4.1 Admin Permissions**
```php
// User Management
'admin-users.view', 'admin-users.create', 'admin-users.update', 'admin-users.delete'

// Store Management
'stores.view', 'stores.create', 'stores.update', 'stores.delete', 'stores.approve'

// Category Management
'categories.view', 'categories.create', 'categories.update', 'categories.delete'

// System Settings
'settings.view', 'settings.update', 'system.maintenance'
```

### **4.2 Store Permissions**
```php
// Product Management
'products.view', 'products.create', 'products.update', 'products.delete'

// Order Management
'orders.view', 'orders.update', 'orders.cancel'

// Staff Management
'staff.view', 'staff.create', 'staff.update', 'staff.delete'

// Store Settings
'store-settings.view', 'store-settings.update'
```

---

## 🏭 **Phase 5: Seeders**

### **5.1 Permission Seeder**
```php
// database/seeders/PermissionSeeder.php
public function run()
{
    $adminPermissions = [
        'admin-users.view', 'admin-users.create', 'admin-users.update', 'admin-users.delete',
        'stores.view', 'stores.create', 'stores.update', 'stores.delete',
        'categories.view', 'categories.create', 'categories.update', 'categories.delete',
        'settings.view', 'settings.update'
    ];

    foreach ($adminPermissions as $permission) {
        Permission::create(['name' => $permission, 'guard_name' => 'admins']);
    }

    $storePermissions = [
        'products.view', 'products.create', 'products.update', 'products.delete',
        'orders.view', 'orders.update', 'orders.cancel',
        'staff.view', 'staff.create', 'staff.update', 'staff.delete',
        'store-settings.view', 'store-settings.update'
    ];

    foreach ($storePermissions as $permission) {
        Permission::create(['name' => $permission, 'guard_name' => 'store_users']);
    }
}
```

### **5.2 Role Seeder**
```php
// database/seeders/RoleSeeder.php
public function run()
{
    // Admin roles
    $superAdmin = Role::create(['name' => 'super_admin', 'guard_name' => 'admins']);
    $superAdmin->givePermissionTo(Permission::where('guard_name', 'admins')->get());

    $admin = Role::create(['name' => 'admin', 'guard_name' => 'admins']);
    $admin->givePermissionTo(['admin-users.view', 'stores.view', 'categories.view']);

    // Store roles
    $storeOwner = Role::create(['name' => 'store_owner', 'guard_name' => 'store_users']);
    $storeOwner->givePermissionTo(Permission::where('guard_name', 'store_users')->get());

    $storeManager = Role::create(['name' => 'store_manager', 'guard_name' => 'store_users']);
    $storeManager->givePermissionTo(['products.view', 'products.create', 'orders.view']);
}
```

---

## 🛠️ **Phase 6: Middleware**

### **6.1 Permission Middleware**
```php
// app/Http/Middleware/CheckPermission.php
public function handle(Request $request, Closure $next, $permission, $guard = null)
{
    if (!auth($guard)->user()->can($permission)) {
        return response()->json([
            'success' => false,
            'message' => LocalizationService::getMessage('errors.insufficient_permissions')
        ], 403);
    }
    
    return $next($request);
}
```

### **6.2 Role Middleware**
```php
// app/Http/Middleware/CheckRole.php
public function handle(Request $request, Closure $next, $role, $guard = null)
{
    if (!auth($guard)->user()->hasRole($role)) {
        return response()->json([
            'success' => false,
            'message' => LocalizationService::getMessage('errors.insufficient_role')
        ], 403);
    }
    
    return $next($request);
}
```

---

## 🛣️ **Phase 7: Route Protection**

### **7.1 Admin Routes**
```php
// routes/api/admin.php
Route::middleware(['auth:admins', 'permission:admin-users.view,admins'])->group(function () {
    Route::get('admin-users', [AdminUserController::class, 'index']);
});

Route::middleware(['auth:admins', 'permission:categories.create,admins'])->group(function () {
    Route::post('main-categories', [MainCategoryController::class, 'store']);
});
```

### **7.2 Store Routes**
```php
// routes/api/store.php
Route::middleware(['auth:store_users', 'permission:products.view,store_users'])->group(function () {
    Route::get('products', [ProductController::class, 'index']);
});
```

---

## 🎮 **Phase 8: Controllers**

### **8.1 Role Management Controller**
```php
// app/Http/Controllers/Api/Admin/RoleController.php
class RoleController extends Controller
{
    use ApiResponse;

    public function index(Request $request): JsonResponse
    {
        $guard = $request->get('guard', 'admins');
        $roles = Role::where('guard_name', $guard)->with('permissions')->paginate(15);
        return $this->successResponse($roles, 'success.roles_retrieved');
    }

    public function store(Request $request): JsonResponse
    {
        $validator = ValidationService::make($request->all(), [
            'name' => 'required|string|max:255',
            'guard_name' => 'required|string|in:admins,store_users',
            'permissions' => 'array'
        ]);

        if ($validator->fails()) {
            return $this->validationErrorWithFirstMessage($validator);
        }

        $role = Role::create($validator->validated());
        
        if ($request->has('permissions')) {
            $role->syncPermissions($request->permissions);
        }

        return $this->successResponse($role->load('permissions'), 'success.role_created', [], 201);
    }
}
```

---

## 🌐 **Phase 9: API Endpoints**

### **9.1 Admin Endpoints**
```
GET    /api/admin/roles                    # List roles
POST   /api/admin/roles                    # Create role
GET    /api/admin/roles/{id}               # Show role
PUT    /api/admin/roles/{id}               # Update role
DELETE /api/admin/roles/{id}               # Delete role

GET    /api/admin/permissions              # List permissions
GET    /api/admin/admin-users/{id}/roles   # Get user roles
POST   /api/admin/admin-users/{id}/roles   # Assign role
```

### **9.2 Store Endpoints**
```
GET    /api/store/roles                    # List store roles
POST   /api/store/roles                    # Create store role
GET    /api/store/staff/{id}/roles         # Get staff roles
POST   /api/store/staff/{id}/roles         # Assign role to staff
```

---

## 🌍 **Phase 10: Localization**

### **10.1 English Messages**
```json
{
    "success": {
        "roles_retrieved": "Roles retrieved successfully",
        "role_created": "Role created successfully",
        "permissions_retrieved": "Permissions retrieved successfully"
    },
    "errors": {
        "insufficient_permissions": "You don't have permission to perform this action",
        "insufficient_role": "Your role doesn't allow this action"
    }
}
```

### **10.2 Arabic Messages**
```json
{
    "success": {
        "roles_retrieved": "تم جلب الأدوار بنجاح",
        "role_created": "تم إنشاء الدور بنجاح",
        "permissions_retrieved": "تم جلب الصلاحيات بنجاح"
    },
    "errors": {
        "insufficient_permissions": "ليس لديك صلاحية لتنفيذ هذا الإجراء",
        "insufficient_role": "دورك لا يسمح بهذا الإجراء"
    }
}
```

---

## 📈 **Phase 11: Implementation Timeline**

### **Week 1: Foundation**
- Package installation
- Model updates
- Basic permissions seeding

### **Week 2: Admin Guard**
- Admin roles & permissions
- Admin controllers
- Admin route protection

### **Week 3: Store Guard**
- Store roles & permissions
- Store controllers
- Store route protection

### **Week 4: Testing & Polish**
- Comprehensive testing
- Documentation
- Performance optimization

---

## 🧪 **Phase 12: Testing Examples**

### **12.1 Permission Testing**
```php
// Test admin can create categories
$admin = Admin::factory()->create();
$admin->givePermissionTo('categories.create');

$response = $this->actingAs($admin, 'admins')
                 ->postJson('/api/admin/main-categories', $data);

$response->assertStatus(201);
```

### **12.2 Role Testing**
```php
// Test store manager can view products
$storeUser = StoreUser::factory()->create();
$storeUser->assignRole('store_manager');

$response = $this->actingAs($storeUser, 'store_users')
                 ->getJson('/api/store/products');

$response->assertStatus(200);
```

---

## 📊 **Expected Benefits**

✅ **Security**: Fine-grained access control  
✅ **Scalability**: Easy to add new roles/permissions  
✅ **Maintainability**: Centralized permission management  
✅ **Flexibility**: Guard-specific implementations  
✅ **Compliance**: Audit trail and access logging  

---

## 🔧 **Commands to Run**

```bash
# Install package
composer require spatie/laravel-permission

# Publish and run migrations
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
php artisan migrate

# Create seeders
php artisan make:seeder PermissionSeeder
php artisan make:seeder RoleSeeder

# Create middleware
php artisan make:middleware CheckPermission
php artisan make:middleware CheckRole

# Create controllers
php artisan make:controller Api/Admin/RoleController
php artisan make:controller Api/Admin/PermissionController

# Run seeders
php artisan db:seed --class=PermissionSeeder
php artisan db:seed --class=RoleSeeder
```

---

**This plan provides a comprehensive roadmap for implementing a robust roles and permissions system that can scale with your application's growth!** 🚀
