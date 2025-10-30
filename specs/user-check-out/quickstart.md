# Quickstart Validation Guide: User Checkout

**Feature**: user-check-out  
**Purpose**: Validate checkout and order management functionality  
**Estimated Time**: 30-45 minutes

---

## Prerequisites

### Required Data
- ✅ Active user account with JWT token
- ✅ At least 2 delivery addresses saved
- ✅ Cart with 2-3 items from same store
- ✅ Active products with options and addons
- ✅ Approved store

### Tools
- API client (Postman/Thunder Client)
- Database client (MySQL Workbench/TablePlus)
- Terminal for artisan commands

---

## Test Scenarios

### Scenario 1: Cash on Delivery Checkout (Happy Path)

**Objective**: Complete checkout with cash payment

**Steps**:
1. **Prepare Cart**:
   ```http
   POST /api/user/cart/items
   Authorization: Bearer {token}
   Content-Type: application/json
   
   {
     "product_id": 1,
     "quantity": 2,
     "option_values": [5, 8],
     "addons": [{"addon_id": 3, "quantity": 1}]
   }
   ```
   
   **Expected**: 201, cart item added

2. **Checkout with Cash**:
   ```http
   POST /api/user/checkout
   Authorization: Bearer {token}
   Content-Type: application/json
   
   {
     "address_id": 1,
     "payment_method": "cash",
     "notes": "Please call before delivery"
   }
   ```
   
   **Expected**:
   - 201 Created
   - Order number format: `ORD-YYYYMMDD-XXXXX`
   - `order_status`: "pending"
   - `payment_status`: "pending"
   - `payment_method`: "cash"
   - Cart is cleared

3. **Verify Order Created**:
   ```http
   GET /api/user/orders/{orderId}
   Authorization: Bearer {token}
   ```
   
   **Expected**:
   - Order details with all items
   - Options and addons preserved
   - Prices match cart calculation
   - Address snapshot stored

4. **Verify Cart Cleared**:
   ```http
   GET /api/user/cart
   Authorization: Bearer {token}
   ```
   
   **Expected**: 404 Not Found (cart cleared)

**Success Criteria**:
- ✅ Order created with correct status
- ✅ Cart cleared after checkout
- ✅ All items, options, addons copied
- ✅ Prices calculated correctly
- ✅ Address snapshot stored

---

### Scenario 2: Online Payment Checkout (Placeholder)

**Objective**: Initiate online payment checkout

**Steps**:
1. **Add Items to Cart** (same as Scenario 1)

2. **Checkout with Online Payment**:
   ```http
   POST /api/user/checkout
   Authorization: Bearer {token}
   Content-Type: application/json
   
   {
     "address_id": 1,
     "payment_method": "online"
   }
   ```
   
   **Expected**:
   - 201 Created
   - `order_status`: "pending_payment"
   - `payment_status`: "pending"
   - `payment_reference`: "PAY-PLACEHOLDER-XXXXX"
   - `payment_url`: Placeholder URL
   - Cart NOT cleared

3. **Verify Cart Still Exists**:
   ```http
   GET /api/user/cart
   Authorization: Bearer {token}
   ```
   
   **Expected**: 200 OK, cart still has items

**Success Criteria**:
- ✅ Order created with pending_payment status
- ✅ Payment reference generated
- ✅ Cart preserved for retry
- ✅ Placeholder payment URL returned

---

### Scenario 3: View Order History

**Objective**: Retrieve user's orders with filtering

**Steps**:
1. **Get All Orders**:
   ```http
   GET /api/user/orders
   Authorization: Bearer {token}
   ```
   
   **Expected**:
   - 200 OK
   - Array of orders
   - Pagination metadata
   - Orders sorted by created_at DESC

2. **Filter by Status**:
   ```http
   GET /api/user/orders?status=pending
   Authorization: Bearer {token}
   ```
   
   **Expected**: Only pending orders returned

