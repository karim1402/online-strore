# 📝 Form-Data Guide for Product System

## 🎯 Overview

This guide shows how to send product data using **form-data** format (multipart/form-data) instead of JSON. This is especially useful when uploading images alongside product data.

---

## ⚠️ Important Rules

### **Boolean Values**
- Use `1` for `true`
- Use `0` for `false`
- ❌ DON'T use: `"true"`, `"false"`, `true`, `false`

### **Array Notation**
- Use PHP array syntax: `field[index][subfield]`
- For simple arrays: `field[]`
- For nested arrays: `field[0][subfield][0][value]`

### **Content-Type**
```
Content-Type: multipart/form-data
```

---

## 📦 Complete Product Creation Example

### **Form-Data Format (Postman)**

```
POST /api/admin/products
Content-Type: multipart/form-data

// Basic Product Info
store_id: 1
category_id: 1
name_en: Margherita Pizza
name_ar: بيتزا مارغريتا
description_en: Classic Italian pizza
description_ar: بيتزا إيطالية كلاسيكية
base_price: 100.00
search_keywords: pizza italian margherita
is_active: 1
sort_order: 1

// Option Group 1: Size
option_groups[0][option_group_id]: 1
option_groups[0][is_required]: 1
option_groups[0][sort_order]: 1

// Size - Small
option_groups[0][option_values][0][option_value_id]: 1
option_groups[0][option_values][0][price_type]: additional
option_groups[0][option_values][0][price_value]: 0
option_groups[0][option_values][0][stock_quantity]: 100
option_groups[0][option_values][0][is_available]: 1

// Size - Medium
option_groups[0][option_values][1][option_value_id]: 2
option_groups[0][option_values][1][price_type]: additional
option_groups[0][option_values][1][price_value]: 30
option_groups[0][option_values][1][stock_quantity]: 80
option_groups[0][option_values][1][is_available]: 1

// Size - Large
option_groups[0][option_values][2][option_value_id]: 3
option_groups[0][option_values][2][price_type]: additional
option_groups[0][option_values][2][price_value]: 50
option_groups[0][option_values][2][stock_quantity]: 60
option_groups[0][option_values][2][is_available]: 1

// Option Group 2: Crust
option_groups[1][option_group_id]: 2
option_groups[1][is_required]: 0
option_groups[1][sort_order]: 2

// Crust - Thin
option_groups[1][option_values][0][option_value_id]: 4
option_groups[1][option_values][0][price_type]: additional
option_groups[1][option_values][0][price_value]: 0
option_groups[1][option_values][0][stock_quantity]: 100

// Crust - Thick
option_groups[1][option_values][1][option_value_id]: 5
option_groups[1][option_values][1][price_type]: additional
option_groups[1][option_values][1][price_value]: 15
option_groups[1][option_values][1][stock_quantity]: 100

// Addons
addon_ids[]: 1
addon_ids[]: 2
addon_ids[]: 3

// Images
images[]: [Select pizza1.jpg]
images[]: [Select pizza2.jpg]
images[]: [Select pizza3.jpg]
```

---

## 🔄 Comparison: JSON vs Form-Data

### **JSON Format** (application/json)
```json
{
  "store_id": 1,
  "name_en": "Pizza",
  "base_price": 100.00,
  "is_active": true,
  "option_groups": [
    {
      "option_group_id": 1,
      "is_required": true,
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
  ],
  "addon_ids": [1, 2, 3]
}
```

### **Form-Data Format** (multipart/form-data)
```
store_id: 1
name_en: Pizza
base_price: 100.00
is_active: 1

option_groups[0][option_group_id]: 1
option_groups[0][is_required]: 1
option_groups[0][option_values][0][option_value_id]: 1
option_groups[0][option_values][0][price_type]: additional
option_groups[0][option_values][0][price_value]: 0
option_groups[0][option_values][0][stock_quantity]: 100
option_groups[0][option_values][0][is_available]: 1

addon_ids[]: 1
addon_ids[]: 2
addon_ids[]: 3

images[]: [file1.jpg]
```

---

## 📋 Field Mapping Table

| JSON | Form-Data | Type |
|------|-----------|------|
| `"is_active": true` | `is_active: 1` | Boolean |
| `"is_active": false` | `is_active: 0` | Boolean |
| `"base_price": 100.00` | `base_price: 100.00` | Number |
| `"name_en": "Pizza"` | `name_en: Pizza` | String |
| `"addon_ids": [1, 2]` | `addon_ids[]: 1`<br>`addon_ids[]: 2` | Array |
| `"option_groups": [...]` | `option_groups[0][field]: value` | Nested Array |

---

## 🎓 Postman Setup

### **Step 1: Set Request Method**
```
POST /api/admin/products
```

### **Step 2: Select Body Type**
- Click **Body** tab
- Select **form-data** (NOT raw!)

### **Step 3: Add Fields**
```
Key                                                    | Value           | Type
-------------------------------------------------------|-----------------|------
store_id                                               | 1               | Text
category_id                                            | 1               | Text
name_en                                                | Supreme Pizza   | Text
name_ar                                                | بيتزا سوبريم    | Text
base_price                                             | 120             | Text
is_active                                              | 1               | Text
option_groups[0][option_group_id]                      | 1               | Text
option_groups[0][is_required]                          | 1               | Text
option_groups[0][option_values][0][option_value_id]    | 1               | Text
option_groups[0][option_values][0][price_type]         | additional      | Text
option_groups[0][option_values][0][price_value]        | 0               | Text
option_groups[0][option_values][0][stock_quantity]     | 100             | Text
option_groups[0][option_values][0][is_available]       | 1               | Text
option_groups[0][option_values][1][option_value_id]    | 2               | Text
option_groups[0][option_values][1][price_type]         | additional      | Text
option_groups[0][option_values][1][price_value]        | 30              | Text
addon_ids[]                                            | 1               | Text
addon_ids[]                                            | 2               | Text
images[]                                               | [Select File]   | File
images[]                                               | [Select File]   | File
```

