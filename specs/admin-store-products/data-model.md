# Database Schema & Data Model

## Database Tables

### 1. products
Main product information table.

```sql
CREATE TABLE products (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    store_id BIGINT UNSIGNED NOT NULL,
    category_id BIGINT UNSIGNED NOT NULL,
    name_en VARCHAR(255) NOT NULL,
    name_ar VARCHAR(255) NOT NULL,
    description_en TEXT,
    description_ar TEXT,
    search_keywords TEXT COMMENT 'For Elasticsearch optimization',
    base_price DECIMAL(10, 2) NOT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    view_count INT DEFAULT 0 COMMENT 'For popularity ranking',
    sales_count INT DEFAULT 0 COMMENT 'For popularity ranking',
    metadata JSON COMMENT 'For flexible future data',
    sort_order INT DEFAULT 0,
    deleted_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (store_id) REFERENCES stores(id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE RESTRICT,
    INDEX idx_store_id (store_id),
    INDEX idx_category_id (category_id),
    INDEX idx_is_active (is_active),
    INDEX idx_view_count (view_count),
    INDEX idx_sales_count (sales_count),
    INDEX idx_deleted_at (deleted_at),
    FULLTEXT idx_search_keywords (search_keywords)
);
```

### 2. product_images
Multiple images per product.

```sql
CREATE TABLE product_images (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    product_id BIGINT UNSIGNED NOT NULL,
    image_path VARCHAR(500) NOT NULL,
    is_primary BOOLEAN DEFAULT FALSE,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    INDEX idx_product_id (product_id),
    INDEX idx_is_primary (is_primary)
);
```

### 3. option_groups
Reusable option categories (Size, Weight, Packaging, etc.).

```sql
CREATE TABLE option_groups (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    name_en VARCHAR(255) NOT NULL,
    name_ar VARCHAR(255) NOT NULL,
    type VARCHAR(50) NOT NULL COMMENT 'size, weight, packaging, color, flavor, etc.',
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_type (type),
    INDEX idx_is_active (is_active)
);
```

### 4. option_values
Specific values within option groups.

```sql
CREATE TABLE option_values (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    option_group_id BIGINT UNSIGNED NOT NULL,
    value_en VARCHAR(255) NOT NULL,
    value_ar VARCHAR(255) NOT NULL,
    sort_order INT DEFAULT 0,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (option_group_id) REFERENCES option_groups(id) ON DELETE CASCADE,
    INDEX idx_option_group_id (option_group_id),
    INDEX idx_is_active (is_active)
);
```

### 5. product_options
Links products to option groups with configuration.

```sql
CREATE TABLE product_options (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    product_id BIGINT UNSIGNED NOT NULL,
    option_group_id BIGINT UNSIGNED NOT NULL,
    is_required BOOLEAN DEFAULT FALSE,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    FOREIGN KEY (option_group_id) REFERENCES option_groups(id) ON DELETE CASCADE,
    UNIQUE KEY unique_product_option (product_id, option_group_id),
    INDEX idx_product_id (product_id),
    INDEX idx_option_group_id (option_group_id)
);
```

### 6. product_option_values
Available option values for products with pricing and stock.

```sql
CREATE TABLE product_option_values (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    product_option_id BIGINT UNSIGNED NOT NULL,
    option_value_id BIGINT UNSIGNED NOT NULL,
    price_type ENUM('fixed', 'additional', 'percentage') DEFAULT 'additional',
    price_value DECIMAL(10, 2) DEFAULT 0.00,
    stock_quantity INT DEFAULT 0,
    is_available BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (product_option_id) REFERENCES product_options(id) ON DELETE CASCADE,
    FOREIGN KEY (option_value_id) REFERENCES option_values(id) ON DELETE CASCADE,
    UNIQUE KEY unique_product_option_value (product_option_id, option_value_id),
    INDEX idx_product_option_id (product_option_id),
    INDEX idx_option_value_id (option_value_id),
    INDEX idx_is_available (is_available)
);
```

### 7. addons
Reusable add-ons (extras).

```sql
CREATE TABLE addons (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    store_id BIGINT UNSIGNED NOT NULL,
    name_en VARCHAR(255) NOT NULL,
    name_ar VARCHAR(255) NOT NULL,
    description_en TEXT,
    description_ar TEXT,
    price DECIMAL(10, 2) NOT NULL,
    addon_category VARCHAR(50) COMMENT 'extras, sides, drinks, etc.',
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (store_id) REFERENCES stores(id) ON DELETE CASCADE,
    INDEX idx_store_id (store_id),
    INDEX idx_addon_category (addon_category),
    INDEX idx_is_active (is_active)
);
```

