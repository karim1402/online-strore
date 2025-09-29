# 🔐 **Makook API - Roles & Permissions System**

## 📋 **Table of Contents**
- [Overview](#overview)
- [Authentication](#authentication)
- [Permission System](#permission-system)
- [Role Management](#role-management)
- [Permission Management](#permission-management)
- [User Role Assignment](#user-role-assignment)
- [Available Roles & Permissions](#available-roles--permissions)
- [Usage Examples](#usage-examples)
- [Error Handling](#error-handling)
- [Bilingual Support](#bilingual-support)

---

## 🎯 **Overview**

The Makook API implements a comprehensive role-based access control (RBAC) system using Spatie Laravel Permission. This system provides:

- **Multi-Guard Support**: Separate permission systems for Admins and Store Users
- **Granular Permissions**: Fine-grained control over API access
- **Role Hierarchy**: Predefined roles with specific permission sets
- **Dynamic Management**: Full CRUD operations for roles and permissions
- **Bilingual Support**: All responses available in English and Arabic

---

## 🔑 **Authentication**

All permission-related endpoints require JWT authentication:

```http
Authorization: Bearer YOUR_JWT_TOKEN
```

### **Getting a JWT Token**
```http
POST /api/admin/login
Content-Type: application/json

{
    "email": "superadmin@test.com",
    "password": "password123"
}
```

**Response:**
```json
{
    "success": true,
    "message": "Admin login successful",
    "data": {
        "access_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...",
        "token_type": "bearer",
        "expires_in": 3600,
        "user": {
            "id": 2,
            "name": "Test Super Admin",
            "email": "superadmin@test.com",
            "role": "super_admin"
        }
    }
}
```

---

## 🛡️ **Permission System**

### **How Permissions Work**

1. **Guards**: Separate permission systems for different user types
   - `admins` - Admin users
   - `store_users` - Store owners and staff

2. **Permissions**: Specific actions users can perform
   - Format: `resource.action` (e.g., `admin-users.view`)
   - Categories: User Management, Store Management, Category Management, System Settings

3. **Roles**: Collections of permissions
   - Predefined roles with specific permission sets
   - Custom roles can be created with any combination of permissions

### **Permission Categories**

#### **Admin Guard Permissions**
```
User Management:
├── admin-users.view        # View admin users list
├── admin-users.create      # Create new admin users
├── admin-users.update      # Update admin user details
├── admin-users.delete      # Delete admin users
└── admin-users.roles       # Manage admin user roles

Store Management:
├── stores.view             # View stores list
├── stores.create           # Create new stores
├── stores.update           # Update store details
├── stores.delete           # Delete stores
└── stores.approve          # Approve store applications

Category Management:
├── categories.view         # View categories
├── categories.create       # Create new categories
├── categories.update       # Update categories
└── categories.delete       # Delete categories

System Settings:
├── settings.view           # View system settings
├── settings.update         # Update system settings
└── system.maintenance      # System maintenance mode
```

#### **Store Guard Permissions**
```
Product Management:
├── products.view           # View products
├── products.create         # Create new products
├── products.update         # Update products
└── products.delete         # Delete products

Order Management:
├── orders.view             # View orders
├── orders.update           # Update order status
└── orders.cancel           # Cancel orders

Staff Management:
├── staff.view              # View staff members
├── staff.create            # Add staff members
├── staff.update            # Update staff details
└── staff.delete            # Remove staff members

Store Settings:
├── store-settings.view     # View store settings
└── store-settings.update   # Update store settings
```

---

## 👑 **Role Management**

### **Base URL**: `/api/admin/roles`
**Required Role**: `super_admin` only

### **1. List All Roles**
```http
GET /api/admin/roles
```

**Query Parameters:**
- `guard` (optional): Filter by guard (`admins` or `store_users`)
- `search` (optional): Search roles by name
- `per_page` (optional): Items per page (default: 15)

**What it does:**
- Retrieves paginated list of roles
- Includes associated permissions for each role
- Supports search and filtering by guard

**Example Request:**
```bash
curl -X GET "http://localhost:8000/api/admin/roles?guard=admins&search=admin" \
  -H "Authorization: Bearer YOUR_JWT_TOKEN"
```

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
                    {"id": 1, "name": "admin-users.view"},
                    {"id": 2, "name": "admin-users.create"}
                ]
            }
        ],
        "current_page": 1,
        "per_page": 15,
        "total": 4
    }
}
```

### **2. Create New Role**
```http
POST /api/admin/roles
```

**What it does:**
- Creates a new role with specified permissions
- Validates role name uniqueness
- Assigns permissions to the role

**Request Body:**
```json
{
    "name": "content_manager",
    "guard_name": "admins",
    "permissions": ["categories.view", "categories.create", "categories.update"]
}
```

**Example:**
```bash
curl -X POST http://localhost:8000/api/admin/roles \
  -H "Authorization: Bearer YOUR_JWT_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "content_manager",
    "guard_name": "admins",
    "permissions": ["categories.view", "categories.create"]
  }'
```

### **3. Get Single Role**
```http
GET /api/admin/roles/{id}
```

**What it does:**
- Retrieves detailed information about a specific role
- Includes all permissions assigned to the role

### **4. Update Role**
```http
PUT /api/admin/roles/{id}
```

**What it does:**
- Updates role name and/or permissions
- Replaces existing permissions with new ones
- Validates role name uniqueness

**Request Body:**
```json
{
    "name": "updated_role_name",
    "permissions": ["categories.view", "categories.update"]
}
```

### **5. Delete Role**
```http
DELETE /api/admin/roles/{id}
```

**What it does:**
- Deletes a role from the system
- Prevents deletion if role is assigned to users
- Returns error if role has active assignments

### **6. Get Available Permissions**
```http
GET /api/admin/roles/permissions
```

**Query Parameters:**
- `guard` (optional): Filter permissions by guard (default: `admins`)

**What it does:**
- Lists all available permissions for a specific guard
- Used when creating/updating roles to show available options

---

## 🔑 **Permission Management**

### **Base URL**: `/api/admin/permissions`
**Required Role**: `super_admin` only

### **1. List All Permissions**
```http
GET /api/admin/permissions
```

**Query Parameters:**
- `guard` (optional): Filter by guard (`admins` or `store_users`)
- `search` (optional): Search permissions by name
- `per_page` (optional): Items per page (default: 50)

**What it does:**
- Retrieves paginated list of all permissions
- Supports filtering by guard and search
- Shows permission details and associated roles

**Example:**
```bash
curl -X GET "http://localhost:8000/api/admin/permissions?guard=admins" \
  -H "Authorization: Bearer YOUR_JWT_TOKEN"
```

### **2. Get Permissions by Category**
```http
GET /api/admin/permissions/categories
```

**What it does:**
- Groups permissions by their category (e.g., admin-users, categories, stores)
- Provides organized view of all permissions
- Useful for building permission selection interfaces

**Response:**
```json
{
    "success": true,
    "message": "Permissions by category retrieved successfully",
    "data": {
        "admin-users": [
            {"id": 1, "name": "admin-users.view"},
            {"id": 2, "name": "admin-users.create"}
        ],
        "categories": [
            {"id": 6, "name": "categories.view"},
            {"id": 7, "name": "categories.create"}
        ]
    }
}
```

### **3. Get Single Permission**
```http
GET /api/admin/permissions/{id}
```

**What it does:**
- Retrieves detailed information about a specific permission
- Shows which roles have this permission
- Includes permission metadata

---

## 👥 **User Role Assignment**

### **Base URL**: `/api/admin/users`
**Required Permission**: `admin-users.roles`

### **1. Get User's Roles and Permissions**
```http
GET /api/admin/users/{userId}/roles
```

**Query Parameters:**
- `guard` (optional): Specify user guard (default: `admins`)

**What it does:**
- Retrieves all roles assigned to a user
- Shows all permissions the user has (direct + role-based)
- Displays user information

**Example:**
```bash
curl -X GET "http://localhost:8000/api/admin/users/2/roles?guard=admins" \
  -H "Authorization: Bearer YOUR_JWT_TOKEN"
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
            {"id": 1, "name": "admin-users.view"},
            {"id": 2, "name": "admin-users.create"}
        ]
    }
}
```

### **2. Assign Role to User**
```http
POST /api/admin/users/{userId}/roles/assign
```

**What it does:**
- Assigns a specific role to a user
- Adds to existing roles (doesn't replace)
- Validates role exists for the specified guard

**Request Body:**
```json
{
    "guard": "admins",
    "role": "admin"
}
```

**Example:**
```bash
curl -X POST http://localhost:8000/api/admin/users/2/roles/assign \
  -H "Authorization: Bearer YOUR_JWT_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "guard": "admins",
    "role": "content_manager"
  }'
