# 🎉 Complete Postman Collection - ALL 47 Endpoints

## ✅ What's Included

**Collection:** `Product_System_API.postman_collection.json`  
**Environment:** `Product_System_Environment.postman_environment.json`  
**Total Endpoints:** 47  
**Format:** Form-Data (multipart/form-data)  
**Boolean Values:** 1 = true, 0 = false

---

## 📋 Collection Structure

### **1. Authentication (1 endpoint)**
- Admin Login ✅ (auto-saves token)

### **2. Option Groups (7 endpoints)**
- Get All Option Groups
- Get Option Group by ID
- Create - Size
- Create - Crust
- Update
- Toggle Status
- Get Types
- Delete

### **3. Option Values (7 endpoints)**
- Get by Group
- Get by ID
- Create - Small
- Create - Medium
- Create - Large
- Update
- Reorder (JSON only)
- Delete

### **4. Addons (7 endpoints)**
- Get All
- Get by ID
- Create - Extra Cheese
- Create - Soft Drink
- Update
- Toggle Status
- Get Categories
- Delete

### **5. Products (10 endpoints)**
- Get All Products
- Get Product by ID
- **Create Complete** (Options + Values + Addons + Images) ⭐
- Create Simple with Addons
- Update Product (with addons) ⭐
- Toggle Status
- Duplicate
- Delete
- Upload Images
- Set Primary Image
- Reorder Images (JSON only)
- Delete Image

### **6. Product Options - Old Method (9 endpoints)**
- Get Product Options
- Assign Option Group
- Update Product Option
- Delete Product Option
- Assign Option Values (JSON only)
- Update Value Pricing
- Update Stock
- Remove Value
- Reorder Options (JSON only)

### **7. Product Addons (5 endpoints)**
- Get Product Addons
- Assign Addons (JSON only)
- Update Product Addon
- Remove Addon
- Reorder Addons (JSON only)

---

## 🚀 Quick Start

### **Step 1: Import Collection**
```
Postman → File → Import
Select: Product_System_API.postman_collection.json
```

### **Step 2: Import Environment**
```
File → Import
Select: Product_System_Environment.postman_environment.json
Select environment from dropdown (top right)
```

### **Step 3: Login**
```
1. Authentication → Admin Login
2. Click Send
3. Token automatically saved to environment!
```

### **Step 4: Create Complete Product**
```
5. Products → Create Complete (Options + Values + Addons + Images)
Click Send

This creates a product with:
- Size options (Small +0, Medium +30, Large +50)
- Crust options (Thin +0, Thick +15)
- 3 addons
- Images (enable and select files)
```

---

## 💡 Key Features

### **✅ Form-Data Format**
All POST/PUT requests use form-data (except 6 JSON-only endpoints)

### **✅ Boolean Conversion**
```
Use: 1 for true, 0 for false
DON'T use: true, false, "true", "false"
```

### **✅ Array Notation**
```
addon_ids[]: 1
addon_ids[]: 2
addon_ids[]: 3
```

### **✅ Nested Arrays**
```
option_groups[0][option_group_id]: 1
option_groups[0][option_values][0][option_value_id]: 1
option_groups[0][option_values][0][price_value]: 30
```

### **✅ Auto-Save IDs**
Test scripts automatically save response IDs to environment:
- option_group_id
- option_value_id
- addon_id
- product_id
- product_option_id
- product_option_value_id

---

## 🎯 Complete Product Creation Example

### **In Postman:**
Go to: `5. Products → Create Complete`

**Form-Data Fields:**
```
store_id: {{store_id}}
category_id: 1
name_en: Supreme Pizza
name_ar: بيتزا سوبريم
base_price: 120
is_active: 1

# Size options
option_groups[0][option_group_id]: 1
option_groups[0][is_required]: 1
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

# Crust options
option_groups[1][option_group_id]: 2
option_groups[1][is_required]: 0
option_groups[1][option_values][0][option_value_id]: 4
option_groups[1][option_values][0][price_type]: additional
option_groups[1][option_values][0][price_value]: 0
option_groups[1][option_values][0][stock_quantity]: 100

option_groups[1][option_values][1][option_value_id]: 5
option_groups[1][option_values][1][price_type]: additional
option_groups[1][option_values][1][price_value]: 15
option_groups[1][option_values][1][stock_quantity]: 100

# Addons
addon_ids[]: 1
addon_ids[]: 2
addon_ids[]: 3

# Images (enable to upload)
images[]: [file1.jpg]
images[]: [file2.jpg]
```

