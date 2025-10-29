# Data Model: User Shopping Cart

**Feature**: user-cart  
**Date**: 2024-10-29  
**Status**: Complete

## Entity Relationship Diagram

```
User (existing)
  ↓ 1:1
Cart
  ↓ 1:N
CartItem
  ↓ N:M         ↓ N:M
CartItemOption  CartItemAddon
  ↓               ↓
ProductOptionValue  Addon
```

## Entities

### Cart

**Purpose**: Represents a user's shopping cart for a single store

**Fields**:
| Field | Type | Constraints | Description |
|-------|------|-------------|-------------|
| id | BIGINT UNSIGNED | PK, AUTO_INCREMENT | Primary key |
| user_id | BIGINT UNSIGNED | FK, UNIQUE, NOT NULL | Reference to users table |
| store_id | BIGINT UNSIGNED | FK, NOT NULL | Reference to stores table |
| created_at | TIMESTAMP | NOT NULL | Creation timestamp |
| updated_at | TIMESTAMP | NOT NULL | Last update timestamp |

**Indexes**:
- PRIMARY KEY (id)
- UNIQUE KEY (user_id) - Enforces one cart per user
- INDEX (store_id)

**Relationships**:
- `belongsTo` User (user_id)
- `belongsTo` Store (store_id)
- `hasMany` CartItem (cart_id)

**Business Rules**:
- One cart per user (enforced by unique constraint)
- Cart must belong to an approved store
- Cascade delete when user is deleted
- Soft delete not required (clean replacement)

**Activity Logging**:
- Log cart creation with store_id
- Log cart deletion with item count and total value
- Log store changes (cart replacement)

---

### CartItem

**Purpose**: Represents a product in the cart with quantity

**Fields**:
| Field | Type | Constraints | Description |
|-------|------|-------------|-------------|
| id | BIGINT UNSIGNED | PK, AUTO_INCREMENT | Primary key |
| cart_id | BIGINT UNSIGNED | FK, NOT NULL | Reference to carts table |
| product_id | BIGINT UNSIGNED | FK, NOT NULL | Reference to products table |
| quantity | INTEGER | NOT NULL, MIN:1, MAX:99 | Item quantity |
| created_at | TIMESTAMP | NOT NULL | Creation timestamp |
| updated_at | TIMESTAMP | NOT NULL | Last update timestamp |

**Indexes**:
- PRIMARY KEY (id)
- INDEX (cart_id)
- INDEX (product_id)
- UNIQUE KEY (cart_id, product_id, options_hash) - Prevent exact duplicates

**Relationships**:
- `belongsTo` Cart (cart_id)
- `belongsTo` Product (product_id)
- `hasMany` CartItemOption (cart_item_id)
- `hasMany` CartItemAddon (cart_item_id)

**Business Rules**:
- Quantity must be between 1 and 99
- Product must be active
- Product's store must match cart's store
- Cascade delete when cart is deleted
- Cascade delete when product is deleted
- Each unique combination of product + options + addons is separate item

**Validation**:
- Product exists and is active
- Product belongs to cart's store
- Quantity is valid integer
- Stock availability for selected options

**Activity Logging**:
- Log item addition with product details
- Log quantity changes (old → new)
- Log item removal with product details

**Computed Attributes**:
- `item_price`: base_price + option_prices + addon_prices
- `item_total`: item_price × quantity

---

### CartItemOption

**Purpose**: Pivot table for selected product option values

**Fields**:
| Field | Type | Constraints | Description |
|-------|------|-------------|-------------|
| id | BIGINT UNSIGNED | PK, AUTO_INCREMENT | Primary key |
| cart_item_id | BIGINT UNSIGNED | FK, NOT NULL | Reference to cart_items table |
| product_option_value_id | BIGINT UNSIGNED | FK, NOT NULL | Reference to product_option_values table |
| created_at | TIMESTAMP | NOT NULL | Creation timestamp |

**Indexes**:
- PRIMARY KEY (id)
- INDEX (cart_item_id)
- INDEX (product_option_value_id)
- UNIQUE KEY (cart_item_id, product_option_value_id) - No duplicate options per item

**Relationships**:
- `belongsTo` CartItem (cart_item_id)
- `belongsTo` ProductOptionValue (product_option_value_id)

**Business Rules**:
- Option value must be available
- Option value must be in stock
- Option value must belong to product's options
- Required options must be selected
- Cascade delete when cart item is deleted

**Validation**:
- Option value exists and is available
- Option value belongs to the product
- Stock quantity > 0
- Required options are present

---

### CartItemAddon

**Purpose**: Pivot table for selected addons with quantities

**Fields**:
| Field | Type | Constraints | Description |
|-------|------|-------------|-------------|
| id | BIGINT UNSIGNED | PK, AUTO_INCREMENT | Primary key |
| cart_item_id | BIGINT UNSIGNED | FK, NOT NULL | Reference to cart_items table |
| addon_id | BIGINT UNSIGNED | FK, NOT NULL | Reference to addons table |
| quantity | INTEGER | NOT NULL, MIN:1, DEFAULT:1 | Addon quantity |
| created_at | TIMESTAMP | NOT NULL | Creation timestamp |

**Indexes**:
- PRIMARY KEY (id)
- INDEX (cart_item_id)
- INDEX (addon_id)
- UNIQUE KEY (cart_item_id, addon_id) - No duplicate addons per item

**Relationships**:
- `belongsTo` CartItem (cart_item_id)
- `belongsTo` Addon (addon_id)

