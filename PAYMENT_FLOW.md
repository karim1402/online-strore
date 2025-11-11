# Online Payment Flow Documentation

## Overview
This document describes the online payment flow for the Makook application.

## Flow Diagram

```
1. User adds items to cart
   ↓
2. User completes payment via payment gateway
   ↓
3. Payment gateway returns payment result
   ↓
4. User submits checkout with payment_method: "online" + payment_details
   ↓
5. Order created and payment processed in single transaction
   ↓
6a. If payment SUCCESS (success: true):
    - Order created with status: "confirmed"
    - Payment status: "paid"
    - Payment record created in payments table
    - Cart is deleted
    
6b. If payment FAILED (success: false):
    - Order created with status: "pending_payment"
    - Payment status: "failed"
    - Payment record created in payments table
    - Cart remains intact (user can retry)
```

## API Endpoints

### 1. Checkout (Create Order)
**Endpoint:** `POST /api/user/checkout`

**Headers:**
```
Authorization: Bearer {token}
Content-Type: application/json
Accept-Language: en
```

**Request Body (Cash Payment):**
```json
{
  "address_id": 1,
  "payment_method": "cash",
  "notes": "Optional delivery notes"
}
```

**Request Body (Online Payment - Success):**
```json
{
  "address_id": 1,
  "payment_method": "online",
  "notes": "Optional delivery notes",
  "payment_details": {
    "transaction_id": "367786330",
    "order_id": "415312014",
    "amount_cents": 10000,
    "currency": "EGP",
    "success": true,
    "is_3d_secure": true,
    "card_type": "MasterCard",
    "card_pan": "2346",
    "gateway_response": "Approved",
    "txn_response_code": "APPROVED",
    "integration_id": 5385685,
    "hmac": "bef21618f8d41ae654bf...",
    "created_at": "2025-11-10T14:50:32.722525",
    "merchant_commission": 0,
    "accept_fees": 0
  }
}
```

**Request Body (Online Payment - Failed):**
```json
{
  "address_id": 1,
  "payment_method": "online",
  "notes": "Optional delivery notes",
  "payment_details": {
    "transaction_id": "367786331",
    "order_id": "415312015",
    "amount_cents": 10000,
    "currency": "EGP",
    "success": false,
    "is_3d_secure": true,
    "card_type": "MasterCard",
    "card_pan": "2346",
    "gateway_response": "Declined",
    "txn_response_code": "DECLINED",
    "integration_id": 5385685,
    "hmac": "bef21618f8d41ae654bf...",
    "created_at": "2025-11-10T14:50:32.722525",
    "merchant_commission": 0,
    "accept_fees": 0
  }
}
```

**Response (Cash Payment):**
```json
{
  "success": true,
  "message": "Order placed successfully",
  "data": {
    "order": {
      "id": 1,
      "order_number": "ORD-20251111-00001",
      "status": "pending",
      "payment_method": "cash",
      "payment_status": "pending",
      "payment_reference": null,
      "subtotal": "100.00",
      "delivery_fee": "0.00",
      "tax": "0.00",
      "total": "100.00",
      "items_count": 2,
      "created_at": "2025-11-11T10:56:00.000000Z"
    }
  }
}
```

**Response (Online Payment - Success):**
```json
{
  "success": true,
  "message": "Payment confirmed successfully",
  "data": {
    "order": {
      "id": 1,
      "order_number": "ORD-20251111-00001",
      "status": "confirmed",
      "payment_method": "online",
      "payment_status": "paid",
      "payment_reference": "367786330",
      "subtotal": "100.00",
      "delivery_fee": "0.00",
      "tax": "0.00",
      "total": "100.00",
      "items_count": 2,
      "created_at": "2025-11-11T10:56:00.000000Z"
    }
  }
}
```

**Response (Online Payment - Failed):**
```json
{
  "success": true,
  "message": "Payment failed",
  "data": {
    "order": {
      "id": 1,
      "order_number": "ORD-20251111-00001",
      "status": "pending_payment",
      "payment_method": "online",
      "payment_status": "failed",
      "payment_reference": null,
      "subtotal": "100.00",
      "delivery_fee": "0.00",
      "tax": "0.00",
      "total": "100.00",
      "items_count": 2,
      "created_at": "2025-11-11T10:56:00.000000Z"
    }
  }
}
```

**Important Notes:**
- For **cash payments**: Cart is deleted immediately, order status is "pending"
- For **successful online payments**: Cart is deleted, order status is "confirmed", payment record created
- For **failed online payments**: Cart is NOT deleted (user can retry), order status is "pending_payment", payment record created

---

### 2. Confirm Payment (Optional - Legacy Support)
**Endpoint:** `POST /api/user/orders/{orderId}/confirm-payment`

> **Note**: This endpoint is now optional. Payment details can be sent directly with the checkout endpoint. This endpoint remains available for backward compatibility or for cases where payment confirmation happens after order creation.

**Headers:**
```
Authorization: Bearer {token}
Content-Type: application/json
Accept-Language: en
```

**Request Body (Payment Gateway Response):**
```json
{
  "transaction_id": "367786330",
  "order_id": "415312014",
  "amount_cents": 10000,
  "currency": "EGP",
  "success": true,
  "is_3d_secure": true,
  "card_type": "MasterCard",
  "card_pan": "2346",
  "gateway_response": "Approved",
  "txn_response_code": "APPROVED",
  "integration_id": 5385685,
  "hmac": "bef21618f8d41ae654bf...",
  "created_at": "2025-11-10T14:50:32.722525",
  "merchant_commission": 0,
  "accept_fees": 0
}
```

