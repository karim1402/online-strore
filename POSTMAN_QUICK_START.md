# 🚀 Makkok Product System - Postman Quick Start

## ✅ New Collection Created!

**File:** `Makkok_Product_API.postman_collection.json`  
**Format:** Form-Data (multipart/form-data)  
**Boolean Values:** Use `1` for true, `0` for false

---

## 📦 What's Included

### **1. Authentication**
- Admin Login (auto-saves token)

### **2. Products Folder**
All product endpoints in ONE folder:
- ✅ Get All Products
- ✅ Get Product by ID
- ✅ **Create Complete** (Options + Values + Addons + Images)
- ✅ Update Product (with addons)
- ✅ Toggle Status
- ✅ Duplicate Product
- ✅ Delete Product
- ✅ Upload Images

---

## 🎯 Quick Start

### **Step 1: Import Collection**
```
Postman → File → Import → Select:
Makkok_Product_API.postman_collection.json
Product_System_Environment.postman_environment.json
```

### **Step 2: Login**
```
Auth → Login
Send request
Token automatically saved to environment
```

### **Step 3: Create Complete Product**
```
Products → Create Complete
Click Send
```

**This creates a complete product with:**
- Size options (Small +0, Medium +30, Large +50)
- Option group ID 1 with 3 values
- 2 addons (IDs: 1, 2)
- Base price: 120 EGP
- Ready for images (disabled by default)

---

## 💡 Complete Product Example

### **Form-Data Fields in Postman**

Open "Create Complete" request and you'll see:

```
Key                                                  | Value        | Type
-----------------------------------------------------|--------------|------
store_id                                             | {{store_id}} | Text
category_id                                          | 1            | Text
name_en                                              | Supreme Pizza| Text
name_ar                                              | بيتزا سوبريم | Text
base_price                                           | 120          | Text
is_active                                            | 1            | Text
option_groups[0][option_group_id]                    | 1            | Text
option_groups[0][is_required]                        | 1            | Text
option_groups[0][option_values][0][option_value_id]  | 1            | Text
option_groups[0][option_values][0][price_type]       | additional   | Text
option_groups[0][option_values][0][price_value]      | 0            | Text
option_groups[0][option_values][0][stock_quantity]   | 100          | Text
option_groups[0][option_values][1][option_value_id]  | 2            | Text
option_groups[0][option_values][1][price_type]       | additional   | Text
option_groups[0][option_values][1][price_value]      | 30           | Text
option_groups[0][option_values][1][stock_quantity]   | 80           | Text
addon_ids[]                                          | 1            | Text
addon_ids[]                                          | 2            | Text
images[]                                             | [File]       | File (disabled)
images[]                                             | [File]       | File (disabled)
```

---

## 🔧 How to Modify

### **Add More Option Values**
Duplicate existing rows and increment the index:
```
option_groups[0][option_values][2][option_value_id]  | 3
option_groups[0][option_values][2][price_type]       | additional
option_groups[0][option_values][2][price_value]      | 50
option_groups[0][option_values][2][stock_quantity]   | 60
```

### **Add More Addons**
Add more rows with same key:
```
addon_ids[]  | 1
addon_ids[]  | 2
addon_ids[]  | 3
addon_ids[]  | 4
```

### **Enable Images**
1. Click on the `images[]` row
2. Check the checkbox to enable
3. Click "Select Files"
4. Choose your image

### **Change Price Type**
```
price_type values:
- additional  (adds to base price)
- fixed      (replaces base price)
- percentage (percentage of base price)
```

---

## 📊 Update Product Example

### **Update with New Addons**
```
Products → Update Product

Fields:
base_price: 150
is_active: 1
addon_ids[]: 1
addon_ids[]: 2
addon_ids[]: 3
addon_ids[]: 4
```

This updates the product price and replaces all addons.

---

## ⚠️ Important Rules

### **Boolean Values**
```
✅ Correct:
is_active: 1
is_required: 0

❌ Wrong:
is_active: true
is_required: false
```

### **Arrays**
```
✅ Correct:
addon_ids[]: 1
addon_ids[]: 2

❌ Wrong:
addon_ids: [1,2]
```

### **Nested Arrays**
```
✅ Correct:
option_groups[0][option_group_id]: 1
option_groups[0][option_values][0][price_value]: 30

❌ Wrong:
option_groups.0.option_group_id: 1
```

---

## 🎯 Testing Workflow

### **Complete Test Sequence:**

1. **Login** → Get admin token
2. Create Option Group "Size" → Save ID
3. Create Values: Small, Medium, Large → Save IDs
4. Create Addons: Extra Cheese, Soft Drink → Save IDs
5. **Create Complete Product** → Uses all above IDs
6. View Product → See complete configuration
7. Update Product → Change addons
8. Upload Images → Add product photos
9. Duplicate Product → Create copy
10. Toggle Status → Activate/deactivate

---

## 📝 Request Descriptions

| Request | Method | What It Does |
|---------|--------|--------------|
| **Create Complete** | POST | Creates product with options, values, addons, images in 1 call |
| Update Product | PUT | Updates product and replaces addons |
| Toggle Status | PATCH | Switches is_active between 0 and 1 |
| Duplicate | POST | Creates exact copy of product |
| Upload Images | POST | Adds images to existing product |

---

## 🔥 Real-World Examples

### **Example 1: Pizza with 2 Sizes**
```
name_en: Margherita Pizza
base_price: 100
option_groups[0][option_group_id]: 1
option_groups[0][option_values][0][option_value_id]: 1  (Small, +0)
option_groups[0][option_values][1][option_value_id]: 2  (Medium, +30)
addon_ids[]: 1  (Extra Cheese)
addon_ids[]: 2  (Soft Drink)
```

### **Example 2: Burger with No Options**
```
name_en: Classic Burger
base_price: 75
is_active: 1
addon_ids[]: 1
addon_ids[]: 2
```
(No option_groups field = product without options)

### **Example 3: Fixed Price Product**
```
name_en: Combo Deal
base_price: 100
option_groups[0][option_values][0][price_type]: fixed
option_groups[0][option_values][0][price_value]: 120
```
(Customer pays 120 EGP regardless of base price)

---

## 📚 Related Documentation

- `FORM_DATA_GUIDE.md` - Complete form-data reference
- `POSTMAN_COMPLETE_ENDPOINTS.md` - All endpoints documentation
- `PRODUCT_CREATION_SIMPLIFIED.md` - Workflow guide

---

## 🎊 Summary

✅ **Compact collection** - Products folder contains everything  
✅ **Form-data format** - Ready for file uploads  
✅ **Boolean conversion** - All values use 1/0  
✅ **Addons included** - Create products with addons in one call  
✅ **Auto-save IDs** - Test scripts save response IDs to environment  
✅ **Clean structure** - No duplicate update endpoints  

**Import and start testing immediately!** 🚀
