# Makook — Roles & Permissions Reference

> **For frontend developers** — Use this document to implement role-based access control (RBAC) in the admin dashboard.

---

## API Endpoints

| Action | Method | Endpoint |
|--------|--------|----------|
| Get all permissions | `GET` | `/api/admin/roles/permissions?guard=admins` |
| Get permissions by category | `GET` | `/api/admin/permissions/categories?guard=admins` |
| List all roles | `GET` | `/api/admin/roles?guard=admins` |
| Get roles (dropdown) | `GET` | `/api/admin/roles/list?guard=admins` |
| Get single role | `GET` | `/api/admin/roles/{id}` |
| Create role | `POST` | `/api/admin/roles` |
| Update role | `PUT` | `/api/admin/roles/{id}` |
| Delete role | `DELETE` | `/api/admin/roles/{id}` |
| Get user roles | `GET` | `/api/admin/users/{userId}/roles?guard=admins` |
| Assign role | `POST` | `/api/admin/users/{userId}/roles/assign` |
| Remove role | `POST` | `/api/admin/users/{userId}/roles/remove` |
| Sync roles | `POST` | `/api/admin/users/{userId}/roles/sync` |

---

## Admin Roles

| Role | Guard | Description |
|------|-------|-------------|
| `super_admin` | `admins` | Full access — has **all** permissions |
| `admin` | `admins` | Limited admin — view users, manage stores & categories, view settings |
| `manager` | `admins` | Stores & categories management |
| `moderator` | `admins` | View-only for stores & categories |

---

## Admin Permissions (47 total)

Use the permission `name` as the key to check access. Format: `{category}.{action}`

### 1. Admin User Management
| Permission | Description |
|-----------|-------------|
| `admin-users.view` | View admin users list & details |
| `admin-users.create` | Create new admin users |
| `admin-users.update` | Update admin users & toggle status |
| `admin-users.delete` | Delete admin users |
| `admin-users.roles` | Assign/remove/sync roles to admin users |

### 2. User Management (Customers)
| Permission | Description |
|-----------|-------------|
| `users.view` | View users list & details |
| `users.create` | Create new users |
| `users.update` | Update users & toggle status |
| `users.delete` | Delete users |

### 3. Delivery User Management
| Permission | Description |
|-----------|-------------|
| `delivery-users.view` | View delivery users list & details |
| `delivery-users.create` | Create delivery users |
| `delivery-users.update` | Update delivery users & toggle status |
| `delivery-users.delete` | Delete delivery users |

### 4. Store Management
| Permission | Description |
|-----------|-------------|
| `stores.view` | View stores, branches, products, categories, options, addons |
| `stores.create` | Create stores, branches, products, categories, options, addons |
| `stores.update` | Update stores, branches, products, categories, options, addons |
| `stores.delete` | Delete stores, branches, products, categories, options, addons |
| `stores.approve` | Approve, reject, suspend, reactivate stores |

### 5. Category & Module Management
| Permission | Description |
|-----------|-------------|
| `categories.view` | View modules, categories, module ads |
| `categories.create` | Create modules, categories, module ads |
| `categories.update` | Update modules, categories, module ads, toggle status |
| `categories.delete` | Delete modules, categories, module ads |

### 6. Order Management
| Permission | Description |
|-----------|-------------|
| `orders.view` | View orders list, details, statistics |
| `orders.create` | Create orders |
| `orders.update` | Update orders, mark ready, mark delivered |
| `orders.delete` | Cancel orders |

### 7. Reports
| Permission | Description |
|-----------|-------------|
| `reports.view` | View all report endpoints (revenue, sales, products, users, delivery, payments) |

### 8. Dashboard
| Permission | Description |
|-----------|-------------|
| `dashboard.view` | View dashboard stats, charts, recent orders |

### 9. Activity Logs
| Permission | Description |
|-----------|-------------|
| `activity-logs.view` | View activity logs, stats, filter by model/user |
| `activity-logs.delete` | Cleanup old activity logs |

### 10. Vendor Invoices
| Permission | Description |
|-----------|-------------|
| `vendor-invoices.view` | View vendor invoices list & details |
| `vendor-invoices.update` | Mark vendor invoices as paid |

### 11. Voucher Management
| Permission | Description |
|-----------|-------------|
| `vouchers.view` | View vouchers list & details |
| `vouchers.create` | Create vouchers |
| `vouchers.update` | Update vouchers |
| `vouchers.delete` | Delete vouchers |