3. **Pagination**:
   ```http
   GET /api/user/orders?page=1&per_page=10
   Authorization: Bearer {token}
   ```
   
   **Expected**: Max 10 orders, pagination info

**Success Criteria**:
- ✅ Orders retrieved successfully
- ✅ Filtering works correctly
- ✅ Pagination functional
- ✅ Localized content

---

### Scenario 4: View Single Order Details

**Objective**: Get complete order information

**Steps**:
1. **Get Order Details**:
   ```http
   GET /api/user/orders/1
   Authorization: Bearer {token}
   ```
   
   **Expected**:
   - 200 OK
   - Complete order details
   - All items with options/addons
   - Store information
   - Address snapshot
   - Price breakdown

**Success Criteria**:
- ✅ All order data present
- ✅ Items show product snapshots
- ✅ Options/addons preserved
- ✅ Localized based on Accept-Language

---

### Scenario 5: Cancel Order

**Objective**: User cancels pending order

**Steps**:
1. **Cancel Pending Order**:
   ```http
   POST /api/user/orders/1/cancel
   Authorization: Bearer {token}
   Content-Type: application/json
   
   {
     "reason": "Changed my mind"
   }
   ```
   
   **Expected**:
   - 200 OK
   - Order status changed to "cancelled"
   - Cancellation logged

2. **Verify Status Changed**:
   ```http
   GET /api/user/orders/1
   Authorization: Bearer {token}
   ```
   
   **Expected**: `order_status`: "cancelled"

3. **Try to Cancel Confirmed Order**:
   ```http
   POST /api/user/orders/2/cancel
   Authorization: Bearer {token}
   ```
   
   **Expected**: 400 Bad Request (cannot cancel confirmed orders)

**Success Criteria**:
- ✅ Pending orders can be cancelled
- ✅ Confirmed orders cannot be cancelled
- ✅ Cancellation reason stored
- ✅ Activity logged

---

## Validation Scenarios

### Validation 1: Empty Cart Checkout

**Steps**:
```http
DELETE /api/user/cart
Authorization: Bearer {token}

POST /api/user/checkout
Authorization: Bearer {token}
Content-Type: application/json

{
  "address_id": 1,
  "payment_method": "cash"
}
```

**Expected**:
- 404 Not Found
- Error: "Cart is empty" or "Cart not found"

---

### Validation 2: Invalid Address

**Steps**:
```http
POST /api/user/checkout
Authorization: Bearer {token}
Content-Type: application/json

{
  "address_id": 99999,
  "payment_method": "cash"
}
```

**Expected**:
- 422 Validation Error
- Error: "The selected address is invalid"

---

### Validation 3: Address Belongs to Another User

**Steps**:
```http
POST /api/user/checkout
Authorization: Bearer {token}
Content-Type: application/json

{
  "address_id": {another_user_address_id},
  "payment_method": "cash"
}
```

**Expected**:
- 422 Validation Error
- Error: "The selected address is invalid"

---

### Validation 4: Invalid Payment Method

**Steps**:
```http
POST /api/user/checkout
Authorization: Bearer {token}
Content-Type: application/json

{
  "address_id": 1,
  "payment_method": "crypto"
}
```

**Expected**:
- 422 Validation Error
- Error: "The selected payment method is invalid"

---

### Validation 5: Product Becomes Inactive

**Steps**:
1. Add product to cart
2. Admin deactivates product
3. Try to checkout

**Expected**:
- 422 Validation Error
- Error listing unavailable products
- User can remove invalid items and retry

---

### Validation 6: Store Becomes Suspended

**Steps**:
1. Add items from store to cart
2. Admin suspends store
3. Try to checkout

**Expected**:
- 422 Validation Error
- Error: "Store is not available"

---

### Validation 7: Notes Too Long

**Steps**:
```http
POST /api/user/checkout
Authorization: Bearer {token}
Content-Type: application/json

{
  "address_id": 1,
  "payment_method": "cash",
  "notes": "{string longer than 500 characters}"
}
```

