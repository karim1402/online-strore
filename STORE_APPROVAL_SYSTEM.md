# Store Approval System

## Overview
Admins can approve or reject vendor store registrations. When rejecting, admins must provide a note explaining the reason for rejection.

---

## Database Changes

### New Fields in `stores` Table
```sql
rejection_note      TEXT NULLABLE           # Admin's reason for rejection
approved_at         TIMESTAMP NULLABLE      # When store was approved
approved_by         BIGINT UNSIGNED FK      # Admin ID who approved/rejected
```

---

## Admin Endpoints

### Base URL
```
/api/admin/stores
```

**Authentication**: Required (Bearer Token - Admin Guard)  
**Permissions**: `stores.view`, `stores.approve`, `stores.delete`

---

### 1. Get All Stores

**GET** `/api/admin/stores`

**Query Parameters:**
- `status` (optional): `pending`, `approved`, or empty for all
- `per_page` (optional): Number of items per page (default: 15)

**Response:**
```json
{
  "success": true,
  "message": {
    "en": "Data retrieved successfully",
    "ar": "تم استرجاع البيانات بنجاح"
  },
  "data": {
    "current_page": 1,
    "data": [
      {
        "id": 1,
        "name_en": "Fresh Market",
        "name_ar": "سوق الطازج",
        "status": false,
        "rejection_note": null,
        "approved_at": null,
        "approved_by": null,
        "created_at": "2025-10-02T10:00:00.000000Z",
        "vendors": [...],
        "main_categories": [...]
      }
    ],
    "per_page": 15,
    "total": 10
  }
}
```

---

### 2. Get Pending Stores Only

**GET** `/api/admin/stores/pending`

Returns only stores awaiting approval (status = false, rejection_note = null)

**Response:**
```json
{
  "success": true,
  "message": {
    "en": "Data retrieved successfully",
    "ar": "تم استرجاع البيانات بنجاح"
  },
  "data": {
    "count": 5,
    "stores": [
      {
        "id": 1,
        "name_en": "Fresh Market",
        "status": false,
        "vendors": [...],
        "main_categories": [...]
      }
    ]
  }
}
```

---

### 3. Get Store Details

**GET** `/api/admin/stores/{id}`

**Response:**
```json
{
  "success": true,
  "message": {
    "en": "Data retrieved successfully",
    "ar": "تم استرجاع البيانات بنجاح"
  },
  "data": {
    "id": 1,
    "name_en": "Fresh Market",
    "name_ar": "سوق الطازج",
    "description_en": "Fresh groceries daily",
    "description_ar": "بقالة طازجة يوميا",
    "address": "456 Store Ave",
    "latitude": "40.7128000",
    "longitude": "-74.0060000",
    "logo": "stores/logos/abc123.jpg",
    "logo_url": "http://domain.com/storage/stores/logos/abc123.jpg",
    "document": "stores/documents/xyz789.pdf",
    "document_url": "http://domain.com/storage/stores/documents/xyz789.pdf",
    "status": false,
    "rejection_note": null,
    "approved_at": null,
    "approved_by": null,
    "vendors": [
      {
        "id": 1,
        "name": "John Doe",
        "email": "john@example.com",
        "phone": "+1234567890"
      }
    ],
    "main_categories": [...]
  }
}
```

---

### 4. Approve Store

**POST** `/api/admin/stores/{id}/approve`

**Permission Required:** `stores.approve`

**Response (Success):**
```json
{
  "success": true,
  "message": {
    "en": "Store approved successfully",
    "ar": "تم اعتماد المتجر بنجاح"
  },
  "data": {
    "id": 1,
    "name_en": "Fresh Market",
    "status": true,
    "approved_at": "2025-10-02T11:30:00.000000Z",
    "approved_by": 1,
    "rejection_note": null,
    "vendors": [...],
    "main_categories": [...]
  }
}
```

**Error (Already Approved - 400):**
```json
{
  "success": false,
  "message": {
    "en": "Store is already approved",
    "ar": "المتجر معتمد بالفعل"
  },
  "data": null
}
```

---

### 5. Reject Store

**POST** `/api/admin/stores/{id}/reject`

**Permission Required:** `stores.approve`

**Request Body:**
```json
{
  "rejection_note": "Logo quality is too low. Please upload a high-resolution logo (minimum 500x500px)."
}
```

**Validation Rules:**
- `rejection_note`: required, string, min 10 characters, max 1000 characters

**Response (Success):**
```json
{
  "success": true,
  "message": {
    "en": "Store rejected successfully",
    "ar": "تم رفض المتجر بنجاح"
  },
  "data": {
    "id": 1,
    "name_en": "Fresh Market",
    "status": false,
    "rejection_note": "Logo quality is too low. Please upload a high-resolution logo (minimum 500x500px).",
    "approved_at": null,
    "approved_by": null,
    "vendors": [...],
    "main_categories": [...]
  }
}
```

**Error (Validation Failed - 422):**
```json
{
  "success": false,
  "message": "Rejection note is required / ملاحظة الرفض مطلوبة",
  "data": null,
  "errors": {
    "rejection_note": [
      "Rejection note is required / ملاحظة الرفض مطلوبة"
    ]
  }
}
```

---

### 6. Toggle Store Status

**PATCH** `/api/admin/stores/{id}/toggle-status`

**Permission Required:** `stores.approve`

Activates or deactivates a store. If activating, sets approval information.

**Response:**
```json
{
  "success": true,
  "message": {
    "en": "Store activated successfully",
    "ar": "تم مفعل المتجر بنجاح"
  },
  "data": {
    "id": 1,
    "status": true
  }
}
```

---

### 7. Delete Store

**DELETE** `/api/admin/stores/{id}`

**Permission Required:** `stores.delete`