### 12. Delivery Invoices
| Permission | Description |
|-----------|-------------|
| `delivery-invoices.view` | View delivery invoices list & details |
| `delivery-invoices.update` | Mark delivery invoices as paid |

### 13. App Settings
| Permission | Description |
|-----------|-------------|
| `app-settings.view` | View working hours, app version |
| `app-settings.update` | Update working hours, app version |

### 14. Home Ads
| Permission | Description |
|-----------|-------------|
| `home-ads.view` | View home ads list & details |
| `home-ads.create` | Create home ads |
| `home-ads.update` | Update home ads, toggle status |
| `home-ads.delete` | Delete home ads |

### 15. Roles & Permissions
| Permission | Description |
|-----------|-------------|
| `roles.view` | View roles & permissions |
| `roles.create` | Create new roles |
| `roles.update` | Update roles & their permissions |
| `roles.delete` | Delete roles |

### 16. Notification Management
| Permission | Description |
|-----------|-------------|
| `notifications.view` | View notification history |
| `notifications.create` | Send notifications |

### 17. System Settings
| Permission | Description |
|-----------|-------------|
| `settings.view` | View system settings |
| `settings.update` | Update system settings |
| `system.maintenance` | System maintenance operations |

---

## Vendor Roles

| Role | Guard | Description |
|------|-------|-------------|
| `vendor_owner` | `vendors` | Full vendor access — all vendor permissions |
| `vendor_manager` | `vendors` | Manage products & orders, view staff & settings |
| `vendor_staff` | `vendors` | View products, view & update orders |
| `vendor_viewer` | `vendors` | View-only for products, orders, settings |

---

## Vendor Permissions (13 total)

### Product Management
| Permission | Description |
|-----------|-------------|
| `products.view` | View products |
| `products.create` | Create products |
| `products.update` | Update products |
| `products.delete` | Delete products |

### Order Management
| Permission | Description |
|-----------|-------------|
| `orders.view` | View orders |
| `orders.update` | Update orders |
| `orders.cancel` | Cancel orders |

### Staff Management
| Permission | Description |
|-----------|-------------|
| `staff.view` | View staff |
| `staff.create` | Create staff |
| `staff.update` | Update staff |
| `staff.delete` | Delete staff |

### Vendor Settings
| Permission | Description |
|-----------|-------------|
| `vendor-settings.view` | View vendor settings |
| `vendor-settings.update` | Update vendor settings |

---

## Frontend Implementation Guide

### How to Check Permissions

After admin login, call `GET /api/admin/users/{userId}/roles?guard=admins` to get the user's permissions. The response includes:

```json
{
  "success": true,
  "data": {
    "user": { ... },
    "roles": [{ "name": "admin", ... }],
    "permissions": [
      { "name": "users.view" },
      { "name": "users.create" },
      { "name": "stores.view" }
    ]
  }
}
```

### Sidebar Visibility Rules

| Sidebar Item | Show if user has permission |
|-------------|---------------------------|
| Dashboard | `dashboard.view` |
| Orders | `orders.view` |
| Users | `users.view` |
| Delivery Users | `delivery-users.view` |
| Admin Users | `admin-users.view` |
| Stores | `stores.view` |
| Categories | `categories.view` |
| Modules | `categories.view` |
| Vouchers | `vouchers.view` |
| Reports | `reports.view` |
| Notifications | `notifications.view` |
| Activity Logs | `activity-logs.view` |
| Vendor Invoices | `vendor-invoices.view` |
| Delivery Invoices | `delivery-invoices.view` |
| Home Ads | `home-ads.view` |
| App Settings | `app-settings.view` |
| Roles & Permissions | `roles.view` |

### Button Visibility Rules

| Button / Action | Show if user has permission |
|----------------|---------------------------|
| "Add" / "Create" button | `{category}.create` |
| "Edit" / "Update" button | `{category}.update` |
| "Delete" button | `{category}.delete` |
| "Toggle Status" switch | `{category}.update` |
| "Approve/Reject" store | `stores.approve` |
| "Mark as Paid" invoice | `{category}.update` |
| "Send Notification" | `notifications.create` |
| "Assign Role" | `admin-users.roles` |

### Create / Update Role Request

```
POST /api/admin/roles
PUT  /api/admin/roles/{id}
```

```json
{
  "name": "custom_role",
  "guard_name": "admins",
  "permissions": [
    "users.view",
    "users.create",
    "orders.view",
    "reports.view"
  ]
}
```
