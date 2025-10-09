# مخطط قاعدة البيانات ونموذج البيانات

## جداول قاعدة البيانات

### 1. products (المنتجات)
جدول معلومات المنتج الرئيسية.

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

### 2. product_images (صور المنتجات)
صور متعددة لكل منتج.

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

### 3. option_groups (مجموعات الخيارات)
فئات الخيارات القابلة لإعادة الاستخدام (الحجم، الوزن، التغليف، إلخ).

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

### 4. option_values (قيم الخيارات)
قيم محددة ضمن مجموعات الخيارات.

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

### 5. product_options (خيارات المنتجات)
ربط المنتجات بمجموعات الخيارات مع التكوين.

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

### 6. product_option_values (قيم خيارات المنتجات)
القيم المتاحة لخيارات المنتج مع التسعير والمخزون.

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

### 7. addons (الإضافات)
الإضافات القابلة لإعادة الاستخدام (الإضافات).

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

### 8. product_addons (إضافات المنتجات)
ربط المنتجات بالإضافات المتاحة.

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

## علاقات الكيانات

```
المتاجر (1) ──────< (N) المنتجات
المتاجر (1) ──────< (N) الفئات
المتاجر (1) ──────< (N) الإضافات
الفئات (1) ──────< (N) المنتجات
المنتجات (1) ────< (N) صور_المنتجات
المنتجات (N) ────< (N) مجموعات_الخيارات (من خلال product_options)
المنتجات (N) ────< (N) الإضافات (من خلال product_addons)
مجموعات_الخيارات (1) < (N) قيم_الخيارات
خيارات_المنتجات (1) < (N) قيم_خيارات_المنتجات
قيم_الخيارات (1) ─< (N) قيم_خيارات_المنتجات
```

## علاقات نماذج Laravel

### نموذج المنتج (Product)
```php
// ينتمي إلى
$product->store()          // BelongsTo Store
$product->category()       // BelongsTo Category

// لديه العديد من
$product->images()         // HasMany ProductImage
$product->productOptions() // HasMany ProductOption

// علاقة كثير لكثير
$product->addons()         // BelongsToMany Addon through product_addons
```

### نموذج مجموعة الخيارات (OptionGroup)
```php
// لديه العديد من
$optionGroup->values()     // HasMany OptionValue
$optionGroup->productOptions() // HasMany ProductOption
```

### نموذج خيار المنتج (ProductOption)
```php
// ينتمي إلى
$productOption->product()        // BelongsTo Product
$productOption->optionGroup()    // BelongsTo OptionGroup

// لديه العديد من
$productOption->productOptionValues() // HasMany ProductOptionValue
```

### نموذج الإضافة (Addon)
```php
// ينتمي إلى
$addon->store()            // BelongsTo Store

// علاقة كثير لكثير
$addon->products()         // BelongsToMany Product through product_addons
```

## منطق حساب السعر

### المعادلة
```
السعر النهائي = (السعر الأساسي + معدل الخيار + مجموع الإضافات) × الكمية

حيث معدل الخيار هو:
- ثابت: استخدم سعر قيمة الخيار مباشرة (تجاهل السعر الأساسي)
- إضافي: أضف سعر قيمة الخيار إلى السعر الأساسي
- نسبة مئوية: السعر الأساسي × (1 + سعر قيمة الخيار / 100)
```

### أمثلة الحسابات

**مثال 1: منتج أساسي مع الحجم**
- المنتج: قميص (السعر الأساسي: 100)
- الخيار: الحجم = كبير (إضافي: +20)
- الكمية: 2
- **الإجمالي: (100 + 20) × 2 = 240**

**مثال 2: منتج مع خيارات متعددة**
- المنتج: قهوة (السعر الأساسي: 50)
- الخيار 1: الحجم = كبير (إضافي: +15)
- الخيار 2: السكر = إضافي (إضافي: +5)
- الكمية: 1
- **الإجمالي: (50 + 15 + 5) × 1 = 70**

**مثال 3: منتج مع خيار سعر ثابت**
- المنتج: بيتزا (السعر الأساسي: 100)
- الخيار: الحجم = عائلي (ثابت: 150)
- الإضافة: جبن إضافي (+20)
- الكمية: 1
- **الإجمالي: (150 + 20) × 1 = 170**

**مثال 4: منتج مع نسبة مئوية**
- المنتج: عنصر مخصص (السعر الأساسي: 200)
- الخيار: جودة ممتازة (نسبة مئوية: 25%)
- الكمية: 1
- **الإجمالي: 200 × 1.25 = 250**

## إدارة المخزون

### قواعد تتبع المخزون
1. يتم تتبع المخزون على مستوى `product_option_values`
2. عند وضع الطلب، يتم الخصم من `stock_quantity`
3. تحقق من `is_available` و `stock_quantity > 0` قبل السماح بالطلبات
4. المنتجات بدون خيارات لا تستخدم نظام المخزون (تحسين مستقبلي)

