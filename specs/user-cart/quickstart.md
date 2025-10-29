# Quickstart: User Shopping Cart

**Feature**: user-cart  
**Date**: 2024-10-29  
**Purpose**: Quick validation guide for cart functionality

## Prerequisites

1. **Database**: Migrations run successfully
2. **Authentication**: JWT token for test user
3. **Test Data**: 
   - 2 approved stores with products
   - Products with options and addons configured
   - Test user account

## Quick Test Scenarios

### Scenario 1: Add First Item to Cart (Happy Path)

**Goal**: Create cart and add first product

```bash
# Request
POST /api/user/cart/items
Authorization: Bearer {token}
Content-Type: application/json

{
  "product_id": 1,
  "quantity": 2,
  "option_values": [5, 8],
  "addons": [
    {"addon_id": 3, "quantity": 1}
  ]
}

# Expected Response: 201 Created
{
  "success": true,
  "message": "Item added to cart successfully",
  "data": {
    "cart": {
      "id": 1,
      "store": {
        "id": 1,
        "name": "Store A",
        ...
      },
      "items": [
        {
          "id": 1,
          "product": {...},
          "quantity": 2,
          "selected_options": [...],
          "selected_addons": [...],
          "item_price": "25.99",
          "item_total": "51.98"
        }
      ],
      "subtotal": "51.98",
      "total": "51.98",
      "item_count": 1
    }
  }
}
```

**Validation**:
- ✅ Cart created in database
- ✅ Cart has correct store_id
- ✅ Item added with correct quantity
- ✅ Options and addons saved
- ✅ Prices calculated correctly

---

### Scenario 2: Add Item from Same Store

**Goal**: Add another product from the same store

```bash
# Request
POST /api/user/cart/items
Authorization: Bearer {token}
Content-Type: application/json

{
  "product_id": 2,
  "quantity": 1,
  "option_values": [10],
  "addons": []
}

# Expected Response: 201 Created
{
  "success": true,
  "message": "Item added to cart successfully",
  "data": {
    "cart": {
      "id": 1,
      "store": {...},
      "items": [
        {...}, // First item
        {
          "id": 2,
          "product": {...},
          "quantity": 1,
          ...
        }
      ],
      "subtotal": "72.97",
      "total": "72.97",
      "item_count": 2
    }
  }
}
```

**Validation**:
- ✅ Item added to existing cart
- ✅ Cart still has same store_id
- ✅ Cart total updated
- ✅ Item count increased

---

### Scenario 3: Store Conflict Detection

**Goal**: Trigger conflict when adding from different store

```bash
# Request
POST /api/user/cart/items
Authorization: Bearer {token}
Content-Type: application/json

{
  "product_id": 10, // Product from Store B
  "quantity": 1,
  "option_values": [],
  "addons": []
}

# Expected Response: 409 Conflict
{
  "success": false,
  "message": "Cart contains items from a different store. Adding this item will clear your current cart.",
  "data": {
    "current_store": {
      "id": 1,
      "name": "Store A",
      ...
    },
    "new_store": {
      "id": 2,
      "name": "Store B",
      ...
    },
    "requires_confirmation": true,
    "confirmation_endpoint": "/api/user/cart/replace"
  }
}
```

**Validation**:
- ✅ 409 status code returned
- ✅ Current and new store info provided
- ✅ Cart not modified
- ✅ Confirmation endpoint provided

---

### Scenario 4: Confirm Cart Replacement

**Goal**: Replace cart with product from different store

```bash
# Request
POST /api/user/cart/replace
Authorization: Bearer {token}
Content-Type: application/json

{
  "product_id": 10,
  "quantity": 1,
  "option_values": [],
  "addons": []
}

# Expected Response: 201 Created
{
  "success": true,
  "message": "Cart replaced successfully",
  "data": {
    "cart": {
      "id": 2, // New cart ID
      "store": {
        "id": 2, // Store B
        "name": "Store B",
        ...
      },
      "items": [
        {
          "id": 3, // New item ID
          "product": {...}, // Product from Store B
          "quantity": 1,
          ...
        }
      ],
      "subtotal": "15.99",
      "total": "15.99",
      "item_count": 1
    }
  }
}
```

**Validation**:
- ✅ Old cart deleted from database
- ✅ New cart created with new store_id
- ✅ New item added
- ✅ Activity log shows cart replacement

---

### Scenario 5: Update Item Quantity

**Goal**: Change quantity of existing item

```bash
# Request
PUT /api/user/cart/items/3
Authorization: Bearer {token}
Content-Type: application/json

{
  "quantity": 5
}

# Expected Response: 200 OK
{
  "success": true,
  "message": "Quantity updated successfully",
  "data": {
    "cart": {
      "id": 2,
      "items": [
        {
          "id": 3,
          "quantity": 5, // Updated
          "item_price": "15.99",
          "item_total": "79.95", // Recalculated
          ...
        }
      ],
      "subtotal": "79.95",
      "total": "79.95",
      ...
    }
  }
}
```

**Validation**:
- ✅ Quantity updated
- ✅ Item total recalculated
- ✅ Cart total recalculated
- ✅ Activity log shows quantity change

---

### Scenario 6: Remove Item

**Goal**: Remove item from cart

```bash
# Request
DELETE /api/user/cart/items/3
Authorization: Bearer {token}

# Expected Response: 204 No Content
# (Last item removed, cart deleted)
```

**Validation**:
- ✅ Item deleted from database
- ✅ Cart deleted (was last item)
- ✅ Activity log shows item removal and cart deletion

---

### Scenario 7: View Cart