---

## 💡 Quick Examples

### **Example 1: Simple Product with Images**
```
store_id: 1
category_id: 1
name_en: Classic Burger
name_ar: برجر كلاسيك
base_price: 75
is_active: 1
images[]: [burger1.jpg]
images[]: [burger2.jpg]
```

### **Example 2: Product with One Option Group**
```
store_id: 1
name_en: T-Shirt
base_price: 50
option_groups[0][option_group_id]: 1
option_groups[0][is_required]: 1
option_groups[0][option_values][0][option_value_id]: 1
option_groups[0][option_values][0][price_type]: additional
option_groups[0][option_values][0][price_value]: 0
option_groups[0][option_values][0][stock_quantity]: 50
```

### **Example 3: Product with Addons Only**
```
store_id: 1
name_en: Pizza Base
base_price: 80
is_active: 1
addon_ids[]: 1
addon_ids[]: 2
addon_ids[]: 3
addon_ids[]: 4
```

### **Example 4: Fixed Price Options**
```
store_id: 1
name_en: Specialty Pizza
base_price: 100
option_groups[0][option_group_id]: 1
option_groups[0][is_required]: 1
option_groups[0][option_values][0][option_value_id]: 1
option_groups[0][option_values][0][price_type]: fixed
option_groups[0][option_values][0][price_value]: 100
option_groups[0][option_values][0][stock_quantity]: 50

option_groups[0][option_values][1][option_value_id]: 2
option_groups[0][option_values][1][price_type]: fixed
option_groups[0][option_values][1][price_value]: 130
option_groups[0][option_values][1][stock_quantity]: 40
```

### **Example 5: Percentage Price Options**
```
store_id: 1
name_en: Custom Pizza
base_price: 100
option_groups[0][option_group_id]: 1
option_groups[0][option_values][0][option_value_id]: 1
option_groups[0][option_values][0][price_type]: percentage
option_groups[0][option_values][0][price_value]: 0
option_groups[0][option_values][0][stock_quantity]: 100

option_groups[0][option_values][1][option_value_id]: 2
option_groups[0][option_values][1][price_type]: percentage
option_groups[0][option_values][1][price_value]: 20
option_groups[0][option_values][1][stock_quantity]: 80
```

---

## ✅ Validation Rules

### **Boolean Fields** (use 1 or 0)
- `is_active`
- `is_required`
- `is_available`

### **Numeric Fields**
- `store_id`, `category_id`, `option_group_id`, `option_value_id`
- `base_price`, `price_value`
- `stock_quantity`, `sort_order`

### **String Fields**
- `name_en`, `name_ar`
- `description_en`, `description_ar`
- `price_type` (must be: `additional`, `fixed`, or `percentage`)

### **Array Fields**
- `addon_ids[]`
- `images[]`
- `option_groups[index][field]`

---

## 🎯 Common Mistakes

### ❌ **Wrong**
```
is_active: true          ❌ String "true"
is_active: "true"        ❌ String
is_required: yes         ❌ Invalid
addon_ids: [1,2,3]       ❌ Wrong array format
```

### ✅ **Correct**
```
is_active: 1             ✅ Integer 1
is_required: 1           ✅ Integer 1
addon_ids[]: 1           ✅ Array notation
addon_ids[]: 2
addon_ids[]: 3
```

---

## 📊 Complete Structure Reference

```
// Product Basic Fields
store_id: integer
category_id: integer
name_en: string
name_ar: string
description_en: string (optional)
description_ar: string (optional)
base_price: decimal
search_keywords: string (optional)
is_active: 0 or 1
sort_order: integer (optional)

// Option Groups Array
option_groups[INDEX][option_group_id]: integer
option_groups[INDEX][is_required]: 0 or 1
option_groups[INDEX][sort_order]: integer

// Option Values Array (nested)
option_groups[INDEX][option_values][INDEX][option_value_id]: integer
option_groups[INDEX][option_values][INDEX][price_type]: additional|fixed|percentage
option_groups[INDEX][option_values][INDEX][price_value]: decimal
option_groups[INDEX][option_values][INDEX][stock_quantity]: integer
option_groups[INDEX][option_values][INDEX][is_available]: 0 or 1

// Addons Array
addon_ids[]: integer (repeat for each addon)

// Images Array
images[]: file (repeat for each image)
```

---

## 🚀 Benefits of Form-Data

✅ **Upload files** (images) alongside data  
✅ **Works with HTML forms** directly  
✅ **No JSON parsing** needed  
✅ **Supports nested arrays** via PHP notation  
✅ **Better for large files**  

---

## 📚 Related Documentation

- `POSTMAN_COMPLETE_ENDPOINTS.md` - All endpoints
- `PRODUCT_CREATION_SIMPLIFIED.md` - Simplified workflows
- `FRONTEND_API_GUIDE.md` - Complete API reference

---

**Both JSON and Form-Data formats are supported!** 🎉
