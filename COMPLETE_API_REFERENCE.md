# 📮 Complete API Reference - ALL Endpoints with Form-Data

**Total Endpoints:** 47  
**Format:** Form-Data (multipart/form-data)  
**Boolean Values:** 1 = true, 0 = false

---

## 1. Authentication (1 endpoint)

### Login
```http
POST /api/admin/login
Content-Type: multipart/form-data

email: superadmin@test.com
password: password
```

---

## 2. Option Groups (8 endpoints)

### Get All
```http
GET /api/admin/option-groups?type=size&is_active=1
```

### Get by ID
```http
GET /api/admin/option-groups/{id}
```

### Create
```http
POST /api/admin/option-groups
Content-Type: multipart/form-data

name_en: Size
name_ar: الحجم
type: size
is_active: 1
```

### Update
```http
PUT /api/admin/option-groups/{id}
Content-Type: multipart/form-data

name_en: Size Updated
name_ar: الحجم المحدث
is_active: 1
```

### Toggle Status
```http
PATCH /api/admin/option-groups/{id}/toggle-status
```

### Get Types
```http
GET /api/admin/option-groups/types
```

### Delete
```http
DELETE /api/admin/option-groups/{id}
```

---

## 3. Option Values (8 endpoints)

### Get by Group
```http
GET /api/admin/option-values/group/{groupId}?is_active=1
```

### Get by ID
```http
GET /api/admin/option-values/{id}
```

### Create
```http
POST /api/admin/option-values
Content-Type: multipart/form-data

option_group_id: 1
value_en: Small
value_ar: صغير
sort_order: 1
is_active: 1
```

### Update
```http
PUT /api/admin/option-values/{id}
Content-Type: multipart/form-data

value_en: Extra Large
value_ar: كبير جداً
sort_order: 4
```

### Reorder (JSON only)
```http
POST /api/admin/option-values/reorder
Content-Type: application/json

{
  "values": [
    {"id": 1, "sort_order": 0},
    {"id": 2, "sort_order": 1}
  ]
}
```

### Delete
```http
DELETE /api/admin/option-values/{id}
```

---

## 4. Addons (8 endpoints)

### Get All
```http
GET /api/admin/addons?store_id=1&is_active=1&addon_category=extras
```

### Get by ID
```http
GET /api/admin/addons/{id}
```

### Create
```http
POST /api/admin/addons
Content-Type: multipart/form-data

store_id: 1
name_en: Extra Cheese
name_ar: جبن إضافي
description_en: Add extra cheese
description_ar: أضف جبن إضافي
price: 15.00
addon_category: extras
is_active: 1
```

### Update
```http
PUT /api/admin/addons/{id}
Content-Type: multipart/form-data

name_en: Double Cheese
name_ar: جبن مزدوج
price: 20.00
is_active: 1
```

### Toggle Status
```http
PATCH /api/admin/addons/{id}/toggle-status
```

### Get Categories
```http
GET /api/admin/addons/categories/{storeId}
```

### Delete
```http
DELETE /api/admin/addons/{id}
```

---

## 5. Products (13 endpoints)

### Get All
```http
GET /api/admin/products?store_id=1&category_id=1&is_active=1&search=pizza
```

### Get by ID
```http
GET /api/admin/products/{id}
```

