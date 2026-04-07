# Push Notification Targeting — Frontend Integration Guide

> **Endpoint:** `POST /api/admin/notifications/send`  
> **Auth:** Admin JWT token required (`Authorization: Bearer <token>`)  
> **Content-Type:** `multipart/form-data`

---

## What Changed

The notification send endpoint now supports **audience targeting**. Instead of always sending to all users or a manual list of IDs, admins can pass a `target_type` to automatically select the right audience.

---

## Request Parameters

### Always Required

| Field   | Type   | Description                  |
|---------|--------|------------------------------|
| `title` | string | Notification title (max 255) |
| `body`  | string | Notification body text       |

### Optional

| Field    | Type | Description                                         |
|----------|------|-----------------------------------------------------|
| `image`  | file | Image to attach (jpeg/png/webp, max 2MB)            |
| `target_type` | string | Audience segment (see table below). Defaults to `all_users` |

---

## `target_type` Values

| Value | Description | Extra Params Required |
|---|---|---|
| `all_devices` | All devices that have installed the app (including guests) | — |
| `all_users` | All registered users with an FCM token | — |
| `specific_users` | Manually provided list of user IDs | `user_ids[]` *(required)* |
| `one_time_orderers` | Users who placed **exactly 1** order | — |
| `multiple_orderers` | Users who placed **N or more** orders | `min_order_count` *(default: 2)* |
| `never_ordered` | Users who registered but never ordered | — |
| `never_logged_in` | Users who never opened the app (no FCM token) — **in-app notification only, no push** | — |
| `inactive_users` | Users with no order in the last **N days** | `inactive_days` *(default: 30)* |
| `high_spenders` | Users whose total spend ≥ X EGP | `min_total_spent` *(required)* |
| `cancelled_order_users` | Users who have at least 1 cancelled order | — |
| `new_registrants` | Users who registered within the last N days | `registered_within_days` *(default: 7)* |
| `cash_on_delivery_users` | Users who always pay cash | — |
| `online_payment_users` | Users who pay online (non-cash) | — |

---

## Extra Parameters (per `target_type`)

| Parameter | Type | Used With | Default |
|---|---|---|---|
| `user_ids[]` | array of integers | `specific_users` | — |
| `min_order_count` | integer (min: 2) | `multiple_orderers` | `2` |
| `inactive_days` | integer (min: 1) | `inactive_users` | `30` |
| `min_total_spent` | numeric (min: 0) | `high_spenders` | `0` |
| `registered_within_days` | integer (min: 1) | `new_registrants` | `7` |

---

## Example Requests

### 1. Send to All Users
```
POST /api/admin/notifications/send
Content-Type: multipart/form-data

title         = "🎉 Special Offer!"
body          = "Get 20% off your next order today only."
target_type   = all_users
```

---

### 1.1 Send to All Devices (Including Guests)
```
POST /api/admin/notifications/send
Content-Type: multipart/form-data

title         = "🚨 App Update Available!"
body          = "Update now to get the latest features."
target_type   = all_devices
```

---

### 2. Send to Specific Users
```
POST /api/admin/notifications/send
Content-Type: multipart/form-data

title         = "We miss you!"
body          = "Here's a gift just for you."
target_type   = specific_users
user_ids[]    = 12
user_ids[]    = 45
user_ids[]    = 78
```

---

### 3. Send to Users Who Never Ordered
```
POST /api/admin/notifications/send
Content-Type: multipart/form-data

title         = "Place your first order!"
body          = "Explore hundreds of products and order now."
target_type   = never_ordered
```

---

### 4. Send to High Spenders (≥ 500 EGP)
```
POST /api/admin/notifications/send
Content-Type: multipart/form-data

title         = "VIP Offer 👑"
body          = "You're one of our top customers — enjoy exclusive deals."
target_type   = high_spenders
min_total_spent = 500
```

---

### 5. Send to Inactive Users (last 14 days)
```
POST /api/admin/notifications/send
Content-Type: multipart/form-data

title         = "We haven't seen you in a while!"
body          = "Come back and check what's new."
target_type   = inactive_users
inactive_days = 14
```

---

### 6. Send to Multiple Orderers (3+ orders)
```
POST /api/admin/notifications/send
Content-Type: multipart/form-data

title         = "Loyal Customer Reward 🎁"
body          = "Thank you for ordering with us multiple times!"
target_type   = multiple_orderers
min_order_count = 3
```

---

### 7. Send to New Registrants (last 3 days)
```
POST /api/admin/notifications/send
Content-Type: multipart/form-data

title         = "Welcome to Makook! 👋"
body          = "Your first order is waiting."
target_type   = new_registrants
registered_within_days = 3
```

---

### 8. Attach an Image
```
POST /api/admin/notifications/send
Content-Type: multipart/form-data

title         = "New Items Just Dropped! 🍕"
body          = "Check out what's new in the app."
target_type   = all_users
image         = <file>
```

---

## Success Response

```json
{
  "success": true,
  "message": "Notification sent successfully",
  "data": {
    "success": 143,
    "failure": 2,
    "target_type": "inactive_users",
    "targeted_users": 145
  }
}
```

| Field | Description |
|---|---|
| `success` | Number of devices that received the push notification |
| `failure` | Number of devices where delivery failed |
| `target_type` | The segment used (echoed back) |
| `targeted_users` | Total number of users matched by the segment |

---

## Error Responses

| HTTP | Scenario |
|---|---|
| `422` | Validation failed (e.g. missing `user_ids` when `target_type=specific_users`) |
| `500` | Server error |

### Example Validation Error
```json
{
  "success": false,
  "message": "The user ids field is required when target type is specific users."
}
```

---

## Notes

- **`never_logged_in`**: These users have no FCM token so **no push is sent to their device**. A notification record is still saved in-app and will appear when they first log in.
- **`target_type` is optional** — omitting it defaults to `all_users`.
- All segmentation happens server-side. The frontend only needs to send the `target_type` and any relevant extra param.
- Notification history is saved automatically for every send (visible in `GET /api/admin/notifications`).
