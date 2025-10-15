# 🎯 Option Groups Seeder - Documentation

## ✅ Simplified & Ready!

**File:** `database/seeders/OptionGroupSeeder.php`

---

## 📦 What Was Created

### **3 Essential Option Groups with 18 Total Values**

A simplified seeder with basic option groups: Size, Weight, and Volume (Liter).

---

## 📊 Complete Option Groups List

### **1. Size (4 values)**
- Small / صغير
- Medium / وسط
- Large / كبير
- Extra Large / كبير جداً

**Use Cases:** Pizzas, Burgers, Drinks, Meals, Any product with different sizes

---

### **2. Weight (6 values)**
- 100g / 100 جرام
- 250g / 250 جرام
- 500g / 500 جرام
- 1kg / 1 كيلوجرام
- 2kg / 2 كيلوجرام
- 5kg / 5 كيلوجرام

**Use Cases:** Meat, Seafood, Fruits, Vegetables, Bakery items, Bulk products

---

### **3. Volume (8 values)**
- 250ml / 250 مل
- 500ml / 500 مل
- 750ml / 750 مل
- 1L / 1 لتر
- 1.5L / 1.5 لتر
- 2L / 2 لتر
- 3L / 3 لتر
- 5L / 5 لتر

**Use Cases:** Drinks, Beverages, Juices, Milk, Water, Oils, Liquid products

---

## 🚀 How to Run

### **Method 1: Run All Seeders**
```bash
php artisan db:seed
```

### **Method 2: Run Only Option Groups Seeder**
```bash
php artisan db:seed --class=OptionGroupSeeder
```

### **Method 3: Fresh Migration + Seed**
```bash
php artisan migrate:fresh --seed
```

---

## 📝 Usage in DatabaseSeeder

The seeder has been added to `DatabaseSeeder.php`:

```php
$this->call([
    PermissionSeeder::class,
    RoleSeeder::class,
    AdminSeeder::class,
    MainCategorySeeder::class,
    OptionGroupSeeder::class,  // ← Added
]);
```

---

## 💡 How to Use Option Groups

### **Example 1: Pizza with Size & Crust**

```php
// Create Pizza product
$pizza = Product::create([
    'store_id' => 1,
    'category_id' => 1,
    'name_en' => 'Margherita Pizza',
    'base_price' => 80,
]);

// Assign Size option group (required)
$sizeOption = ProductOption::create([
    'product_id' => $pizza->id,
    'option_group_id' => 1,  // Size
    'is_required' => true,
]);

// Add size values with pricing
ProductOptionValue::create([
    'product_option_id' => $sizeOption->id,
    'option_value_id' => 1,  // Small
    'price_type' => 'additional',
    'price_value' => 0,
]);

ProductOptionValue::create([
    'product_option_id' => $sizeOption->id,
    'option_value_id' => 2,  // Medium
    'price_type' => 'additional',
    'price_value' => 20,
]);

ProductOptionValue::create([
    'product_option_id' => $sizeOption->id,
    'option_value_id' => 3,  // Large
    'price_type' => 'additional',
    'price_value' => 40,
]);

// Assign Crust option group (optional)
$crustOption = ProductOption::create([
    'product_id' => $pizza->id,
    'option_group_id' => 2,  // Crust
    'is_required' => false,
]);

// Add crust values
ProductOptionValue::create([
    'product_option_id' => $crustOption->id,
    'option_value_id' => 6,  // Thin Crust
    'price_type' => 'additional',
    'price_value' => 0,
]);
```

### **Example 2: Coffee with Milk & Shots**

```php
// Create Latte product
$latte = Product::create([
    'store_id' => 1,
    'category_id' => 5,
    'name_en' => 'Latte',
    'base_price' => 25,
]);

// Assign Size
$sizeOption = ProductOption::create([
    'product_id' => $latte->id,
    'option_group_id' => 1,  // Size
    'is_required' => true,
]);

// Assign Milk Type
$milkOption = ProductOption::create([
    'product_id' => $latte->id,
    'option_group_id' => 8,  // Milk Type
    'is_required' => true,
]);

// Assign Coffee Shots
$shotOption = ProductOption::create([
    'product_id' => $latte->id,
    'option_group_id' => 9,  // Coffee Shots
    'is_required' => false,
]);
```

