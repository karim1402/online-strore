# 📮 Complete Postman Collection Guide - All Endpoints

**Total Endpoints:** 47  
**Postman Files:** Use existing `Product_System_API.postman_collection.json` + New endpoints below

---

## 🎯 NEW ENDPOINTS (Nested Options & Values)

### **Create Product with Complete Configuration (Form-Data)**
```http
POST /api/admin/products
Content-Type: multipart/form-data

store_id: 1
category_id: 1
name_en: Supreme Pizza
name_ar: بيتزا سوبريم
base_price: 120.00
is_active: 1

option_groups[0][option_group_id]: 1
option_groups[0][is_required]: 1
option_groups[0][sort_order]: 1
option_groups[0][option_values][0][option_value_id]: 1
option_groups[0][option_values][0][price_type]: additional
option_groups[0][option_values][0][price_value]: 0
option_groups[0][option_values][0][stock_quantity]: 100
option_groups[0][option_values][0][is_available]: 1

option_groups[0][option_values][1][option_value_id]: 2
option_groups[0][option_values][1][price_type]: additional
option_groups[0][option_values][1][price_value]: 30
option_groups[0][option_values][1][stock_quantity]: 80

addon_ids[]: 1
addon_ids[]: 2
addon_ids[]: 3

images[]: [file1.jpg]
images[]: [file2.jpg]
```

**Note:** 
- Use `1` for `true` and `0` for `false`
- Use `multipart/form-data` for file uploads and complex nested data
- Arrays use PHP array notation: `field[index][subfield]`

### **Update Product with Options & Values**
```http
PUT /api/admin/products/{id}
Content-Type: application/json

{
  "base_price": 150.00,
  "option_groups": [
    {
      "option_group_id": 1,
      "is_required": true,
      "option_values": [
        {
          "option_value_id": 1,
          "price_type": "additional",
          "price_value": 0,
          "stock_quantity": 150
        }
      ]
    }
  ]
}
```

---

## 📋 All Endpoints by Category

### **1. Option Groups** (7 endpoints)

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/option-groups` | Get all with filters |
| GET | `/option-groups/{id}` | Get single |
| POST | `/option-groups` | Create new |
| PUT | `/option-groups/{id}` | Update |
| PATCH | `/option-groups/{id}/toggle-status` | Toggle status |
| GET | `/option-groups/types` | Get types |
| DELETE | `/option-groups/{id}` | Delete |

**Create Example:**
```json
{
  "name_en": "Size",
  "name_ar": "الحجم",
  "type": "size",
  "is_active": true
}
```

---

### **2. Option Values** (6 endpoints)

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/option-values/group/{groupId}` | Get by group |
| GET | `/option-values/{id}` | Get single |
| POST | `/option-values` | Create new |
| PUT | `/option-values/{id}` | Update |
| POST | `/option-values/reorder` | Reorder values |
| DELETE | `/option-values/{id}` | Delete |

**Create Example:**
```json
{
  "option_group_id": 1,
  "value_en": "Small",
  "value_ar": "صغير",
  "sort_order": 1,
  "is_active": true
}
```

---

