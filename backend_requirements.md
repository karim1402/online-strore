# Backend Requirements for Reports Dashboard Updates

To support the newly requested features on the Reports Dashboard, the backend needs to implement or update the following endpoints:

### 1. Product Buyers Details (Orders Tab)
**Context:** When a user clicks on a product in the "Items Sold Breakdown (Stocktaking)", a dialog should display all users who ordered that product, including the quantity they bought and the date/time of the order.
**Endpoint Needed:** `GET /reports/products/:id/buyers` (or similar)
**Expected Response:**
```json
{
  "data": [
    {
      "user_name": "John Doe",
      "user_id": 123,
      "quantity": 2,
      "order_date": "2026-03-18T10:30:00Z"
    },
    ...
  ]
}
```

### 2. User Orders Pagination (Users Tab)
**Context:** When clicking on a user in the "Top Spenders" list, a dialog opens showing all orders made by that specific user with pagination.
**Endpoint Needed:** `GET /users/:id/orders` (or via reports service)
**Query Parameters:** `page`, `limit`
**Expected Response:** Paginated list of orders (similar to the current orders list endpoint) containing `order_id`, `status`, `total_amount`, `created_at`, etc.

### 3. Top Spenders Enhancements (Users Tab)
**Context:** The frontend needs to display *all* users who made orders, rather than just the top 50. It also needs to support a custom `limit` filter (e.g., top 10, top 20, all) and a `search` query.
**Endpoint Needed:** Update the existing `GET /reports/users/top-customers` endpoint (or create a new one).
**Query Parameters required:**
- `limit` (optional): to specify Top 10, Top 20, etc. If omitted or set to "all", it should paginate through all customers who made orders.
- `search` (optional): search by name, email, or phone.
- `page` (optional): for pagination if "all" users are requested.
**Expected Response:** Paginated or fully filtered list of customers based on the query params.

### 4. Month Navigation for Charts
Currently, charts like "Orders Per Day", "Revenue Trend", and "Deliveries Per Day" return data based on the global date filter (which could span a whole year). To allow horizontal navigation by month on the frontend without refetching the entire year's data, the backend endpoints (`getOrdersPerDay`, `getRevenueTrend`, `getDeliveriesPerDay`) should ideally support efficient date range querying, or the frontend will chunk the year's data by month client-side. No strict backend change is required for this if the frontend handles chunking, but supporting fast month-based queries is recommended.