**Business Rules**:
- Addon must be active
- Addon must belong to product's store
- Addon must be available for the product
- Quantity must be at least 1
- Cascade delete when cart item is deleted

**Validation**:
- Addon exists and is active
- Addon belongs to product's store
- Addon is available for the product
- Quantity is valid integer

---

## State Transitions

### Cart Lifecycle

```
[No Cart] 
    ↓ Add first product
[Active Cart - Store A]
    ↓ Add product from Store A
[Active Cart - Store A with multiple items]
    ↓ Add product from Store B (triggers conflict)
[Conflict State - Requires confirmation]
    ↓ User confirms
[Active Cart - Store B] (old cart deleted)
    ↓ User clears cart
[No Cart]
```

### CartItem Lifecycle

```
[Not in Cart]
    ↓ Add to cart
[In Cart - Quantity 1]
    ↓ Update quantity
[In Cart - Quantity N]
    ↓ Remove item
[Not in Cart]
```

## Validation Rules

### Add Item to Cart

1. **Product Validation**:
   - Product exists
   - Product is active
   - Product's store is approved
   - Product belongs to cart's store (or cart is empty)

2. **Option Validation**:
   - All required options are selected
   - Option values exist and are active
   - Option values belong to the product
   - Option values are in stock

3. **Addon Validation**:
   - Addons exist and are active
   - Addons belong to product's store
   - Addons are available for the product

4. **Quantity Validation**:
   - Quantity is integer between 1 and 99
   - Stock is available for selected options

5. **Cart Limit Validation**:
   - Cart has fewer than 50 items

### Update Item Quantity

1. **Item Validation**:
   - Item exists in user's cart
   - Product is still active
   - Product's store is still approved

2. **Quantity Validation**:
   - New quantity is integer between 1 and 99
   - Stock is available for new quantity

3. **Stock Validation**:
   - Selected option values are still in stock

### Store Switching

1. **Conflict Detection**:
   - Cart exists
   - New product's store_id ≠ cart's store_id

2. **Confirmation Flow**:
   - Return 409 Conflict with current cart info
   - Require explicit confirmation
   - Delete old cart atomically
   - Create new cart with new product

## Price Calculation

### Item Price Formula

```
item_price = product.base_price
           + SUM(option_value.calculated_price)
           + SUM(addon.price × addon.quantity)
```

Where `option_value.calculated_price` is based on `price_type`:
- `fixed`: Use price_value directly
- `additional`: base_price + price_value
- `percentage`: base_price × (1 + price_value/100)

### Cart Total Formula

```
cart_subtotal = SUM(item_price × item.quantity for all items)
cart_total = cart_subtotal (no tax/delivery in v1)
```

## Performance Considerations

### Query Optimization

**Cart Retrieval Query**:
```php
Cart::with([
    'store:id,name_en,name_ar,logo,status',
    'items.product:id,name_en,name_ar,description_en,description_ar,base_price',
    'items.product.primaryImage',
    'items.options.optionValue:id,value_en,value_ar',
    'items.options.optionValue.productOptionValue:id,price_type,price_value',
    'items.addons:id,name_en,name_ar,price'
])->where('user_id', $userId)->first();
```

**Expected Query Count**: 2-3 queries total
- 1 query for cart
- 1-2 queries for eager loaded relationships

### Indexes

- Foreign keys are indexed automatically
- Unique constraints create indexes
- Additional indexes on frequently queried columns

### Caching Strategy (Future)

- Cache cart for 5 minutes
- Invalidate on any cart modification
- Use Redis for high-traffic users

## Migration Order

1. `create_carts_table` - Base cart table
2. `create_cart_items_table` - Cart items with FK to carts
3. `create_cart_item_options_table` - Pivot for options
4. `create_cart_item_addons_table` - Pivot for addons

**Important**: Migrations must be run in order due to foreign key dependencies

## Data Integrity

### Foreign Key Constraints

- All foreign keys use `ON DELETE CASCADE` except:
  - `carts.user_id` → CASCADE (delete cart when user deleted)
  - `carts.store_id` → RESTRICT (prevent store deletion if carts exist)
  - `cart_items.product_id` → CASCADE (remove from cart if product deleted)

### Unique Constraints

- `carts.user_id` - One cart per user
- `cart_item_options(cart_item_id, product_option_value_id)` - No duplicate options
- `cart_item_addons(cart_item_id, addon_id)` - No duplicate addons

### Check Constraints

- `cart_items.quantity` BETWEEN 1 AND 99
- `cart_item_addons.quantity` >= 1

## Testing Data Requirements

### Test Fixtures Needed

1. **Users**: 2-3 test users
2. **Stores**: 2 approved stores
3. **Products**: 5-10 products across stores
4. **Options**: Size, Color options with values
5. **Addons**: 3-5 addons per store
6. **Product Images**: At least 1 per product

### Test Scenarios

1. Empty cart → Add item → Cart created
2. Existing cart → Add same store → Item added
3. Existing cart → Add different store → Conflict
4. Confirm replacement → Old cart deleted, new created
5. Update quantity → Quantity changed
6. Remove item → Item deleted
7. Remove last item → Cart deleted
8. Price calculation with options and addons
9. Stock validation on add
10. Product availability validation

## Summary

Data model is complete with:
- ✅ 4 tables (Cart, CartItem, CartItemOption, CartItemAddon)
- ✅ All relationships defined
- ✅ Validation rules documented
- ✅ State transitions mapped
- ✅ Price calculation formulas
- ✅ Performance optimizations planned
- ✅ Migration order specified
- ✅ Test scenarios identified

Ready to proceed to contract generation.
