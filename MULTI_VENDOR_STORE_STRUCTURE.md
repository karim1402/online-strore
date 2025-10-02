# Multi-Vendor Store Structure

## Overview
The system now supports **multiple vendors per store**. The relationship has been reversed so that vendors belong to stores, not the other way around. This enables future features like:
- Multiple staff members per store
- Store managers and assistants
- Team collaboration
- Store ownership transfers

---

## Relationship Structure Change

### ❌ Before (One-to-One)
```
Vendor (1) ──has one──> Store (1)
Store has vendor_id column
```

### ✅ After (One-to-Many)
```
Store (1) ──has many──> Vendor (Many)
Vendor has store_id column
```

---

## Database Schema

### vendors Table (Updated)
```sql
id                  BIGINT UNSIGNED PRIMARY KEY
store_id            BIGINT UNSIGNED (FK -> stores.id) CASCADE - NEW!
name                VARCHAR(255) NOT NULL
email               VARCHAR(255) UNIQUE NOT NULL
password            VARCHAR(255) NOT NULL
phone               VARCHAR(255) NULLABLE
address             TEXT NULLABLE
status              BOOLEAN DEFAULT true
created_at          TIMESTAMP
updated_at          TIMESTAMP

INDEX: store_id
```

### stores Table (Updated)
```sql
id                  BIGINT UNSIGNED PRIMARY KEY
# REMOVED: vendor_id column
name_en             VARCHAR(255) NOT NULL
name_ar             VARCHAR(255) NOT NULL
description_en      TEXT NOT NULL
description_ar      TEXT NOT NULL
address             TEXT NOT NULL
latitude            DECIMAL(10,7) NOT NULL
longitude           DECIMAL(10,7) NOT NULL
logo                VARCHAR(255) NOT NULL
document            VARCHAR(255) NOT NULL
status              BOOLEAN DEFAULT false
created_at          TIMESTAMP
updated_at          TIMESTAMP

INDEX: status
```

---

## Model Relationships

### Vendor Model
```php
/**
 * Vendor belongs to Store
 */
public function store()
{
    return $this->belongsTo(Store::class);
}
```

### Store Model
```php
/**
 * Store has many Vendors
 */
public function vendors()
{
    return $this->hasMany(Vendor::class);
}

/**
 * Store belongs to many MainCategories
 */
public function mainCategories()
{
    return $this->belongsToMany(MainCategory::class, 'main_category_store');
}
```

---

## Registration Flow (Updated)

### Process Order Changed

**Before:**
1. Create Vendor
2. Create Store with vendor_id
3. Attach Categories

**After:**
1. Upload Files (logo, document)
2. **Create Store** (no vendor_id)
3. Attach Categories to Store
4. **Create Vendor** with store_id
5. Login Vendor

---

## Registration Endpoint

**POST** `/api/vendor/register`

### Request (No Changes)
```json
{
  "name": "John Doe",
  "email": "john@example.com",
  "password": "password123",
  "phone": "+1234567890",
  "address": "123 Main St",
  
  "main_category_ids": [1, 3, 5],
  "name_en": "Fresh Market",
  "name_ar": "سوق الطازج",
  "description_en": "Fresh groceries and produce",
  "description_ar": "بقالة ومنتجات طازجة",
  "store_address": "456 Store Ave",
  "latitude": 40.7128,
  "longitude": -74.0060,
  "logo": [file],
  "document": [file]
}
```

### Response Structure (No Changes)
```json
{
  "success": true,
  "message": "Vendor and store registered successfully. Awaiting approval",
  "data": {
    "access_token": "...",
    "vendor": {
      "id": 1,
      "name": "John Doe",
      "email": "john@example.com",
      "store_id": 1,
      "store": {
        "id": 1,
        "name_en": "Fresh Market",
        "main_categories": [...]
      }
    },
    "store": {...}
  }
}
```

---

## Future: Adding More Vendors to a Store

### Scenario: Store Owner Adds Staff

```php
// Create additional vendor for existing store
$newVendor = Vendor::create([
    'name' => 'Jane Smith',
    'email' => 'jane@example.com',
    'password' => Hash::make('password'),
    'phone' => '+9876543210',
    'address' => '789 Staff Ave',
    'store_id' => 1, // Existing store ID
    'status' => true,
]);
```

### Get All Vendors for a Store
```php
$store = Store::find(1);
$allVendors = $store->vendors; // Collection of Vendor models
```

### Check if Vendor Belongs to Store
```php
$vendor = Vendor::find(1);
if ($vendor->store_id === $storeId) {
    // Vendor belongs to this store
}
```

---

## Query Examples

### Get Store with All Vendors
```php
$store = Store::with('vendors')->find(1);
```

### Get All Stores and Their Vendors
```php
$stores = Store::with('vendors', 'mainCategories')->get();
```

### Count Vendors per Store
```php
$store->vendors()->count();
```

