# Backend Handoff: Campaign Attribution Payload

We added campaign attribution data to the mobile app backend requests so the backend can attribute orders/payments to marketing campaigns.

## What changed in the mobile app

The app already captures campaign parameters from deep links and stores them locally for 30 days.

Captured keys:

- `utm_source`
- `utm_medium`
- `utm_campaign`
- `utm_content`
- `utm_term`
- `fbclid`
- `gclid`

When sending data to Meta SDK, `fbclid` is mapped to `fb_click_id`. We now send the same backend-safe attribution map to backend order/payment APIs.

## Affected endpoints

### 1. Create payment intention

```http
POST user/payments/create-intention
```

The app now includes an optional `attribution` object in the request body when campaign data exists.

Example:

```json
{
  "address_id": 123,
  "is_delivery": true,
  "scheduled_time": "30 : 14",
  "voucher_code": "WELCOME10",
  "attribution": {
    "utm_source": "facebook",
    "utm_medium": "paid_social",
    "utm_campaign": "ramadan_campaign",
    "utm_content": "video_ad_1",
    "utm_term": "new_customers",
    "fb_click_id": "fbclid-value-here",
    "gclid": "gclid-value-here"
  }
}
```

### 2. Checkout / complete order

```http
POST user/checkout
```

The app now includes the same optional `attribution` object in the checkout request body when campaign data exists.

Example:

```json
{
  "address_id": 123,
  "payment_method": "cash",
  "notes": "Please call before delivery",
  "transaction_id": "CASH_1714480000000",
  "gateway_order_id": "CASH_1714480000000",
  "amount_cents": 25000,
  "currency": "EGP",
  "success": 1,
  "is_3d_secure": 0,
  "is_delivery": 1,
  "card_type": "",
  "card_pan": "",
  "voucher_code": "WELCOME10",
  "gateway_response": "",
  "txn_response_code": "",
  "integration_id": 0,
  "hmac": "",
  "scheduled_time": null,
  "payment_created_at": "2026-04-30T13:59:00.000",
  "merchant_commission": 0,
  "accept_fees": 0,
  "attribution": {
    "utm_source": "facebook",
    "utm_medium": "paid_social",
    "utm_campaign": "ramadan_campaign",
    "utm_content": "video_ad_1",
    "utm_term": "new_customers",
    "fb_click_id": "fbclid-value-here",
    "gclid": "gclid-value-here"
  }
}
```

## Important behavior

- `attribution` is optional.
- If the user is organic or campaign data expired, the app does not send `attribution` at all.
- Not every key is guaranteed to exist. Backend should accept partial attribution objects.
- Attribution is captured once from campaign links and reused for up to 30 days.
- The app does not currently mirror all Meta events to backend. We only send attribution with payment/order-related backend requests.

## Backend recommendation

Backend should:

- Accept nullable/optional `attribution` on both endpoints.
- Store attribution with the created payment intention/order when present.
- Prefer final order attribution from `user/checkout` if both endpoints receive attribution.
- Keep fields flexible because some campaigns may only include `utm_campaign`, `fb_click_id`, or a subset of UTM values.

Suggested database fields can be either:

- A JSON column like `attribution` / `campaign_attribution`.
- Separate nullable columns for each key.

Recommended JSON shape:

```json
{
  "utm_source": "facebook",
  "utm_medium": "paid_social",
  "utm_campaign": "ramadan_campaign",
  "utm_content": "video_ad_1",
  "utm_term": "new_customers",
  "fb_click_id": "fbclid-value-here",
  "gclid": "gclid-value-here"
}
```

## Mobile code reference

Updated file:

```text
lib/data/datasources/payment_datasource.dart
```

Source of attribution data:

```text
lib/utils/attribution_service.dart
```