**Result:** Complete product created in ONE API call! 🔥

---

## 📝 JSON-Only Endpoints (6 endpoints)

These endpoints MUST use JSON format:
1. POST `/option-values/reorder`
2. POST `/product-options/assign-values`
3. POST `/product-options/reorder`
4. POST `/products/images/reorder`
5. POST `/product-addons/assign`
6. POST `/product-addons/reorder`

All others (41 endpoints) use form-data!

---

## 🔧 How to Modify Requests

### **Add More Option Values**
Duplicate existing rows and increment index:
```
option_groups[0][option_values][3][option_value_id]: 4
option_groups[0][option_values][3][price_type]: additional
option_groups[0][option_values][3][price_value]: 70
```

### **Add More Addons**
Add more rows with same key:
```
addon_ids[]: 1
addon_ids[]: 2
addon_ids[]: 3
addon_ids[]: 4
addon_ids[]: 5
```

### **Enable Image Upload**
1. Find `images[]` rows
2. Check the checkbox to enable
3. Click "Select Files"
4. Choose your images

### **Change Price Types**
```
price_type options:
- additional: Adds to base price (120 + 30 = 150)
- fixed: Replaces base price (price = 150)
- percentage: Percentage of base (120 + 20% = 144)
```

---

## 📊 Endpoint Breakdown

| Category | Endpoints | Form-Data | JSON | GET/PATCH/DELETE |
|----------|-----------|-----------|------|------------------|
| Authentication | 1 | ✅ | | |
| Option Groups | 7 | ✅ | | ✅ |
| Option Values | 7 | ✅ | 1 | ✅ |
| Addons | 7 | ✅ | | ✅ |
| Products | 10 | ✅ | 1 | ✅ |
| Product Options | 9 | ✅ | 2 | ✅ |
| Product Addons | 5 | ✅ | 2 | ✅ |
| **TOTAL** | **47** | **41** | **6** | **47** |

---

## ⚠️ Important Rules

### **Boolean Values**
```
✅ Correct:
is_active: 1
is_required: 0
is_available: 1

❌ Wrong:
is_active: true
is_required: false
is_available: "true"
```

### **Arrays**
```
✅ Correct:
addon_ids[]: 1
addon_ids[]: 2

❌ Wrong:
addon_ids: [1,2]
addon_ids: 1,2
```

### **Nested Arrays**
```
✅ Correct:
option_groups[0][option_group_id]: 1
option_groups[0][option_values][0][option_value_id]: 1

❌ Wrong:
option_groups.0.option_group_id: 1
option_groups[0].option_values[0].option_value_id: 1
```

---

## 🎓 Testing Workflow

### **Complete Test Sequence:**

1. **Login** (Authentication)
   - Get admin token → auto-saved

2. **Create Option Group - Size**
   - ID saved to environment

3. **Create Option Values**
   - Small (ID=1)
   - Medium (ID=2)
   - Large (ID=3)

4. **Create Option Group - Crust**
   - ID saved to environment

5. **Create Option Values**
   - Thin (ID=4)
   - Thick (ID=5)

6. **Create Addons**
   - Extra Cheese (ID=1)
   - Soft Drink (ID=2)

7. **Create Complete Product** ⭐
   - Uses all IDs from above
   - Configures everything in ONE call

8. **Upload Images**
   - Add product photos

9. **View Product**
   - See complete configuration

10. **Update Product**
    - Change addons, prices, etc.

---

## 📚 Documentation Files

| File | Purpose |
|------|---------|
| `Product_System_API.postman_collection.json` | **Main collection - 47 endpoints** |
| `Product_System_Environment.postman_environment.json` | Environment variables |
| `README_POSTMAN_COLLECTION.md` | **This file - Quick start guide** |
| `COMPLETE_API_REFERENCE.md` | All endpoints with examples |
| `FORM_DATA_GUIDE.md` | Complete form-data reference |
| `POSTMAN_COMPLETE_ENDPOINTS.md` | Detailed endpoint documentation |

---

## 🎊 Summary

✅ **47 endpoints** - Complete API coverage  
✅ **Form-data format** - 41 endpoints  
✅ **JSON format** - 6 endpoints only  
✅ **Boolean conversion** - All values use 1/0  
✅ **Addons support** - Create products with addons  
✅ **Nested options** - Complete configuration in one call  
✅ **Auto-save IDs** - Test scripts included  
✅ **Clean structure** - Organized by category  
✅ **Ready to use** - Import and test immediately  

**Import the collection and start testing all 47 endpoints!** 🚀
