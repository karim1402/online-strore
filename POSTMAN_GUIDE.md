# 📮 Postman Collection Guide - Product System API

## 📦 **Files Created**

1. **Product_System_API.postman_collection.json** - Complete API collection
2. **Product_System_Environment.postman_environment.json** - Environment variables

---

## 🚀 **How to Import into Postman**

### **Step 1: Import Collection**
1. Open Postman
2. Click **Import** button (top left)
3. Select **Product_System_API.postman_collection.json**
4. Click **Import**

### **Step 2: Import Environment**
1. Click **Import** again
2. Select **Product_System_Environment.postman_environment.json**
3. Click **Import**

### **Step 3: Select Environment**
1. In top-right corner, click environment dropdown
2. Select **"Product System - Local"**

---

## 🔐 **Step 1: Get Authentication Token**

### **Login to Admin Panel**
```http
POST http://localhost:8000/api/admin/login
Content-Type: application/json

{
  "email": "superadmin@test.com",
  "password": "password"
}
```

### **Copy the Token**
From the response, copy the `access_token`:
```json
{
  "access_token": "eyJ0eXAiOiJKV1QiLCJhbGc...",
  "token_type": "bearer"
}
```

### **Set Token in Environment**
1. Click the **eye icon** (👁️) in top-right
2. Click **Edit** next to "Product System - Local"
3. Paste token in `admin_token` value
4. Click **Save**

---

## 📝 **Workflow: Complete Product Setup**

### **Workflow 1: Create Pizza with Size Options**

#### **1.1 Create Size Option Group**
```
POST /api/admin/option-groups
```
**Body:**
```json
{
  "name_en": "Size",
  "name_ar": "الحجم",
  "type": "size",
  "is_active": true
}
```
**Response:** Save `id` as `option_group_id` in environment

---

#### **1.2 Create Size Values**

**Small:**
```
POST /api/admin/option-values
```
```json
{
  "option_group_id": {{option_group_id}},
  "value_en": "Small",
  "value_ar": "صغير",
  "sort_order": 1
}
```

**Medium:**
```json
{
  "option_group_id": {{option_group_id}},
  "value_en": "Medium",
  "value_ar": "متوسط",
  "sort_order": 2
}
```

**Large:**
```json
{
  "option_group_id": {{option_group_id}},
  "value_en": "Large",
  "value_ar": "كبير",
  "sort_order": 3
}
```

---

#### **1.3 Create Extra Cheese Addon**
```
POST /api/admin/addons
```
```json
{
  "store_id": 1,
  "name_en": "Extra Cheese",
  "name_ar": "جبن إضافي",
  "price": 15.00,
  "addon_category": "extras"
}
```
**Response:** Save `id` as `addon_id`

---

#### **1.4 Create Pizza Product**
```
POST /api/admin/products
```
```json
{
  "store_id": 1,
  "category_id": 1,
  "name_en": "Margherita Pizza",
  "name_ar": "بيتزا مارغريتا",
  "description_en": "Classic Italian pizza",
  "description_ar": "بيتزا إيطالية كلاسيكية",
  "base_price": 100.00,
  "is_active": true
}
```
**Response:** Save `id` as `product_id`

---

#### **1.5 Assign Size Option to Pizza**
```
POST /api/admin/product-options/assign-group
```
```json
{
  "product_id": {{product_id}},
  "option_group_id": {{option_group_id}},
  "is_required": true
}
```
**Response:** Save `id` as `product_option_id`

---

#### **1.6 Assign Size Values with Pricing**
```
POST /api/admin/product-options/assign-values
```
```json
{
  "product_option_id": {{product_option_id}},
  "option_values": [
    {
      "option_value_id": 1,
      "price_type": "additional",
      "price_value": 0.00,
      "stock_quantity": 100
    },
    {
      "option_value_id": 2,
      "price_type": "additional",
      "price_value": 30.00,
      "stock_quantity": 80
    },
    {
      "option_value_id": 3,
      "price_type": "additional",
      "price_value": 50.00,
      "stock_quantity": 60
    }
  ]
}
```

---

#### **1.7 Assign Addons to Pizza**
```
POST /api/admin/product-addons/assign
```
```json
{
  "product_id": {{product_id}},
  "addon_ids": [1]
}
```

---

#### **1.8 View Complete Product**
```
GET /api/admin/products/{{product_id}}
```

**Result:**
- Base Price: 100 EGP
- Small: 100 EGP
- Medium: 130 EGP
- Large: 150 EGP
- Large + Extra Cheese: 165 EGP

---

## 📋 **Collection Structure**

### **1. Option Groups** (6 requests)
- Get All Option Groups
- Create Option Group
- Get by ID
- Update
- Toggle Status
- Delete

### **2. Option Values** (4 requests)
- Get Values by Group
- Create Value (Small/Medium/Large examples)
- Update Value

### **3. Products** (7 requests)
- Get All Products
- Create Product
- Get by ID
- Update Product
- Toggle Status
- Duplicate Product
- Delete Product

### **4. Product Options** (6 requests)
- Get Product Options
- Assign Option Group
- Assign Option Values with Pricing
- Update Pricing
- Update Stock
- Remove Option Group

### **5. Addons** (4 requests)
- Get All Addons
- Create Addon
- Update Addon
- Delete Addon

### **6. Product Addons** (3 requests)
- Get Product Addons
- Assign Addons
- Remove Addon

---

## 🎯 **Price Type Examples**

### **Additional Pricing**
```json
{
  "price_type": "additional",
  "price_value": 30.00
}
```
**Result:** Base Price (100) + 30 = **130 EGP**

### **Fixed Pricing**
```json
{
  "price_type": "fixed",
  "price_value": 150.00
}
```
**Result:** **150 EGP** (replaces base price)

### **Percentage Pricing**
```json
{
  "price_type": "percentage",
  "price_value": 20
}
```
**Result:** Base Price (100) + 20% = **120 EGP**

---

## 🔧 **Environment Variables**

| Variable | Description | Example |
|----------|-------------|---------|
| `base_url` | API base URL | http://localhost:8000/api/admin |
| `admin_token` | JWT auth token | eyJ0eXAiOiJKV1Qi... |
| `store_id` | Store ID | 1 |
| `category_id` | Category ID | 1 |
| `option_group_id` | Current option group | 1 |
| `option_value_id` | Current option value | 1 |
| `product_id` | Current product | 1 |
| `product_option_id` | Product-option link | 1 |
| `product_option_value_id` | Product option value | 1 |
| `addon_id` | Current addon | 1 |

---

## ✅ **Testing Checklist**

- [ ] Login and get token
- [ ] Create Option Group (Size)
- [ ] Create 3 Option Values (Small, Medium, Large)
- [ ] Create Addon (Extra Cheese)
- [ ] Create Product (Pizza)
- [ ] Assign Size Option to Product
- [ ] Assign Size Values with pricing
- [ ] Assign Addons to Product
- [ ] View complete product with all options
- [ ] Test price calculation

---

## 🎉 **You're Ready!**

Import both files into Postman and follow the workflow above to test the complete product system!
