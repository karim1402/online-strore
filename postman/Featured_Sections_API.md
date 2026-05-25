# Featured Sections - Admin API

Base URL: `/api/admin/featured-sections`

This feature allows adding modules, categories, or subcategories to a featured section on the homepage. Each item displays its **image** and **name** (AR/EN) with a **type** label.

---

## Object Structure

Each featured section item returns:

```json
{
  "id": 1,
  "type": "module",
  "item_id": 3,
  "sort_order": 1,
  "is_active": true,
  "image": "featured_sections/abc123.png",
  "name_en": "Sandwich",
  "name_ar": "ساندويتش",
  "image_url": "http://domain.com/storage/featured_sections/abc123.png",
  "created_at": "2026-05-05T12:00:00.000000Z",
  "updated_at": "2026-05-05T12:00:00.000000Z"
}
```

| Field      | Type    | Description                                                                 |
|------------|---------|-----------------------------------------------------------------------------|
| id         | integer | Featured section record ID                                                  |
| type       | string  | `module` \| `category` \| `subcategory`                                     |
| item_id    | integer | ID of the linked module or category                                         |
| sort_order | integer | Display order (ascending)                                                   |
| is_active  | boolean | Whether this item is visible to users                                       |
| image      | string  | Stored path of the section's custom image (nullable)                        |
| name_en    | string  | English name (resolved from the linked item)                                |
| name_ar    | string  | Arabic name (resolved from the linked item)                                 |
| image_url  | string  | Full image URL — uses section's own image if set, otherwise the item's image |

---

## Endpoints

### 1. List Featured Sections

```
GET /api/admin/featured-sections
```

**Query Parameters (all optional):**

| Param     | Type    | Description                                  |
|-----------|---------|----------------------------------------------|
| type      | string  | Filter by `module`, `category`, `subcategory`|
| is_active | boolean | Filter by status (`1` or `0`)                |
| per_page  | integer | Items per page (default: `15`)               |

**Response:** Paginated list of featured section objects.

---

### 2. Get Available Items

Use this to populate a dropdown/selector when creating a new featured section.

```
GET /api/admin/featured-sections/available-items?type=module
```

**Query Parameters:**

| Param | Type   | Required | Description                                  |
|-------|--------|----------|----------------------------------------------|
| type  | string | yes      | `module`, `category`, or `subcategory`       |

**Response:**

```json
{
  "success": true,
  "data": [
    { "id": 1, "name_en": "Sandwich", "name_ar": "ساندويتش", "image": "modules/sandwich.png" },
    { "id": 2, "name_en": "Pizza", "name_ar": "بيتزا", "image": "modules/pizza.png" }
  ]
}
```

---

### 3. Create Featured Section

```
POST /api/admin/featured-sections
Content-Type: multipart/form-data
```

**Body:**

| Field      | Type    | Required | Description                                                    |
|------------|---------|----------|----------------------------------------------------------------|
| type       | string  | yes      | `module`, `category`, or `subcategory`                         |
| item_id    | integer | yes      | ID of the module/category to feature                           |
| sort_order | integer | no       | Display order (default: `0`)                                   |
| is_active  | boolean | no       | Active status (default: `true`)                                |
| image      | file    | no       | Custom image for this section (max 2MB). Overrides item image. |

**Example (multipart/form-data):**

```
type=module
item_id=3
sort_order=1
is_active=1
image=<file>
```

**Validation:**
- The `item_id` must exist in the corresponding table (modules for `module`, categories with no parent for `category`, categories with a parent for `subcategory`).
- Duplicate `type` + `item_id` combinations are rejected (422).
- `image` must be a valid image file (jpg, png, etc.), max 2MB.

**Response:** `201` with the created object.

---

### 4. Show Featured Section

```
GET /api/admin/featured-sections/{id}
```

**Response:** Single featured section object.

---

### 5. Update Featured Section

```
PUT /api/admin/featured-sections/{id}
Content-Type: multipart/form-data
```

> **Note:** Use `POST` with `_method=PUT` when sending `multipart/form-data` from clients that don't support PUT file uploads.

**Body (all fields optional):**

| Field      | Type    | Description                                                    |
|------------|---------|----------------------------------------------------------------|
| type       | string  | `module`, `category`, or `subcategory`                         |
| item_id    | integer | New item ID                                                    |
| sort_order | integer | New display order                                              |
| is_active  | boolean | New active status                                              |
| image      | file    | New custom image (max 2MB). Replaces and deletes the old one.  |

**Response:** Updated object.

---

### 6. Toggle Status

```
PATCH /api/admin/featured-sections/{id}/toggle-status
```

Flips `is_active` between `true` and `false`.

**Response:** Updated object.

---

### 7. Update Sort Order (Bulk)

Use this for drag-and-drop reordering.

```
POST /api/admin/featured-sections/update-sort-order
Content-Type: application/json
```

**Body:**

```json
{
  "items": [
    { "id": 1, "sort_order": 0 },
    { "id": 2, "sort_order": 1 },
    { "id": 3, "sort_order": 2 }
  ]
}
```

**Response:** Success message.

---

### 8. Delete Featured Section

```
DELETE /api/admin/featured-sections/{id}
```

Deletes the record and removes the section's custom image file from storage (if any).

**Response:** Success message.

---

## Image Resolution Logic

`image_url` is resolved with the following priority:

1. **Section's own image** — if an `image` was uploaded directly to the featured section, it takes precedence.
2. **Linked item's image** — falls back to the image of the linked module/category.
3. **`null`** — if neither has an image.

---

## UI Flow (Suggested)

1. **List page** - Table showing: image, name (EN/AR), type badge, sort order, active toggle, actions (edit/delete).
2. **Create/Edit form:**
   - Step 1: Select **type** from dropdown (`module` / `category` / `subcategory`).
   - Step 2: Call `GET /available-items?type=<selected>` to populate item dropdown.
   - Step 3: Select **item** from the populated list.
   - Set **sort_order**, **is_active**, and optionally upload a **custom image** to override the item's default image.
3. **Drag & drop** reordering calls `POST /update-sort-order` with the new order.

---

## Auth

All admin endpoints require `Authorization: Bearer {{admin_token}}` header.