### **3. Products** (13 endpoints)

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/products` | Get all with filters |
| GET | `/products/{id}` | Get single with options |
| POST | `/products` | **Create (NEW! with nested options)** |
| PUT | `/products/{id}` | **Update (NEW! with nested options)** |
| PATCH | `/products/{id}/toggle-status` | Toggle status |
| POST | `/products/{id}/duplicate` | Duplicate product |
| DELETE | `/products/{id}` | Soft delete |
| POST | `/products/{id}/images` | Upload images |
| DELETE | `/products/images/{imageId}` | Delete image |
| PATCH | `/products/images/{imageId}/set-primary` | Set primary |
| POST | `/products/images/reorder` | Reorder images |

**Create Simple:**
```json
{
  "store_id": 1,
  "category_id": 1,
  "name_en": "Classic Burger",
  "name_ar": "برجر كلاسيك",
  "base_price": 75.00
}
```

**Create with Options Only:**
```json
{
  "store_id": 1,
  "name_en": "Pizza",
  "base_price": 100.00,
  "option_groups": [
    {
      "option_group_id": 1,
      "is_required": true
    }
  ]
}
```

**Create with Options + Values (Complete):**
```json
{
  "store_id": 1,
  "name_en": "Pizza",
  "base_price": 100.00,
  "option_groups": [
    {
      "option_group_id": 1,
      "is_required": true,
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
        },
        {
          "option_value_id": 3,
          "price_type": "fixed",
          "price_value": 150,
          "stock_quantity": 60
        }
      ]
    }
  ]
}
```

---

### **4. Product Options** (9 endpoints) - Old Method

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/product-options/product/{productId}` | Get all options |
| POST | `/product-options/assign-group` | Assign group |
| PUT | `/product-options/{id}` | Update option |
| DELETE | `/product-options/{id}` | Remove group |
| POST | `/product-options/assign-values` | Assign values |
| PUT | `/product-options/values/{id}` | Update value pricing |
| PATCH | `/product-options/values/{id}/stock` | Update stock |
| DELETE | `/product-options/values/{id}` | Remove value |
| POST | `/product-options/reorder` | Reorder options |

**Assign Group:**
```json
{
  "product_id": 1,
  "option_group_id": 1,
  "is_required": true,
  "sort_order": 1
}
```

**Assign Values:**
```json
{
  "product_option_id": 1,
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
}
```

---

### **5. Addons** (7 endpoints)

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/addons` | Get all with filters |
| GET | `/addons/{id}` | Get single |
| POST | `/addons` | Create new |
| PUT | `/addons/{id}` | Update |
| PATCH | `/addons/{id}/toggle-status` | Toggle status |
| GET | `/addons/categories/{storeId}` | Get categories |
| DELETE | `/addons/{id}` | Delete |

**Create Example:**
```json
{
  "store_id": 1,
  "name_en": "Extra Cheese",
  "name_ar": "جبن إضافي",
  "description_en": "Add extra cheese",
  "description_ar": "أضف جبن إضافي",
  "price": 15.00,
  "addon_category": "extras",
  "is_active": true
}
```

---

### **6. Product Addons** (5 endpoints)

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/product-addons/product/{productId}` | Get product addons |
| POST | `/product-addons/assign` | Assign addons |
| PUT | `/product-addons/product/{productId}/addon/{addonId}` | Update addon |
| DELETE | `/product-addons/product/{productId}/addon/{addonId}` | Remove addon |
| POST | `/product-addons/reorder` | Reorder addons |

**Assign Addons:**
```json
{
  "product_id": 1,
  "addon_ids": [1, 2, 3]
}
```

**Reorder Addons:**
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

## 🔥 Complete Workflow Examples

### **Workflow 1: Create Pizza - Simplified (1 API Call)**

```json
POST /api/admin/products
{
  "store_id": 1,
  "category_id": 1,
  "name_en": "Margherita Pizza",
  "name_ar": "بيتزا مارغريتا",
  "base_price": 100.00,
  "is_active": true,
  "option_groups": [
    {
      "option_group_id": 1,
      "is_required": true,
      "sort_order": 1,
      "option_values": [
        {"option_value_id": 1, "price_type": "additional", "price_value": 0, "stock_quantity": 100},
        {"option_value_id": 2, "price_type": "additional", "price_value": 30, "stock_quantity": 80},
        {"option_value_id": 3, "price_type": "additional", "price_value": 50, "stock_quantity": 60}
      ]
    },
    {
      "option_group_id": 2,
      "is_required": false,
      "sort_order": 2,
      "option_values": [
        {"option_value_id": 4, "price_type": "additional", "price_value": 0, "stock_quantity": 100},
        {"option_value_id": 5, "price_type": "additional", "price_value": 15, "stock_quantity": 100}
      ]
    }
  ]
}
```

**Then assign addons:**
```json
POST /api/admin/product-addons/assign
{
  "product_id": 1,
  "addon_ids": [1, 2, 3]
}
```

**Done! Product fully configured in 2 API calls!**