### **Example 3: Burger with Cooking Level**

```php
// Create Burger product
$burger = Product::create([
    'store_id' => 1,
    'category_id' => 2,
    'name_en' => 'Beef Burger',
    'base_price' => 45,
]);

// Assign Size
$sizeOption = ProductOption::create([
    'product_id' => $burger->id,
    'option_group_id' => 1,  // Size
    'is_required' => true,
]);

// Assign Cooking Level
$cookingOption = ProductOption::create([
    'product_id' => $burger->id,
    'option_group_id' => 5,  // Cooking Level
    'is_required' => true,
]);

// Assign Bread Type
$breadOption = ProductOption::create([
    'product_id' => $burger->id,
    'option_group_id' => 7,  // Bread Type
    'is_required' => false,
]);
```

---

## 🎯 Postman Testing

After running the seeder, you can test in Postman:

### **1. Get All Option Groups**
```http
GET /api/admin/option-groups
GET /api/vendor/option-groups
```

### **2. Get Option Values by Group**
```http
GET /api/admin/option-values/group/1  (Size)
GET /api/vendor/option-values/group/2  (Crust)
```

### **3. Get Types**
```http
GET /api/admin/option-groups/types
GET /api/vendor/option-groups/types
```

**Expected Response:**
```json
{
  "success": true,
  "data": [
    "size", "crust", "temperature", "spice", 
    "cooking", "sugar", "bread", "milk", 
    "shot", "cheese", "sauce", "portion"
  ]
}
```

---

## 🔄 Re-running the Seeder

If you need to re-run the seeder:

### **Option 1: Fresh Start**
```bash
php artisan migrate:fresh --seed
```
⚠️ Warning: This will delete all data!

### **Option 2: Just Option Groups**
```bash
# Delete existing option groups first
php artisan tinker
>>> App\Models\OptionGroup::truncate();
>>> exit

# Run seeder
php artisan db:seed --class=OptionGroupSeeder
```

---

## 📊 Database Impact

### **Tables Affected:**
- `option_groups` - 3 records
- `option_values` - 18 records

### **Storage:**
- Approximately 5 KB total data
- All records have bilingual support (EN/AR)

---

## 🎨 Customization

### **Add New Option Group:**

Edit `database/seeders/OptionGroupSeeder.php`:

```php
// 13. Your Custom Group
$customGroup = OptionGroup::create([
    'name_en' => 'Your Group Name',
    'name_ar' => 'اسم المجموعة',
    'type' => 'custom_type',
    'is_active' => true,
]);

$customValues = [
    ['value_en' => 'Option 1', 'value_ar' => 'خيار 1', 'sort_order' => 1],
    ['value_en' => 'Option 2', 'value_ar' => 'خيار 2', 'sort_order' => 2],
];

foreach ($customValues as $value) {
    OptionValue::create(array_merge($value, [
        'option_group_id' => $customGroup->id,
        'is_active' => true,
    ]));
}
```

---

## ✅ Features

- ✅ Bilingual (English/Arabic)
- ✅ Sorted by `sort_order`
- ✅ All active by default
- ✅ Transaction support (rollback on error)
- ✅ Informative console output
- ✅ Ready to use immediately
- ✅ Covers most common restaurant scenarios

---

## 🎊 Summary

### **Created:**
- ✅ `OptionGroupSeeder.php` with 3 option groups
- ✅ 18 option values across all groups
- ✅ Updated `DatabaseSeeder.php` to include it
- ✅ Bilingual support (EN/AR)
- ✅ Simplified and ready for immediate use

### **Usage:**
```bash
# Run the seeder
php artisan db:seed --class=OptionGroupSeeder

# Or run all seeders
php artisan db:seed
```

### **Next Steps:**
1. Run the seeder
2. Test in Postman (GET /option-groups)
3. Use in product creation
4. Customize as needed

**The option groups seeder is ready to use!** 🚀