### Create Product - Complete Configuration
```http
POST /api/admin/products
Content-Type: multipart/form-data

store_id: 1
category_id: 1
name_en: Supreme Pizza
name_ar: بيتزا سوبريم
description_en: Pizza with everything
description_ar: بيتزا بكل شيء
base_price: 120.00
search_keywords: pizza italian supreme
is_active: 1
sort_order: 1

# Size option group
option_groups[0][option_group_id]: 1
option_groups[0][is_required]: 1
option_groups[0][sort_order]: 1

# Small size
option_groups[0][option_values][0][option_value_id]: 1
option_groups[0][option_values][0][price_type]: additional
option_groups[0][option_values][0][price_value]: 0
option_groups[0][option_values][0][stock_quantity]: 100
option_groups[0][option_values][0][is_available]: 1

# Medium size
option_groups[0][option_values][1][option_value_id]: 2
option_groups[0][option_values][1][price_type]: additional
option_groups[0][option_values][1][price_value]: 30
option_groups[0][option_values][1][stock_quantity]: 80
option_groups[0][option_values][1][is_available]: 1

# Large size
option_groups[0][option_values][2][option_value_id]: 3
option_groups[0][option_values][2][price_type]: additional
option_groups[0][option_values][2][price_value]: 50
option_groups[0][option_values][2][stock_quantity]: 60

# Crust option group
option_groups[1][option_group_id]: 2
option_groups[1][is_required]: 0
option_groups[1][sort_order]: 2

# Thin crust
option_groups[1][option_values][0][option_value_id]: 4
option_groups[1][option_values][0][price_type]: additional
option_groups[1][option_values][0][price_value]: 0
option_groups[1][option_values][0][stock_quantity]: 100

# Thick crust
option_groups[1][option_values][1][option_value_id]: 5
option_groups[1][option_values][1][price_type]: additional
option_groups[1][option_values][1][price_value]: 15
option_groups[1][option_values][1][stock_quantity]: 100

# Addons
addon_ids[]: 1
addon_ids[]: 2
addon_ids[]: 3

# Images
images[]: [file1.jpg]
images[]: [file2.jpg]
```

### Create Product - Simple with Addons
```http
POST /api/admin/products
Content-Type: multipart/form-data

store_id: 1
category_id: 1
name_en: Classic Burger
name_ar: برجر كلاسيك
description_en: Delicious beef burger
base_price: 75.00
is_active: 1
addon_ids[]: 1
addon_ids[]: 2
```

### Create Product - Fixed Price Options
```http
POST /api/admin/products
Content-Type: multipart/form-data

store_id: 1
name_en: Specialty Pizza
base_price: 100.00
is_active: 1
option_groups[0][option_group_id]: 1
option_groups[0][is_required]: 1
option_groups[0][option_values][0][option_value_id]: 1
option_groups[0][option_values][0][price_type]: fixed
option_groups[0][option_values][0][price_value]: 100
option_groups[0][option_values][0][stock_quantity]: 50
option_groups[0][option_values][1][option_value_id]: 2
option_groups[0][option_values][1][price_type]: fixed
option_groups[0][option_values][1][price_value]: 130
addon_ids[]: 1
addon_ids[]: 2
```

### Update Product
```http
PUT /api/admin/products/{id}
Content-Type: multipart/form-data

name_en: Updated Pizza Name
base_price: 150.00
is_active: 1
option_groups[0][option_group_id]: 1
option_groups[0][is_required]: 1
option_groups[0][option_values][0][option_value_id]: 1
option_groups[0][option_values][0][price_type]: additional
option_groups[0][option_values][0][price_value]: 0
option_groups[0][option_values][0][stock_quantity]: 150
addon_ids[]: 1
addon_ids[]: 2
addon_ids[]: 3
addon_ids[]: 4
```

### Toggle Status
```http
PATCH /api/admin/products/{id}/toggle-status
```

### Duplicate Product
```http
POST /api/admin/products/{id}/duplicate
```

### Delete Product
```http
DELETE /api/admin/products/{id}
```

### Upload Images
```http
POST /api/admin/products/{id}/images
Content-Type: multipart/form-data

images[]: [file1.jpg]
images[]: [file2.jpg]
images[]: [file3.jpg]
```

### Set Primary Image
```http
PATCH /api/admin/products/images/{imageId}/set-primary
```

### Reorder Images (JSON only)
```http
POST /api/admin/products/images/reorder
Content-Type: application/json

{
  "images": [
    {"id": 1, "sort_order": 0},
    {"id": 2, "sort_order": 1}
  ]
}
```

### Delete Image
```http
DELETE /api/admin/products/images/{imageId}
```

---

## 6. Product Options - Old Method (9 endpoints)

### Get Product Options
```http
GET /api/admin/product-options/product/{productId}
```

