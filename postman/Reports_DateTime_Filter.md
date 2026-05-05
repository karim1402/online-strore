# Reports API — DateTime Filter Update

## What Changed

All `start_date` and `end_date` filter parameters across every report endpoint (including export) now accept **full datetime values** in addition to plain dates.

---

## How It Works

| Input format | Behavior |
|---|---|
| `2026-05-05` | Date only — start defaults to `00:00:00`, end defaults to `23:59:59` |
| `2026-05-05 14:30:00` | Datetime — used exactly as provided |

The logic is: if the value contains a `:` (a time component), use it as-is. Otherwise, apply start/end of day automatically. This is fully **backward compatible** — existing integrations passing plain dates work exactly as before.

---

## Affected Endpoints

All report endpoints that accept `period=custom` with `start_date` / `end_date`:

| Endpoint | Notes |
|---|---|
| `GET /api/admin/reports/total-revenue` | |
| `GET /api/admin/reports/revenue-over-time` | |
| `GET /api/admin/reports/revenue-by-module` | |
| `GET /api/admin/reports/revenue-by-category` | |
| `GET /api/admin/reports/revenue-by-store` | |
| `GET /api/admin/reports/revenue-by-payment-method` | |
| `GET /api/admin/reports/discount-impact` | |
| `GET /api/admin/reports/revenue-trend` | Has its own `start_date`/`end_date` (no `period`) |
| `GET /api/admin/reports/payment-methods-breakdown` | |
| `GET /api/admin/reports/items-summary` | |
| `GET /api/admin/reports/orders-by-store` | |
| `GET /api/admin/reports/orders-by-module` | |
| `GET /api/admin/reports/orders-per-day` | |
| `GET /api/admin/reports/delivery-vs-pickup` | |
| `GET /api/admin/reports/order-distribution` | |
| `GET /api/admin/reports/order-cancellations` | |
| `GET /api/admin/reports/average-order-value` | |
| `GET /api/admin/reports/admin-created-orders` | |
| `GET /api/admin/reports/top-selling-products` | |
| `GET /api/admin/reports/most-viewed-products` | |
| `GET /api/admin/reports/best-sellers` | |
| `GET /api/admin/reports/products-with-offers` | |
| `GET /api/admin/reports/user-growth` | |
| `GET /api/admin/reports/top-customers` | |
| `GET /api/admin/reports/inactive-users` | |
| `GET /api/admin/reports/driver-performance` | |
| `GET /api/admin/reports/driver-availability` | |
| `GET /api/admin/reports/deliveries-per-day` | Has its own `start_date`/`end_date` (no `period`) |
| `GET /api/admin/reports/voucher-usage` | |
| `GET /api/admin/reports/voucher-users/{id}` | |
| `GET /api/admin/reports/payment-success-rate` | |
| `GET /api/admin/reports/merchant-fees` | |
| `GET /api/admin/reports/monthly-payment-trend` | Has its own `start_date`/`end_date` (no `period`) |
| `GET /api/admin/reports/store-status-overview` | |
| `GET /api/admin/reports/campaign-performance` | |
| `GET /api/admin/reports/source-performance` | |
| `POST /api/admin/reports/export` | Delegates to the above — inherits datetime support automatically |

---

## Filter Parameters

### Standard period filter (most endpoints)

| Param | Type | Description |
|---|---|---|
| `period` | string | `today` \| `week` \| `month` \| `year` \| `custom` \| `all` |
| `start_date` | string | Used when `period=custom`. Accepts `YYYY-MM-DD` or `YYYY-MM-DD HH:MM:SS` |
| `end_date` | string | Used when `period=custom`. Accepts `YYYY-MM-DD` or `YYYY-MM-DD HH:MM:SS` |

### Direct date filter (revenue-trend, deliveries-per-day, monthly-payment-trend)

These endpoints use `start_date` / `end_date` directly without requiring `period=custom`:

| Param | Type | Description |
|---|---|---|
| `start_date` | string | Accepts `YYYY-MM-DD` or `YYYY-MM-DD HH:MM:SS` |
| `end_date` | string | Accepts `YYYY-MM-DD` or `YYYY-MM-DD HH:MM:SS` |

---

## Examples

**Filter orders between 9 AM and 5 PM on a specific day:**
```
GET /api/admin/reports/revenue-trend
    ?start_date=2026-05-05 09:00:00
    &end_date=2026-05-05 17:00:00
```

**Filter a custom date range (date only — backward compatible):**
```
GET /api/admin/reports/total-revenue
    ?period=custom
    &start_date=2026-04-01
    &end_date=2026-04-30
```

**Export with datetime precision:**
```
POST /api/admin/reports/export
Content-Type: application/json

{
  "report_type": "revenue_by_module",
  "period": "custom",
  "start_date": "2026-05-01 08:00:00",
  "end_date": "2026-05-05 20:00:00"
}
```