---

### **Workflow 2: Create Pizza - Old Way (5 API Calls)**

```json
// 1. Create product
POST /products
{"name_en": "Pizza", "base_price": 100}

// 2. Assign Size option
POST /product-options/assign-group
{"product_id": 1, "option_group_id": 1}

// 3. Assign Size values
POST /product-options/assign-values
{
  "product_option_id": 1,
  "option_values": [...]
}

// 4. Assign Crust option
POST /product-options/assign-group
{"product_id": 1, "option_group_id": 2}

// 5. Assign Crust values
POST /product-options/assign-values
{
  "product_option_id": 2,
  "option_values": [...]
}
```

---

## 📝 Query Parameters Reference

### **Products GET**
```
?store_id=1
&category_id=1
&is_active=true
&search=pizza
&sort_by=base_price|created_at|view_count|sales_count|sort_order
&sort_order=asc|desc
&per_page=15
```

### **Option Groups GET**
```
?type=size
&is_active=true
&search=size
```

### **Option Values GET**
```
?is_active=true
```

### **Addons GET**
```
?store_id=1
&addon_category=extras
&is_active=true
&search=cheese
```

---

## 🎯 Postman Environment Variables

```json
{
  "admin_token": "your-jwt-token-here",
  "base_url": "http://localhost:8000/api/admin",
  "store_id": "1",
  "category_id": "1",
  "option_group_id": "",
  "option_value_id": "",
  "product_id": "",
  "product_option_id": "",
  "product_option_value_id": "",
  "addon_id": "",
  "image_id": ""
}
```

---

## 🔑 Authentication

All endpoints require:
```
Authorization: Bearer {{admin_token}}
Content-Type: application/json
```

Get token from:
```http
POST /api/admin/login
{
  "email": "admin@example.com",
  "password": "password"
}
```

---

## ✅ Testing Checklist

### Option Groups & Values
- [ ] Create Size option group
- [ ] Create Small, Medium, Large values
- [ ] Create Crust option group
- [ ] Create Thin, Thick values

### Products - New Method
- [ ] Create product with nested options & values (1 call)
- [ ] Update product with new options
- [ ] View complete product

### Products - Old Method
- [ ] Create product
- [ ] Assign option group
- [ ] Assign option values
- [ ] View complete product

### Addons
- [ ] Create Extra Cheese addon
- [ ] Create Soft Drink addon
- [ ] Assign addons to product

### Images
- [ ] Upload product images
- [ ] Set primary image
- [ ] Reorder images
- [ ] Delete image

---

## 📊 Response Examples

### Success
```json
{
  "success": true,
  "message": "Product created successfully",
  "data": { ... }
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

### Complete Product Response
```json
{
  "success": true,
  "data": {
    "id": 1,
    "name_en": "Margherita Pizza",
    "base_price": "100.00",
    "images": [...],
    "productOptions": [
      {
        "id": 1,
        "option_group_id": 1,
        "is_required": true,
        "optionGroup": {
          "name_en": "Size"
        },
        "productOptionValues": [
          {
            "id": 1,
            "option_value_id": 1,
            "price_type": "additional",
            "price_value": "0.00",
            "stock_quantity": 100,
            "optionValue": {
              "value_en": "Small"
            }
          }
        ]
      }
    ],
    "addons": [...]
  }
}
```

---

## 🚀 Quick Start

1. Import existing `Product_System_API.postman_collection.json`
2. Import `Product_System_Environment.postman_environment.json`
3. Login and set `admin_token`
4. Use examples above for new endpoints
5. Test complete workflows

---

## 📚 Related Files

- `Product_System_API.postman_collection.json` - Base collection
- `Product_System_Environment.postman_environment.json` - Variables
- `FRONTEND_API_GUIDE.md` - Full API docs
- `PRODUCT_CREATION_SIMPLIFIED.md` - Workflow guide
- `POSTMAN_GUIDE.md` - Original guide

---

**Total Endpoints: 47**  
**New Nested Functionality: ✅ Included**  
**Backward Compatible: ✅ Yes**

