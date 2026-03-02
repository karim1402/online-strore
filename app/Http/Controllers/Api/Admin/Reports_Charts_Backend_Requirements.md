# Reports — Backend API Requirements for Charts

This document lists every visual chart or data visualization in the Reports section that currently uses **sample (dummy) data**, along with the exact endpoint URL, accepted query parameters, and expected JSON response shape needed to replace them with live data.

All endpoints are prefixed with `/admin/reports/`. All dates are `YYYY-MM-DD` strings unless noted.

---

## Common Query Parameters

All filter-aware endpoints accept these optional query parameters:

| Parameter    | Type   | Description                        |
|--------------|--------|------------------------------------|
| `start_date` | string | Filter start date (`YYYY-MM-DD`)   |
| `end_date`   | string | Filter end date (`YYYY-MM-DD`)     |
| `period`     | string | `day` \| `week` \| `month` \| `year` |

---

## 1. Sales Tab

### 1.1 Revenue Trend (Bar Chart — DUMMY)

> Displays daily revenue over the selected period as a vertical bar chart.

**Endpoint:** `GET /admin/reports/sales/revenue-trend`

**Query Params:** `start_date`, `end_date`, `period` (default: `day`)

**Expected Response:**
```json
{
  "success": true,
  "data": [
    { "date": "2025-02-01", "gross_revenue": 4500.00, "net_revenue": 3900.00 },
    { "date": "2025-02-04", "gross_revenue": 5200.00, "net_revenue": 4600.00 }
  ]
}
```

| Field           | Type   | Description                      |
|-----------------|--------|----------------------------------|
| `date`          | string | The date label for the data point|
| `gross_revenue` | number | Gross revenue on that date       |
| `net_revenue`   | number | Net revenue after discounts/fees |

---

### 1.2 Payment Methods Breakdown (Progress Bar Chart — DUMMY)

> Shows the percentage split of payment methods (Credit Card, Cash, Wallet, etc.).

**Endpoint:** `GET /admin/reports/sales/payment-methods`

**Query Params:** `start_date`, `end_date`

**Expected Response:**
```json
{
  "success": true,
  "data": [
    { "method": "credit_card", "label": "Credit Card", "count": 520, "percentage": 65.0 },
    { "method": "cash",        "label": "Cash on Delivery", "count": 200, "percentage": 25.0 },
    { "method": "wallet",      "label": "Wallet", "count": 80, "percentage": 10.0 }
  ]
}
```

| Field        | Type   | Description                        |
|--------------|--------|------------------------------------|
| `method`     | string | Machine-readable method key        |
| `label`      | string | Human-readable display name        |
| `count`      | number | Number of transactions             |
| `percentage` | number | Share of total (0–100)             |

---

### 1.3 Revenue by Module (Table — DUMMY)

> Breaks down total orders and revenue by business module (Ecommerce, Food Delivery, Pharmacy, etc.).

**Endpoint:** `GET /admin/reports/sales/revenue-by-module`

**Query Params:** `start_date`, `end_date`

**Expected Response:**
```json
{
  "success": true,
  "data": [
    { "module": "ecommerce",     "label": "Ecommerce",     "orders": 1240, "revenue": 450000.00, "percentage": 68.0 },
    { "module": "food_delivery", "label": "Food Delivery", "orders": 850,  "revenue": 180000.00, "percentage": 27.0 },
    { "module": "pharmacy",      "label": "Pharmacy",      "orders": 120,  "revenue": 35000.00,  "percentage": 5.0  }
  ]
}
```

| Field        | Type   | Description                    |
|--------------|--------|--------------------------------|
| `module`     | string | Machine-readable module key    |
| `label`      | string | Display name                   |
| `orders`     | number | Total order count              |
| `revenue`    | number | Total revenue (EGP)            |
| `percentage` | number | Share of total revenue (0–100) |

---

## 2. Orders Tab

### 2.1 Order Statuses Distribution (Progress Bar Chart — DUMMY)

> Shows how many orders are in each status (Completed, Out for Delivery, Preparing, Pending, Cancelled, etc.).

**Endpoint:** `GET /admin/reports/orders/status-distribution`

**Query Params:** `start_date`, `end_date`

**Expected Response:**
```json
{
  "success": true,
  "data": {
    "total": 2400,
    "statuses": [
      { "status": "completed",         "label": "Completed",         "count": 1840, "percentage": 76.7 },
      { "status": "out_for_delivery",  "label": "Out for Delivery",  "count": 320,  "percentage": 13.3 },
      { "status": "preparing",         "label": "Preparing",         "count": 180,  "percentage": 7.5  },
      { "status": "pending",           "label": "Pending",           "count": 60,   "percentage": 2.5  }
    ]
  }
}
```

