# 🔄 Postman Collection - JSON to Form-Data Conversion Guide

## ✅ What Changed

The collection has been updated to use **form-data** format instead of raw JSON for all POST/PUT requests.

### **Key Changes:**
1. ✅ All boolean values now use `1` for true, `0` for false
2. ✅ Body mode changed from `raw` to `formdata`
3. ✅ Headers changed from `application/json` to none (automatic multipart/form-data)
4. ✅ Added `addon_ids[]` support in product creation/update

---

## 🎯 Complete Product Creation Example

### **Form-Data Request in Postman**

```
POST /api/admin/products

Body → form-data:

Key                                                          | Value              | Type
-------------------------------------------------------------|-------------------|---------
store_id                                                     | 1                 | Text
category_id                                                  | 1                 | Text
name_en                                                      | Supreme Pizza     | Text
name_ar                                                      | بيتزا سوبريم      | Text
description_en                                               | Pizza with all    | Text
base_price                                                   | 120               | Text
is_active                                                    | 1                 | Text
option_groups[0][option_group_id]                            | 1                 | Text
option_groups[0][is_required]                                | 1                 | Text
option_groups[0][sort_order]                                 | 1                 | Text
option_groups[0][option_values][0][option_value_id]          | 1                 | Text
option_groups[0][option_values][0][price_type]               | additional        | Text
option_groups[0][option_values][0][price_value]              | 0                 | Text
option_groups[0][option_values][0][stock_quantity]           | 100               | Text
option_groups[0][option_values][0][is_available]             | 1                 | Text
option_groups[0][option_values][1][option_value_id]          | 2                 | Text
option_groups[0][option_values][1][price_type]               | additional        | Text
option_groups[0][option_values][1][price_value]              | 30                | Text
option_groups[0][option_values][1][stock_quantity]           | 80                | Text
addon_ids[]                                                  | 1                 | Text
addon_ids[]                                                  | 2                 | Text
addon_ids[]                                                  | 3                 | Text
images[]                                                     | [Select File]     | File
images[]                                                     | [Select File]     | File
```

---

## 📋 Quick Conversion Table

| JSON Format | Form-Data Format |
|------------|------------------|
| `"is_active": true` | `is_active: 1` |
| `"is_active": false` | `is_active: 0` |
| `"base_price": 100.00` | `base_price: 100.00` |
| `"name_en": "Pizza"` | `name_en: Pizza` |
| `"addon_ids": [1, 2, 3]` | `addon_ids[]: 1`<br>`addon_ids[]: 2`<br>`addon_ids[]: 3` |

---

## 🚀 How to Use Updated Collection

### **Step 1: Import Collection**
```
File → Import → Select:
Product_System_API.postman_collection.json
```

### **Step 2: Go to "Create Product - Complete"**
Navigate to:
```
3. Products - Complete Configuration
└── Create Product - Complete (Options + Values + Addons + Images)
```

### **Step 3: Enable Image Upload**
- Click on `images[]` fields
- Click "Select Files"
- Choose image files
- Enable the row

### **Step 4: Modify Values**
- All fields are editable
- Booleans: use `1` or `0`
- Arrays: duplicate row and change index

### **Step 5: Send Request**
- Click "Send"
- Product created with everything configured!

---

## 💡 Example Requests

### **1. Simple Product with Addons**
```
store_id: 1
category_id: 1
name_en: Classic Burger
name_ar: برجر كلاسيك
base_price: 75
is_active: 1
addon_ids[]: 1
addon_ids[]: 2
```

### **2. Product with One Option Group + Addons**
```
store_id: 1
name_en: Pizza
base_price: 100
option_groups[0][option_group_id]: 1
option_groups[0][is_required]: 1
option_groups[0][option_values][0][option_value_id]: 1
option_groups[0][option_values][0][price_type]: additional
option_groups[0][option_values][0][price_value]: 0
option_groups[0][option_values][0][stock_quantity]: 100
addon_ids[]: 1
addon_ids[]: 2
addon_ids[]: 3
```

### **3. Update Product with Addons**
```
PUT /api/admin/products/{{product_id}}

base_price: 150
is_active: 1
addon_ids[]: 1
addon_ids[]: 2
addon_ids[]: 3
addon_ids[]: 4
```

---

## ⚠️ Important Notes

### **Boolean Fields - Always use 1 or 0:**
- `is_active`
- `is_required`
- `is_available`

### **Array Fields - Use [] notation:**
- `addon_ids[]`
- `images[]`
- `option_groups[index][field]`

### **Nested Arrays:**
```
option_groups[0][option_group_id]: 1
option_groups[0][option_values][0][option_value_id]: 1
option_groups[0][option_values][0][price_value]: 30
option_groups[1][option_values][0][option_value_id]: 4
```

---

## 📊 New Features in Collection

### ✅ **Addons Support**
```
addon_ids[]: 1
addon_ids[]: 2
addon_ids[]: 3
```

### ✅ **Complete Configuration in ONE Call**
- Product basic info
- Option groups
- Option values with pricing
- Stock quantities
- Addons
- Images

### ✅ **Form-Data Format**
- Better for file uploads
- Works with HTML forms
- No JSON parsing needed
- PHP array notation support

---

## 🎯 Testing Checklist

- [ ] Create Option Group (Size)
- [ ] Create Option Values (Small, Medium, Large)
- [ ] Create Addons (Extra Cheese, Soft Drink)
- [ ] Create Product with ALL features in one call
- [ ] Upload images
- [ ] Update product with new addons
- [ ] View complete product response

---

## 📚 Related Files

- `FORM_DATA_GUIDE.md` - Complete form-data reference
- `POSTMAN_COMPLETE_ENDPOINTS.md` - All endpoints documentation
- `PRODUCT_CREATION_SIMPLIFIED.md` - Workflow guide
- `Product_System_API.postman_collection.json` - Updated collection

---

**The collection now supports creating complete products with addons in ONE API call using form-data!** 🎉
