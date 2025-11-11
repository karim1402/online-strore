# User Checkout Feature Specification

**Feature**: user-check-out  
**Version**: 1.0  
**Status**: Planning  
**Created**: 2025-10-30

---

## Overview

Enable authenticated users to complete their purchase by converting their cart into an order. Users can choose between cash on delivery or online payment methods. The system will validate cart items, calculate totals, create orders, and handle payment processing (online payment gateway integration is placeholder for future implementation).

---

## User Stories

### US-1: Checkout with Cash on Delivery
**As a** user  
**I want to** checkout my cart and pay cash on delivery  
**So that** I can complete my order without online payment

**Acceptance Criteria**:
- User must be authenticated
- User must have items in cart
- User must select a delivery address
- User can add order notes (optional)
- System validates all cart items are still available
- System creates order with status "pending"
- System clears the cart after successful checkout
- User receives order confirmation with order number

### US-2: Checkout with Online Payment
**As a** user  
**I want to** checkout my cart and pay online  
**So that** I can complete payment immediately

**Acceptance Criteria**:
- User must be authenticated
- User must have items in cart
- User must select a delivery address
- User can add order notes (optional)
- System validates all cart items are still available
- System creates order with status "pending_payment"
- System generates payment intent/reference (placeholder)
- User receives payment URL/reference (placeholder)
- Cart is cleared only after successful payment confirmation

### US-3: View Order Summary Before Checkout
**As a** user  
**I want to** review my order summary before confirming  
**So that** I can verify items, prices, and delivery details

**Acceptance Criteria**:
- Display all cart items with quantities and prices
- Show selected options and addons for each item
- Display subtotal, delivery fee, tax (if applicable)
- Display total amount
- Show selected delivery address
- Show estimated delivery time (if available)

### US-4: Validate Cart Before Checkout
**As a** user  
**I want to** be notified if cart items are no longer available  
**So that** I can update my cart before checkout

**Acceptance Criteria**:
- System checks product availability
- System checks product active status
- System checks store status (must be approved)
- System validates stock levels (if implemented)
- User receives clear error messages for unavailable items
- User can remove unavailable items and retry

---

## Functional Requirements

### FR-1: Checkout Initiation
- **FR-1.1**: User must be authenticated with JWT token
- **FR-1.2**: User must have at least one item in cart
- **FR-1.3**: User must select a delivery address (from saved addresses)
- **FR-1.4**: User must select payment method (cash/online)
- **FR-1.5**: User can optionally add order notes (max 500 characters)

### FR-2: Cart Validation
- **FR-2.1**: Validate all products are active
- **FR-2.2**: Validate all products belong to approved stores
- **FR-2.3**: Validate product prices haven't changed (use current prices)
- **FR-2.4**: Validate addons are still active and available
- **FR-2.5**: Validate selected options are still valid
- **FR-2.6**: Return detailed validation errors if any item fails

### FR-3: Order Creation
- **FR-3.1**: Generate unique order number (e.g., ORD-20251030-XXXXX)
- **FR-3.2**: Create order record with user, store, and address details
- **FR-3.3**: Copy cart items to order_items table
- **FR-3.4**: Copy selected options to order_item_options table
- **FR-3.5**: Copy selected addons to order_item_addons table
- **FR-3.6**: Store snapshot of prices at time of order
- **FR-3.7**: Calculate and store subtotal, delivery fee, tax, total
- **FR-3.8**: Set order status based on payment method:
  - Cash: `pending` (awaiting confirmation)
  - Online: `pending_payment` (awaiting payment)

### FR-4: Payment Processing
- **FR-4.1**: Cash on Delivery:
  - Mark payment method as "cash"
  - Set payment status as "pending"
  - Order is ready for vendor confirmation
- **FR-4.2**: Online Payment (Placeholder):
  - Mark payment method as "online"
  - Generate payment reference/intent ID
  - Return payment URL or reference (placeholder response)
  - Do NOT clear cart until payment confirmed
  - Provide webhook endpoint for payment confirmation (future)

### FR-5: Post-Checkout Actions
- **FR-5.1**: Clear user's cart after successful cash order
- **FR-5.2**: Keep cart for online payment until payment confirmed
- **FR-5.3**: Log order creation activity
- **FR-5.4**: Send order confirmation response with order details
- **FR-5.5**: Prepare for future notification system (email/SMS)

### FR-6: Order Retrieval
- **FR-6.1**: User can view their order history
- **FR-6.2**: User can view single order details
- **FR-6.3**: User can filter orders by status
- **FR-6.4**: Orders include all items, options, addons, and pricing

---

## Non-Functional Requirements

### NFR-1: Performance
- Checkout process completes within 2 seconds
- Order creation is atomic (transaction-based)
- Cart validation uses eager loading (max 5 queries)

### NFR-2: Security
- All endpoints require JWT authentication
- Validate user owns the cart being checked out
- Validate user owns the selected delivery address
- Prevent price manipulation (use server-side prices)
- Sanitize order notes input