| Field               | Type   | Description                     |
|---------------------|--------|---------------------------------|
| `total`             | number | Grand total orders in period    |
| `statuses[].status` | string | Machine-readable status key     |
| `statuses[].label`  | string | Display label                   |
| `statuses[].count`  | number | Number of orders in this status |
| `statuses[].percentage` | number | Share of total (0–100)     |

---

## 3. Delivery Tab

### 3.1 Deliveries Per Day (Bar Chart — DUMMY)

> Shows how many deliveries were completed each day of the week/range.

**Endpoint:** `GET /admin/reports/delivery/deliveries-per-day`

**Query Params:** `start_date`, `end_date`

**Expected Response:**
```json
{
  "success": true,
  "data": [
    { "date": "2025-02-17", "day_label": "Mon", "deliveries": 18, "avg_delivery_time_minutes": 34 },
    { "date": "2025-02-18", "day_label": "Tue", "deliveries": 24, "avg_delivery_time_minutes": 29 },
    { "date": "2025-02-19", "day_label": "Wed", "deliveries": 15, "avg_delivery_time_minutes": 41 }
  ]
}
```

| Field                       | Type   | Description                              |
|-----------------------------|--------|------------------------------------------|
| `date`                      | string | Full date of the data point              |
| `day_label`                 | string | Short day label (`Mon`, `Tue`, …)        |
| `deliveries`                | number | Completed deliveries on that day         |
| `avg_delivery_time_minutes` | number | Average delivery duration (minutes)      |

---

## 4. Payments Tab

### 4.1 Monthly Payment Trend (Stacked Bar Chart — DUMMY)

> Shows successful vs. failed payment counts per month.

**Endpoint:** `GET /admin/reports/payments/monthly-trend`

**Query Params:** `start_date`, `end_date`

**Expected Response:**
```json
{
  "success": true,
  "data": [
    { "month": "2024-09", "month_label": "Sep", "successful": 62, "failed": 4, "total": 66 },
    { "month": "2024-10", "month_label": "Oct", "successful": 78, "failed": 2, "total": 80 },
    { "month": "2025-02", "month_label": "Feb", "successful": 19, "failed": 0, "total": 19 }
  ]
}
```

| Field         | Type   | Description                         |
|---------------|--------|-------------------------------------|
| `month`       | string | Year-month key (`YYYY-MM`)          |
| `month_label` | string | Short label (`Jan`, `Feb`, …)       |
| `successful`  | number | Count of successful payments        |
| `failed`      | number | Count of failed/declined payments   |
| `total`       | number | Total payment attempts              |

---

## 5. Summary: Existing (Real) Endpoints Still in Use

These endpoints are already implemented on the backend. Documented here for completeness.

| Tab       | Endpoint                                        | Data Used For                              |
|-----------|-------------------------------------------------|--------------------------------------------|
| Sales     | `GET /sales/revenue/total`                      | Gross/Net revenue, taxes, delivery fees    |
| Sales     | `GET /sales/discounts-impact`                   | Discount ratio, discounted order count     |
| Orders    | `GET /orders/average-value`                     | Avg / min / max order value                |
| Orders    | `GET /orders/cancellations`                     | Cancellation count, lost revenue, reasons  |
| Products  | `GET /products/top-selling`                     | Top selling products table                 |
| Products  | `GET /products/best-sellers`                    | Manually featured best sellers             |
| Products  | `GET /products/with-offers`                     | Products with active offer prices          |
| Users     | `GET /users/top-customers`                      | Top spenders list                          |
| Users     | `GET /users/inactive`                           | Inactive users (paginated infinite scroll) |
| Delivery  | `GET /delivery/driver-availability`             | Driver fleet status (available/busy/offline) |
| Vouchers  | `GET /vouchers/usage`                           | Voucher usage table (code, limit, utilization %) |
| Payments  | `GET /payments/success-rate`                    | Success rate %, total attempts, collected  |
| Payments  | `GET /payments/merchant-fees`                   | Total processed, commission, gateway fees  |
| Stores    | `GET /stores/status-overview`                   | Approved / Pending / Suspended count       |

---

## 6. Known Backend Issues to Fix

> [!CAUTION]
> The following existing endpoints have response shape issues that need to be fixed server-side.

### 6.1 `GET /payments/success-rate` — Eloquent Model Leak
The response currently leaks the raw Eloquent model, nesting real fields under a `\0*\0attributes` private key. The frontend works around this with fallback logic, but the backend should return a clean flat object:

```json
{
  "success": true,
  "data": {
    "success_rate_percentage": 96.5,
    "total_attempts": 400,
    "successful_payments": 386,
    "failed_payments": 14,
    "total_collected": 128500.00
  }
}
```

### 6.2 `GET /payments/merchant-fees` — Renamed Fields
The frontend expects these field names (already updated on the frontend):

```json
{
  "success": true,
  "data": {
    "total_processed": 250000.00,
    "total_commission": 12500.00,
    "total_gateway_fees": 4800.00
  }
}
```

---

*Generated: 2026-02-25 | Frontend project: `makook_web`*
