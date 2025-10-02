# Store - Many-to-Many Categories Relationship

## Overview
Stores can now belong to **multiple main categories** instead of just one. This allows vendors to categorize their stores in multiple areas (e.g., a store can be both "Groceries" and "Fresh Produce").

---

## Database Structure

### Pivot Table: `main_category_store`
```sql
id                  BIGINT UNSIGNED PRIMARY KEY
store_id            BIGINT UNSIGNED (FK -> stores.id) CASCADE
main_category_id    BIGINT UNSIGNED (FK -> main_categories.id) CASCADE
created_at          TIMESTAMP
updated_at          TIMESTAMP

UNIQUE INDEX: (store_id, main_category_id) - prevents duplicates
INDEX: store_id
INDEX: main_category_id
```

### Updated `stores` Table
```sql
# REMOVED: main_category_id column
# Categories are now stored in the pivot table
```

---

## Model Relationships

### Store Model
```php
// Store belongs to many main categories
public function mainCategories()
{
    return $this->belongsToMany(MainCategory::class, 'main_category_store');
}
```

### MainCategory Model (Optional Enhancement)
```php
// Main category has many stores
public function stores()
{
    return $this->belongsToMany(Store::class, 'main_category_store');
}
```

---

## Vendor Registration Updates

### Request Field Change
**Before:**
```json
{
  "main_category_id": 2
}
```

**After:**
```json
{
  "main_category_ids": [1, 2, 5]
}
```

### Validation Rules
```php
'main_category_ids' => 'required|array|min:1',
'main_category_ids.*' => 'required|integer|exists:main_categories,id'
```

- **Required**: Must provide at least one category
- **Array**: Must be an array of IDs
- **Exists**: Each ID must exist in main_categories table
- **Active Check**: All categories must have status = true

---

## Registration Endpoint

**POST** `/api/vendor/register`

### Request Body (multipart/form-data)

```json
{
  "name": "John Doe",
  "email": "john@example.com",
  "password": "password123",
  "password_confirmation": "password123",
  "phone": "+1234567890",
  "address": "123 Main St",
  
  "main_category_ids": [1, 3, 5],  // Array of category IDs
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

### Success Response (201)
```json
{
  "success": true,
  "message": "Vendor and store registered successfully. Awaiting approval",
  "data": {
    "access_token": "eyJ0eXAiOiJKV1QiLCJhb...",
    "token_type": "bearer",
    "expires_in": 3600,
    "vendor": {
      "id": 1,
      "name": "John Doe",
      "email": "john@example.com",
      "store": {
        "id": 1,
        "vendor_id": 1,
        "name_en": "Fresh Market",
        "name_ar": "سوق الطازج",
        "status": false,
        "main_categories": [
          {
            "id": 1,
            "name_en": "Groceries",
            "name_ar": "بقالة",
            "status": true
          },
          {
            "id": 3,
            "name_en": "Fresh Produce",
            "name_ar": "منتجات طازجة",
            "status": true
          },
          {
            "id": 5,
            "name_en": "Dairy",
            "name_ar": "ألبان",
            "status": true
          }
        ]
      }
    },
    "store": { ... }
  }
}
```

---

## Validation Examples

### Valid Requests

**Single Category:**
```json
{
  "main_category_ids": [2]
}
```

**Multiple Categories:**
```json
{
  "main_category_ids": [1, 2, 3, 5, 8]
}
```

### Invalid Requests

**Empty Array (422):**
```json
{
  "main_category_ids": []
}
// Error: "The main categories must have at least 1 items."
```

**Not an Array (422):**
```json
{
  "main_category_ids": 2
}
// Error: "The main categories must be an array."
```

**Invalid Category ID (422):**
```json
{
  "main_category_ids": [1, 999]
}
// Error: "The selected main categories.1 is invalid."
```

**Inactive Category (404):**
```json
{
  "main_category_ids": [1, 5]
}
// Error: "Main category not found" (if category 5 has status = false)
```

---

## CRUD Operations

### Attach Categories (During Registration)
```php
$store->mainCategories()->attach([1, 2, 5]);
```

### Sync Categories (Update)
```php
$store->mainCategories()->sync([2, 3, 7]); // Removes old, adds new
```

### Add Category
```php
$store->mainCategories()->attach(4); // Add category ID 4
```

### Remove Category
```php
$store->mainCategories()->detach(2); // Remove category ID 2
```

### Get Store Categories
```php
$store->mainCategories; // Collection of MainCategory models
```

### Check if Store has Category
```php
$store->mainCategories()->where('main_category_id', 3)->exists();
```

---

## Query Examples

### Get All Stores in a Category
```php
$stores = MainCategory::find(1)->stores;
```

### Get Stores with Multiple Categories
```php
$stores = Store::whereHas('mainCategories', function($query) {
    $query->whereIn('main_category_id', [1, 2]);
})->get();
```

### Eager Load Categories
```php
$vendor = Vendor::with('store.mainCategories')->find(1);
```

---

## Migration Commands

```bash
# Run the new migrations
php artisan migrate

