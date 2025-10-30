# Data Model: User Checkout

**Feature**: user-check-out  
**Date**: 2025-10-30  
**Status**: Complete

---

## Overview

The checkout feature requires 4 new database tables to store orders and their associated items, options, and addons. The design uses JSON snapshots to preserve data at order time, ensuring historical accuracy even if source data changes or is deleted.

---

## Entity Relationship Diagram

```
User (existing) ──┐
                  │
                  ├──< Cart (existing)
                  │
                  └──< Order
                       │
                       ├──< OrderItem
                       │    │
                       │    ├──< OrderItemOption
                       │    │
                       │    └──< OrderItemAddon
                       │
                       └──> Store (existing)
                       └──> UserAddress (existing)
```

---

## Tables

### 1. orders

**Purpose**: Main order record with payment and delivery information

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | BIGINT UNSIGNED | PK, AUTO_INCREMENT | Primary key |
| order_number | VARCHAR(50) | UNIQUE, INDEXED | Format: ORD-YYYYMMDD-XXXXX |
| user_id | BIGINT UNSIGNED | FK → users, INDEXED | Customer who placed order |
| store_id | BIGINT UNSIGNED | FK → stores, INDEXED | Store fulfilling order |
| address_id | BIGINT UNSIGNED | FK → user_addresses, NULLABLE | Reference to delivery address |
| address_snapshot | JSON | NOT NULL | Complete address at order time |
| payment_method | ENUM | 'cash', 'online' | Payment method selected |
| payment_status | ENUM | 'pending', 'paid', 'failed', 'refunded' | Payment state |
| payment_reference | VARCHAR(255) | NULLABLE, INDEXED | Gateway reference for online payments |
| order_status | ENUM | See statuses below | Current order state |
| subtotal | DECIMAL(10,2) | NOT NULL | Sum of all items |
| delivery_fee | DECIMAL(10,2) | DEFAULT 0.00 | Delivery charge |
| tax | DECIMAL(10,2) | DEFAULT 0.00 | Tax amount (placeholder) |
| total | DECIMAL(10,2) | NOT NULL | subtotal + delivery_fee + tax |
| notes | TEXT | NULLABLE | Customer notes (max 500 chars) |
| created_at | TIMESTAMP | NOT NULL | Order placement time |
| updated_at | TIMESTAMP | NOT NULL | Last modification time |

**Indexes**:
- PRIMARY KEY (id)
- UNIQUE KEY (order_number)
- INDEX (user_id)
- INDEX (store_id)
- INDEX (order_status)
- INDEX (payment_status)
- INDEX (payment_reference)
- INDEX (created_at)

**Order Status Values**:
- `pending`: Cash order awaiting vendor confirmation
- `pending_payment`: Online order awaiting payment
- `confirmed`: Vendor accepted order
- `preparing`: Being prepared
- `ready`: Ready for pickup/delivery
- `out_for_delivery`: With delivery person
- `delivered`: Successfully completed
- `cancelled`: Cancelled

**Address Snapshot Structure** (JSON):
```json
{
  "id": 1,
  "address_type": "apartment",
  "building_name": "Tower A",
  "apartment_number": "501",
  "floor_number": "5",
  "street_name": "Main Street",
  "landmark": "Near City Mall",
  "phone": "+966501234567",
  "latitude": "24.7136",
  "longitude": "46.6753"
}
```

---

### 2. order_items

**Purpose**: Individual products in the order with pricing snapshot

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | BIGINT UNSIGNED | PK, AUTO_INCREMENT | Primary key |
| order_id | BIGINT UNSIGNED | FK → orders CASCADE, INDEXED | Parent order |
| product_id | BIGINT UNSIGNED | FK → products | Product reference |
| product_snapshot | JSON | NOT NULL | Product details at order time |
| quantity | INTEGER UNSIGNED | NOT NULL, >= 1 | Number of items |
| unit_price | DECIMAL(10,2) | NOT NULL | Price per item (includes options/addons) |
| total_price | DECIMAL(10,2) | NOT NULL | unit_price × quantity |
| created_at | TIMESTAMP | NOT NULL | Item added time |
| updated_at | TIMESTAMP | NOT NULL | Last modification time |

**Indexes**:
- PRIMARY KEY (id)
- INDEX (order_id)
- INDEX (product_id)

**Product Snapshot Structure** (JSON):
```json
{
  "id": 1,
  "name_en": "Margherita Pizza",
  "name_ar": "بيتزا مارجريتا",
  "description_en": "Classic pizza with tomato and mozzarella",
  "description_ar": "بيتزا كلاسيكية مع الطماطم والموتزاريلا",
  "base_price": "45.00",
  "image_url": "https://example.com/images/pizza.jpg",
  "category_name_en": "Pizzas",
  "category_name_ar": "بيتزا"
}
```

---

