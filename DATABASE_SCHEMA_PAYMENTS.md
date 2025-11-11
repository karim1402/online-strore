# Payments Table Schema

## Table: `payments`

Stores all payment transactions for orders. Each order can have multiple payment attempts.

### Columns

| Column | Type | Nullable | Default | Description |
|--------|------|----------|---------|-------------|
| `id` | bigint unsigned | NO | AUTO_INCREMENT | Primary key |
| `order_id` | bigint unsigned | NO | - | Foreign key to orders table |
| `transaction_id` | varchar(255) | NO | - | Unique payment gateway transaction ID |
| `gateway_order_id` | varchar(255) | YES | NULL | Payment gateway's order reference |
| `amount_cents` | int | NO | - | Payment amount in cents (e.g., 10000 = 100.00) |
| `currency` | varchar(10) | NO | 'EGP' | Currency code |
| `success` | tinyint(1) | NO | 0 | Payment success flag (0=failed, 1=success) |
| `status` | enum | NO | 'pending' | Payment status: pending, completed, failed, refunded |
| `is_3d_secure` | tinyint(1) | NO | 0 | 3D Secure authentication flag |
| `card_type` | varchar(255) | YES | NULL | Card type (Visa, MasterCard, etc.) |
| `card_pan` | varchar(255) | YES | NULL | Last 4 digits of card number |
| `gateway_response` | varchar(255) | YES | NULL | Gateway response message |
| `txn_response_code` | varchar(255) | YES | NULL | Transaction response code |
| `integration_id` | int | YES | NULL | Payment gateway integration ID |
| `hmac` | text | YES | NULL | HMAC signature for verification |
| `merchant_commission` | decimal(10,2) | NO | 0.00 | Merchant commission amount |
| `accept_fees` | decimal(10,2) | NO | 0.00 | Payment gateway fees |
| `payment_created_at` | timestamp | YES | NULL | Timestamp from payment gateway |
| `raw_response` | json | YES | NULL | Complete payment gateway response |
| `created_at` | timestamp | YES | NULL | Record creation timestamp |
| `updated_at` | timestamp | YES | NULL | Record update timestamp |

### Indexes

- **PRIMARY KEY**: `id`
- **FOREIGN KEY**: `order_id` → `orders(id)` ON DELETE CASCADE
- **UNIQUE**: `transaction_id`
- **INDEX**: `order_id`
- **INDEX**: `transaction_id`
- **INDEX**: `status`
- **INDEX**: `created_at`

### Relationships

- **Belongs To**: `Order` (many payments can belong to one order)

---

## Order-Payment Relationship

### One-to-Many Relationship

An order can have multiple payment attempts (e.g., failed payment followed by successful retry).

```php
// Order Model
public function payments()
{
    return $this->hasMany(Payment::class);
}

public function latestPayment()
{
    return $this->hasOne(Payment::class)->latestOfMany();
}

// Payment Model
public function order()
{
    return $this->belongsTo(Order::class);
}
```

### Usage Examples

```php
// Get all payments for an order
$order = Order::find(1);
$payments = $order->payments;

// Get latest payment for an order
$latestPayment = $order->latestPayment;

// Get successful payments only
$successfulPayments = $order->payments()->successful()->get();

// Get order from payment
$payment = Payment::find(1);
$order = $payment->order;
```

---

## Payment Status Flow

```
pending → completed (success: true)
        → failed (success: false)
        → refunded (manual refund)
```

### Status Descriptions

- **pending**: Payment initiated but not yet confirmed
- **completed**: Payment successfully processed
- **failed**: Payment processing failed
- **refunded**: Payment was refunded to customer

---

## Sample Data

### Successful Payment

```json
{
  "id": 1,
  "order_id": 123,
  "transaction_id": "367786330",
  "gateway_order_id": "415312014",
  "amount_cents": 10000,
  "currency": "EGP",
  "success": true,
  "status": "completed",
  "is_3d_secure": true,
  "card_type": "MasterCard",
  "card_pan": "2346",
  "gateway_response": "Approved",
  "txn_response_code": "APPROVED",
  "integration_id": 5385685,
  "hmac": "bef21618f8d41ae654bf...",
  "merchant_commission": 0.00,
  "accept_fees": 0.00,
  "payment_created_at": "2025-11-10T14:50:32.000000Z",
  "raw_response": {
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
  },
  "created_at": "2025-11-11T10:56:00.000000Z",
  "updated_at": "2025-11-11T10:56:00.000000Z"
}
```

---

## Querying Payments

### Get All Successful Payments

```php
$successfulPayments = Payment::successful()->get();
```

### Get Failed Payments for an Order

```php
$failedPayments = Payment::where('order_id', $orderId)
    ->failed()
    ->get();
```

### Get Payments by Date Range

```php
$payments = Payment::whereBetween('created_at', [$startDate, $endDate])
    ->successful()
    ->get();
```

### Calculate Total Revenue

```php
$totalRevenue = Payment::successful()
    ->sum('amount_cents') / 100;
```

### Get Payment with Order Details

```php
$payment = Payment::with('order.user', 'order.store')
    ->find($paymentId);
```

---

## Benefits of Separate Payments Table

1. **Multiple Payment Attempts**: Track all payment attempts for an order
2. **Better Auditing**: Complete history of all transactions
3. **Easier Reconciliation**: Match payments with gateway reports
4. **Refund Support**: Track refunds separately from original payments
5. **Analytics**: Generate payment reports and statistics
6. **Scalability**: Better performance for payment-related queries
7. **Data Integrity**: Proper foreign key constraints
8. **Flexibility**: Add payment-specific features without cluttering orders table