**Goal**: Retrieve current cart

```bash
# Request
GET /api/user/cart
Authorization: Bearer {token}

# Expected Response: 200 OK (if cart exists)
{
  "success": true,
  "message": "Cart retrieved successfully",
  "data": {
    "cart": {
      "id": 1,
      "store": {...},
      "items": [...],
      "subtotal": "51.98",
      "total": "51.98",
      "item_count": 1
    }
  }
}

# OR 404 Not Found (if no cart)
{
  "success": false,
  "message": "Cart not found"
}
```

**Validation**:
- ✅ Cart retrieved with all relationships
- ✅ Prices calculated correctly
- ✅ Localized names/descriptions
- ✅ 404 when cart doesn't exist

---

### Scenario 8: Clear Cart

**Goal**: Remove all items and delete cart

```bash
# Request
DELETE /api/user/cart
Authorization: Bearer {token}

# Expected Response: 200 OK
{
  "success": true,
  "message": "Cart cleared successfully"
}
```

**Validation**:
- ✅ All items deleted
- ✅ Cart deleted
- ✅ Activity log shows cart clearing

---

## Validation Scenarios

### V1: Invalid Product

```bash
POST /api/user/cart/items
{
  "product_id": 99999, // Non-existent
  "quantity": 1
}

# Expected: 422 Unprocessable Entity
{
  "success": false,
  "message": "Validation failed",
  "errors": {
    "product_id": ["The selected product is invalid"]
  }
}
```

### V2: Inactive Product

```bash
POST /api/user/cart/items
{
  "product_id": 5, // Inactive product
  "quantity": 1
}

# Expected: 422 Unprocessable Entity
{
  "success": false,
  "message": "Validation failed",
  "errors": {
    "product_id": ["The product is not available"]
  }
}
```

### V3: Invalid Quantity

```bash
POST /api/user/cart/items
{
  "product_id": 1,
  "quantity": 0 // Invalid
}

# Expected: 422 Unprocessable Entity
{
  "success": false,
  "message": "Validation failed",
  "errors": {
    "quantity": ["The quantity must be at least 1"]
  }
}
```

### V4: Out of Stock Option

```bash
POST /api/user/cart/items
{
  "product_id": 1,
  "quantity": 1,
  "option_values": [99] // Out of stock
}

# Expected: 422 Unprocessable Entity
{
  "success": false,
  "message": "Validation failed",
  "errors": {
    "option_values.0": ["The selected option is out of stock"]
  }
}
```

### V5: Missing Required Option

```bash
POST /api/user/cart/items
{
  "product_id": 1,
  "quantity": 1,
  "option_values": [] // Missing required size option
}

# Expected: 422 Unprocessable Entity
{
  "success": false,
  "message": "Validation failed",
  "errors": {
    "option_values": ["Required option 'Size' must be selected"]
  }
}
```

### V6: Cart Item Limit

```bash
# After adding 50 items
POST /api/user/cart/items
{
  "product_id": 1,
  "quantity": 1
}

# Expected: 422 Unprocessable Entity
{
  "success": false,
  "message": "Validation failed",
  "errors": {
    "cart": ["Cart cannot contain more than 50 items"]
  }
}
```

---

## Database Verification

After each scenario, verify database state:

```sql
-- Check cart exists
SELECT * FROM carts WHERE user_id = {test_user_id};

-- Check cart items
SELECT * FROM cart_items WHERE cart_id = {cart_id};

-- Check selected options
SELECT * FROM cart_item_options WHERE cart_item_id = {item_id};

-- Check selected addons
SELECT * FROM cart_item_addons WHERE cart_item_id = {item_id};

-- Check activity logs
SELECT * FROM activity_log 
WHERE log_name = 'cart' 
ORDER BY created_at DESC 
LIMIT 10;
```

---

## Performance Verification

```bash
# Check query count (should be ≤ 3)
GET /api/user/cart
# Enable query logging in Laravel
# Verify eager loading is working

# Check response time (should be < 500ms)
# Use browser dev tools or curl -w "@curl-format.txt"
```

---

## Localization Verification

```bash
# Test with English
GET /api/user/cart
Accept-Language: en

# Test with Arabic
GET /api/user/cart
Accept-Language: ar

# Verify:
# - Product names localized
# - Store names localized
# - Option/addon names localized
# - Error messages localized
```

---

## Success Criteria Checklist

- [ ] All 8 scenarios pass
- [ ] All 6 validation scenarios return correct errors
- [ ] Database state is correct after each operation
- [ ] Activity logs are created for all operations
- [ ] Query count ≤ 3 for cart retrieval
- [ ] Response time < 500ms
- [ ] Localization works for both EN and AR
- [ ] Price calculations are accurate
- [ ] Store constraint is enforced
- [ ] Cart replacement flow works correctly

---

## Troubleshooting

### Issue: 500 Error on Add Item
**Check**:
- Migrations run successfully
- Foreign keys exist
- Product/options/addons exist in database

### Issue: Incorrect Prices
**Check**:
- Option value price_type and price_value
- Addon prices
- Price calculation logic in model

### Issue: Store Conflict Not Detected
**Check**:
- Cart store_id matches product's store_id
- Validation logic in controller

### Issue: Slow Response
**Check**:
- Eager loading is configured
- Database indexes exist
- Query count using debugbar

---

## Next Steps

After quickstart validation:
1. Run full test suite: `php artisan test --filter CartTest`
2. Check code coverage
3. Review activity logs
4. Test edge cases
5. Performance profiling
6. Security audit

---

**Quickstart Complete**: All scenarios validated ✅
