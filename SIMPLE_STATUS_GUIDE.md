# Simple Order Status Guide

## Overview
Simple 4-status system for tracking orders with clear, user-friendly statuses.

---

## The 4 Statuses

### 1. **in_progress** (Default)
- **When**: Automatically set when order is created
- **Meaning**: Order is being prepared by the store
- **User sees**: "In Progress" / "قيد التحضير"

### 2. **in_delivery**
- **When**: Driver picks up the order
- **Meaning**: Order is on the way to customer
- **User sees**: "In Delivery" / "في الطريق"

### 3. **cancelled**
- **When**: Order is cancelled by user or store
- **Meaning**: Order will not be fulfilled
- **User sees**: "Cancelled" / "ملغي"

### 4. **delivered**
- **When**: Customer receives the order
- **Meaning**: Order completed successfully
- **User sees**: "Delivered" / "تم التوصيل"

---

## Database Schema

```sql
ALTER TABLE orders 
ADD COLUMN simple_status ENUM(
    'in_progress',
    'in_delivery',
    'cancelled',
    'delivered'
) DEFAULT 'in_progress' AFTER order_status;
```

---

## Order Model Usage

### Helper Methods

```php
// Check status
$order->isInProgress()        // true if 'in_progress'
$order->isInDelivery()        // true if 'in_delivery'
$order->isSimpleCancelled()   // true if 'cancelled'
$order->isSimpleDelivered()   // true if 'delivered'

// Get localized label
$order->simple_status_label   // Returns translated status
```

### Update Status

```php
// Mark as in delivery
$order->simple_status = 'in_delivery';
$order->save();

// Mark as delivered
$order->simple_status = 'delivered';
$order->save();

// Cancel order
$order->simple_status = 'cancelled';
$order->save();
```

---

## API Response Format

All order endpoints now return `simple_status`:

### Checkout Response
```json
{
  "success": true,
  "message": "Order placed successfully",
  "data": {
    "order": {
      "id": 1,
      "order_number": "ORD-20251111-00001",
      "status": "confirmed",
      "simple_status": "in_progress",
      "simple_status_label": "In Progress",
      "payment_method": "cash",
      "total": "100.00"
    }
  }
}
```

### Get Orders List
```json
{
  "success": true,
  "data": {
    "orders": [
      {
        "id": 1,
        "order_number": "ORD-20251111-00001",
        "store": {
          "id": 1,
          "name": "Restaurant Name"
        },
        "status": "confirmed",
        "simple_status": "in_progress",
        "simple_status_label": "In Progress",
        "total": "100.00",
        "created_at": "2025-11-11T14:00:00.000Z"
      }
    ]
  }
}
```

### Get Single Order
```json
{
  "success": true,
  "data": {
    "order": {
      "id": 1,
      "order_number": "ORD-20251111-00001",
      "store": { ... },
      "items": [ ... ],
      "status": "confirmed",
      "simple_status": "in_delivery",
      "simple_status_label": "In Delivery",
      "payment_method": "cash",
      "payment_status": "paid",
      "total": "100.00"
    }
  }
}
```

---

## Localization

Add these to your language files:

### English (`resources/lang/en/order.php`)
```php
'simple_status' => [
    'in_progress' => 'In Progress',
    'in_delivery' => 'In Delivery',
    'cancelled' => 'Cancelled',
    'delivered' => 'Delivered',
],
```

### Arabic (`resources/lang/ar/order.php`)
```php
'simple_status' => [
    'in_progress' => 'قيد التحضير',
    'in_delivery' => 'في الطريق',
    'cancelled' => 'ملغي',
    'delivered' => 'تم التوصيل',
],
```

---

## Status Flow

```
┌──────────────┐
│ Order Placed │
└──────┬───────┘
       │
       ▼
┌──────────────┐
│ in_progress  │ (Default - Store preparing)
└──────┬───────┘
       │
       ├─────────────────┐
       │                 │
       ▼                 ▼
┌──────────────┐   ┌──────────┐
│ in_delivery  │   │cancelled │
└──────┬───────┘   └──────────┘
       │
       ▼
┌──────────────┐
│  delivered   │
└──────────────┘
```

---

## Usage Examples

### Example 1: Update to In Delivery
```php
$order = Order::find($orderId);

if ($order->isInProgress()) {
    $order->simple_status = 'in_delivery';
    $order->save();
    
    // Notify customer
    // Send push notification...
}
```

### Example 2: Mark as Delivered
```php
$order = Order::find($orderId);

if ($order->isInDelivery()) {
    $order->simple_status = 'delivered';
    $order->save();
    
    // Log activity
    activity()
        ->performedOn($order)
        ->log('Order delivered successfully');
}
```

### Example 3: Filter by Status
```php
// Get all orders in delivery
$deliveryOrders = Order::where('simple_status', 'in_delivery')
    ->where('user_id', $userId)
    ->get();

// Get completed orders
$completedOrders = Order::where('simple_status', 'delivered')
    ->where('user_id', $userId)
    ->get();
```

### Example 4: Check Status in Controller
```php
public function updateStatus(Request $request, $orderId)
{
    $order = Order::findOrFail($orderId);
    
    $validator = Validator::make($request->all(), [
        'simple_status' => 'required|in:in_progress,in_delivery,cancelled,delivered'
    ]);
    
    if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()], 422);
    }
    
    $order->simple_status = $request->simple_status;
    $order->save();
    
    return response()->json([
        'success' => true,
        'message' => 'Status updated successfully',
        'data' => ['order' => $order]
    ]);
}
```

---

## Endpoints That Return simple_status

All order endpoints now include `simple_status` and `simple_status_label`:

1. ✅ `POST /api/user/checkout` - Create order
2. ✅ `GET /api/user/orders` - List orders
3. ✅ `GET /api/user/orders/{id}` - Get order details
4. ✅ `POST /api/user/orders/{id}/cancel` - Cancel order
5. ✅ `POST /api/user/orders/{id}/confirm-payment` - Confirm payment

---

## Files Modified

1. ✅ **Migration**: `2025_11_11_141234_add_simple_status_to_orders_table.php`
2. ✅ **Model**: `app/Models/Order.php`
   - Added `simple_status` to fillable
   - Added helper methods
   - Added `simple_status_label` accessor
3. ✅ **Controller**: `app/Http/Controllers/Api/User/OrderController.php`
   - Updated `transformOrder()`
   - Updated `transformOrderListItem()`
   - Updated `transformOrderDetail()`

---

## Benefits

✅ **Simple**: Only 4 clear statuses  
✅ **User-Friendly**: Easy to understand for customers  
✅ **Automatic**: Defaults to `in_progress` on order creation  
✅ **Localized**: Supports multiple languages  
✅ **Consistent**: Returned in all order endpoints  

---

## Quick Reference

| Status | Default | Meaning | Next Status |
|--------|---------|---------|-------------|
| `in_progress` | ✅ Yes | Being prepared | `in_delivery`, `cancelled` |
| `in_delivery` | ❌ No | On the way | `delivered` |
| `cancelled` | ❌ No | Cancelled | None (final) |
| `delivered` | ❌ No | Completed | None (final) |

---

Your simple status system is ready! 🚀
