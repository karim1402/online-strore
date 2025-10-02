# Vendor Registration with Store Creation

## Overview
The vendor registration system now creates both a **Vendor** account and an associated **Store** in a single transaction. Store approval is required before vendors can start selling.

---

## Registration Flow

### 1. Vendor Registers with Store Details
**Endpoint**: `POST /api/vendor/register`

**Required Fields:**

#### Vendor Information:
- `name` (string, 2-100 chars) - Vendor's full name
- `email` (string, unique) - Vendor's email
- `password` (string, min 6 chars) - Password
- `password_confirmation` (string) - Must match password
- `phone` (string, max 20 chars) - Vendor's phone
- `address` (string) - Vendor's personal address

#### Store Information:
- `main_category_id` (integer) - Must exist in main_categories table
- `name_en` (string, max 255) - Store name in English
- `name_ar` (string, max 255) - Store name in Arabic
- `description_en` (text) - Store description in English
- `description_ar` (text) - Store description in Arabic
- `store_address` (string) - Store physical address
- `latitude` (decimal, -90 to 90) - Store location latitude
- `longitude` (decimal, -180 to 180) - Store location longitude
- `logo` (image file) - Store logo (jpeg, jpg, png, webp, max 2MB)
- `document` (file) - Verification document (pdf, jpeg, jpg, png, max 5MB)

### 2. System Process

```
1. Validates all vendor and store data
2. Checks if main_category exists and is active
3. Begins database transaction
4. Creates vendor account (status: active)
5. Uploads logo to storage/app/public/stores/logos/
6. Uploads document to storage/app/public/stores/documents/
7. Creates store record (status: pending = false)
8. Commits transaction
9. Auto-login vendor with JWT token
10. Returns vendor + store data
```

### 3. Response Structure

**Success (201):**
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
      "phone": "+1234567890",
      "address": "123 Main St",
      "status": true,
      "created_at": "2025-10-01T11:00:00.000000Z",
      "store": {
        "id": 1,
        "vendor_id": 1,
        "main_category_id": 2,
        "name_en": "Fresh Market",
        "name_ar": "سوق الطازج",
        "description_en": "Fresh groceries daily",
        "description_ar": "بقالة طازجة يوميا",
        "address": "456 Store Ave",
        "latitude": 40.7128,
        "longitude": -74.0060,
        "logo": "stores/logos/abc123.jpg",
        "document": "stores/documents/xyz789.pdf",
        "logo_url": "http://yourdomain.com/storage/stores/logos/abc123.jpg",
        "document_url": "http://yourdomain.com/storage/stores/documents/xyz789.pdf",
        "status": false,
        "created_at": "2025-10-01T11:00:00.000000Z",
        "main_category": {
          "id": 2,
          "name_en": "Groceries",
          "name_ar": "بقالة",
          "status": true
        }
      }
    },
    "store": { ... }
  }
}
```

**Validation Error (422):**
```json
{
  "success": false,
  "message": "The logo field is required.",
  "data": null,
  "errors": {
    "logo": ["The logo field is required."],
    "latitude": ["The latitude must be between -90 and 90."]
  }
}
```

**Category Not Found (404):**
```json
{
  "success": false,
  "message": "Main category not found",
  "data": null,
  "error": "category_not_found"
}
```

---

## Database Schema

### stores Table
```sql
id                  BIGINT UNSIGNED PRIMARY KEY
vendor_id           BIGINT UNSIGNED (FK -> vendors.id) CASCADE
main_category_id    BIGINT UNSIGNED (FK -> main_categories.id) RESTRICT
name_en             VARCHAR(255) NOT NULL
name_ar             VARCHAR(255) NOT NULL
description_en      TEXT NOT NULL
description_ar      TEXT NOT NULL
address             TEXT NOT NULL
latitude            DECIMAL(10,7) NOT NULL
longitude           DECIMAL(10,7) NOT NULL
logo                VARCHAR(255) NOT NULL
document            VARCHAR(255) NOT NULL
status              BOOLEAN DEFAULT false (pending approval)
created_at          TIMESTAMP
updated_at          TIMESTAMP

Indexes: vendor_id, main_category_id, status
```

### vendors Table (Updated)
```sql
id                  BIGINT UNSIGNED PRIMARY KEY
name                VARCHAR(255) NOT NULL
email               VARCHAR(255) UNIQUE NOT NULL
password            VARCHAR(255) NOT NULL
phone               VARCHAR(255) NULLABLE
address             TEXT NULLABLE
status              BOOLEAN DEFAULT true
created_at          TIMESTAMP
updated_at          TIMESTAMP