### 8. product_addons
Links products to available add-ons.

```sql
CREATE TABLE product_addons (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    product_id BIGINT UNSIGNED NOT NULL,
    addon_id BIGINT UNSIGNED NOT NULL,
    is_available BOOLEAN DEFAULT TRUE,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    FOREIGN KEY (addon_id) REFERENCES addons(id) ON DELETE CASCADE,
    UNIQUE KEY unique_product_addon (product_id, addon_id),
    INDEX idx_product_id (product_id),
    INDEX idx_addon_id (addon_id)
);
```

## Entity Relationships

```
stores (1) ──────< (N) products
stores (1) ──────< (N) categories
stores (1) ──────< (N) addons
categories (1) ──< (N) products
products (1) ────< (N) product_images
products (N) ────< (N) option_groups (through product_options)
products (N) ────< (N) addons (through product_addons)
option_groups (1) < (N) option_values
product_options (1) < (N) product_option_values
option_values (1) ─< (N) product_option_values
```

## Laravel Model Relationships

### Product Model
```php
// Belongs to
$product->store()          // BelongsTo Store
$product->category()       // BelongsTo Category

// Has many
$product->images()         // HasMany ProductImage
$product->productOptions() // HasMany ProductOption

// Many to many
$product->addons()         // BelongsToMany Addon through product_addons
```

### OptionGroup Model
```php
// Has many
$optionGroup->values()     // HasMany OptionValue
$optionGroup->productOptions() // HasMany ProductOption
```

### ProductOption Model
```php
// Belongs to
$productOption->product()        // BelongsTo Product
$productOption->optionGroup()    // BelongsTo OptionGroup

// Has many
$productOption->productOptionValues() // HasMany ProductOptionValue
```

### Addon Model
```php
// Belongs to
$addon->store()            // BelongsTo Store

// Many to many
$addon->products()         // BelongsToMany Product through product_addons
```

## Price Calculation Logic

### Formula
```
Final Price = (Base Price + Option Modifier + Sum of Add-ons) × Quantity

Where Option Modifier is:
- Fixed: Use option value price directly (ignore base price)
- Additional: Add option value price to base price
- Percentage: Base price × (1 + option value price / 100)
```

### Example Calculations

**Example 1: Basic Product with Size**
- Product: T-Shirt (base price: 100)
- Option: Size = Large (additional: +20)
- Quantity: 2
- **Total: (100 + 20) × 2 = 240**

**Example 2: Product with Multiple Options**
- Product: Coffee (base price: 50)
- Option 1: Size = Large (additional: +15)
- Option 2: Sugar = Extra (additional: +5)
- Quantity: 1
- **Total: (50 + 15 + 5) × 1 = 70**

**Example 3: Product with Fixed Price Option**
- Product: Pizza (base price: 100)
- Option: Size = Family (fixed: 150)
- Add-on: Extra Cheese (+20)
- Quantity: 1
- **Total: (150 + 20) × 1 = 170**

**Example 4: Product with Percentage**
- Product: Custom Item (base price: 200)
- Option: Premium Quality (percentage: 25%)
- Quantity: 1
- **Total: 200 × 1.25 = 250**

## Stock Management

### Stock Tracking Rules
1. Stock is tracked at `product_option_values` level
2. When order is placed, deduct from `stock_quantity`
3. Check `is_available` AND `stock_quantity > 0` before allowing orders
4. Products without options don't use stock system (future enhancement)

### Stock Update Flow
```
1. Admin sets stock for "Large T-shirt" = 100
2. Customer orders 3 "Large T-shirts"
3. System checks: stock >= quantity
4. System deducts: 100 - 3 = 97
5. System updates product_option_values.stock_quantity = 97
```

## Indexes Strategy

### Performance Indexes
- **products**: store_id, category_id, is_active, deleted_at
- **product_images**: product_id, is_primary
- **option_groups**: type, is_active
- **option_values**: option_group_id, is_active
- **product_options**: product_id, option_group_id, unique(product_id, option_group_id)
- **product_option_values**: product_option_id, option_value_id, unique(product_option_id, option_value_id)
- **addons**: store_id, addon_category, is_active
- **product_addons**: product_id, addon_id, unique(product_id, addon_id)

## Data Integrity Rules

1. **Cascade Deletes**:
   - When store deleted → cascade to products, addons
   - When product deleted → cascade to product_images, product_options, product_addons
   - When option_group deleted → cascade to option_values, product_options
   
2. **Soft Deletes**:
   - Products use soft deletes (deleted_at) to preserve order history
   
3. **Unique Constraints**:
   - product_options: unique(product_id, option_group_id) - prevent duplicate option assignments
   - product_option_values: unique(product_option_id, option_value_id)
   - product_addons: unique(product_id, addon_id)

