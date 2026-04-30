# Backend Reports Export Fixes

This ticket documents the backend changes needed for the admin reports page so payment methods include revenue, exports return complete datasets, and failing report exports stop returning `500 Internal Server Error`.

## Scope

- **Frontend page:** `https://makook.devdigitalvibes.com/reports`
- **Backend base URL seen in network:** `https://mainmak.devdigitalvibes.com/public/api`
- **Export endpoint:** `GET /admin/reports/export`
- **Frontend export caller:** `src/services/reports.service.ts` -> `reportsService.exportReport(reportType, params)`
- **Frontend export behavior:** sends `report_type` plus date filters such as `period`, `start_date`, and `end_date`.

## Current Problems

### 1. Payment Methods only show percentage/count

The reports page has a Sales & Revenue card named **Payment Methods**. It currently receives payment method data as count and percentage only.

Current frontend shape:

```json
[
  {
    "method": "cash",
    "label": "COD",
    "count": 120,
    "percentage": 55.8
  },
  {
    "method": "online",
    "label": "Online",
    "count": 95,
    "percentage": 44.2
  }
]
```

Required backend change:

- **Add revenue totals per payment method.**
- Keep the existing fields for backward compatibility.
- Include both `online` and `COD/cash` as revenue groups, not only percentages.
- Revenue should respect the selected report date filter.

Expected response shape:

```json
[
  {
    "method": "cash",
    "label": "COD",
    "count": 120,
    "percentage": 55.8,
    "revenue": 152340.5
  },
  {
    "method": "online",
    "label": "Online",
    "count": 95,
    "percentage": 44.2,
    "revenue": 184100.0
  }
]
```

Recommended calculations:

- **count:** number of completed/valid orders for the payment method.
- **revenue:** sum of order totals for the same orders used in the count.
- **percentage:** percentage by order count unless the product owner explicitly asks for revenue percentage.
- **total reference:** `cash.revenue + online.revenue` should match the same eligible order-total base used by the report filter.

Affected endpoint:

```http
GET /admin/reports/sales/payment-methods?period=all
```

Affected export type:

```http
GET /admin/reports/export?period=all&report_type=payment_methods
```

Required export columns:

- Payment Method
- Orders Count
- Revenue
- Percentage

### 2. `top_selling_products` export returns only 50 rows

Observed request:

```http
GET /admin/reports/export?period=all&report_type=top_selling_products
Status: 200 OK
```

Current problem:

- Export succeeds, but it returns only 50 products.
- The export should include **all matching products** for the selected filters.

Required backend change:

- Do not apply the dashboard/list default limit when generating exports.
- For exports, use an unpaginated/chunked export query or explicitly fetch all matching rows.
- Preserve filters such as `period`, `start_date`, `end_date`, and `search` if provided.
- Sort by the report's expected ranking, usually `units_sold DESC` and then revenue/order tie-breakers if needed.

Expected export columns:

- Product ID
- Product Name
- Category
- Units Sold
- Revenue
- Store/Vendor, if available

Acceptance rule:

- If the matching query has 287 products, the Excel/CSV export must contain 287 data rows, not 50.

### 3. `top_customers` export returns only 50 rows

Observed request:

```http
GET /admin/reports/export?period=all&report_type=top_customers
Status: 200 OK
```

Current problem:

- Export succeeds, but it returns only 50 users/customers.
- The export should include **all matching users/customers** for the selected filters.

Required backend change:

- Do not apply the dashboard/list default limit when generating exports.
- For exports, use an unpaginated/chunked query or explicitly fetch all matching rows.
- Preserve filters such as `period`, `start_date`, `end_date`, and `search` if provided.
- Sort by `total_spend DESC` and/or `order_count DESC`, matching the report endpoint.

Expected export columns:

- User ID
- Customer Name
- Email
- Phone
- Orders Count
- Total Spend

Acceptance rule:

- If the matching query has 1,240 customers, the Excel/CSV export must contain 1,240 data rows, not 50.

### 4. Export report types returning 500

The following export requests currently fail with `500 Internal Server Error` and must return a valid export file.

#### `best_sellers`

```http
GET /admin/reports/export?period=all&report_type=best_sellers
Status: 500 Internal Server Error
```

Frontend source:

```tsx
<ExportReportButton reportType="best_sellers" filters={filters} />
```

Expected export columns:

- Product ID
- Product Name
- Category
- Base Price
- Effective Price / Offer Price
- Is Best Seller / Featured Flag
- Status

Likely backend checks:

- Ensure `best_sellers` is registered in the export report-type dispatch/map.
- Ensure product category/price relations are null-safe.
- Ensure selected columns match the actual product model fields.

#### `products_with_offers`

```http
GET /admin/reports/export?period=all&report_type=products_with_offers
Status: 500 Internal Server Error
```

Frontend source:

```tsx
<ExportReportButton reportType="products_with_offers" filters={filters} />
```

Expected export columns:

- Product ID
- Product Name
- Category
- Base Price
- Offer Price
- Discount Percentage
- Offer Start Date, if available
- Offer End Date, if available
- Status

Likely backend checks:

- Ensure `products_with_offers` is registered in the export report-type dispatch/map.
- Handle nullable offer fields and expired/missing offer rows safely.
- Export active offers according to the same logic used by `GET /admin/reports/products/with-offers`.

