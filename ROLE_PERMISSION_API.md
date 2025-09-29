# 🔐 **Role & Permission Management API**

## 🎯 **Overview**
Complete CRUD API for managing roles, permissions, and user role assignments in the admin guard with bilingual support.

---

## 🛡️ **Authentication & Permissions**

All endpoints require:
- **JWT Authentication**: `Authorization: Bearer {token}`
- **Admin Guard**: User must be authenticated as admin
- **Specific Permissions**: As detailed below

---

## 📋 **Role Management API**

### **Base URL**: `/api/admin/roles`
**Required Role**: `super_admin` only

### **1. List Roles**
```http
GET /api/admin/roles?guard=admins&search=admin&per_page=15
```

**Query Parameters:**
- `guard` (optional): `admins` or `store_users` (default: `admins`)
- `search` (optional): Search by role name
- `per_page` (optional): Items per page (default: 15)

**Response:**
```json
{
    "success": true,
    "message": "Roles retrieved successfully",
    "data": {
        "data": [
            {
                "id": 1,
                "name": "super_admin",
                "guard_name": "admins",
                "created_at": "2025-09-28T10:00:00.000000Z",
                "permissions": [
                    {"id": 1, "name": "admin-users.view", "guard_name": "admins"},
                    {"id": 2, "name": "admin-users.create", "guard_name": "admins"}
                ]
            }
        ],
        "current_page": 1,
        "per_page": 15,
        "total": 4
    }
}
```

### **2. Create Role**
```http
POST /api/admin/roles
Content-Type: application/json

{
    "name": "content_manager",
    "guard_name": "admins",
    "permissions": ["categories.view", "categories.create", "categories.update"]
}
```

**Request Body:**
- `name` (required): Unique role name
- `guard_name` (required): `admins` or `store_users`
- `permissions` (optional): Array of permission names

### **3. Show Role**
```http
GET /api/admin/roles/{id}
```

### **4. Update Role**
```http
PUT /api/admin/roles/{id}
Content-Type: application/json

{
    "name": "updated_role_name",
    "permissions": ["categories.view", "categories.update"]
}
```

### **5. Delete Role**
```http
DELETE /api/admin/roles/{id}
```

**Note**: Cannot delete roles that are assigned to users.

### **6. Get Available Permissions**
```http
GET /api/admin/roles/permissions?guard=admins
```

---

## 🔑 **Permission Management API**

### **Base URL**: `/api/admin/permissions`
**Required Role**: `super_admin` only

### **1. List Permissions**
```http
GET /api/admin/permissions?guard=admins&search=admin&per_page=50
```

**Response:**
```json
{
    "success": true,
    "message": "Permissions retrieved successfully",
    "data": {
        "data": [
            {
                "id": 1,
                "name": "admin-users.view",
                "guard_name": "admins",
                "created_at": "2025-09-28T10:00:00.000000Z"
            }
        ]
    }
}
```

### **2. Get Permissions by Category**
```http
GET /api/admin/permissions/categories?guard=admins
```

**Response:**
```json
{
    "success": true,
    "message": "Permissions by category retrieved successfully",
    "data": {
        "admin-users": [
            {"id": 1, "name": "admin-users.view", "guard_name": "admins"},
            {"id": 2, "name": "admin-users.create", "guard_name": "admins"}
        ],
        "categories": [
            {"id": 6, "name": "categories.view", "guard_name": "admins"},
            {"id": 7, "name": "categories.create", "guard_name": "admins"}
        ]
    }
}
```

### **3. Show Permission**
```http
GET /api/admin/permissions/{id}
```

---

## 👥 **User Role Assignment API**

### **Base URL**: `/api/admin/users`
**Required Permission**: `admin-users.roles`

### **1. Get User Roles**
```http
GET /api/admin/users/{userId}/roles?guard=admins
```

**Response:**
```json
{
    "success": true,
    "message": "User roles retrieved successfully",
    "data": {
        "user": {
            "id": 2,
            "name": "Test Admin",
            "email": "admin@test.com"
        },
        "roles": [
            {"id": 1, "name": "super_admin", "guard_name": "admins"}
        ],
        "permissions": [
            {"id": 1, "name": "admin-users.view", "guard_name": "admins"},
            {"id": 2, "name": "admin-users.create", "guard_name": "admins"}
        ]
    }
}
```

### **2. Assign Role to User**
```http
POST /api/admin/users/{userId}/roles/assign
Content-Type: application/json

{
    "guard": "admins",
    "role": "admin"
}
```

### **3. Remove Role from User**
```http
POST /api/admin/users/{userId}/roles/remove
Content-Type: application/json

{
    "guard": "admins",
    "role": "admin"
}
```

