# Form-Data Update Summary

## Overview
Updated the checkout endpoint to accept payment details as **flat form-data fields** instead of nested JSON object.

---

## What Changed

### ✅ Backend (OrderController.php)

**Validation Updated:**
- Changed from nested `payment_details.*` validation to flat field validation
- Fields like `transaction_id`, `gateway_order_id`, `amount_cents`, etc. are now top-level
- Boolean fields accept: `0`, `1`, `true`, `false`

**Payment Processing Updated:**
- Reads payment data from flat request fields instead of `payment_details` array
- Uses `filter_var()` to convert string booleans to actual booleans
- Stores complete payment data in `raw_response` JSON field

### ✅ Postman Collection Updated

**All online payment requests now use form-data:**
- "2. Checkout - Online Payment (Success)" - form-data with `success=1`
- "2b. Checkout - Online Payment (Failed)" - form-data with `success=0`

---

## Request Format

### Cash Payment (Unchanged)
```
POST /api/user/checkout
Content-Type: multipart/form-data

address_id=1
payment_method=cash
notes=Optional notes
```

### Online Payment - Success (NEW FORMAT)
```
POST /api/user/checkout
Content-Type: multipart/form-data

address_id=1
payment_method=online
notes=Optional notes
transaction_id=367786330
gateway_order_id=415312014
amount_cents=10000
currency=EGP
success=1
is_3d_secure=1
card_type=MasterCard
card_pan=2346
gateway_response=Approved
txn_response_code=APPROVED
integration_id=5385685
hmac=bef21618f8d41ae654bf...
payment_created_at=2025-11-10T14:50:32.722525
merchant_commission=0
accept_fees=0
```

### Online Payment - Failed (NEW FORMAT)
```
POST /api/user/checkout
Content-Type: multipart/form-data

address_id=1
payment_method=online
transaction_id=367786331
gateway_order_id=415312015
amount_cents=10000
currency=EGP
success=0
... (other fields)
```

---

## Field Mapping

| Field Name | Type | Required | Description | Example |
|------------|------|----------|-------------|---------|
| `address_id` | integer | Yes | User's delivery address ID | `1` |
| `payment_method` | string | Yes | `cash` or `online` | `online` |
| `notes` | string | No | Order notes (max 500 chars) | `Deliver soon` |
| `transaction_id` | string | Yes (online) | Payment gateway transaction ID | `367786330` |
| `gateway_order_id` | string | Yes (online) | Gateway's order reference | `415312014` |
| `amount_cents` | integer | Yes (online) | Amount in cents | `10000` |
| `currency` | string | No | Currency code | `EGP` |
| `success` | boolean | Yes (online) | Payment success (1/0) | `1` |
| `is_3d_secure` | boolean | No | 3D Secure flag (1/0) | `1` |
| `card_type` | string | No | Card type | `MasterCard` |
| `card_pan` | string | No | Last 4 digits | `2346` |
| `gateway_response` | string | No | Gateway message | `Approved` |
| `txn_response_code` | string | No | Transaction code | `APPROVED` |
| `integration_id` | integer | No | Integration ID | `5385685` |
| `hmac` | string | No | HMAC signature | `bef21...` |
| `payment_created_at` | string | No | Gateway timestamp | `2025-11-10...` |
| `merchant_commission` | numeric | No | Commission amount | `0` |
| `accept_fees` | numeric | No | Gateway fees | `0` |

---

## Boolean Fields

**Important:** Boolean fields in form-data are sent as strings. The backend accepts:
- `1`, `"1"`, `true`, `"true"` → `true`
- `0`, `"0"`, `false`, `"false"` → `false`

**Affected fields:**
- `success`
- `is_3d_secure`

---

## Backend Processing

```php
// Convert string boolean to actual boolean
$success = filter_var($request->success, FILTER_VALIDATE_BOOLEAN);
$is3dSecure = filter_var($request->is_3d_secure ?? false, FILTER_VALIDATE_BOOLEAN);

// Create payment record
$payment = Payment::create([
    'order_id' => $order->id,
    'transaction_id' => $request->transaction_id,
    'gateway_order_id' => $request->gateway_order_id,
    'amount_cents' => $request->amount_cents,
    'currency' => $request->currency ?? 'EGP',
    'success' => $success,
    'status' => $success ? 'completed' : 'failed',
    'is_3d_secure' => $is3dSecure,
    // ... other fields
    'raw_response' => $request->only([
        'transaction_id', 'gateway_order_id', 'amount_cents', 
        // ... all payment fields
    ]),
]);
```

---

## Response

### Success Response (success=1)
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
      "total": "100.00"
    }
  }
}
```

### Failed Response (success=0)
```json
{
  "success": true,
  "message": "Payment failed",
  "data": {
    "order": {
      "id": 2,
      "order_number": "ORD-20251111-00002",
      "status": "pending_payment",
      "payment_method": "online",
      "payment_status": "failed",
      "payment_reference": null,
      "total": "100.00"
    }
  }
}
```

---

## Benefits

✅ **Consistent Format** - All requests use form-data (no mixing JSON and form-data)  
✅ **Simpler Frontend** - No need to nest objects, just flat key-value pairs  
✅ **Better Compatibility** - Works with all HTTP clients and tools  
✅ **Easier Testing** - Postman form-data is easier to edit than JSON  
✅ **Standard Practice** - Form-data is common for multipart requests  

---

## Testing

1. **Import Updated Collection**: `postman/Order_API_Collection.json`
2. **Set Variables**: `user_token`, `base_url`
3. **Test Scenarios**:
   - Cash payment (basic fields)
   - Online payment success (`success=1`)
   - Online payment failed (`success=0`)

---

## Migration Notes

### Old Format (JSON)
```json
{
  "address_id": 1,
  "payment_method": "online",
  "payment_details": {
    "transaction_id": "367786330",
    "success": true
  }
}
```

### New Format (Form-Data)
```
address_id=1
payment_method=online
transaction_id=367786330
success=1
```

**Key Differences:**
- No nested `payment_details` object
- All fields are flat/top-level
- Booleans as `1`/`0` instead of `true`/`false`
- Content-Type: `multipart/form-data` (not `application/json`)
