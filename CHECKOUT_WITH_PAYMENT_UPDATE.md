# Checkout with Payment Details - Update Summary

## Overview
Updated the checkout endpoint to accept payment details directly, eliminating the need for a separate payment confirmation step in most cases.

---

## What Changed

### ✅ Checkout Endpoint Updated
**Endpoint**: `POST /api/user/checkout`

Now accepts optional `payment_details` object for online payments:

```json
{
  "address_id": 1,
  "payment_method": "online",
  "notes": "Optional notes",
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

---

## New Flow

### Previous Flow (2-Step)
```
1. Checkout → Order created (pending_payment)
2. Confirm Payment → Payment processed
```

### New Flow (1-Step)
```
1. User completes payment at gateway
2. Checkout with payment_details → Order created + Payment processed
```

---

## Validation Rules

### Required Fields (when payment_method = "online")
- `payment_details` (array)
- `payment_details.transaction_id` (string)
- `payment_details.order_id` (string)
- `payment_details.amount_cents` (integer)
- `payment_details.success` (boolean)

### Optional Fields
- `payment_details.currency` (string, default: "EGP")
- `payment_details.is_3d_secure` (boolean)
- `payment_details.card_type` (string)
- `payment_details.card_pan` (string)
- `payment_details.gateway_response` (string)
- `payment_details.txn_response_code` (string)
- `payment_details.integration_id` (integer)
- `payment_details.hmac` (string)
- `payment_details.created_at` (string)
- `payment_details.merchant_commission` (numeric)
- `payment_details.accept_fees` (numeric)

---

## Behavior

### Cash Payment
```json
{
  "address_id": 1,
  "payment_method": "cash",
  "notes": "Optional"
}
```
**Result:**
- Order created with status: `pending`
- Payment status: `pending`
- Cart deleted immediately
- No payment record created

### Online Payment - Success
```json
{
  "address_id": 1,
  "payment_method": "online",
  "payment_details": {
    "transaction_id": "367786330",
    "success": true,
    ...
  }
}
```
**Result:**
- Order created with status: `confirmed`
- Payment status: `paid`
- Payment record created in `payments` table
- Cart deleted
- Payment reference set to transaction_id

### Online Payment - Failed
```json
{
  "address_id": 1,
  "payment_method": "online",
  "payment_details": {
    "transaction_id": "367786331",
    "success": false,
    ...
  }
}
```
**Result:**
- Order created with status: `pending_payment`
- Payment status: `failed`
- Payment record created in `payments` table
- Cart NOT deleted (user can retry)
- Payment reference remains null

---

## Database Changes

### Payment Record Created
When `payment_details` is provided, a record is created in the `payments` table:

```php
Payment::create([
    'order_id' => $order->id,
    'transaction_id' => '367786330',
    'gateway_order_id' => '415312014',
    'amount_cents' => 10000,
    'currency' => 'EGP',
    'success' => true,
    'status' => 'completed', // or 'failed'
    'is_3d_secure' => true,
    'card_type' => 'MasterCard',
    'card_pan' => '2346',
    'gateway_response' => 'Approved',
    'txn_response_code' => 'APPROVED',
    'integration_id' => 5385685,
    'hmac' => 'bef21618f8d41ae654bf...',
    'merchant_commission' => 0.00,
    'accept_fees' => 0.00,
    'payment_created_at' => '2025-11-10T14:50:32',
    'raw_response' => { /* complete payment_details object */ }
]);
```

---

## Response Messages

| Scenario | Message |
|----------|---------|
| Cash payment | `order.placed_successfully` |
| Online payment success | `order.payment_confirmed` |
| Online payment failed | `order.payment_failed` |

---

## Backward Compatibility

The separate **confirm-payment endpoint** still exists and works:
```
POST /api/user/orders/{orderId}/confirm-payment
```

This allows for:
- Legacy integrations
- Async payment confirmations
- Webhook-based payment updates
- Two-step payment flows

---

## Testing

### Postman Collection Updated
File: `postman_payment_confirmation.json`

**New Requests:**
1. **Checkout with Online Payment (Success)** - Full payment details with success=true
2. **Checkout with Online Payment (Failed)** - Full payment details with success=false
3. **Checkout with Cash Payment** - No payment details
4. **Confirm Payment (Success)** - Legacy endpoint (still works)
5. **Confirm Payment (Failed)** - Legacy endpoint (still works)

---

## Code Changes

### OrderController.php

**Added Validation:**
```php
'payment_details' => 'required_if:payment_method,online|array',
'payment_details.transaction_id' => 'required_if:payment_method,online|string',
'payment_details.order_id' => 'required_if:payment_method,online|string',
'payment_details.amount_cents' => 'required_if:payment_method,online|integer',
'payment_details.success' => 'required_if:payment_method,online|boolean',
// ... other fields
```

**Added Payment Processing:**
```php
// Process online payment if payment details provided
if ($request->payment_method === 'online' && $request->has('payment_details')) {
    $payment = Payment::create([...]);
    
    if ($paymentDetails['success']) {
        $order->payment_status = 'paid';
        $order->order_status = 'confirmed';
        $order->payment_reference = $paymentDetails['transaction_id'];
    } else {
        $order->payment_status = 'failed';
    }
}
```

**Updated Cart Deletion Logic:**
```php
// Clear cart for cash orders OR successful online payments
if ($request->payment_method === 'cash' || 
    ($request->payment_method === 'online' && 
     $request->has('payment_details') && 
     $request->payment_details['success'])) {
    $cart->items()->delete();
    $cart->delete();
}
```

---

## Benefits

✅ **Single API Call** - Order creation and payment in one request  
✅ **Atomic Transaction** - Order and payment created together  
✅ **Immediate Feedback** - User knows payment status instantly  
✅ **Simpler Integration** - Frontend makes one call instead of two  
✅ **Better UX** - Faster checkout process  
✅ **Backward Compatible** - Old confirm-payment endpoint still works  
✅ **Flexible** - Supports both sync and async payment flows  

---

## Migration Guide

### Old Implementation
```javascript
// Step 1: Create order
const orderResponse = await fetch('/api/user/checkout', {
  method: 'POST',
  body: JSON.stringify({
    address_id: 1,
    payment_method: 'online',
    notes: 'Deliver soon'
  })
});
const { order } = await orderResponse.json();

// Step 2: Confirm payment
await fetch(`/api/user/orders/${order.id}/confirm-payment`, {
  method: 'POST',
  body: JSON.stringify(paymentDetails)
});
```

### New Implementation
```javascript
// Single step: Create order with payment
const response = await fetch('/api/user/checkout', {
  method: 'POST',
  body: JSON.stringify({
    address_id: 1,
    payment_method: 'online',
    notes: 'Deliver soon',
    payment_details: paymentDetails // Include payment details directly
  })
});
```

---

## Next Steps

1. ✅ Run migration: `php artisan migrate`
2. ✅ Test with Postman collection
3. ✅ Update frontend to send payment_details with checkout
4. ✅ Monitor payment success rates
5. ⏳ Consider deprecating confirm-payment endpoint in future (optional)