### Get Active Vendors for Store
```php
$activeVendors = $store->vendors()->where('status', true)->get();
```

---

## Migration Order

Run migrations in this order:

```bash
# 1. Create stores table (without vendor_id)
2025_10_01_000001_create_stores_table.php

# 2. Create vendors table (has store_id but nullable for now)
2024_01_01_000001_create_vendors_table.php

# 3. Add store_id to vendors (if not created above)
2025_10_01_000005_add_store_id_to_vendors_table.php

# 4. Remove vendor_id from stores
2025_10_01_000006_remove_vendor_id_from_stores_table.php

# Run all
php artisan migrate
```

**Note:** If you have existing data, create a data migration to copy relationships before removing columns.

---

## Benefits of This Structure

✅ **Multi-Vendor Support** - Multiple users can manage one store
✅ **Role Flexibility** - Can add owner, manager, staff roles later
✅ **Team Collaboration** - Multiple vendors work on same store
✅ **Ownership Transfer** - Easy to transfer store to new owner
✅ **Staff Management** - Add/remove staff without affecting store
✅ **Access Control** - Fine-grained permissions per vendor
✅ **Scalability** - One store can have unlimited vendors

---

## Use Cases

### 1. Store Owner + Assistants
```
Store: Fresh Market
├── Vendor 1 (Owner): john@example.com
├── Vendor 2 (Manager): jane@example.com
└── Vendor 3 (Staff): bob@example.com
```

### 2. Family Business
```
Store: Family Restaurant
├── Vendor 1 (Father): dad@restaurant.com
├── Vendor 2 (Son): son@restaurant.com
└── Vendor 3 (Daughter): daughter@restaurant.com
```

### 3. Franchise Model
```
Store: Coffee Shop Branch A
├── Vendor 1 (Franchisee): owner@branch-a.com
└── Vendor 2 (Employee): staff@branch-a.com
```

---

## Future Enhancements

### 1. Vendor Roles (Using Spatie Permission)
```php
// Define vendor roles within a store
$owner = $vendor->assignRole('store_owner');
$manager = $vendor->assignRole('store_manager');
$staff = $vendor->assignRole('store_staff');
```

### 2. Pivot Table with Metadata
```sql
CREATE TABLE store_vendor (
    id BIGINT UNSIGNED PRIMARY KEY,
    store_id BIGINT UNSIGNED,
    vendor_id BIGINT UNSIGNED,
    role ENUM('owner', 'manager', 'staff'),
    joined_at TIMESTAMP,
    permissions JSON
);
```

### 3. Invitation System
```php
// Owner invites new vendor
StoreInvitation::create([
    'store_id' => 1,
    'email' => 'newstaff@example.com',
    'role' => 'staff',
    'invited_by' => $vendor->id
]);
```

---

## API Endpoints (Future)

### Get Store Vendors
```http
GET /api/vendor/store/vendors
Authorization: Bearer {token}

Response:
{
  "vendors": [
    {
      "id": 1,
      "name": "John Doe",
      "email": "john@example.com",
      "role": "owner"
    },
    {
      "id": 2,
      "name": "Jane Smith",
      "email": "jane@example.com",
      "role": "manager"
    }
  ]
}
```

### Add Vendor to Store
```http
POST /api/vendor/store/vendors
Authorization: Bearer {owner_token}

Body:
{
  "name": "New Staff",
  "email": "staff@example.com",
  "password": "password123",
  "role": "staff"
}
```

---

## Important Notes

1. **Store ID is Required**: Vendors must be assigned to a store
2. **Nullable Store ID**: Initially nullable in migration to allow existing data
3. **One Store Per Vendor**: Current structure is one-to-many (vendor → store)
4. **Future Enhancement**: Can change to many-to-many if vendors need multiple stores
5. **Backward Compatible**: Registration flow remains the same for end-users

---

## Testing

### Test Multi-Vendor Scenario
```php
// Create store
$store = Store::create([...]);

// Create first vendor (owner)
$owner = Vendor::create([
    'name' => 'Owner',
    'email' => 'owner@store.com',
    'store_id' => $store->id,
]);

// Create second vendor (staff)
$staff = Vendor::create([
    'name' => 'Staff',
    'email' => 'staff@store.com',
    'store_id' => $store->id,
]);

// Verify
assert($store->vendors()->count() === 2);
assert($owner->store->id === $store->id);
assert($staff->store->id === $store->id);
```

---

## Summary

| Aspect | Before | After |
|--------|--------|-------|
| **Relationship** | Vendor hasOne Store | Store hasMany Vendors |
| **Foreign Key** | stores.vendor_id | vendors.store_id |
| **Vendors per Store** | 1 | Many (unlimited) |
| **Store Creation** | After vendor | Before vendor |
| **Future-Ready** | No | Yes ✅ |

**The system is now prepared for multi-user store management!** 🎉