### **4. Sync User Roles (Replace All)**
```http
POST /api/admin/users/{userId}/roles/sync
Content-Type: application/json

{
    "guard": "admins",
    "roles": ["admin", "manager"]
}
```

---

## 🌍 **Bilingual Support**

All responses are automatically localized based on the `Accept-Language` header:

**English** (`Accept-Language: en`):
```json
{
    "success": true,
    "message": "Role created successfully"
}
```

**Arabic** (`Accept-Language: ar`):
```json
{
    "success": true,
    "message": "تم إنشاء الدور بنجاح"
}
```

---

## 🚀 **Usage Examples**

### **1. Create a New Role**
```bash
curl -X POST http://localhost:8000/api/admin/roles \
  -H "Authorization: Bearer YOUR_JWT_TOKEN" \
  -H "Content-Type: application/json" \
  -H "Accept-Language: en" \
  -d '{
    "name": "content_manager",
    "guard_name": "admins",
    "permissions": ["categories.view", "categories.create", "categories.update"]
  }'
```

### **2. Assign Role to Admin User**
```bash
curl -X POST http://localhost:8000/api/admin/users/2/roles/assign \
  -H "Authorization: Bearer YOUR_JWT_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "guard": "admins",
    "role": "content_manager"
  }'
```

### **3. Get All Permissions by Category**
```bash
curl -X GET "http://localhost:8000/api/admin/permissions/categories?guard=admins" \
  -H "Authorization: Bearer YOUR_JWT_TOKEN" \
  -H "Accept-Language: ar"
```

### **4. List Roles with Search**
```bash
curl -X GET "http://localhost:8000/api/admin/roles?search=admin&per_page=10" \
  -H "Authorization: Bearer YOUR_JWT_TOKEN"
```

---

## 🔒 **Security Features**

### **Role-Based Access**
- **Role Management**: Only `super_admin` can manage roles and permissions
- **User Role Assignment**: Requires `admin-users.roles` permission
- **Guard Separation**: Admin and Store roles are completely separate

### **Validation**
- **Unique Role Names**: Prevents duplicate role creation
- **Guard Validation**: Ensures roles/permissions match the correct guard
- **User Existence**: Validates user exists before role assignment
- **Role Existence**: Validates role exists before assignment

### **Safety Checks**
- **Role Deletion**: Prevents deletion of roles assigned to users
- **Permission Validation**: Only valid permissions can be assigned
- **Guard Matching**: Roles and permissions must match guard types

---

## 📊 **Available Roles & Permissions**

### **Admin Guard Roles**
- `super_admin` - Full system access (17 permissions)
- `admin` - Standard admin access (8 permissions)
- `manager` - Limited management (4 permissions)
- `moderator` - Content moderation (2 permissions)

### **Admin Guard Permissions**
```
User Management:
- admin-users.view, admin-users.create, admin-users.update, admin-users.delete, admin-users.roles

Store Management:
- stores.view, stores.create, stores.update, stores.delete, stores.approve

Category Management:
- categories.view, categories.create, categories.update, categories.delete

System Settings:
- settings.view, settings.update, system.maintenance
```

### **Store Guard Roles**
- `store_owner` - Full store control (12 permissions)
- `store_manager` - Store operations (7 permissions)
- `store_staff` - Limited access (3 permissions)
- `store_viewer` - Read-only access (3 permissions)

---

## ⚠️ **Error Responses**

### **Insufficient Permissions**
```json
{
    "success": false,
    "message": "You don't have permission to perform this action"
}
```

### **Role Not Found**
```json
{
    "success": false,
    "message": "Role not found"
}
```

### **Cannot Delete Role**
```json
{
    "success": false,
    "message": "Cannot delete role that is assigned to users"
}
```

---

## 🎯 **Complete API Endpoints Summary**

```
# Role Management (super_admin only)
GET    /api/admin/roles                    # List roles
POST   /api/admin/roles                    # Create role
GET    /api/admin/roles/permissions        # Get available permissions
GET    /api/admin/roles/{id}               # Show role
PUT    /api/admin/roles/{id}               # Update role
DELETE /api/admin/roles/{id}               # Delete role

# Permission Management (super_admin only)
GET    /api/admin/permissions              # List permissions
GET    /api/admin/permissions/categories   # Permissions by category
GET    /api/admin/permissions/{id}         # Show permission

# User Role Assignment (admin-users.roles permission)
GET    /api/admin/users/{userId}/roles     # Get user roles
POST   /api/admin/users/{userId}/roles/assign    # Assign role
POST   /api/admin/users/{userId}/roles/remove    # Remove role
POST   /api/admin/users/{userId}/roles/sync      # Sync all roles
```

**The role and permission management system is now fully operational with complete CRUD capabilities!** 🚀
