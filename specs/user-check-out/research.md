# Research: User Checkout Feature

**Feature**: user-check-out  
**Date**: 2025-10-30  
**Status**: Complete

---

## Research Questions

### RQ-1: Order Number Generation Strategy
**Question**: How to generate unique, sequential order numbers with date prefix?

**Decision**: Use format `ORD-YYYYMMDD-XXXXX` where XXXXX is daily sequential number

**Rationale**:
- Human-readable and sortable
- Date prefix helps with daily order tracking
- Sequential number within day prevents collisions
- Easy to implement with database query: `SELECT MAX(order_number) WHERE DATE(created_at) = TODAY`

**Alternatives Considered**:
- UUID: Too long, not human-friendly
- Simple auto-increment: No date context
- Timestamp-based: Risk of collisions in high-traffic scenarios

**Implementation**:
```php
$date = now()->format('Ymd');
$lastOrder = Order::whereDate('created_at', now())->orderBy('id', 'desc')->first();
$sequence = $lastOrder ? (int)substr($lastOrder->order_number, -5) + 1 : 1;
$orderNumber = sprintf('ORD-%s-%05d', $date, $sequence);
```

---

### RQ-2: Cart-to-Order Data Snapshot Strategy
**Question**: Should we reference cart items or snapshot all data at order time?

**Decision**: Snapshot approach - store complete item details in JSON

**Rationale**:
- Prices may change after order placed
- Products may be deleted or modified
- Options/addons may be removed
- Order must reflect exact state at purchase time
- Enables accurate order history and reprints

**Alternatives Considered**:
- Reference only (FK to products): Data loss if product deleted, price changes affect history
- Hybrid (FK + price snapshot): Still loses product names/descriptions if deleted

**Implementation**:
- `product_snapshot` (JSON): {id, name_en, name_ar, description_en, description_ar, image_url}
- `option_snapshot` (JSON): {option_group_name, option_value_name, price_adjustment}
- `addon_snapshot` (JSON): {addon_name, price, quantity}
- Also store address_snapshot for delivery address

---

### RQ-3: Payment Method Handling
**Question**: How to structure payment for cash vs online without implementing gateway?

**Decision**: Two-field approach: `payment_method` (enum) + `payment_status` (enum)

**Rationale**:
- Separates method selection from payment state
- Allows future payment gateway integration without schema changes
- Clear status tracking for both methods

**Payment Method Values**:
- `cash`: Cash on delivery
- `online`: Online payment (placeholder)

**Payment Status Values**:
- `pending`: Awaiting payment (cash orders, or online before payment)
- `paid`: Payment confirmed (online orders after gateway confirmation)
- `failed`: Payment failed (online only)
- `refunded`: Payment refunded

**Order Status Flow**:
```
Cash: pending → confirmed → preparing → ready → out_for_delivery → delivered
Online: pending_payment → (payment) → confirmed → preparing → ready → out_for_delivery → delivered
```

---

### RQ-4: Cart Validation Strategy
**Question**: What validations are needed before checkout?

**Decision**: Multi-level validation with detailed error responses

**Validations Required**:
1. **Cart Level**:
   - Cart exists and belongs to user
   - Cart has at least one item
   - All items from same store

2. **Product Level**:
   - Product exists and is active
   - Product belongs to approved store
   - Product price hasn't been deleted (use current price)

3. **Options/Addons Level**:
   - Selected options still exist and are valid
   - Selected addons still exist and are active
   - Option/addon prices are current

4. **Address Level**:
   - Address exists and belongs to user
   - Address has complete information
   - Address has coordinates (for delivery)

**Error Response Format**:
```json
{
  "success": false,
  "message": "Cart validation failed",
  "errors": {
    "cart": ["Cart is empty"],
    "items": [
      {"product_id": 5, "error": "Product no longer available"},
      {"product_id": 8, "error": "Store is not approved"}
    ],
    "address": ["Selected address not found"]
  }
}
```

---

### RQ-5: Transaction Management
**Question**: How to ensure atomic cart-to-order conversion?

