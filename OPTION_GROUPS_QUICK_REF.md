# 📋 Option Groups Quick Reference

## 🚀 Quick Start

```bash
# Run the seeder
php artisan db:seed --class=OptionGroupSeeder
```

---

## 📊 All Option Groups IDs (After Seeding)

| ID | Group Name (EN) | Type | Values Count | Example Values |
|----|-----------------|------|--------------|----------------|
| 1 | Size | size | 4 | Small, Medium, Large, Extra Large |
| 2 | Weight | weight | 6 | 100g, 250g, 500g, 1kg, 2kg, 5kg |
| 3 | Volume | volume | 8 | 250ml, 500ml, 1L, 1.5L, 2L, 3L, 5L |

---

## 🍕 Use Case Examples

### **Pizza Product:**
- Option Group 1: Size (required) - Small, Medium, Large

### **Burger Product:**
- Option Group 1: Size (required) - Small, Medium, Large

### **Meat Product:**
- Option Group 2: Weight (required) - 500g, 1kg, 2kg

### **Drink/Beverage:**
- Option Group 3: Volume (required) - 500ml, 1L, 1.5L, 2L

### **Juice:**
- Option Group 3: Volume (required) - 250ml, 500ml, 1L

### **Bulk Item (Rice, Flour):**
- Option Group 2: Weight (required) - 1kg, 2kg, 5kg

---

## 🎯 Postman Quick Test

```http
# Get all option groups
GET /api/vendor/option-groups

# Get size values
GET /api/vendor/option-values/group/1

# Get weight values
GET /api/vendor/option-values/group/2

# Get volume values
GET /api/vendor/option-values/group/3

# Create product with options
POST /api/vendor/products
option_groups[0][option_group_id]: 1
option_groups[0][is_required]: 1
option_groups[0][option_values][0][option_value_id]: 1
option_groups[0][option_values][0][price_type]: additional
option_groups[0][option_values][0][price_value]: 0
```

---

## 💡 Price Calculation Example

### **Pizza - Base: $80**
```
Base Price: $80
+ Medium Size: +$20
+ Extra Cheese (addon): +$10
= Total: $110
```

### **Configuration in Product:**
```
option_groups[0][option_group_id]: 1  (Size)
option_groups[0][option_values][0][option_value_id]: 1  (Small)
option_groups[0][option_values][0][price_value]: 0

option_groups[0][option_values][1][option_value_id]: 2  (Medium)
option_groups[0][option_values][1][price_value]: 20

option_groups[0][option_values][2][option_value_id]: 3  (Large)
option_groups[0][option_values][2][price_value]: 40

addon_ids[]: 1  (Extra Cheese - $10)
```

### **Drink - Base: $15**
```
Base Price: $15 (500ml)
+ 1L: +$10
= Total: $25
```

### **Configuration in Product:**
```
option_groups[0][option_group_id]: 3  (Volume)
option_groups[0][option_values][1][option_value_id]: 7  (500ml)
option_groups[0][option_values][1][price_value]: 0

option_groups[0][option_values][3][option_value_id]: 9  (1L)
option_groups[0][option_values][3][price_value]: 10
```

---

## 🔧 Troubleshooting

### **If seeder fails:**
```bash
# Check if tables exist
php artisan migrate

# Run seeder again
php artisan db:seed --class=OptionGroupSeeder
```

### **If you see duplicate errors:**
```bash
# Clear existing data
php artisan tinker
>>> App\Models\OptionGroup::truncate();
>>> exit

# Run seeder
php artisan db:seed --class=OptionGroupSeeder
```

---

## ✅ Verification

After seeding, verify in database:

```sql
SELECT COUNT(*) FROM option_groups;  -- Should be 3
SELECT COUNT(*) FROM option_values;  -- Should be 18

SELECT * FROM option_groups;
SELECT * FROM option_values WHERE option_group_id = 1;  -- Size values
SELECT * FROM option_values WHERE option_group_id = 2;  -- Weight values
SELECT * FROM option_values WHERE option_group_id = 3;  -- Volume values
```

---

## 🎊 Ready to Use!

Run the seeder and start creating products with these option groups! 🚀