### NFR-3: Data Integrity
- Use database transactions for order creation
- Ensure cart items are copied accurately to order
- Maintain price history (snapshot at order time)
- Prevent duplicate orders (idempotency)

### NFR-4: Localization
- All messages in English and Arabic
- Order confirmation in user's preferred language
- Currency formatting (SAR)
- Date/time formatting based on locale

### NFR-5: Scalability
- Support concurrent checkouts from multiple users
- Handle cart validation for large carts (up to 50 items)
- Optimize database queries with indexes

---

## Data Model

### Orders Table
```
orders:
  - id (PK)
  - order_number (unique, indexed)
  - user_id (FK → users)
  - store_id (FK → stores)
  - address_id (FK → user_addresses, nullable)
  - address_snapshot (JSON) - Copy of address at order time
  - payment_method (enum: cash, online)
  - payment_status (enum: pending, paid, failed, refunded)
  - payment_reference (nullable) - For online payments
  - order_status (enum: pending, pending_payment, confirmed, preparing, ready, out_for_delivery, delivered, cancelled)
  - subtotal (decimal)
  - delivery_fee (decimal, default 0)
  - tax (decimal, default 0)
  - total (decimal)
  - notes (text, nullable)
  - created_at
  - updated_at
```

### Order Items Table
```
order_items:
  - id (PK)
  - order_id (FK → orders, cascade)
  - product_id (FK → products)
  - product_snapshot (JSON) - Product details at order time
  - quantity (integer)
  - unit_price (decimal) - Price per item including options/addons
  - total_price (decimal) - unit_price × quantity
  - created_at
  - updated_at
```

### Order Item Options Table
```
order_item_options:
  - id (PK)
  - order_item_id (FK → order_items, cascade)
  - option_snapshot (JSON) - Option details at order time
  - created_at
```

### Order Item Addons Table
```
order_item_addons:
  - id (PK)
  - order_item_id (FK → order_items, cascade)
  - addon_snapshot (JSON) - Addon details at order time
  - quantity (integer)
  - unit_price (decimal)
  - total_price (decimal)
  - created_at
```

---

## API Endpoints

### 1. POST /api/user/checkout
**Description**: Initiate checkout and create order

**Request**:
```json
{
  "address_id": 1,
  "payment_method": "cash",
  "notes": "Please call before delivery"
}
```

**Response (Cash - 201)**:
```json
{
  "success": true,
  "message": "Order placed successfully",
  "data": {
    "order": {
      "id": 1,
      "order_number": "ORD-20251030-00001",
      "status": "pending",
      "payment_method": "cash",
      "payment_status": "pending",
      "subtotal": "150.00",
      "delivery_fee": "10.00",
      "tax": "0.00",
      "total": "160.00",
      "items_count": 3,
      "created_at": "2025-10-30T10:30:00Z"
    }
  }
}
```

**Response (Online - 201)**:
```json
{
  "success": true,
  "message": "Order created. Please complete payment",
  "data": {
    "order": {
      "id": 2,
      "order_number": "ORD-20251030-00002",
      "status": "pending_payment",
      "payment_method": "online",
      "payment_status": "pending",
      "payment_reference": "PAY-PLACEHOLDER-12345",
      "payment_url": "https://payment.example.com/pay/12345",
      "total": "160.00"
    }
  }
}
```

**Validation Errors (422)**:
```json
{
  "success": false,
  "message": "Validation failed",
  "errors": {
    "cart": ["Cart is empty"],
    "address_id": ["Invalid address"],
    "items": [
      {
        "product_id": 5,
        "error": "Product is no longer available"
      }
    ]
  }
}
```

### 2. GET /api/user/orders
**Description**: Get user's order history

**Query Parameters**:
- `status` (optional): Filter by order status
- `page` (optional): Pagination
- `per_page` (optional): Items per page

**Response (200)**:
```json
{
  "success": true,
  "data": {
    "orders": [...],
    "pagination": {...}
  }
}
```

### 3. GET /api/user/orders/{orderId}
**Description**: Get single order details

**Response (200)**:
```json
{
  "success": true,
  "data": {
    "order": {
      "id": 1,
      "order_number": "ORD-20251030-00001",
      "store": {...},
      "address": {...},
      "items": [...],
      "status": "pending",
      "payment_method": "cash",
      "total": "160.00",
      "created_at": "..."
    }
  }
}
```

### 4. POST /api/user/orders/{orderId}/cancel
**Description**: Cancel order (only if status is pending or pending_payment)

**Response (200)**:
```json
{
  "success": true,
  "message": "Order cancelled successfully"
}
```

---

## Business Rules

### BR-1: Cart Requirements
- Cart must not be empty
- All cart items must be from the same store
- Cart must belong to authenticated user

### BR-2: Address Validation
- Address must exist and belong to user
- Address must have complete information
- Address coordinates required for delivery calculation