**Decision**: Database transaction wrapping entire checkout process

**Rationale**:
- Prevents partial order creation
- Ensures cart is cleared only after successful order
- Handles concurrent checkout attempts
- Rollback on any failure

**Transaction Scope**:
```php
DB::beginTransaction();
try {
    // 1. Validate cart
    // 2. Create order
    // 3. Copy cart items to order items
    // 4. Copy options and addons
    // 5. Clear cart (for cash) or mark pending (for online)
    // 6. Log activity
    DB::commit();
} catch (\Exception $e) {
    DB::rollBack();
    // Return error
}
```

---

### RQ-6: Price Calculation at Checkout
**Question**: Use cart prices or recalculate from products?

**Decision**: Recalculate from current product/option/addon prices

**Rationale**:
- Ensures user sees and pays current prices
- Prevents price manipulation via cart tampering
- Transparent pricing - what you see is what you pay
- Cart is just a selection tool, not a price lock

**Calculation Flow**:
1. Get product base_price
2. Add option price adjustments (percentage or fixed)
3. Add addon prices × quantities
4. Multiply by item quantity
5. Sum all items for subtotal
6. Add delivery fee (placeholder: 10.00)
7. Add tax (placeholder: 0.00)
8. Calculate total

---

### RQ-7: Online Payment Placeholder Design
**Question**: How to structure placeholder for future payment gateway?

**Decision**: Generate payment reference, return placeholder URL, keep cart until confirmed

**Rationale**:
- Allows frontend to show "payment pending" state
- Cart preserved for retry if payment fails
- Easy to replace placeholder with real gateway
- Webhook endpoint structure defined for future

**Placeholder Response**:
```json
{
  "order": {
    "id": 123,
    "order_number": "ORD-20251030-00001",
    "status": "pending_payment",
    "payment_method": "online",
    "payment_reference": "PAY-PLACEHOLDER-12345",
    "payment_url": "https://payment.example.com/pay/12345",
    "total": "160.00"
  }
}
```

**Future Integration Points**:
- Replace payment_reference generation with gateway API call
- Replace payment_url with real gateway URL
- Implement webhook endpoint: `POST /api/webhooks/payment-confirmation`
- Add payment gateway credentials to .env

---

### RQ-8: Order Status Management
**Question**: What order statuses are needed and who can change them?

**Decision**: 8 statuses with role-based transitions

**Statuses**:
1. `pending`: Cash order awaiting vendor confirmation
2. `pending_payment`: Online order awaiting payment
3. `confirmed`: Vendor accepted order
4. `preparing`: Vendor is preparing order
5. `ready`: Order ready for pickup/delivery
6. `out_for_delivery`: With delivery person
7. `delivered`: Completed successfully
8. `cancelled`: Cancelled by user/vendor/admin

**Transition Rules**:
- User can cancel: `pending` or `pending_payment` only
- Vendor can: confirm, prepare, mark ready, cancel (with reason)
- Delivery can: mark out_for_delivery, mark delivered
- Admin can: any transition
- System auto-transitions: `pending_payment` → `paid` (on webhook)

---

### RQ-9: Delivery Fee Calculation
**Question**: How to calculate delivery fees?

**Decision**: Fixed placeholder value (10.00 SAR) for v1

**Rationale**:
- Delivery fee calculation requires:
  - Distance calculation (user address to store/branch)
  - Pricing tiers or zones
  - Time-based pricing (peak hours)
  - Integration with delivery service
- All of above are out of scope for v1
- Fixed fee allows feature to work while planning proper integration

**Future Enhancement**:
- Integrate with Google Maps Distance Matrix API
- Define delivery zones and pricing
- Add delivery_fee_calculation field to orders
- Create DeliveryFeeService

---

### RQ-10: Concurrent Checkout Handling
**Question**: What if two users checkout simultaneously or user double-clicks?

**Decision**: Database transactions + idempotency check