### 3. order_item_options

**Purpose**: Selected product options (size, color, etc.) with pricing

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | BIGINT UNSIGNED | PK, AUTO_INCREMENT | Primary key |
| order_item_id | BIGINT UNSIGNED | FK → order_items CASCADE, INDEXED | Parent order item |
| option_snapshot | JSON | NOT NULL | Complete option details |
| created_at | TIMESTAMP | NOT NULL | Option added time |

**Indexes**:
- PRIMARY KEY (id)
- INDEX (order_item_id)

**Option Snapshot Structure** (JSON):
```json
{
  "option_group_id": 1,
  "option_group_name_en": "Size",
  "option_group_name_ar": "الحجم",
  "option_value_id": 3,
  "option_value_name_en": "Large",
  "option_value_name_ar": "كبير",
  "price_type": "percentage",
  "price_value": "20.00",
  "calculated_price": "9.00"
}
```

---

### 4. order_item_addons

**Purpose**: Selected addons (extra cheese, sauce, etc.) with quantities

| Column | Type | Constraints | Description |
|--------|------|-------------|-------------|
| id | BIGINT UNSIGNED | PK, AUTO_INCREMENT | Primary key |
| order_item_id | BIGINT UNSIGNED | FK → order_items CASCADE, INDEXED | Parent order item |
| addon_snapshot | JSON | NOT NULL | Complete addon details |
| quantity | INTEGER UNSIGNED | NOT NULL, >= 1 | Number of this addon |
| unit_price | DECIMAL(10,2) | NOT NULL | Price per addon |
| total_price | DECIMAL(10,2) | NOT NULL | unit_price × quantity |
| created_at | TIMESTAMP | NOT NULL | Addon added time |

**Indexes**:
- PRIMARY KEY (id)
- INDEX (order_item_id)

**Addon Snapshot Structure** (JSON):
```json
{
  "id": 3,
  "name_en": "Extra Cheese",
  "name_ar": "جبن إضافي",
  "description_en": "Additional mozzarella cheese",
  "description_ar": "جبن موتزاريلا إضافي",
  "price": "5.00"
}
```

---

## Relationships

### Order Relationships
```php
// Order.php
public function user() {
    return $this->belongsTo(User::class);
}

public function store() {
    return $this->belongsTo(Store::class);
}

public function address() {
    return $this->belongsTo(UserAddress::class);
}

public function items() {
    return $this->hasMany(OrderItem::class);
}
```

### OrderItem Relationships
```php
// OrderItem.php
public function order() {
    return $this->belongsTo(Order::class);
}

public function product() {
    return $this->belongsTo(Product::class);
}

public function options() {
    return $this->hasMany(OrderItemOption::class);
}

public function addons() {
    return $this->hasMany(OrderItemAddon::class);
}
```

### OrderItemOption Relationships
```php
// OrderItemOption.php
public function orderItem() {
    return $this->belongsTo(OrderItem::class);
}
```

### OrderItemAddon Relationships
```php
// OrderItemAddon.php
public function orderItem() {
    return $this->belongsTo(OrderItem::class);
}
```

---

## Validation Rules

### Order Validation
```php
[
    'address_id' => 'required|exists:user_addresses,id',
    'payment_method' => 'required|in:cash,online',
    'notes' => 'nullable|string|max:500',
]
```

### Business Rules
1. **Order Creation**:
   - User must be authenticated
   - Cart must not be empty
   - All cart items must be from same store
   - Selected address must belong to user
   - All products must be active
   - Store must be approved

2. **Order Number Generation**:
   - Format: `ORD-YYYYMMDD-XXXXX`
   - XXXXX is sequential within the day (00001-99999)
   - Must be unique across all orders

3. **Price Calculation**:
   ```
   item_unit_price = product.base_price + sum(option_prices) + sum(addon_prices)
   item_total_price = item_unit_price × quantity
   order_subtotal = sum(all_item_total_prices)
   order_total = subtotal + delivery_fee + tax
   ```

4. **Payment Status Rules**:
   - Cash orders: `payment_status = 'pending'`, `order_status = 'pending'`
   - Online orders: `payment_status = 'pending'`, `order_status = 'pending_payment'`
   - After payment: `payment_status = 'paid'`, `order_status = 'confirmed'`

5. **Cancellation Rules**:
   - User can cancel if status is `pending` or `pending_payment`
   - Cannot cancel after `confirmed`
   - Vendor/Admin can cancel at any time with reason

---

## State Transitions

### Cash Payment Flow
```
pending → confirmed → preparing → ready → out_for_delivery → delivered
   ↓
cancelled
```

### Online Payment Flow
```
pending_payment → (payment confirmed) → confirmed → preparing → ready → out_for_delivery → delivered
   ↓                                        ↓
failed                                  cancelled
```

