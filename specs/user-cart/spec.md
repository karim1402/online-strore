# Feature Specification: User Shopping Cart

**Feature ID**: user-cart  
**Version**: 1.0  
**Status**: Draft  
**Created**: 2024-10-29

## Overview

A shopping cart system for users that enforces single-store ordering. Users can only have items from one store in their cart at a time. Adding items from a different store automatically clears the existing cart and starts fresh with the new store's items.

## User Stories

### US-1: Add Product to Empty Cart
**As a** user  
**I want to** add a product to my empty cart  
**So that** I can start building my order

**Acceptance Criteria**:
- User can add a product with selected options and addons
- Cart is created with the product's store ID
- Product quantity can be specified
- Selected options and addons are saved with the cart item
- Cart total is calculated including base price + options + addons

### US-2: Add Product from Same Store
**As a** user  
**I want to** add more products from the same store to my cart  
**So that** I can order multiple items in one order

**Acceptance Criteria**:
- User can add multiple products from the same store
- Each product maintains its own options and addons
- Cart total updates with each addition
- Duplicate products (same options) increment quantity

### US-3: Replace Cart When Switching Stores
**As a** user  
**I want to** be notified when adding a product from a different store  
**So that** I understand my current cart will be cleared

**Acceptance Criteria**:
- System detects when new product is from a different store
- User receives warning message about cart replacement
- User can confirm or cancel the action
- On confirmation, old cart is deleted and new cart is created
- On cancellation, current cart remains unchanged

### US-4: Update Cart Item Quantity
**As a** user  
**I want to** change the quantity of items in my cart  
**So that** I can order the right amount

**Acceptance Criteria**:
- User can increase or decrease quantity
- Quantity must be at least 1
- Cart total updates automatically
- Stock availability is validated

### US-5: Remove Cart Item
**As a** user  
**I want to** remove items from my cart  
**So that** I can change my mind about products

**Acceptance Criteria**:
- User can remove individual items
- Cart total updates automatically
- If last item is removed, cart is deleted
- Removed items don't affect other cart items

### US-6: View Cart
**As a** user  
**I want to** see all items in my cart with details  
**So that** I can review my order before checkout

**Acceptance Criteria**:
- Display all cart items with product details
- Show selected options and addons for each item
- Display individual item prices and totals
- Show cart subtotal and total
- Display store information
- Show product images

### US-7: Clear Cart
**As a** user  
**I want to** clear my entire cart  
**So that** I can start over

**Acceptance Criteria**:
- User can clear all items at once
- Confirmation required before clearing
- Cart is deleted after clearing
- User is notified of successful clearing

## Functional Requirements

### FR-1: Single Store Constraint
- System MUST enforce one store per cart
- Adding product from different store MUST trigger cart replacement flow
- Cart MUST store the store_id for validation

### FR-2: Cart Persistence
- Cart MUST be persisted in database
- Cart MUST be associated with authenticated user
- Cart MUST survive app restarts and sessions
- Cart items MUST maintain product configuration (options, addons)

### FR-3: Price Calculation
- System MUST calculate item price: base_price + option_price + addon_prices
- System MUST calculate item total: item_price × quantity
- System MUST calculate cart total: sum of all item totals
- Prices MUST be stored as decimals with 2 decimal places

### FR-4: Stock Validation
- System MUST validate product availability
- System MUST validate option value stock
- System MUST prevent adding out-of-stock items
- System MUST handle stock changes while items are in cart

### FR-5: Data Integrity
- Cart items MUST reference valid products
- Cart items MUST reference valid option values
- Cart items MUST reference valid addons
- System MUST handle deleted products/options gracefully

## Non-Functional Requirements

### NFR-1: Performance
- Cart operations MUST complete within 500ms
- Cart retrieval MUST use eager loading to minimize queries
- Cart updates MUST be atomic (all or nothing)

### NFR-2: Concurrency
- System MUST handle concurrent cart updates from same user
- Last write wins for quantity updates
- Optimistic locking for cart modifications

### NFR-3: Data Consistency
- Cart totals MUST always match sum of items
- Prices MUST be recalculated on retrieval (not stored)
- Deleted products MUST be removed from cart automatically

### NFR-4: Localization
- All product names and descriptions MUST be localized
- Error messages MUST be bilingual (English/Arabic)
- Prices MUST use appropriate currency formatting

## Data Model

### Cart Table
- `id` - Primary key
- `user_id` - Foreign key to users table
- `store_id` - Foreign key to stores table
- `created_at` - Timestamp
- `updated_at` - Timestamp

**Constraints**:
- One cart per user (unique user_id)
- Cascade delete when user is deleted

