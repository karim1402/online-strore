---
description: Plan for implementing Admin Push Notifications with Image Support
---

# Admin Push Notifications Implementation Plan

## 1. Database Changes
- Create a migration for `push_notifications` table to store notification history.
  - `id`
  - `title`
  - `body`
  - `image_url` (nullable)
  - `target_type` (e.g., 'all_users', 'specific_users')
  - `success_count`
  - `failure_count`
  - `sender_id` (admin who sent it)
  - `created_at`, `updated_at`

## 2. Service Updates
- Update `App\Services\FcmService` to support image URLs.
  - Modify `sendNotification`, `sendToMultiple`, and `sendToTopic` to accept an optional `$imageUrl` parameter.
  - Pass `$imageUrl` to `Notification::create($title, $body, $imageUrl)`.

## 3. Controller Implementation
- Create `App\Http\Controllers\Api\Admin\NotificationController`.
  - `index()`: List sent notifications history (paginated).
  - `send(Request $request)`:
    - Validation: `title` (required), `body` (required), `image` (optional image file), `user_ids` (optional array).
    - Image Upload: If `image` is present, upload to public storage and get URL.
    - Token Fetching:
      - If `user_ids` provided: Fetch `fcm_token` for these users.
      - If no `user_ids`: Fetch all users with non-null `fcm_token`.
    - Sending: Call `FcmService::sendToMultiple`.
    - Logging: Save record to `push_notifications` table.

## 4. Routes & Permissions
- Add permissions to `PermissionSeeder`:
  - `notifications.view`
  - `notifications.create`
- Add routes in `routes/api/admin.php`:
  - `GET /notifications`
  - `POST /notifications/send`

## 5. Localization
- Add success/error messages to `messages.json` (en/ar).
