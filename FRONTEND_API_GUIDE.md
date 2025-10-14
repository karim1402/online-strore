# 🚀 Product System API - Complete Frontend Integration Guide

**Base URL:** `/api/admin`  
**Auth:** Bearer Token  
**Language:** EN/AR

---

## 📑 Quick Navigation
- [Authentication](#authentication)
- [Option Groups](#option-groups) - 7 endpoints
- [Option Values](#option-values) - 6 endpoints  
- [Products](#products) - 13 endpoints
- [Product Options](#product-options) - 9 endpoints
- [Addons](#addons) - 7 endpoints
- [Product Addons](#product-addons) - 5 endpoints
- [Response Format](#response-format)
- [Error Handling](#error-handling)

---

## 🔐 Authentication

**All requests require:**
```http
Authorization: Bearer {jwt-token}
Content-Type: application/json
```

---

## 📦 Option Groups

### GET /option-groups
**Query:** `?type=size&is_active=true&search=text`  
**Returns:** Paginated list of option groups with values

### GET /option-groups/{id}
**Returns:** Single option group with all values

### POST /option-groups
**Permission:** `stores.create`  
**Body:**
```json
{
  "name_en": "Size",
  "name_ar": "الحجم",
  "type": "size",
  "is_active": true
}
```

### PUT /option-groups/{id}
**Permission:** `stores.update`  
**Body:** Same as POST (all fields optional)

### PATCH /option-groups/{id}/toggle-status
**Permission:** `stores.update`  
**Toggles:** is_active boolean

### DELETE /option-groups/{id}
**Permission:** `stores.delete`

### GET /option-groups/types
**Returns:** Array of distinct types

---

## 🔢 Option Values

### GET /option-values/group/{groupId}
**Query:** `?is_active=true`  
**Returns:** All values for specific group

### GET /option-values/{id}
**Returns:** Single option value

### POST /option-values
**Permission:** `stores.create`  
**Body:**
```json
{
  "option_group_id": 1,
  "value_en": "Small",
  "value_ar": "صغير",
  "sort_order": 1,
  "is_active": true
}
```

### PUT /option-values/{id}
**Permission:** `stores.update`  
**Body:** Same as POST (all fields optional)

### DELETE /option-values/{id}
**Permission:** `stores.delete`

### POST /option-values/reorder
**Permission:** `stores.update`  
**Body:**
```json
{
  "values": [
    {"id": 1, "sort_order": 0},
    {"id": 2, "sort_order": 1}
  ]
}
```

---

## 🍕 Products

### GET /products
**Query:** `?store_id=1&category_id=1&is_active=true&search=pizza&sort_by=base_price&sort_order=asc&per_page=15`  
**Returns:** Paginated products with images

### GET /products/{id}
**Returns:** Full product with options, option values, addons  
**Note:** Increments view_count automatically

### POST /products
**Permission:** `stores.create`  
**Content-Type:** `multipart/form-data` or `application/json`  
**Body:**
```json
{
  "store_id": 1,
  "category_id": 1,
  "name_en": "Pizza",
  "name_ar": "بيتزا",
  "description_en": "...",
  "description_ar": "...",
  "base_price": 100.00,
  "is_active": true,
  "search_keywords": "pizza italian",
  "sort_order": 1,
  "metadata": {},
  "option_groups": [
    {
      "option_group_id": 1,
      "is_required": true,
      "sort_order": 1,
      "option_values": [
        {
          "option_value_id": 1,
          "price_type": "additional",
          "price_value": 0,
          "stock_quantity": 100
        },
        {
          "option_value_id": 2,
          "price_type": "additional",
          "price_value": 30,
          "stock_quantity": 80
        }
      ]
    },
    {
      "option_group_id": 2,
      "is_required": false,
      "sort_order": 2,
      "option_values": [
        {
          "option_value_id": 4,
          "price_type": "additional",
          "price_value": 0,
          "stock_quantity": 100
        }
      ]
    }
  ]
}
```
**With images:** Use form-data with `images[]` array  
**NEW!** You can now assign option groups AND option values with pricing in one call!

### PUT /products/{id}
**Permission:** `stores.update`  
**Body:** Same as POST (all fields optional)  
**Note:** When updating `option_groups`, it will replace all existing assignments

### PATCH /products/{id}/toggle-status
**Permission:** `stores.update`

### POST /products/{id}/duplicate
**Permission:** `stores.create`  
**Creates:** Copy with " (Copy)" suffix, inactive, zero counters

### DELETE /products/{id}
**Permission:** `stores.delete`  
**Type:** Soft delete

### POST /products/{id}/images
**Permission:** `stores.update`  
**Content-Type:** `multipart/form-data`  
**Body:** `images[]` array  
**Max:** 2MB per image, jpeg/jpg/png/webp

### DELETE /products/images/{imageId}
**Permission:** `stores.update`  
**Action:** Deletes file + DB record, auto-promotes new primary if needed

### PATCH /products/images/{imageId}/set-primary
**Permission:** `stores.update`

### POST /products/images/reorder
**Permission:** `stores.update`  
**Body:**
```json
{
  "images": [
    {"id": 1, "sort_order": 0},
    {"id": 2, "sort_order": 1}
  ]
}
```

---

## ⚙️ Product Options

### GET /product-options/product/{productId}
**Returns:** All option groups assigned to product with values & pricing

### POST /product-options/assign-group
**Permission:** `stores.create`  
**Body:**
```json
{
  "product_id": 1,
  "option_group_id": 1,
  "is_required": true,
  "sort_order": 1
}
```

### PUT /product-options/{id}
**Permission:** `stores.update`  
**Body:**
```json
{
  "is_required": false,
  "sort_order": 2
}
```

### DELETE /product-options/{id}
**Permission:** `stores.delete`  
**Removes:** Option group + all values from product

### POST /product-options/assign-values
**Permission:** `stores.create`  
**Body:**
```json
{
  "product_option_id": 1,
  "option_values": [
    {
      "option_value_id": 1,
      "price_type": "additional",
      "price_value": 0.00,
      "stock_quantity": 100,
      "is_available": true
    },
    {
      "option_value_id": 2,
      "price_type": "additional",
      "price_value": 30.00,
      "stock_quantity": 80
    }
  ]
}
```

**Price Types:**
- `additional`: Base + value (100 + 30 = 130)
- `fixed`: Replaces base (150)
- `percentage`: Base * (1 + value/100) (100 + 20% = 120)

### PUT /product-options/values/{id}
**Permission:** `stores.update`  
**Body:**
```json
{
  "price_type": "additional",
  "price_value": 35.00,
  "stock_quantity": 75,
  "is_available": true
}
```

### PATCH /product-options/values/{id}/stock
**Permission:** `stores.update`  
**Body:**
```json
{
  "stock_quantity": 150
}
```

### DELETE /product-options/values/{id}
**Permission:** `stores.delete`

---

## 🧀 Addons

### GET /addons
**Query:** `?store_id=1&addon_category=extras&is_active=true&search=cheese`  
**Returns:** Paginated addons

### GET /addons/{id}
**Returns:** Single addon

### POST /addons
**Permission:** `stores.create`  
**Body:**
```json
{
  "store_id": 1,
  "name_en": "Extra Cheese",
  "name_ar": "جبن إضافي",
  "description_en": "...",
  "description_ar": "...",
  "price": 15.00,
  "addon_category": "extras",
  "is_active": true
}
```

### PUT /addons/{id}
**Permission:** `stores.update`  
**Body:** Same as POST (all optional)

### PATCH /addons/{id}/toggle-status
**Permission:** `stores.update`

### DELETE /addons/{id}
**Permission:** `stores.delete`

### GET /addons/categories/{storeId}
**Returns:** Distinct addon categories for store

---

## 🔗 Product Addons

### GET /product-addons/product/{productId}
**Returns:** All addons assigned to product

### POST /product-addons/assign
**Permission:** `stores.create`  
**Body:**
```json
{
  "product_id": 1,
  "addon_ids": [1, 2, 3]
}
```

### PUT /product-addons/product/{productId}/addon/{addonId}
**Permission:** `stores.update`  
**Body:**
```json
{
  "is_available": true,
  "sort_order": 1
}
```

### DELETE /product-addons/product/{productId}/addon/{addonId}
**Permission:** `stores.delete`

### POST /product-addons/reorder
**Permission:** `stores.update`  
**Body:**
```json
{
  "product_id": 1,
  "addons": [
    {"addon_id": 1, "sort_order": 0},
    {"addon_id": 2, "sort_order": 1}
  ]
}
```

---

## ✅ Response Format

### Success
```json
{
  "success": true,
  "message": "Data retrieved successfully",
  "data": { ... }
}
```

### Success (Paginated)
```json
{
  "success": true,
  "message": "...",
  "data": {
    "current_page": 1,
    "data": [ ... ],
    "per_page": 15,
    "total": 50,
    "last_page": 4
  }
}
```

### Validation Error
```json
{
  "success": false,
  "message": "The name_en field is required",
  "errors": {
    "name_en": ["The name_en field is required"]
  },
  "status": 422
}
```

### Error
```json
{
  "success": false,
  "message": "Error message",
  "status": 404
}
```

---

## ⚠️ Error Codes

- `200` - Success
- `201` - Created
- `400` - Bad Request
- `401` - Unauthorized (missing/invalid token)
- `403` - Forbidden (insufficient permissions)
- `404` - Not Found
- `409` - Conflict (duplicate)
- `422` - Validation Error
- `500` - Server Error

---

## 💡 Common Patterns

### Create Product with Options (Ultra-Simplified Flow - NEW! 🔥)

```javascript
// 1. Create complete product in ONE call
POST /products
{ 
  name, price, ...,
  option_groups: [
    { 
      option_group_id: 1, 
      is_required: true,
      option_values: [
        { option_value_id: 1, price_type: "additional", price_value: 0, stock_quantity: 100 },
        { option_value_id: 2, price_type: "additional", price_value: 30, stock_quantity: 80 }
      ]
    }
  ]
}
// Response: product with options AND pricing fully configured!

// 2. Assign addons (optional)
POST /product-addons/assign
{ product_id, addon_ids: [1, 2, 3] }

// 3. View complete product
GET /products/{product_id}
```

**That's it! From 5+ API calls to 2 API calls!** ⚡

### Create Product with Options (Old Flow - Still Supported)

```javascript
// 1. Create product
POST /products { name, price, ... }

// 2. Assign option group
POST /product-options/assign-group
{ product_id, option_group_id, is_required: true }

// 3. Assign values with pricing
POST /product-options/assign-values
{ product_option_id, option_values: [...] }

// 4. Assign addons
POST /product-addons/assign

// 5. View complete product
GET /products/{product_id}
```

### Calculate Price (Frontend Logic)

```javascript
function calculatePrice(product, selectedOptions, selectedAddons) {
  let price = parseFloat(product.base_price);
  
  // Add option prices
  selectedOptions.forEach(optionValueId => {
    const optValue = findOptionValue(optionValueId);
    if (optValue.price_type === 'additional') {
      price += parseFloat(optValue.price_value);
    } else if (optValue.price_type === 'fixed') {
      price = parseFloat(optValue.price_value);
    } else if (optValue.price_type === 'percentage') {
      price += price * (parseFloat(optValue.price_value) / 100);
    }
  });
  
  // Add addon prices
  selectedAddons.forEach(addonId => {
    const addon = findAddon(addonId);
    price += parseFloat(addon.price);
  });
  
  return price;
}
```

---

## 🎯 Frontend Integration Checklist

### Product Listing Page
- [ ] GET /products with filters
- [ ] Display product images (primary)
- [ ] Show base price or price range
- [ ] Handle pagination

### Product Detail Page
- [ ] GET /products/{id}
- [ ] Display all images (with carousel)
- [ ] Show all options with radio/select
- [ ] Show addons with checkboxes
- [ ] Calculate real-time price
- [ ] Check stock availability
- [ ] Handle required options validation

### Admin Product Management
- [ ] Create product form
- [ ] Image upload with preview
- [ ] Assign options modal
- [ ] Set pricing per option value
- [ ] Manage stock levels
- [ ] Assign addons
- [ ] Toggle active status
- [ ] Duplicate product

---

## 📊 Data Structures

### Product Object (Full)
```typescript
interface Product {
  id: number;
  store_id: number;
  category_id: number;
  name_en: string;
  name_ar: string;
  description_en: string;
  description_ar: string;
  base_price: string;
  is_active: boolean;
  view_count: number;
  sales_count: number;
  images: ProductImage[];
  productOptions: ProductOption[];
  addons: Addon[];
}

interface ProductOption {
  id: number;
  option_group_id: number;
  is_required: boolean;
  optionGroup: OptionGroup;
  productOptionValues: ProductOptionValue[];
}

interface ProductOptionValue {
  id: number;
  option_value_id: number;
  price_type: 'additional' | 'fixed' | 'percentage';
  price_value: string;
  stock_quantity: number;
  is_available: boolean;
  optionValue: OptionValue;
}
```

---

**Total Endpoints:** 70  
**Authentication:** Required for all  
**Permissions:** Store-based (`stores.*`)