### تدفق تحديث المخزون
```
1. المسؤول يحدد المخزون لـ "قميص كبير" = 100
2. العميل يطلب 3 "قمصان كبيرة"
3. النظام يتحقق: المخزون >= الكمية
4. النظام يخصم: 100 - 3 = 97
5. النظام يحدث product_option_values.stock_quantity = 97
```

## استراتيجية الفهارس

### فهارس الأداء
- **products**: store_id, category_id, is_active, deleted_at
- **product_images**: product_id, is_primary
- **option_groups**: type, is_active
- **option_values**: option_group_id, is_active
- **product_options**: product_id, option_group_id, unique(product_id, option_group_id)
- **product_option_values**: product_option_id, option_value_id, unique(product_option_id, option_value_id)
- **addons**: store_id, addon_category, is_active
- **product_addons**: product_id, addon_id, unique(product_id, addon_id)

## قواعد سلامة البيانات

1. **الحذف المتسلسل**:
   - عند حذف المتجر → متسلسل إلى المنتجات، الإضافات
   - عند حذف المنتج → متسلسل إلى product_images، product_options، product_addons
   - عند حذف option_group → متسلسل إلى option_values، product_options
   
2. **الحذف الناعم**:
   - تستخدم المنتجات الحذف الناعم (deleted_at) للحفاظ على سجل الطلبات
   
3. **قيود التفرد**:
   - product_options: unique(product_id, option_group_id) - منع تعيينات الخيارات المكررة
   - product_option_values: unique(product_option_id, option_value_id)
   - product_addons: unique(product_id, addon_id)

4. **قيود المفاتيح الخارجية**:
   - جميع المفاتيح الخارجية لها إجراءات ON DELETE مناسبة
   - تقييد الحذف على الفئات (يجب إعادة تعيين المنتجات أولاً)

## اعتبارات قابلية التوسع

1. **أنواع الخيارات الديناميكية**: حقل `type` في `option_groups` هو VARCHAR، يسمح بأي نوع خيار مستقبلي دون تغييرات في المخطط

2. **التسعير المرن**: ثلاث استراتيجيات تسعير (ثابت، إضافي، نسبة مئوية) تغطي معظم حالات الاستخدام

3. **مكونات قابلة لإعادة الاستخدام**: مجموعات الخيارات والإضافات قابلة لإعادة الاستخدام عبر المنتجات، مما يقلل من التكرار

4. **تحسين الاستعلام**: الفهارس المناسبة على الأعمدة المستعلم عنها بشكل متكرر تضمن الأداء مع مجموعات البيانات الكبيرة

5. **دعم JSON**: إذا لزم الأمر، يمكن أن يكون لـ `option_groups` أو `addons` عمود JSON لبيانات وصفية إضافية دون تغييرات في المخطط

## الاستعداد لـ Elasticsearch (تحسين مستقبلي)

### حقول إضافية لتحسين البحث

إضافة هذه الحقول إلى جدول `products` للاستعداد لـ Elasticsearch:

```sql
ALTER TABLE products ADD COLUMN search_keywords TEXT AFTER description_ar;
ALTER TABLE products ADD COLUMN view_count INT DEFAULT 0 AFTER is_active;
ALTER TABLE products ADD COLUMN sales_count INT DEFAULT 0 AFTER view_count;
ALTER TABLE products ADD COLUMN metadata JSON AFTER sales_count;
ALTER TABLE products ADD INDEX idx_view_count (view_count);
ALTER TABLE products ADD INDEX idx_sales_count (sales_count);
```

### جدول قائمة انتظار مزامنة Elasticsearch

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

### هيكل وثيقة Elasticsearch الموصى به

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

### أحداث النموذج للمزامنة التلقائية

إضافة إلى نموذج `Product.php`:

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

### فوائد Elasticsearch

1. **هيكل غير معياري**: جميع البيانات ذات الصلة في وثيقة واحدة للاسترجاع السريع
2. **بحث ثنائي اللغة**: فهرسة كلتا اللغتين للبحث متعدد اللغات
3. **تصفية نطاق السعر**: الأسعار الدنيا/القصوى محسوبة مسبقاً لاستعلامات النطاق
4. **البحث المصنف**: الخيارات والإضافات جاهزة للتصفية
5. **تصنيف الصلة**: view_count و sales_count لتصنيف الشعبية
6. **المزامنة التلقائية**: أحداث النموذج تضع التغييرات في قائمة الانتظار للمزامنة في الخلفية

### ملاحظات التنفيذ

- تصميم قاعدة البيانات الحالي متوافق بالفعل مع Elasticsearch
- لا حاجة لتغييرات هيكلية في الجداول الموجودة
- الحقول الإضافية اختيارية وللتحسين فقط
- قائمة انتظار المزامنة تسمح بالفهرسة غير المتزامنة دون حظر الطلبات