**Response (Success):**
```json
{
  "success": true,
  "message": "Payment confirmed successfully",
  "data": {
    "order": {
      "id": 1,
      "order_number": "ORD-20251111-00001",
      "status": "confirmed",
      "payment_method": "online",
      "payment_status": "paid",
      "payment_reference": "367786330",
      "subtotal": "100.00",
      "delivery_fee": "0.00",
      "tax": "0.00",
      "total": "100.00",
      "notes": null,
      "created_at": "2025-11-11T10:56:00.000000Z",
      "updated_at": "2025-11-11T11:00:00.000000Z"
    }
  }
}
```

**What Happens on Success (success: true):**
1. Payment record created in `payments` table with status "completed"
2. Order payment status updated to "paid"
3. Order status updated to "confirmed"
4. Order payment reference updated with transaction_id
5. User's cart is deleted
6. Activity log created

**What Happens on Failure (success: false):**
1. Payment record created in `payments` table with status "failed"
2. Order payment status updated to "failed"
3. Order status remains "pending_payment"
4. Cart is NOT deleted (user can retry)
5. Activity log created

---

## Database Changes

### Migration: `create_payments_table`
Creates a dedicated `payments` table to store payment transactions:

```php
Schema::create('payments', function (Blueprint $table) {
    $table->id();
    $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
    $table->string('transaction_id')->unique();
    $table->string('gateway_order_id')->nullable();
    $table->integer('amount_cents');
    $table->string('currency', 10)->default('EGP');
    $table->boolean('success')->default(false);
    $table->enum('status', ['pending', 'completed', 'failed', 'refunded']);
    $table->boolean('is_3d_secure')->default(false);
    $table->string('card_type')->nullable();
    $table->string('card_pan')->nullable();
    $table->string('gateway_response')->nullable();
    $table->string('txn_response_code')->nullable();
    $table->integer('integration_id')->nullable();
    $table->text('hmac')->nullable();
    $table->decimal('merchant_commission', 10, 2)->default(0.00);
    $table->decimal('accept_fees', 10, 2)->default(0.00);
    $table->timestamp('payment_created_at')->nullable();
    $table->json('raw_response')->nullable();
    $table->timestamps();
});
```

### Payment Model
New `Payment` model with:
- Relationship to `Order` model
- Scopes for filtering (successful, failed, pending)
- Helper methods (`isSuccessful()`, `isFailed()`, etc.)
- Computed attributes (`amount`, `formatted_amount`)

### Order Model Updates
- Added `payments()` relationship (hasMany)
- Added `latestPayment()` relationship (hasOne latest)

---

## Payment Data Storage

Payment data is now stored in a separate `payments` table with the following structure:

**Structured Fields:**
- `transaction_id`: Unique payment gateway transaction ID
- `gateway_order_id`: Payment gateway's order reference
- `amount_cents`: Amount in cents (e.g., 10000 = 100.00 EGP)
- `currency`: Currency code (default: EGP)
- `success`: Boolean flag for payment success
- `status`: Payment status (pending, completed, failed, refunded)
- `card_type`: Card type (Visa, MasterCard, etc.)
- `card_pan`: Last 4 digits of card
- `gateway_response`: Gateway response message
- `txn_response_code`: Transaction response code

**Complete Response:**
- `raw_response`: JSON field storing the complete gateway response

**Benefits:**
- Proper relational structure
- Multiple payment attempts per order
- Complete audit trail
- Easy querying and reporting
- Payment reconciliation
- Dispute resolution support

---

## Testing with Postman

1. **Import Collection:** Import `postman_payment_confirmation.json`

2. **Set Variables:**
   - `base_url`: Your API base URL (e.g., http://localhost:8000)
   - `user_token`: JWT token from login
   - `order_id`: Will be set from checkout response

3. **Test Flow:**
   ```
   Step 1: Add items to cart (use cart endpoints)
   Step 2: Run "Checkout with Online Payment"
   Step 3: Copy the order.id from response
   Step 4: Update {{order_id}} variable
   Step 5: Run "Confirm Payment (Success)" or "Confirm Payment (Failed)"
   ```

---

## Error Handling

### Validation Errors (422)
```json
{
  "success": false,
  "message": "Validation failed",
  "errors": {
    "transaction_id": ["The transaction id field is required."]
  }
}
```

### Order Not Found (404)
```json
{
  "success": false,
  "message": "Order not found"
}
```

### Invalid Payment Method (400)
```json
{
  "success": false,
  "message": "Order payment method is not online"
}
```

### Already Paid (400)
```json
{
  "success": false,
  "message": "Order has already been paid"
}
```

---

## Security Considerations

1. **HMAC Validation:** In production, validate the `hmac` field to ensure the payment response is authentic
2. **Amount Verification:** Verify `amount_cents` matches the order total
3. **User Authorization:** Endpoint verifies the order belongs to the authenticated user
4. **Idempotency:** Check if payment is already confirmed before processing

---

## Next Steps for Production

1. **Integrate Real Payment Gateway:**
   - Replace placeholder payment URL with actual gateway URL
   - Implement HMAC validation
   - Add webhook endpoint for payment gateway callbacks

2. **Add Webhook Endpoint:**
   - Create a webhook endpoint for payment gateway to call directly
   - Implement signature verification
   - Handle async payment confirmations

3. **Implement Retry Logic:**
   - Allow users to retry failed payments
   - Generate new payment URLs for pending orders

4. **Add Payment Timeout:**
   - Auto-cancel orders with pending payment after X hours
   - Restore cart items if payment times out
