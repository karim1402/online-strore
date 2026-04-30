# Admin Dashboard: Campaign Attribution Reports Integration

The backend is now capturing marketing attribution data (like `utm_campaign`, `utm_source`, etc.) from mobile/web users during the checkout and payment process. This data is saved in a JSON column called `campaign_attribution` on the `orders` table.

To display this data in the React Admin Dashboard, we've exposed two brand new reporting endpoints under the Reports module.

## 1. JSON Endpoints

You can fetch the raw JSON data to populate charts, graphs, or data tables in the dashboard:

### A. Campaign Performance
Returns total orders, revenue, and average order value grouped by the `utm_campaign` tag.

**Endpoint:** `GET /api/v1/admin/reports/attribution/campaign-performance`

**Query Parameters:**
- `period`: Optional (e.g. `today`, `week`, `month`, `year`, `all`, `custom`)
- `start_date` / `end_date`: Required if `period=custom`

**Response Example:**
```json
{
  "success": true,
  "message": "Data retrieved successfully",
  "data": [
    {
      "campaign": "ramadan_campaign",
      "total_orders": 145,
      "total_revenue": 25000.50,
      "avg_order_value": 172.41
    },
    {
      "campaign": "Organic / Unknown",
      "total_orders": 50,
      "total_revenue": 10000.00,
      "avg_order_value": 200.00
    }
  ]
}
```

### B. Traffic Source Performance
Returns total orders and revenue grouped by the `utm_source` tag (e.g., facebook, google, tiktok).

**Endpoint:** `GET /api/v1/admin/reports/attribution/source-performance`

**Query Parameters:**
- `period`: Optional
- `start_date` / `end_date`: Optional

**Response Example:**
```json
{
  "success": true,
  "message": "Data retrieved successfully",
  "data": [
    {
      "source": "facebook",
      "total_orders": 85,
      "total_revenue": 12000.00
    },
    {
      "source": "google",
      "total_orders": 60,
      "total_revenue": 13000.50
    }
  ]
}
```

---

## 2. Excel / CSV Export Integration

We have also added these reports to the main **Reports Export System**. You can trigger a file download using the standard export endpoint by passing the new `report_type` keys.

**Endpoint:** `GET /api/v1/admin/reports/export`

**New `report_type` Values:**
1. `campaign_performance`
2. `source_performance`

**Example Usage (Download Campaign Excel):**
`GET /api/v1/admin/reports/export?report_type=campaign_performance&period=month`

This will automatically generate and download an `.xlsx` file formatted with appropriate headers (`Campaign Name`, `Total Orders`, `Total Revenue`, `Average Order Value`).

> **Note:** The attribution payload itself is also returned inside the `order` JSON object when fetching an individual order (`GET /api/v1/admin/orders/{id}`). You can find it under `order.campaign_attribution`.