**Expected**:
- 422 Validation Error
- Error: "The notes may not be greater than 500 characters"

---

### Validation 8: Unauthorized Access

**Steps**:
```http
GET /api/user/orders/1
Authorization: Bearer {another_user_token}
```

**Expected**:
- 404 Not Found (order belongs to different user)

---

## Database Verification

### Check Order Created
```sql
SELECT * FROM orders WHERE user_id = {user_id} ORDER BY created_at DESC LIMIT 1;
```

**Verify**:
- order_number format correct
- user_id, store_id set
- payment_method, payment_status correct
- subtotal, delivery_fee, tax, total calculated
- address_snapshot is valid JSON

### Check Order Items
```sql
SELECT * FROM order_items WHERE order_id = {order_id};
```

**Verify**:
- All cart items copied
- product_snapshot is valid JSON
- quantity, unit_price, total_price correct

### Check Options and Addons
```sql
SELECT * FROM order_item_options WHERE order_item_id IN (
  SELECT id FROM order_items WHERE order_id = {order_id}
);

SELECT * FROM order_item_addons WHERE order_item_id IN (
  SELECT id FROM order_items WHERE order_id = {order_id}
);
```

**Verify**:
- option_snapshot/addon_snapshot are valid JSON
- All selected options/addons preserved

### Check Cart Cleared (Cash Orders)
```sql
SELECT * FROM carts WHERE user_id = {user_id};
```

**Verify**: No cart exists after cash checkout

### Check Activity Log
```sql
SELECT * FROM activity_log 
WHERE subject_type = 'App\\Models\\Order' 
AND subject_id = {order_id}
ORDER BY created_at DESC;
```

**Verify**:
- Order creation logged
- Status changes logged
- Causer (user) recorded

---

## Price Calculation Verification

### Manual Calculation
```
Product Base Price: 45.00
Option 1 (Size Large, +20%): 45.00 × 0.20 = 9.00
Option 2 (Extra Topping, +5.00): 5.00
Addon 1 (Extra Cheese, 5.00 × 1): 5.00
---
Item Unit Price: 45.00 + 9.00 + 5.00 + 5.00 = 64.00
Item Quantity: 2
Item Total: 64.00 × 2 = 128.00
---
Order Subtotal: 128.00
Delivery Fee: 10.00
Tax: 0.00
---
Order Total: 138.00
```

### Verify in Database
```sql
SELECT 
  oi.unit_price,
  oi.quantity,
  oi.total_price,
  o.subtotal,
  o.delivery_fee,
  o.tax,
  o.total
FROM order_items oi
JOIN orders o ON oi.order_id = o.id
WHERE o.id = {order_id};
```

**Expected**: All calculations match manual calculation

---

## Performance Checks

### Checkout Response Time
```bash
# Using curl with timing
curl -w "@curl-format.txt" -o /dev/null -s \
  -X POST http://localhost:8000/api/user/checkout \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{"address_id":1,"payment_method":"cash"}'
```

**Expected**: < 2 seconds total time

### Query Count
Enable query logging in Laravel:
```php
DB::enableQueryLog();
// Perform checkout
$queries = DB::getQueryLog();
dd(count($queries));
```

**Expected**: ≤ 10 queries for checkout process

### Order List Performance
```bash
# Load 100 orders
curl -w "@curl-format.txt" -o /dev/null -s \
  "http://localhost:8000/api/user/orders?per_page=100" \
  -H "Authorization: Bearer {token}"
```

**Expected**: < 1 second

---

## Localization Checks

### English Response
```http
GET /api/user/orders/1
Authorization: Bearer {token}
Accept-Language: en
```

**Verify**:
- Product names in English
- Status labels in English
- Error messages in English

### Arabic Response
```http
GET /api/user/orders/1
Authorization: Bearer {token}
Accept-Language: ar
```

**Verify**:
- Product names in Arabic
- Status labels in Arabic
- Error messages in Arabic

