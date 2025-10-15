# 📮 Vendor Product System - Postman Guide

## 🚀 Quick Setup

### **Step 1: Use Existing Collection**
You can use the existing `Product_System_API.postman_collection.json` with these changes:

1. **Change Base URL:**
   - From: `http://localhost:8000/api/admin`
   - To: `http://localhost:8000/api/vendor`

2. **Change Token Variable:**
   - From: `{{admin_token}}`
   - To: `{{vendor_token}}`

3. **Remove store_id:**
   - Delete `store_id` field from all product/addon creation requests
   - It's automatically set to vendor's store!

---

## 🔑 Authentication

### **Vendor Login**
```http
POST /api/vendor/login
Content-Type: multipart/form-data

email: vendor@example.com
password: password
```

**Save Response Token:**
```javascript
if (pm.response.code === 200) {
    pm.environment.set('vendor_token', pm.response.json().access_token);
}
```

---

## 📝 Key Differences from Admin Collection

### **❌ DON'T Include:**
```
store_id: 1  ← Remove this field!
```

### **✅ Just Send:**
```
category_id: 1
name_en: Product Name
base_price: 100
```

The `store_id` is automatically set from the authenticated vendor!

---

## 🎯 Complete Product Creation Example

### **Vendor Request (Simplified):**
```http
POST /api/vendor/products
Authorization: Bearer {{vendor_token}}
Content-Type: multipart/form-data

category_id: 1
name_en: Supreme Pizza
name_ar: بيتزا سوبريم
description_en: Pizza with everything
base_price: 120
is_active: 1

# Option Groups
option_groups[0][option_group_id]: 1
option_groups[0][is_required]: 1
option_groups[0][sort_order]: 1

# Option Values with Pricing
option_groups[0][option_values][0][option_value_id]: 1
option_groups[0][option_values][0][price_type]: additional
option_groups[0][option_values][0][price_value]: 0
option_groups[0][option_values][0][stock_quantity]: 100
option_groups[0][option_values][0][is_available]: 1

option_groups[0][option_values][1][option_value_id]: 2
option_groups[0][option_values][1][price_type]: additional
option_groups[0][option_values][1][price_value]: 30
option_groups[0][option_values][1][stock_quantity]: 80

option_groups[0][option_values][2][option_value_id]: 3
option_groups[0][option_values][2][price_type]: additional
option_groups[0][option_values][2][price_value]: 50
option_groups[0][option_values][2][stock_quantity]: 60

# Addons
addon_ids[]: 1
addon_ids[]: 2
addon_ids[]: 3

# Images
images[]: [file1.jpg]
images[]: [file2.jpg]
```

**Note:** NO `store_id` field! It's automatic!

---

## 📋 All Vendor Endpoints (47 total)

### **1. Authentication (1)**
```
POST   /api/vendor/login
```

### **2. Products (11)**
```
GET    /api/vendor/products
GET    /api/vendor/products/{id}
POST   /api/vendor/products
PUT    /api/vendor/products/{id}
DELETE /api/vendor/products/{id}
PATCH  /api/vendor/products/{id}/toggle-status
POST   /api/vendor/products/{id}/duplicate
POST   /api/vendor/products/{id}/images
DELETE /api/vendor/products/images/{id}
PATCH  /api/vendor/products/images/{id}/set-primary
POST   /api/vendor/products/images/reorder
```

### **3. Option Groups (7)**
```
GET    /api/vendor/option-groups
GET    /api/vendor/option-groups/types
GET    /api/vendor/option-groups/{id}
POST   /api/vendor/option-groups
PUT    /api/vendor/option-groups/{id}
PATCH  /api/vendor/option-groups/{id}/toggle-status
DELETE /api/vendor/option-groups/{id}
```

### **4. Option Values (6)**
```
GET    /api/vendor/option-values/group/{groupId}
GET    /api/vendor/option-values/{id}
POST   /api/vendor/option-values
PUT    /api/vendor/option-values/{id}
POST   /api/vendor/option-values/reorder (JSON)
DELETE /api/vendor/option-values/{id}
```

### **5. Addons (7)**
```
GET    /api/vendor/addons
GET    /api/vendor/addons/categories
GET    /api/vendor/addons/{id}
POST   /api/vendor/addons
PUT    /api/vendor/addons/{id}
PATCH  /api/vendor/addons/{id}/toggle-status
DELETE /api/vendor/addons/{id}
```