### Assign Option Group
```http
POST /api/admin/product-options/assign-group
Content-Type: multipart/form-data

product_id: 1
option_group_id: 1
is_required: 1
sort_order: 1
```

### Update Product Option
```http
PUT /api/admin/product-options/{id}
Content-Type: multipart/form-data

is_required: 0
sort_order: 2
```

### Delete Product Option
```http
DELETE /api/admin/product-options/{id}
```

### Assign Option Values
```http
POST /api/admin/product-options/assign-values
Content-Type: application/json

{
  "product_option_id": 1,
  "option_values": [
    {
      "option_value_id": 1,
      "price_type": "additional",
      "price_value": 0,
      "stock_quantity": 100,
      "is_available": true
    }
  ]
}
```

### Update Option Value Pricing
```http
PUT /api/admin/product-options/values/{id}
Content-Type: multipart/form-data

price_type: additional
price_value: 35
stock_quantity: 120
is_available: 1
```

### Update Stock
```http
PATCH /api/admin/product-options/values/{id}/stock
Content-Type: multipart/form-data

stock_quantity: 150
is_available: 1
```

### Remove Option Value
```http
DELETE /api/admin/product-options/values/{id}
```

### Reorder Product Options (JSON only)
```http
POST /api/admin/product-options/reorder
Content-Type: application/json

{
  "options": [
    {"id": 1, "sort_order": 0},
    {"id": 2, "sort_order": 1}
  ]
}
```

---

## 7. Product Addons (5 endpoints)

### Get Product Addons
```http
GET /api/admin/product-addons/product/{productId}
```

### Assign Addons
```http
POST /api/admin/product-addons/assign
Content-Type: application/json

{
  "product_id": 1,
  "addon_ids": [1, 2, 3]
}
```

### Update Product Addon
```http
PUT /api/admin/product-addons/product/{productId}/addon/{addonId}
Content-Type: multipart/form-data

sort_order: 2
```

### Remove Addon
```http
DELETE /api/admin/product-addons/product/{productId}/addon/{addonId}
```

### Reorder Addons (JSON only)
```http
POST /api/admin/product-addons/reorder
Content-Type: application/json

{
  "product_id": 1,
  "addons": [
    {"addon_id": 1, "sort_order": 0},
    {"addon_id": 2, "sort_order": 1}
  ]
}
```

---

## 📊 Summary Table

| Category | Endpoints | Form-Data | JSON | GET/PATCH/DELETE |
|----------|-----------|-----------|------|------------------|
| Authentication | 1 | ✅ | | |
| Option Groups | 8 | ✅ | | ✅ |
| Option Values | 8 | ✅ | ✅ (reorder) | ✅ |
| Addons | 8 | ✅ | | ✅ |
| Products | 13 | ✅ | ✅ (reorder) | ✅ |
| Product Options | 9 | ✅ | ✅ (assign values) | ✅ |
| Product Addons | 5 | ✅ | ✅ (assign/reorder) | ✅ |
| **TOTAL** | **47** | **45** | **2** | **All** |

---

## ⚠️ Important Notes

### Boolean Values
```
✅ Use: 1 for true, 0 for false
❌ Don't use: true, false, "true", "false"
```

### Array Notation
```
✅ addon_ids[]: 1
✅ addon_ids[]: 2
✅ option_groups[0][option_group_id]: 1
✅ option_groups[0][option_values][0][option_value_id]: 1
```

### JSON-Only Endpoints
These 5 endpoints MUST use JSON:
1. POST /option-values/reorder
2. POST /product-options/assign-values
3. POST /product-options/reorder
4. POST /products/images/reorder
5. POST /product-addons/assign
6. POST /product-addons/reorder

All other 42 endpoints use form-data!

---

## 🎯 Quick Postman Setup

**In Postman Body tab:**
1. Select **form-data** (not raw!)
2. Add keys with values
3. For booleans: use `1` or `0`
4. For arrays: add multiple rows with same key `addon_ids[]`
5. For files: select "File" type

---

**All 47 endpoints documented with form-data format!** 🎉