### Status Change Permissions
| Status | User | Vendor | Delivery | Admin |
|--------|------|--------|----------|-------|
| pending → confirmed | ❌ | ✅ | ❌ | ✅ |
| pending → cancelled | ✅ | ✅ | ❌ | ✅ |
| confirmed → preparing | ❌ | ✅ | ❌ | ✅ |
| preparing → ready | ❌ | ✅ | ❌ | ✅ |
| ready → out_for_delivery | ❌ | ✅ | ✅ | ✅ |
| out_for_delivery → delivered | ❌ | ❌ | ✅ | ✅ |
| any → cancelled | ❌ | ✅ | ❌ | ✅ |

---

## Migration Order

Execute migrations in this order to satisfy foreign key constraints:

1. `create_orders_table` - Main order table
2. `create_order_items_table` - Depends on orders
3. `create_order_item_options_table` - Depends on order_items
4. `create_order_item_addons_table` - Depends on order_items

---

## Computed Attributes

### Order Model
```php
// Order.php
public function getItemCountAttribute() {
    return $this->items->count();
}

public function getTotalItemsQuantityAttribute() {
    return $this->items->sum('quantity');
}

public function getStatusLabelAttribute() {
    return LocalizationService::getMessage("order.status.{$this->order_status}");
}

public function getPaymentStatusLabelAttribute() {
    return LocalizationService::getMessage("order.payment_status.{$this->payment_status}");
}
```

### OrderItem Model
```php
// OrderItem.php
public function getProductNameAttribute() {
    $locale = LocalizationService::getCurrentLocale();
    return $this->product_snapshot["name_{$locale}"] ?? $this->product_snapshot['name_en'];
}

public function getFormattedTotalAttribute() {
    return number_format($this->total_price, 2) . ' SAR';
}
```

---

## Indexes Strategy

### Performance Indexes
- `orders.order_number` - Unique lookup
- `orders.user_id` - User's order history
- `orders.store_id` - Store's orders
- `orders.order_status` - Filter by status
- `orders.created_at` - Date range queries
- `orders.payment_reference` - Payment gateway lookups

### Query Optimization
```sql
-- Get user's orders
SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC;

-- Get store's pending orders
SELECT * FROM orders WHERE store_id = ? AND order_status = 'pending';

-- Find order by number
SELECT * FROM orders WHERE order_number = ?;

-- Payment webhook lookup
SELECT * FROM orders WHERE payment_reference = ?;
```

---

## Data Integrity

### Foreign Key Constraints
- `orders.user_id` → `users.id` (RESTRICT - cannot delete user with orders)
- `orders.store_id` → `stores.id` (RESTRICT - cannot delete store with orders)
- `orders.address_id` → `user_addresses.id` (SET NULL - address can be deleted)
- `order_items.order_id` → `orders.id` (CASCADE - delete items with order)
- `order_items.product_id` → `products.id` (RESTRICT - keep product reference)
- `order_item_options.order_item_id` → `order_items.id` (CASCADE)
- `order_item_addons.order_item_id` → `order_items.id` (CASCADE)

### Unique Constraints
- `orders.order_number` - Prevent duplicate order numbers

### Check Constraints (Application Level)
- `quantity >= 1` - At least one item
- `unit_price >= 0` - Non-negative prices
- `total_price >= 0` - Non-negative totals
- `subtotal >= 0` - Non-negative subtotal
- `delivery_fee >= 0` - Non-negative delivery fee
- `tax >= 0` - Non-negative tax

---

## Activity Logging

Log these events using Spatie Activity Log:

### Order Events
- **created**: Order placed (user_id, store_id, payment_method, total)
- **updated**: Order modified (changed fields)
- **deleted**: Order deleted (rare, admin only)

### Status Change Events
- **status_changed**: Order status updated (old_status → new_status, changed_by)
- **payment_confirmed**: Payment successful (payment_reference, amount)
- **cancelled**: Order cancelled (cancelled_by, reason)

### Implementation
```php
// Order.php
use LogsActivity;

public function getActivitylogOptions(): LogOptions {
    return LogOptions::defaults()
        ->logOnly(['order_status', 'payment_status', 'payment_reference'])
        ->logOnlyDirty()
        ->dontSubmitEmptyLogs()
        ->setDescriptionForEvent(fn(string $eventName) => "Order {$eventName}");
}
```

---

## Storage Estimates

### Per Order
- Order record: ~500 bytes
- Order items (avg 3): ~1.5 KB
- Options (avg 2 per item): ~1 KB
- Addons (avg 1 per item): ~500 bytes
- **Total per order**: ~3.5 KB

### Scale Projections
- 1,000 orders/day = ~3.5 MB/day
- 30,000 orders/month = ~105 MB/month
- 365,000 orders/year = ~1.3 GB/year

---

**Status**: ✅ Data Model Complete  
**Next**: API Contracts & Quickstart Guide