4. **Foreign Key Constraints**:
   - All foreign keys have proper ON DELETE actions
   - Restrict deletes on categories (must reassign products first)

## Scalability Considerations

1. **Dynamic Option Types**: The `type` field in `option_groups` is a VARCHAR, allowing any future option type without schema changes

2. **Flexible Pricing**: Three pricing strategies (fixed, additional, percentage) cover most use cases

3. **Reusable Components**: Option groups and add-ons are reusable across products, reducing redundancy

4. **Query Optimization**: Proper indexes on frequently queried columns ensure performance with large datasets

5. **JSON Support**: If needed, `option_groups` or `addons` could have a JSON column for additional metadata without schema changes

## Elasticsearch Preparation (Future Enhancement)

### Additional Fields for Search Optimization

Add these fields to the `products` table for Elasticsearch readiness:

```sql
ALTER TABLE products ADD COLUMN search_keywords TEXT AFTER description_ar;
ALTER TABLE products ADD COLUMN view_count INT DEFAULT 0 AFTER is_active;
ALTER TABLE products ADD COLUMN sales_count INT DEFAULT 0 AFTER view_count;
ALTER TABLE products ADD COLUMN metadata JSON AFTER sales_count;
ALTER TABLE products ADD INDEX idx_view_count (view_count);
ALTER TABLE products ADD INDEX idx_sales_count (sales_count);
```

### Elasticsearch Sync Queue Table

```sql
CREATE TABLE elasticsearch_sync_queue (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    entity_type VARCHAR(50) NOT NULL,
    entity_id BIGINT UNSIGNED NOT NULL,
    action ENUM('create', 'update', 'delete') NOT NULL,
    synced_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_synced (synced_at),
    INDEX idx_entity (entity_type, entity_id)
);
```

### Recommended Elasticsearch Document Structure

```json
{
  "id": 1,
  "store_id": 1,
  "store": {"id": 1, "name_en": "Store Name", "name_ar": "اسم المتجر"},
  "category_id": 1,
  "category": {"id": 1, "name_en": "Category", "name_ar": "الفئة"},
  "name_en": "Product Name",
  "name_ar": "اسم المنتج",
  "description_en": "Description",
  "description_ar": "الوصف",
  "base_price": 100.00,
  "min_price": 100.00,
  "max_price": 150.00,
  "is_active": true,
  "images": ["url1", "url2"],
  "primary_image": "url1",
  "options": [
    {
      "group_id": 1,
      "group_name_en": "Size",
      "group_name_ar": "الحجم",
      "is_required": false,
      "values": [
        {"id": 1, "value_en": "Small", "value_ar": "صغير", "price": 100.00},
        {"id": 2, "value_en": "Large", "value_ar": "كبير", "price": 120.00}
      ]
    }
  ],
  "addons": [
    {"id": 1, "name_en": "Extra Cheese", "name_ar": "جبن إضافي", "price": 15.00}
  ],
  "search_keywords": "pizza food italian",
  "view_count": 150,
  "sales_count": 45,
  "created_at": "2024-10-07T10:00:00Z",
  "updated_at": "2024-10-07T12:00:00Z"
}
```

### Model Events for Auto-Sync

Add to `Product.php` model:

```php
protected static function boot()
{
    parent::boot();
    
    static::created(function ($product) {
        DB::table('elasticsearch_sync_queue')->insert([
            'entity_type' => 'product',
            'entity_id' => $product->id,
            'action' => 'create',
        ]);
    });
    
    static::updated(function ($product) {
        DB::table('elasticsearch_sync_queue')->insert([
            'entity_type' => 'product',
            'entity_id' => $product->id,
            'action' => 'update',
        ]);
    });
    
    static::deleted(function ($product) {
        DB::table('elasticsearch_sync_queue')->insert([
            'entity_type' => 'product',
            'entity_id' => $product->id,
            'action' => 'delete',
        ]);
    });
}
```

### Benefits for Elasticsearch

1. **Denormalized Structure**: All related data in one document for fast retrieval
2. **Bilingual Search**: Both languages indexed for multi-language search
3. **Price Range Filtering**: Min/max prices pre-calculated for range queries
4. **Faceted Search**: Options and addons ready for filtering
5. **Relevance Scoring**: view_count and sales_count for popularity ranking
6. **Auto-Sync**: Model events queue changes for background sync

### Implementation Notes

- The current database design is already Elasticsearch-compatible
- No structural changes needed to existing tables
- Additional fields are optional and for optimization only
- Sync queue allows asynchronous indexing without blocking requests