# This will:
# 1. Create main_category_store pivot table
# 2. Remove main_category_id from stores table
```

**Important:** If you have existing data, you need to migrate it before running these migrations:

```php
// Create a data migration to move existing relationships
DB::table('stores')->get()->each(function($store) {
    if ($store->main_category_id) {
        DB::table('main_category_store')->insert([
            'store_id' => $store->id,
            'main_category_id' => $store->main_category_id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
});
```

---

## Testing with cURL

```bash
curl -X POST http://yourdomain.com/api/vendor/register \
  -F "name=John Doe" \
  -F "email=john@example.com" \
  -F "password=password123" \
  -F "password_confirmation=password123" \
  -F "phone=+1234567890" \
  -F "address=123 Main St" \
  -F "main_category_ids[]=1" \
  -F "main_category_ids[]=3" \
  -F "main_category_ids[]=5" \
  -F "name_en=Fresh Market" \
  -F "name_ar=سوق الطازج" \
  -F "description_en=Fresh groceries" \
  -F "description_ar=بقالة طازجة" \
  -F "store_address=456 Store Ave" \
  -F "latitude=40.7128" \
  -F "longitude=-74.0060" \
  -F "logo=@/path/to/logo.jpg" \
  -F "document=@/path/to/document.pdf"
```

---

## Benefits of Many-to-Many

✅ **Flexibility**: Stores can belong to multiple categories
✅ **Better Discovery**: Users can find stores through multiple category filters
✅ **Cross-Category Sales**: Vendors can reach more customers
✅ **Accurate Classification**: E.g., "Groceries + Organic + Local"
✅ **Advanced Filtering**: Search stores by category combinations

---

## Login & Profile Responses

All endpoints now return `mainCategories` (plural) instead of `mainCategory`:

### Login Response
```json
{
  "vendor": {
    "id": 1,
    "store": {
      "id": 1,
      "main_categories": [ ... ]
    }
  }
}
```

### Profile Response
```json
{
  "id": 1,
  "store": {
    "id": 1,
    "main_categories": [ ... ]
  }
}
```

---

## Summary of Changes

| Component | Before | After |
|-----------|--------|-------|
| **Field Name** | `main_category_id` | `main_category_ids` |
| **Data Type** | Integer | Array of Integers |
| **Validation** | `required\|integer\|exists` | `required\|array\|min:1` |
| **Relationship** | `belongsTo` | `belongsToMany` |
| **Model Method** | `mainCategory()` | `mainCategories()` |
| **Table Column** | `stores.main_category_id` | Pivot table |
| **Min Categories** | 1 (implicit) | 1 (explicit) |
| **Max Categories** | 1 | Unlimited |

---

## Next Steps

1. ✅ Run migrations
2. ✅ Test registration with multiple categories
3. ✅ Update any admin panels to support multi-select
4. ✅ Update store search/filtering logic
5. ✅ Consider adding category weights/priorities if needed