### **6. Product Options (8)**
```
GET    /api/vendor/product-options/product/{productId}
POST   /api/vendor/product-options/assign-group
PUT    /api/vendor/product-options/{id}
DELETE /api/vendor/product-options/{id}
POST   /api/vendor/product-options/assign-values (JSON)
PUT    /api/vendor/product-options/values/{id}
PATCH  /api/vendor/product-options/values/{id}/stock
DELETE /api/vendor/product-options/values/{id}
```

### **7. Product Addons (5)**
```
GET    /api/vendor/product-addons/product/{productId}
POST   /api/vendor/product-addons/assign (JSON)
PUT    /api/vendor/product-addons/product/{productId}/addon/{addonId}
DELETE /api/vendor/product-addons/product/{productId}/addon/{addonId}
POST   /api/vendor/product-addons/reorder (JSON)
```

---

## 🔧 Postman Environment Variables

Create these variables in your Postman environment:

```json
{
  "vendor_token": "",
  "base_url": "http://localhost:8000/api/vendor",
  "product_id": "",
  "option_group_id": "",
  "option_value_id": "",
  "addon_id": "",
  "product_option_id": "",
  "product_option_value_id": "",
  "image_id": ""
}
```

---

## 📊 Testing Workflow

### **Complete Workflow:**

1. **Login**
   ```
   POST /api/vendor/login
   → Saves vendor_token
   ```

2. **Create Option Group - Size**
   ```
   POST /api/vendor/option-groups
   Body:
   name_en: Size
   name_ar: الحجم
   type: size
   is_active: 1
   
   → Saves option_group_id
   ```

3. **Create Option Values**
   ```
   POST /api/vendor/option-values (Small)
   POST /api/vendor/option-values (Medium)
   POST /api/vendor/option-values (Large)
   
   → Saves option_value_id
   ```

4. **Create Addons**
   ```
   POST /api/vendor/addons (Extra Cheese)
   POST /api/vendor/addons (Soft Drink)
   
   → Saves addon_id
   ← NO store_id needed!
   ```

5. **Create Complete Product**
   ```
   POST /api/vendor/products
   - Include option groups
   - Include option values with pricing
   - Include addon IDs
   - Upload images
   
   ← NO store_id needed!
   → Product automatically assigned to vendor's store
   ```

6. **View Products**
   ```
   GET /api/vendor/products
   → Shows only vendor's store products
   ```

---

## 💡 Request Examples

### **Create Addon (Vendor)**
```http
POST /api/vendor/addons
Authorization: Bearer {{vendor_token}}
Content-Type: multipart/form-data

name_en: Extra Cheese
name_ar: جبن إضافي
description_en: Add extra cheese
price: 15.00
addon_category: extras
is_active: 1
```

**vs Admin:**
```http
POST /api/admin/addons
Authorization: Bearer {{admin_token}}
Content-Type: multipart/form-data

store_id: 1  ← Required for admin!
name_en: Extra Cheese
name_ar: جبن إضافي
price: 15.00
```

### **Get Products (Vendor)**
```http
GET /api/vendor/products?is_active=1&search=pizza
Authorization: Bearer {{vendor_token}}
```

**Returns:** Only products from vendor's store (automatic filtering)

---

## ⚠️ Important Notes

### **Boolean Values**
```
✅ Use: 1 for true, 0 for false
❌ Don't: true, false, "true", "false"
```

### **Arrays**
```
✅ Correct:
addon_ids[]: 1
addon_ids[]: 2
addon_ids[]: 3
```

### **Nested Arrays**
```
✅ Correct:
option_groups[0][option_group_id]: 1
option_groups[0][option_values][0][price_value]: 30
```

### **JSON-Only Endpoints (4)**
These endpoints use JSON, not form-data:
1. `POST /api/vendor/option-values/reorder`
2. `POST /api/vendor/product-options/assign-values`
3. `POST /api/vendor/product-addons/assign`
4. `POST /api/vendor/product-addons/reorder`

All others use form-data!

---

## 🆚 Admin vs Vendor Quick Reference

| Aspect | Admin | Vendor |
|--------|-------|--------|
| **Base URL** | `/api/admin` | `/api/vendor` |
| **Token Variable** | `{{admin_token}}` | `{{vendor_token}}` |
| **store_id Field** | ✅ Required | ❌ Not needed |
| **Access Scope** | All stores | Own store only |
| **Guard** | `auth:admins` | `auth:vendors` |

---

## 🎊 Summary

✅ **Use existing Admin collection**  
✅ **Change base URL** to `/api/vendor`  
✅ **Remove store_id** from requests  
✅ **Change token** variable to `{{vendor_token}}`  
✅ **Same request format** otherwise  
✅ **47 endpoints** ready to test  

**The vendor system works exactly like admin, just simpler (no store_id needed)!** 🚀