**Note:** Cannot delete stores with active vendors.

**Response (Success):**
```json
{
  "success": true,
  "message": {
    "en": "Store deleted successfully",
    "ar": "تم حذف المتجر بنجاح"
  },
  "data": null
}
```

**Error (Has Vendors - 400):**
```json
{
  "success": false,
  "message": {
    "en": "Cannot delete store with active vendors. Remove vendors first.",
    "ar": "لا يمكن حذف المتجر الذي يحتوي على موظفين نشطين. قم بإزالة الموظفين أولاً."
  },
  "data": null
}
```

---

## Vendor Store View

### Get Vendor's Store

**GET** `/api/vendor/store`

Vendors can view their store details including rejection notes if their store was rejected.

**Response (Pending Store):**
```json
{
  "success": true,
  "message": {
    "en": "Store details retrieved successfully",
    "ar": "تم استرجاع تفاصيل المتجر بنجاح"
  },
  "data": {
    "id": 1,
    "name_en": "Fresh Market",
    "status": false,
    "rejection_note": null,
    "approved_at": null,
    "main_categories": [...]
  }
}
```

**Response (Rejected Store):**
```json
{
  "success": true,
  "message": {
    "en": "Store details retrieved successfully",
    "ar": "تم استرجاع تفاصيل المتجر بنجاح"
  },
  "data": {
    "id": 1,
    "name_en": "Fresh Market",
    "status": false,
    "rejection_note": "Logo quality is too low. Please upload a high-resolution logo (minimum 500x500px).",
    "approved_at": null,
    "main_categories": [...]
  }
}
```

**Response (Approved Store):**
```json
{
  "success": true,
  "message": {
    "en": "Store details retrieved successfully",
    "ar": "تم استرجاع تفاصيل المتجر بنجاح"
  },
  "data": {
    "id": 1,
    "name_en": "Fresh Market",
    "status": true,
    "rejection_note": null,
    "approved_at": "2025-10-02T11:30:00.000000Z",
    "main_categories": [...]
  }
}
```

---

## Workflow Examples

### Scenario 1: Approve Store

1. Admin views pending stores: `GET /api/admin/stores/pending`
2. Admin reviews store details: `GET /api/admin/stores/1`
3. Admin approves store: `POST /api/admin/stores/1/approve`
4. Vendor can now process orders

### Scenario 2: Reject Store

1. Admin views pending stores: `GET /api/admin/stores/pending`
2. Admin reviews store details: `GET /api/admin/stores/1`
3. Admin finds issue (e.g., invalid document)
4. Admin rejects with note: `POST /api/admin/stores/1/reject`
   ```json
   {
     "rejection_note": "Business license document is expired. Please upload a valid license."
   }
   ```
5. Vendor views store: `GET /api/vendor/store`
6. Vendor sees rejection note in response
7. Vendor corrects issue and re-submits (updates store)
8. Admin re-reviews and approves

### Scenario 3: Temporary Deactivation

1. Admin deactivates approved store due to policy violation: `PATCH /api/admin/stores/1/toggle-status`
2. Store status changes to `false`
3. Vendor cannot process new orders
4. After resolution, admin reactivates: `PATCH /api/admin/stores/1/toggle-status`

---

## Testing with cURL

### Approve Store
```bash
curl -X POST http://yourdomain.com/api/admin/stores/1/approve \
  -H "Authorization: Bearer {admin_token}" \
  -H "Content-Type: application/json"
```

### Reject Store
```bash
curl -X POST http://yourdomain.com/api/admin/stores/1/reject \
  -H "Authorization: Bearer {admin_token}" \
  -H "Content-Type: application/json" \
  -d '{
    "rejection_note": "Logo quality is too low. Please upload a high-resolution logo."
  }'
```

### Get Pending Stores
```bash
curl -X GET http://yourdomain.com/api/admin/stores/pending \
  -H "Authorization: Bearer {admin_token}"
```

---

## Migration Command

Run the migration to add approval fields:

```bash
php artisan migrate
```

This will add:
- `rejection_note` (text, nullable)
- `approved_at` (timestamp, nullable)
- `approved_by` (foreign key to admins table)

---

## Notes

1. **Rejection Note Minimum Length**: 10 characters to ensure meaningful feedback
2. **Vendor Notification**: Consider adding email/push notification when store is approved/rejected [Future Enhancement]
3. **Re-submission Workflow**: Vendors can update store details and wait for re-approval [Future Enhancement]
4. **Audit Trail**: `approved_by` tracks which admin approved/rejected the store
5. **Multiple Rejections**: Previous rejection notes are overwritten when re-rejecting
6. **Cannot Delete with Vendors**: Safety measure to prevent data loss

---

## Permission Requirements

| Action | Permission | Roles with Access |
|--------|-----------|------------------|
| View Stores | `stores.view` | super_admin, admin, manager |
| Approve/Reject | `stores.approve` | super_admin, admin |
| Delete Store | `stores.delete` | super_admin |

---

## Status Meanings

| Status | Rejection Note | Meaning |
|--------|---------------|---------|
| `false` | `null` | **Pending** - Awaiting admin review |
| `false` | Not null | **Rejected** - Admin rejected with reason |
| `true` | `null` | **Approved** - Active and can process orders |

---

## Future Enhancements

1. **Email Notifications**: Notify vendors when store status changes
2. **Rejection Categories**: Predefined rejection reasons (Invalid Document, Poor Image Quality, etc.)
3. **Re-submission Tracking**: Track how many times store was rejected/resubmitted
4. **Approval Comments**: Allow admins to add internal notes (separate from vendor-visible rejection note)
5. **Bulk Approval**: Approve multiple stores at once
6. **Store Analytics**: Track approval rates, average review time, common rejection reasons