REMOVED: store_name column (now in stores table)
```

---

## Model Relationships

### Vendor Model
```php
// One vendor has one store
public function store()
{
    return $this->hasOne(Store::class);
}
```

### Store Model
```php
// Store belongs to vendor
public function vendor()
{
    return $this->belongsTo(Vendor::class);
}

// Store belongs to main category
public function mainCategory()
{
    return $this->belongsTo(MainCategory::class);
}
```

---

## Login Response

**Endpoint**: `POST /api/vendor/login`

**Response includes store data:**
```json
{
  "success": true,
  "message": "Vendor login successful",
  "data": {
    "access_token": "...",
    "token_type": "bearer",
    "expires_in": 3600,
    "vendor": {
      "id": 1,
      "name": "John Doe",
      "email": "john@example.com",
      "store": { ... }
    }
  }
}
```

---

## Profile Endpoint

**Endpoint**: `GET /api/vendor/profile`

**Response includes store relationship:**
```json
{
  "success": true,
  "message": "Profile fetched successfully",
  "data": {
    "id": 1,
    "name": "John Doe",
    "email": "john@example.com",
    "store": {
      "id": 1,
      "name_en": "Fresh Market",
      "name_ar": "سوق الطازج",
      "status": false,
      "main_category": { ... }
    }
  }
}
```

---

## Key Features

### ✅ Transaction Safety
- Vendor and Store creation wrapped in database transaction
- If store creation fails, vendor creation is rolled back
- Uploaded files are cleaned up on failure

### ✅ File Upload Handling
- Logo stored in `storage/app/public/stores/logos/`
- Document stored in `storage/app/public/stores/documents/`
- Files are accessible via public URLs
- Automatic cleanup on transaction failure

### ✅ Store Approval System
- All stores start with `status = false` (pending)
- Admin must approve stores before they go live
- Vendors can login but cannot sell until approved

### ✅ Localization Support
- Store names and descriptions in English and Arabic
- Automatic language detection based on `Accept-Language` header
- Dynamic accessor returns correct language based on locale

### ✅ Location Tracking
- Latitude and longitude stored for mapping
- Validation ensures valid coordinates
- Can be used for proximity searches

### ✅ Category Association
- Each store must belong to a main category
- Category must be active to accept new stores
- Foreign key prevents store creation with invalid category

---

## Migration Commands

```bash
# Run new migrations
php artisan migrate

# This will create:
# - stores table
# - Remove store_name from vendors table
```

---

## File Storage Setup

Ensure the public disk is linked:
```bash
php artisan storage:link
```

This creates a symbolic link from `public/storage` to `storage/app/public`.

---

## Validation Rules Summary

| Field | Type | Rules |
|-------|------|-------|
| name | string | required, 2-100 chars |
| email | string | required, email, max 100, unique |
| password | string | required, min 6, confirmed |
| phone | string | required, max 20 |
| address | string | required |
| main_category_id | integer | required, exists in main_categories |
| name_en | string | required, max 255 |
| name_ar | string | required, max 255 |
| description_en | text | required |
| description_ar | text | required |
| store_address | string | required |
| latitude | numeric | required, between -90 and 90 |
| longitude | numeric | required, between -180 and 180 |
| logo | file | required, image, max 2MB |
| document | file | required, pdf/image, max 5MB |

---

## Testing the Registration

**cURL Example:**
```bash
curl -X POST http://yourdomain.com/api/vendor/register \
  -F "name=John Doe" \
  -F "email=john@example.com" \
  -F "password=password123" \
  -F "password_confirmation=password123" \
  -F "phone=+1234567890" \
  -F "address=123 Main St" \
  -F "main_category_id=2" \
  -F "name_en=Fresh Market" \
  -F "name_ar=سوق الطازج" \
  -F "description_en=Fresh groceries daily" \
  -F "description_ar=بقالة طازجة يوميا" \
  -F "store_address=456 Store Ave" \
  -F "latitude=40.7128" \
  -F "longitude=-74.0060" \
  -F "logo=@/path/to/logo.jpg" \
  -F "document=@/path/to/document.pdf"
```

---

## Admin Store Approval

Admins can approve/reject stores through the admin panel. When a store is approved:
```php
$store->update(['status' => true]);
```

The vendor can then start managing their store and products.