### Cart Items Table
- `id` - Primary key
- `cart_id` - Foreign key to carts table
- `product_id` - Foreign key to products table
- `quantity` - Integer (min: 1)
- `created_at` - Timestamp
- `updated_at` - Timestamp

**Constraints**:
- Cascade delete when cart is deleted
- Cascade delete when product is deleted

### Cart Item Options Table (Pivot)
- `id` - Primary key
- `cart_item_id` - Foreign key to cart_items table
- `product_option_value_id` - Foreign key to product_option_values table
- `created_at` - Timestamp

**Constraints**:
- Cascade delete when cart_item is deleted

### Cart Item Addons Table (Pivot)
- `id` - Primary key
- `cart_item_id` - Foreign key to cart_items table
- `addon_id` - Foreign key to addons table
- `quantity` - Integer (default: 1)
- `created_at` - Timestamp

**Constraints**:
- Cascade delete when cart_item is deleted

## API Endpoints

### GET /api/user/cart
Get current user's cart with all items

**Response**: Cart object with items, store info, totals

### POST /api/user/cart/items
Add item to cart

**Request**:
```json
{
  "product_id": 1,
  "quantity": 2,
  "option_values": [5, 8],
  "addons": [
    {"addon_id": 3, "quantity": 1}
  ]
}
```

**Response**: Updated cart or store conflict warning

### PUT /api/user/cart/items/{id}
Update cart item quantity

**Request**:
```json
{
  "quantity": 3
}
```

**Response**: Updated cart

### DELETE /api/user/cart/items/{id}
Remove item from cart

**Response**: Updated cart or empty if last item

### DELETE /api/user/cart
Clear entire cart

**Response**: Success message

### POST /api/user/cart/replace
Confirm cart replacement when switching stores

**Request**:
```json
{
  "product_id": 1,
  "quantity": 2,
  "option_values": [5, 8],
  "addons": [
    {"addon_id": 3, "quantity": 1}
  ]
}
```

**Response**: New cart with new store's product

## Business Rules

### BR-1: Store Switching
- When user adds product from different store:
  1. Return 409 Conflict with current store info
  2. Provide confirmation endpoint
  3. On confirmation, delete old cart and create new

### BR-2: Quantity Limits
- Minimum quantity: 1
- Maximum quantity: 99 per item
- Stock availability must be validated

### BR-3: Price Calculation
- Item price = base_price + sum(option_value_prices) + sum(addon_prices × addon_quantity)
- Cart total = sum(item_price × quantity for all items)

### BR-4: Cart Expiration
- Carts older than 7 days should be considered for cleanup
- Expired carts can be deleted by background job

### BR-5: Product Validation
- Product must be active
- Product's store must be approved
- Selected options must be required or optional
- Selected option values must be available and in stock
- Selected addons must be active and available

## Success Criteria

1. Users can successfully add products to cart
2. Single-store constraint is enforced
3. Cart persists across sessions
4. Price calculations are accurate
5. Stock validation prevents overselling
6. Store switching flow works smoothly
7. Cart operations are performant (<500ms)
8. All data is properly localized

## Technical Constraints

- Laravel 10.x framework
- MySQL database
- JWT authentication
- RESTful API design
- Spatie Activity Log for audit trail
- Bilingual support (English/Arabic)

## Dependencies

- Existing Product model with options and addons
- Existing Store model with approval system
- Existing User authentication system
- LocalizationService for translations

## Clarifications

### Session 1: Initial Requirements (2024-10-29)

**Q1: Should cart items be updated or duplicated when adding same product with different options?**
A: Create separate cart items. Each unique combination of product + options + addons is a separate line item.

**Q2: How should we handle products that become unavailable while in cart?**
A: Display warning when viewing cart. Allow user to remove unavailable items. Prevent checkout if unavailable items exist.

**Q3: Should we store calculated prices or recalculate on each request?**
A: Recalculate on each request to ensure prices are always current. Don't store calculated totals.

**Q4: What happens to cart when user logs out?**
A: Cart persists in database. When user logs back in, cart is restored.

**Q5: Should there be a maximum cart size (number of items)?**
A: Yes, limit to 50 items per cart to prevent abuse.

## Out of Scope

- Guest cart (anonymous users)
- Cart sharing between users
- Save for later functionality
- Cart merging from multiple devices
- Promo codes or discounts
- Delivery fee calculation
- Tax calculation
- Checkout process (separate feature)

## Future Considerations

- Cart analytics and abandonment tracking
- Recommended products based on cart
- Cart synchronization across devices
- Save cart as wishlist
- Recurring orders from cart history