### BR-3: Payment Method Rules
- Cash on delivery: Order status = "pending"
- Online payment: Order status = "pending_payment"
- Online payment: Cart not cleared until payment confirmed

### BR-4: Order Number Generation
- Format: ORD-YYYYMMDD-XXXXX
- XXXXX is sequential number for the day
- Must be unique across all orders

### BR-5: Price Calculation
- Use current product prices at checkout time
- Include option price adjustments
- Include addon prices × quantities
- Delivery fee: Fixed or calculated (placeholder: 10.00)
- Tax: Placeholder (0.00 for now)

### BR-6: Order Status Flow
```
Cash Payment:
pending → confirmed → preparing → ready → out_for_delivery → delivered
                ↓
            cancelled

Online Payment:
pending_payment → paid → confirmed → preparing → ready → out_for_delivery → delivered
       ↓                    ↓
    failed              cancelled
```

### BR-7: Cancellation Rules
- User can cancel if status is "pending" or "pending_payment"
- Cannot cancel after "confirmed"
- Vendor/Admin can cancel at any time with reason

---

## Success Criteria

### SC-1: Functional Success
- ✅ User can checkout with cash on delivery
- ✅ User can initiate online payment checkout
- ✅ Cart is validated before order creation
- ✅ Order is created with all cart items
- ✅ Prices are captured at order time
- ✅ Cart is cleared after successful cash order
- ✅ User can view order history
- ✅ User can view order details

### SC-2: Technical Success
- ✅ All database tables created
- ✅ All API endpoints functional
- ✅ Transactions ensure data integrity
- ✅ Activity logging for all order operations
- ✅ Bilingual support (EN/AR)
- ✅ Response time < 2 seconds

### SC-3: Quality Success
- ✅ No duplicate orders created
- ✅ Price manipulation prevented
- ✅ Proper error handling
- ✅ Clear validation messages
- ✅ Comprehensive API documentation

---

## Technical Constraints

### TC-1: Dependencies
- Requires completed cart system
- Requires user address system
- Uses existing Product, Store, Addon models
- Uses LocalizationService for translations
- Uses Spatie Activity Log for tracking

### TC-2: Technology Stack
- Laravel 10.x
- MySQL database
- JWT authentication (Tymon)
- RESTful API design

### TC-3: Future Integrations
- Payment gateway (placeholder for now)
- Notification system (email/SMS)
- Real-time order tracking
- Delivery fee calculation service

---

## Out of Scope (v1)

- ❌ Actual payment gateway integration
- ❌ Payment webhook handling
- ❌ Email/SMS notifications
- ❌ Real-time order tracking
- ❌ Dynamic delivery fee calculation
- ❌ Tax calculation
- ❌ Discount codes/coupons
- ❌ Order rating/review
- ❌ Vendor order management (separate feature)

---

## Clarifications

### Session 1: Initial Planning (2025-10-30)

**Q1: What payment methods should be supported?**
**A**: Cash on delivery and online payment. Online payment will be placeholder (no actual gateway integration yet).

**Q2: Should cart be cleared immediately for online payments?**
**A**: No. Cart should only be cleared after payment confirmation. For now, it remains until manual confirmation or timeout.

**Q3: What order statuses are needed?**
**A**: 
- `pending` - Cash orders awaiting vendor confirmation
- `pending_payment` - Online orders awaiting payment
- `confirmed` - Vendor confirmed
- `preparing` - Being prepared
- `ready` - Ready for pickup/delivery
- `out_for_delivery` - With delivery person
- `delivered` - Completed
- `cancelled` - Cancelled by user/vendor

**Q4: Should we validate stock levels?**
**A**: Not in v1. Assume unlimited stock. Validation focuses on product availability and store status.

**Q5: How should delivery fees be calculated?**
**A**: Use fixed placeholder value (10.00 SAR) for now. Future version will integrate with delivery service.

**Q6: Should we store address as reference or snapshot?**
**A**: Both. Store address_id as reference and address_snapshot (JSON) to preserve address at order time.

**Q7: What happens if product price changes between cart and checkout?**
**A**: Use current price at checkout time. This is intentional - users see final price before confirming.

---

## Dependencies

- ✅ User authentication system (JWT)
- ✅ Cart system (user-cart feature)
- ✅ User address system
- ✅ Product, Store, Addon models
- ✅ LocalizationService
- ✅ Activity logging system

---

## Risks & Mitigations

| Risk | Impact | Mitigation |
|------|--------|------------|
| Concurrent checkout attempts | High | Use database transactions and row locking |
| Price changes during checkout | Medium | Accept current prices, show clear confirmation |
| Cart validation failures | Medium | Provide detailed error messages, allow retry |
| Payment gateway delays | Low | Use placeholder, design for future integration |
| Large cart performance | Medium | Optimize queries, use eager loading |

---

**Next Steps**: Run `/plan` to generate implementation plan with research, data model, and task breakdown.
