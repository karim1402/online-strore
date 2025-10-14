# 🛍️ Product Management System - Complete Documentation

A comprehensive Laravel-based product management system with flexible options, dynamic pricing, and add-ons support.

**🌍 [النسخة العربية - Arabic Version](PRODUCT_SYSTEM_README_AR.md)**

---

## 📋 Table of Contents

- [Overview](#overview)
- [Features](#features)
- [System Architecture](#system-architecture)
- [Database Structure](#database-structure)
- [Installation](#installation)
- [Core Concepts](#core-concepts)
- [API Endpoints](#api-endpoints)
- [Usage Examples](#usage-examples)
- [Files Structure](#files-structure)
- [Workflows](#workflows)
- [Testing](#testing)

---

## 🎯 Overview

This system provides a complete product management solution for multi-vendor e-commerce platforms with:
- **Flexible Product Options** (Size, Weight, Color, etc.)
- **Dynamic Pricing** (Fixed, Additional, Percentage-based)
- **Stock Management** at variation level
- **Product Add-ons** (Extras, Sides, Drinks)
- **Image Management** (Multiple images per product)
- **Bilingual Support** (English/Arabic)
- **Elasticsearch Integration** (Search optimization)

---

## ✨ Features

### 🎨 Product Variations
- Create reusable option groups (Size, Weight, Packaging, etc.)
- Define multiple values per group (Small, Medium, Large)
- Assign any combination of options to products
- Mark options as required/optional
- Control display order

### 💰 Flexible Pricing
Three pricing strategies for option values:
1. **Additional** - Adds to base price (Base: $100 + Option: $30 = **$130**)
2. **Fixed** - Replaces base price (Option: $150 = **$150**)
3. **Percentage** - Percentage increase (Base: $100 + 20% = **$120**)

### 📦 Stock Management
- Track stock at variation level
- Auto stock deduction on orders
- Availability toggle per variation
- Low stock warnings

### 🧀 Add-ons System
- Reusable add-ons across products
- Categorized (extras, sides, drinks, etc.)
- Simple fixed pricing
- Per-product availability control

### 🖼️ Image Management
- Multiple images per product
- Primary image designation
- Drag-and-drop reordering
- Auto-optimization
- CDN-ready storage

### 🌍 Multi-language
- All content in English & Arabic
- Auto locale detection
- Consistent naming convention

### 🔍 Search Integration
- Elasticsearch sync queue
- Auto-indexing on changes
- Custom search keywords
- Full-text search ready

---

## 🏗️ System Architecture

### Components
```
┌─────────────────────────────────────────┐
│         Product System                   │
├─────────────────────────────────────────┤
│                                          │
│  ┌──────────────┐  ┌─────────────────┐ │
│  │  Option      │  │  Product Core   │ │
│  │  Groups      │→ │  - Products     │ │
│  │  - Size      │  │  - Categories   │ │
│  │  - Weight    │  │  - Images       │ │
│  │  - etc.      │  │  - Metadata     │ │
│  └──────────────┘  └─────────────────┘ │
│         ↓                    ↓          │
│  ┌──────────────┐  ┌─────────────────┐ │
│  │  Option      │  │  Product        │ │
│  │  Values      │→ │  Options        │ │
│  │  - Small     │  │  (Assignment)   │ │
│  │  - Medium    │  └─────────────────┘ │
│  │  - Large     │           ↓          │
│  └──────────────┘  ┌─────────────────┐ │
│                     │  Pricing &      │ │
│  ┌──────────────┐  │  Stock          │ │
│  │  Addons      │→ │  Management     │ │
│  │  - Cheese    │  └─────────────────┘ │
│  │  - Drinks    │           ↓          │
│  └──────────────┘  ┌─────────────────┐ │
│         ↓           │  Final Price    │ │
│  ┌──────────────┐  │  Calculation    │ │
│  │  Product     │  └─────────────────┘ │
│  │  Addons      │                      │
│  │  (Assignment)│                      │
│  └──────────────┘                      │
└─────────────────────────────────────────┘
```

### Services
- **PriceCalculationService** - Handles all pricing logic
- **ValidationService** - Centralized validation
- **Elasticsearch Sync** - Search index management

---

## 🗄️ Database Structure

### Tables (9 Total)

#### 1. **products**
Core product information
```
- id, store_id, category_id
- name_en, name_ar
- description_en, description_ar
- base_price, search_keywords
- is_active, view_count, sales_count
- sort_order, metadata
- soft deletes, timestamps
```

#### 2. **product_images**
Product photos
```
- id, product_id
- image_path, is_primary
- sort_order, timestamps
```

#### 3. **option_groups**
Reusable option templates (Size, Color, etc.)
```
- id, name_en, name_ar
- type (size, weight, color, etc.)
- is_active, timestamps
```

#### 4. **option_values**
Specific choices (Small, Red, etc.)
```
- id, option_group_id
- value_en, value_ar
- sort_order, is_active
- timestamps
```

#### 5. **product_options**
Links products to option groups
```
- id, product_id, option_group_id
- is_required, sort_order
- timestamps
- UNIQUE(product_id, option_group_id)
```

#### 6. **product_option_values**
Pricing & stock for each variation
```
- id, product_option_id, option_value_id
- price_type (fixed|additional|percentage)
- price_value, stock_quantity
- is_available, timestamps
- UNIQUE(product_option_id, option_value_id)
```

#### 7. **addons**
Reusable extras
```
- id, store_id
- name_en, name_ar
- description_en, description_ar
- price, addon_category
- is_active, timestamps
```

#### 8. **product_addons**
Links products to addons
```
- id, product_id, addon_id
- is_available, sort_order
- timestamps
- UNIQUE(product_id, addon_id)
```

#### 9. **elasticsearch_sync_queue**
Search sync tracking
```
- id, entity_type, entity_id
- action (create|update|delete)
- synced_at, created_at
```

---

## 📥 Installation

### 1. Run Migrations
```bash
php artisan migrate
```

### 2. Seed Sample Data (Optional)
```bash
php artisan db:seed --class=ProductSystemSeeder
```

Creates:
- 4 Option Groups (Size, Weight, Packaging, Crust)
- 11 Option Values
- 4 Addons (Extra Cheese, Extra Sauce, Soft Drink, French Fries)
- 3 Sample Products

### 3. Configure Storage
```bash
php artisan storage:link
```

### 4. Set Permissions
Ensure `stores.*` permissions exist:
- stores.view
- stores.create
- stores.update
- stores.delete

---

## 💡 Core Concepts

### 1. **Option Groups vs Option Values**

**Option Group** = Category of choice (e.g., "Size")  
**Option Values** = Specific choices (e.g., "Small", "Medium", "Large")

```
Option Group: Size
├── Option Value: Small
├── Option Value: Medium
└── Option Value: Large

Option Group: Crust
├── Option Value: Thin
└── Option Value: Thick
```

### 2. **Product Options Assignment**

**Step 1:** Assign Option Group to Product
```
Product: Pizza + Option Group: Size → Product Option (ID: 1)
```

**Step 2:** Assign Values with Pricing
```
Product Option (1) + Small → Price: +$0 (additional)
Product Option (1) + Medium → Price: +$30 (additional)
Product Option (1) + Large → Price: +$50 (additional)
```

### 3. **Price Calculation**

**Example Product:** Pizza (Base: $100)

**Variations:**
- Small: $100 + $0 = **$100**
- Medium: $100 + $30 = **$130**
- Large: $100 + $50 = **$150**

**With Addons:**
- Large + Extra Cheese ($15) = **$165**
- Large + Extra Cheese + Soft Drink ($20) = **$185**

### 4. **Stock Management**

Stock is tracked at **variation level**, not product level:
```
Product: Pizza
├── Small: 100 units in stock
├── Medium: 80 units in stock
└── Large: 60 units in stock
```

When order placed: Stock deducted for specific variation.

---

## 🌐 API Endpoints

### Summary (47 Endpoints)

| Resource | Endpoints | Purpose |
|----------|-----------|---------|
| **Option Groups** | 7 | Manage option categories |
| **Option Values** | 6 | Manage option choices |
| **Products** | 13 | Full product CRUD + images |
| **Product Options** | 9 | Assign options with pricing |
| **Addons** | 7 | Manage add-ons |
| **Product Addons** | 5 | Assign add-ons to products |

**Base URL:** `/api/admin`  
**Authentication:** Bearer Token (JWT)  
**Permissions:** `stores.*`

**📚 Full API Documentation:** See `FRONTEND_API_GUIDE.md`

---

## 📖 Usage Examples

### Example 1: Create Pizza with Size Options

#### Step 1: Create Option Group
```http
POST /api/admin/option-groups
{
  "name_en": "Size",
  "name_ar": "الحجم",
  "type": "size"
}
```
→ Returns `option_group_id: 1`

#### Step 2: Create Size Values
```http
POST /api/admin/option-values
{
  "option_group_id": 1,
  "value_en": "Small",
  "value_ar": "صغير",
  "sort_order": 1
}
```
Repeat for Medium (2) and Large (3)

#### Step 3: Create Product
```http
POST /api/admin/products
{
  "store_id": 1,
  "category_id": 1,
  "name_en": "Margherita Pizza",
  "name_ar": "بيتزا مارغريتا",
  "base_price": 100.00
}
```
→ Returns `product_id: 1`

#### Step 4: Assign Option to Product
```http
POST /api/admin/product-options/assign-group
{
  "product_id": 1,
  "option_group_id": 1,
  "is_required": true
}
```
→ Returns `product_option_id: 1`

#### Step 5: Set Pricing
```http
POST /api/admin/product-options/assign-values
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
    },
    {
      "option_value_id": 3,
      "price_type": "additional",
      "price_value": 50,
      "stock_quantity": 60
    }
  ]
}
```

#### Step 6: Add Extra Cheese Addon
```http
POST /api/admin/addons
{
  "store_id": 1,
  "name_en": "Extra Cheese",
  "name_ar": "جبن إضافي",
  "price": 15.00,
  "addon_category": "extras"
}
```

```http
POST /api/admin/product-addons/assign
{
  "product_id": 1,
  "addon_ids": [1]
}
```

**Result:** Pizza with 3 size options + Extra Cheese addon

---

### Example 2: Olive Oil with Weight & Packaging

```http
# Create Weight option group
POST /option-groups
{ "name_en": "Weight", "type": "weight" }

# Create Packaging option group
POST /option-groups
{ "name_en": "Packaging", "type": "packaging" }

# Create product
POST /products
{
  "name_en": "Extra Virgin Olive Oil",
  "base_price": 80.00
}

# Assign both options
POST /product-options/assign-group
{ "product_id": 2, "option_group_id": 2 }

POST /product-options/assign-group
{ "product_id": 2, "option_group_id": 3 }

# Set weight pricing (250g, 500g, 1kg)
# Set packaging pricing (Bag, Glass Jar)
```

**Result:** Product with 2 independent option groups (Weight × Packaging = 6 variations)

---

## 📁 Files Structure

```
app/
├── Models/
│   ├── Product.php                 # Core product model
│   ├── ProductImage.php            # Image model
│   ├── OptionGroup.php             # Option groups
│   ├── OptionValue.php             # Option values
│   ├── ProductOption.php           # Product-option link
│   ├── ProductOptionValue.php      # Pricing & stock
│   ├── Addon.php                   # Addons
│   └── ProductAddon.php            # Product-addon link
│
├── Http/Controllers/Api/Admin/
│   ├── ProductController.php       # Product CRUD + images
│   ├── OptionGroupController.php   # Option group management
│   ├── OptionValueController.php   # Option value management
│   ├── ProductOptionController.php # Assign options + pricing
│   ├── AddonController.php         # Addon management
│   └── ProductAddonController.php  # Assign addons
│
└── Services/
    └── PriceCalculationService.php # Pricing logic

database/
├── migrations/
│   ├── 2024_10_08_000002_create_products_table.php
│   ├── 2024_10_08_000003_create_product_images_table.php
│   ├── 2024_10_08_000004_create_option_groups_table.php
│   ├── 2024_10_08_000004_create_option_values_table.php
│   ├── 2024_10_08_000005_create_product_options_table.php
│   ├── 2024_10_08_000006_create_product_option_values_table.php
│   ├── 2024_10_08_000007_create_addons_table.php
│   ├── 2024_10_08_000008_create_product_addons_table.php
│   └── 2024_10_08_000009_create_elasticsearch_sync_queue_table.php
│
└── seeders/
    └── ProductSystemSeeder.php     # Sample data

routes/
└── api/
    └── admin.php                   # All product routes (70 endpoints)
```

---

## 🔄 Workflows

### Customer Order Flow
```
1. View Product → Display base price
2. Select Options → Calculate real-time price
3. Select Addons → Add to total
4. Add to Cart → Validate stock
5. Checkout → Deduct stock
6. Order Complete → Increment sales_count
```

### Admin Product Creation Flow
```
1. Create Option Groups (reusable)
2. Add Option Values to groups
3. Create Addons (reusable)
4. Create Product
5. Upload Images
6. Assign Option Groups
7. Set Pricing for each value
8. Assign Addons
9. Publish (set active)
```

### Price Calculation Logic
```php
$finalPrice = $product->base_price;

foreach ($selectedOptions as $optionValue) {
    if ($optionValue->price_type === 'additional') {
        $finalPrice += $optionValue->price_value;
    } elseif ($optionValue->price_type === 'fixed') {
        $finalPrice = $optionValue->price_value;
    } elseif ($optionValue->price_type === 'percentage') {
        $finalPrice += ($product->base_price * $optionValue->price_value / 100);
    }
}

foreach ($selectedAddons as $addon) {
    $finalPrice += $addon->price;
}
```

---

## 🧪 Testing

### Using Postman

1. **Import Collection**
   - File: `Product_System_API.postman_collection.json`

2. **Import Environment**
   - File: `Product_System_Environment.postman_environment.json`

3. **Set Token**
   - Login → Copy token → Set in environment

4. **Run Tests**
   - Follow workflows in `POSTMAN_GUIDE.md`

### Sample Data

Run seeder to get:
- **Margherita Pizza** - Size options (Small/Medium/Large)
- **Olive Oil** - Weight (250g/500g/1kg) + Packaging (Bag/Glass)
- **Classic Burger** - Fixed price with addons only

---

## 📊 Key Features Summary

| Feature | Capability |
|---------|-----------|
| **Flexibility** | Any option type (size, weight, color, etc.) |
| **Pricing** | 3 strategies (fixed, additional, percentage) |
| **Stock** | Variation-level tracking |
| **Addons** | Unlimited reusable extras |
| **Images** | Multiple per product with ordering |
| **Languages** | English + Arabic throughout |
| **Search** | Elasticsearch integration |
| **Soft Delete** | Preserve order history |
| **Permissions** | Store-level access control |
| **Duplication** | Clone products with options |

---

## 🎯 Use Cases

✅ **Restaurant Menu** - Size, toppings, extras  
✅ **Clothing Store** - Size, color, material  
✅ **Electronics** - Storage, color, warranty  
✅ **Grocery** - Weight, packaging, brand  
✅ **Services** - Duration, features, support level  
✅ **Subscriptions** - Plan tier, billing cycle, addons  

---

## 📚 Additional Documentation

- **Frontend Integration:** `FRONTEND_API_GUIDE.md`
- **Postman Guide:** `POSTMAN_GUIDE.md`
- **API Reference:** `API_DOCUMENTATION.md`

---

## 🚀 Quick Start

```bash
# 1. Run migrations
php artisan migrate

# 2. Seed sample data
php artisan db:seed --class=ProductSystemSeeder

# 3. Test in Postman
# Import collection & environment
# Set your JWT token
# Try: GET /api/admin/products

# 4. View sample product
# GET /api/admin/products/1
```

---

## ✨ System Highlights

- **70 API Endpoints** - Complete CRUD for all entities
- **9 Database Tables** - Normalized structure
- **8 Eloquent Models** - Full relationships
- **6 Controllers** - Organized by resource
- **1 Service Layer** - Pricing calculations
- **3 Price Strategies** - Maximum flexibility
- **Bilingual** - EN/AR throughout
- **Permission-Based** - Secure access control

---

**Built with Laravel 11 | Spatie Permissions | JWT Auth | Elasticsearch Ready**