### Snapshot Localization
Check that snapshots contain both languages:
```sql
SELECT product_snapshot FROM order_items WHERE id = 1;
```

**Expected JSON**:
```json
{
  "name_en": "Margherita Pizza",
  "name_ar": "بيتزا مارجريتا",
  ...
}
```

---

## Edge Cases

### EC-1: Concurrent Checkout
1. User A adds items to cart
2. User A clicks checkout twice rapidly
3. Verify only one order created (idempotency)

### EC-2: Price Changes During Checkout
1. Add product (price: 50.00) to cart
2. Admin changes product price to 60.00
3. Checkout
4. Verify order uses new price (60.00)

### EC-3: Daily Order Number Rollover
1. Create order on Day 1: ORD-20251030-00001
2. Create order on Day 2: ORD-20251031-00001
3. Verify sequence resets daily

### EC-4: Maximum Cart Items
1. Add 50 items to cart
2. Checkout
3. Verify all 50 items in order

### EC-5: Order Without Notes
1. Checkout without notes field
2. Verify order created successfully
3. Verify notes is NULL in database

---

## Troubleshooting

### Issue: Order number collision
**Symptom**: Duplicate order number error  
**Check**: 
```sql
SELECT order_number, COUNT(*) FROM orders 
GROUP BY order_number HAVING COUNT(*) > 1;
```
**Fix**: Ensure unique constraint on order_number

### Issue: Cart not cleared after cash checkout
**Symptom**: Cart still exists after successful order  
**Check**: Transaction committed? Cart delete in transaction?  
**Fix**: Verify DB::commit() called

### Issue: Prices don't match
**Symptom**: Order total doesn't match cart total  
**Check**: Price calculation logic, option/addon prices  
**Fix**: Recalculate from current product prices

### Issue: Snapshot data missing
**Symptom**: Product names show as NULL  
**Check**: 
```sql
SELECT product_snapshot FROM order_items WHERE id = {id};
```
**Fix**: Ensure snapshot includes all required fields

### Issue: Slow checkout
**Symptom**: Checkout takes > 2 seconds  
**Check**: Query count, missing indexes  
**Fix**: Add indexes, optimize eager loading

---

## Success Checklist

### Functional Requirements
- [ ] Cash checkout creates order with status "pending"
- [ ] Online checkout creates order with status "pending_payment"
- [ ] Cart cleared after cash checkout
- [ ] Cart preserved after online checkout
- [ ] Order number format correct (ORD-YYYYMMDD-XXXXX)
- [ ] All cart items copied to order
- [ ] Options and addons preserved
- [ ] Prices calculated correctly
- [ ] Address snapshot stored
- [ ] User can view order history
- [ ] User can view order details
- [ ] User can cancel pending orders
- [ ] User cannot cancel confirmed orders

### Validation Requirements
- [ ] Empty cart rejected
- [ ] Invalid address rejected
- [ ] Invalid payment method rejected
- [ ] Inactive products detected
- [ ] Suspended stores detected
- [ ] Notes length validated
- [ ] Unauthorized access prevented

### Technical Requirements
- [ ] Checkout completes in < 2 seconds
- [ ] Query count ≤ 10 for checkout
- [ ] Transactions ensure atomicity
- [ ] Activity logging works
- [ ] Localization works (EN/AR)
- [ ] Snapshots contain all data
- [ ] Indexes improve performance

### Data Integrity
- [ ] Foreign keys enforced
- [ ] Unique constraints work
- [ ] Cascade deletes work
- [ ] JSON snapshots valid
- [ ] Prices non-negative
- [ ] Quantities >= 1

---

## Next Steps

After validation:
1. ✅ All tests pass → Feature ready for production
2. ❌ Tests fail → Debug and fix issues
3. 📝 Document any deviations from spec
4. 🚀 Deploy to staging for UAT
5. 📧 Integrate notification system (future)
6. 💳 Integrate payment gateway (future)

---

**Validation Status**: ⏳ Pending Implementation  
**Last Updated**: 2025-10-30