```

### **3. Remove Role from User**
```http
POST /api/admin/users/{userId}/roles/remove
```

**What it does:**
- Removes a specific role from a user
- Keeps other roles intact
- Updates user permissions accordingly

**Request Body:**
```json
{
    "guard": "admins",
    "role": "admin"
}
```

### **4. Sync User Roles (Replace All)**
```http
POST /api/admin/users/{userId}/roles/sync
```

**What it does:**
- Replaces ALL user roles with the specified list
- Removes existing roles not in the list
- Adds new roles from the list

**Request Body:**
```json
{
    "guard": "admins",
    "roles": ["admin", "manager"]
}
```

**Example:**
```bash
curl -X POST http://localhost:8000/api/admin/users/2/roles/sync \
  -H "Authorization: Bearer YOUR_JWT_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "guard": "admins",
    "roles": ["admin"]
  }'
```

---

## 🏷️ **Available Roles & Permissions**

### **Admin Guard Roles**

#### **super_admin** (17 permissions)
- **Purpose**: Full system access
- **Permissions**: All admin permissions
- **Use Case**: System administrators, founders

#### **admin** (8 permissions)
- **Purpose**: Standard administrative access
- **Permissions**: 
  - `admin-users.view`
  - `stores.view`, `stores.update`, `stores.approve`
  - `categories.view`, `categories.create`, `categories.update`
  - `settings.view`
- **Use Case**: Regular administrators

#### **manager** (4 permissions)
- **Purpose**: Limited management access
- **Permissions**:
  - `stores.view`, `stores.update`
  - `categories.view`, `categories.update`
- **Use Case**: Department managers

#### **moderator** (2 permissions)
- **Purpose**: Content moderation only
- **Permissions**:
  - `categories.view`
  - `stores.view`
- **Use Case**: Content moderators

### **Store Guard Roles**

#### **store_owner** (12 permissions)
- **Purpose**: Full store control
- **Permissions**: All store permissions
- **Use Case**: Store owners

#### **store_manager** (7 permissions)
- **Purpose**: Store operations management
- **Permissions**:
  - `products.view`, `products.create`, `products.update`
  - `orders.view`, `orders.update`
  - `staff.view`
  - `store-settings.view`
- **Use Case**: Store managers

#### **store_staff** (3 permissions)
- **Purpose**: Limited store access
- **Permissions**:
  - `products.view`
  - `orders.view`, `orders.update`
- **Use Case**: Store employees

#### **store_viewer** (3 permissions)
- **Purpose**: Read-only access
- **Permissions**:
  - `products.view`
  - `orders.view`
  - `store-settings.view`
- **Use Case**: Analysts, viewers

---

## 🚀 **Usage Examples**

### **Scenario 1: Create a Custom Role for Category Management**

```bash
# 1. First, see available permissions
curl -X GET "http://localhost:8000/api/admin/permissions/categories?guard=admins" \
  -H "Authorization: Bearer YOUR_JWT_TOKEN"