#### `driver_availability`

```http
GET /admin/reports/export?report_type=driver_availability
Status: 500 Internal Server Error
```

Frontend source:

```tsx
<ExportReportButton reportType="driver_availability" />
```

Important detail:

- This export is currently called **without** `period`.
- Backend must not require `period`; default to `all` or a sensible default used by the availability endpoint.

Expected export columns:

- Status Group
- Driver Count
- Percentage of Fleet

Expected rows:

- Available
- On Delivery / Busy
- Offline

Likely backend checks:

- Ensure missing `period` does not crash validation or date parsing.
- Ensure boolean fields like `status` and `availability` are converted to readable labels.
- Ensure division by zero is avoided when total drivers is `0`.

#### `driver_performance`

```http
GET /admin/reports/export?period=all&report_type=driver_performance
Status: 500 Internal Server Error
```

Frontend source:

```tsx
<ExportReportButton reportType="driver_performance" filters={filters} />
```

Expected export columns:

- Driver ID
- Driver Name
- Completed Deliveries
- Average Rating

Likely backend checks:

- Ensure `driver_performance` is registered in the export report-type dispatch/map.
- Handle drivers with zero deliveries and null ratings safely.
- Use the same date filters as `GET /admin/reports/delivery/driver-performance`.

## Working Reference

The following export currently returns `200 OK` and can be used as a reference for response headers, file creation, and export download behavior:

```http
GET /admin/reports/export?period=all&report_type=inactive_users
Status: 200 OK
```

## Backend Implementation Guidance

### Export report-type dispatch

Verify the export endpoint has handlers for all frontend report types used by the reports page. The affected report types from this ticket are:

- `payment_methods`
- `top_selling_products`
- `top_customers`
- `best_sellers`
- `products_with_offers`
- `driver_availability`
- `driver_performance`
- `inactive_users`

If the backend uses a switch/match/map, missing keys should return a controlled `422` with a clear message, not a `500`.

### Date filter handling

All report exports that accept filters should use the same period logic as their corresponding data endpoints.

Supported frontend period values:

- `today`
- `week`
- `month`
- `year`
- `all`
- `custom`

For `custom`, use:

- `start_date=YYYY-MM-DD`
- `end_date=YYYY-MM-DD`

Rules:

- `period=all` should not silently become a default 50-row result.
- Missing `period` should not crash exports such as `driver_availability`.
- Invalid period values should return validation errors, not internal server errors.

### Export-all behavior

For export endpoints, avoid dashboard pagination limits.

Recommended approach:

- Build the filtered base query once.
- Apply report sorting.
- Stream/chunk the export data if the dataset can be large.
- Do not call `paginate(50)` or apply a hard-coded `limit(50)` for exports unless the user explicitly passes a limit and the export contract says to respect it.

### Error handling

For every export type:

- Log the failing `report_type`, filters, authenticated admin ID, and exception message.
- Return a controlled JSON error only if file generation cannot start.
- Avoid leaking stack traces to the frontend.
- Null-check optional relations such as category, offer, driver rating, and user contact details.

## Acceptance Criteria

- **Payment methods API:** `GET /admin/reports/sales/payment-methods?period=all` returns `revenue` for `online` and `COD/cash` groups.
- **Payment methods export:** `report_type=payment_methods` includes revenue columns.
- **Top selling export:** `report_type=top_selling_products&period=all` exports all matching products, not only 50.
- **Top customers export:** `report_type=top_customers&period=all` exports all matching customers/users, not only 50.
- **Best sellers export:** `report_type=best_sellers&period=all` returns `200 OK` and a valid file.
- **Products with offers export:** `report_type=products_with_offers&period=all` returns `200 OK` and a valid file.
- **Driver availability export:** `report_type=driver_availability` returns `200 OK` even when `period` is missing.
- **Driver performance export:** `report_type=driver_performance&period=all` returns `200 OK` and a valid file.
- **No regressions:** `report_type=inactive_users&period=all` continues to return `200 OK`.

## Manual Test Matrix

Run these requests as an authenticated admin:

```http
GET /public/api/admin/reports/sales/payment-methods?period=all
GET /public/api/admin/reports/export?period=all&report_type=payment_methods
GET /public/api/admin/reports/export?period=all&report_type=top_selling_products
GET /public/api/admin/reports/export?period=all&report_type=top_customers
GET /public/api/admin/reports/export?period=all&report_type=best_sellers
GET /public/api/admin/reports/export?period=all&report_type=products_with_offers
GET /public/api/admin/reports/export?report_type=driver_availability
GET /public/api/admin/reports/export?period=all&report_type=driver_performance
GET /public/api/admin/reports/export?period=all&report_type=inactive_users
```

For each export response, verify:

- HTTP status is `200 OK`.
- Response downloads a valid Excel/CSV file.
- File has a header row and expected columns.
- File row count matches the unpaginated filtered query.
- Numeric values are not blank unless the real value is zero.
- Empty datasets still return a valid file with headers, not `500`.

## Frontend Compatibility Notes

The frontend currently renders payment methods using:

- `method`
- `label`
- `count`
- `percentage`

Adding `revenue` is backward-compatible. Do not remove or rename the existing fields unless the frontend is updated in the same release.