**Protection Mechanisms**:
1. **Transaction Isolation**: Prevents race conditions
2. **Unique Constraints**: order_number uniqueness
3. **Cart Locking**: Transaction locks cart row during checkout
4. **Idempotency**: Check for recent duplicate orders (same user, same total, within 5 minutes)

**Implementation**:
```php
// Check for duplicate recent order
$recentOrder = Order::where('user_id', $user->id)
    ->where('total', $calculatedTotal)
    ->where('created_at', '>', now()->subMinutes(5))
    ->first();

if ($recentOrder) {
    return response()->json([
        'success' => true,
        'message' => 'Order already placed',
        'data' => ['order' => $recentOrder]
    ], 200);
}
```

---

## Technology Decisions

### TD-1: Database Schema Design
**Decision**: 4 tables with JSON snapshots

**Tables**:
1. **orders**: Main order record with totals and status
2. **order_items**: Individual products in order
3. **order_item_options**: Selected options (with snapshot)
4. **order_item_addons**: Selected addons (with snapshot)

**Why JSON Snapshots**:
- Preserves data even if source deleted
- Faster queries (no joins to products/options/addons)
- Complete order history
- Easier to display order details

---

### TD-2: Activity Logging
**Decision**: Log all order lifecycle events

**Events to Log**:
- Order created (user, store, payment method, total)
- Order confirmed (by vendor)
- Order cancelled (by user/vendor, with reason)
- Status changed (old → new status)
- Payment status changed (pending → paid/failed)

**Implementation**: Use Spatie Activity Log trait on Order model

---

### TD-3: Localization Strategy
**Decision**: Bilingual support using LocalizationService

**Localized Content**:
- Order confirmation messages
- Status labels
- Error messages
- Email/SMS notifications (future)

**Snapshot Localization**:
- Store both EN and AR in snapshots
- Display based on user's Accept-Language header
- Ensures historical orders show in correct language

---

## Performance Considerations

### PC-1: Query Optimization
**Target**: Max 5 queries for checkout

**Query Plan**:
1. Get cart with items (eager load: product, store, options, addons)
2. Validate address
3. Create order (single INSERT)
4. Bulk insert order_items
5. Bulk insert order_item_options and order_item_addons

**Optimization Techniques**:
- Eager loading with `with()`
- Bulk inserts for order items
- Index on order_number, user_id, store_id, status
- Transaction reduces lock time

---

### PC-2: Scalability
**Considerations**:
- Order number generation: Use database sequence or Redis counter for high traffic
- Cart locking: Row-level locks prevent contention
- Read replicas: Order history queries can use read replicas
- Caching: Cache store/product data during validation

---

## Security Considerations

### SC-1: Authorization
- Verify user owns cart
- Verify user owns selected address
- Prevent price manipulation (server-side calculation)
- Sanitize order notes input

### SC-2: Data Integrity
- Use transactions for atomicity
- Validate all foreign keys
- Prevent SQL injection (use Eloquent)
- Log all order operations for audit

---

## Dependencies

### Existing Systems
- ✅ Cart system (user-cart feature)
- ✅ User authentication (JWT)
- ✅ User addresses system
- ✅ Product, Store, Addon models
- ✅ LocalizationService
- ✅ Activity logging

### External Services (Future)
- ❌ Payment gateway (placeholder in v1)
- ❌ Email/SMS service (future)
- ❌ Delivery fee calculation service (future)

---

## Risks & Mitigations

| Risk | Probability | Impact | Mitigation |
|------|-------------|--------|------------|
| Concurrent checkouts | Medium | High | Database transactions + row locking |
| Price changes during checkout | High | Low | Accept current prices, show confirmation |
| Payment gateway delays | Low | Medium | Use placeholder, design for async |
| Cart validation failures | Medium | Medium | Detailed error messages, allow retry |
| Order number collisions | Low | High | Unique constraint + sequential generation |

---

## Open Questions (Resolved)

All questions from Technical Context have been researched and resolved. No NEEDS CLARIFICATION remaining.

---

**Status**: ✅ Research Complete  
**Next Phase**: Design & Contracts (Phase 1)