# 2. Create the role
curl -X POST http://localhost:8000/api/admin/roles \
  -H "Authorization: Bearer YOUR_JWT_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "category_manager",
    "guard_name": "admins",
    "permissions": [
      "categories.view",
      "categories.create", 
      "categories.update",
      "categories.delete"
    ]
  }'

# 3. Assign to a user
curl -X POST http://localhost:8000/api/admin/users/3/roles/assign \
  -H "Authorization: Bearer YOUR_JWT_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "guard": "admins",
    "role": "category_manager"
  }'
```

### **Scenario 2: Check User Permissions**

```bash
# Get user's current roles and permissions
curl -X GET "http://localhost:8000/api/admin/users/2/roles?guard=admins" \
  -H "Authorization: Bearer YOUR_JWT_TOKEN"
```

### **Scenario 3: Update Role Permissions**

```bash
# Update an existing role
curl -X PUT http://localhost:8000/api/admin/roles/5 \
  -H "Authorization: Bearer YOUR_JWT_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "updated_category_manager",
    "permissions": [
      "categories.view",
      "categories.create",
      "categories.update"
    ]
  }'
```

### **Scenario 4: Remove All Roles and Assign New Ones**

```bash
# Sync roles (replace all existing roles)
curl -X POST http://localhost:8000/api/admin/users/3/roles/sync \
  -H "Authorization: Bearer YOUR_JWT_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "guard": "admins",
    "roles": ["manager"]
  }'
```

---

## ❌ **Error Handling**

### **Common Error Responses**

#### **Insufficient Permissions (403)**
```json
{
    "success": false,
    "message": "You don't have permission to perform this action"
}
```

#### **Insufficient Role (403)**
```json
{
    "success": false,
    "message": "Your role doesn't allow this action"
}
```

#### **User Not Found (404)**
```json
{
    "success": false,
    "message": "User not found"
}
```

#### **Role Not Found (404)**
```json
{
    "success": false,
    "message": "Role not found"
}
```

#### **Cannot Delete Role (422)**
```json
{
    "success": false,
    "message": "Cannot delete role that is assigned to users"
}
```

#### **Validation Failed (422)**
```json
{
    "success": false,
    "message": "The name field is required.",
    "data": null,
    "errors": {
        "name": ["The name field is required."],
        "guard_name": ["The selected guard name is invalid."]
    }
}
```

#### **Unauthenticated (401)**
```json
{
    "success": false,
    "message": "Unauthenticated. Please login first"
}
```

---

## 🌍 **Bilingual Support**

All API responses support both English and Arabic based on the `Accept-Language` header:

### **English Responses**
```http
Accept-Language: en
```

```json
{
    "success": true,
    "message": "Role created successfully"
}
```

### **Arabic Responses**
```http
Accept-Language: ar
```

```json
{
    "success": true,
    "message": "تم إنشاء الدور بنجاح"
}
```

### **Language Header Options**
- `Accept-Language: en` - English responses
- `Accept-Language: ar` - Arabic responses
- `X-Language: en` - Alternative English header
- `X-Language: ar` - Alternative Arabic header

---

## 📊 **Complete API Endpoints Summary**

### **Role Management Endpoints**
```
GET    /api/admin/roles                    # List all roles
POST   /api/admin/roles                    # Create new role
GET    /api/admin/roles/permissions        # Get available permissions
GET    /api/admin/roles/{id}               # Get single role
PUT    /api/admin/roles/{id}               # Update role
DELETE /api/admin/roles/{id}               # Delete role
```

### **Permission Management Endpoints**
```
GET    /api/admin/permissions              # List all permissions
GET    /api/admin/permissions/categories   # Get permissions by category
GET    /api/admin/permissions/{id}         # Get single permission
```

### **User Role Assignment Endpoints**
```
GET    /api/admin/users/{userId}/roles           # Get user roles
POST   /api/admin/users/{userId}/roles/assign    # Assign role to user
POST   /api/admin/users/{userId}/roles/remove    # Remove role from user
POST   /api/admin/users/{userId}/roles/sync      # Sync user roles
```

### **Access Requirements**
- **Role Management**: Requires `super_admin` role
- **Permission Management**: Requires `super_admin` role  
- **User Role Assignment**: Requires `admin-users.roles` permission

---

## 🔧 **Testing the API**

### **1. Login as Super Admin**
```bash
curl -X POST http://localhost:8000/api/admin/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "superadmin@test.com",
    "password": "password123"
  }'
```

### **2. Use the Token**
Copy the `access_token` from the login response and use it in subsequent requests:

```bash
export JWT_TOKEN="your_access_token_here"

curl -X GET http://localhost:8000/api/admin/roles \
  -H "Authorization: Bearer $JWT_TOKEN"
```

### **3. Test Different Endpoints**
Try the various endpoints to see the full functionality in action.

---

**The Makook Roles & Permissions system provides complete control over user access with a clean, RESTful API interface supporting both English and Arabic languages.** 🚀
