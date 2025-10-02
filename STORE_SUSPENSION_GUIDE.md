# Store Suspension System

## Overview
The `suspended` status allows admins to temporarily stop/deactivate approved stores without fully rejecting them. This is useful for handling policy violations, temporary issues, or disputes.

---

## Store Status Options (4 States)

| Status | Meaning | Can Process Orders | Admin Actions |
|--------|---------|-------------------|---------------|
| **pending** | Awaiting approval | ❌ No | Approve, Reject |
| **approved** | Active & operational | ✅ Yes | Suspend, Toggle to pending |
| **rejected** | Application rejected | ❌ No | Re-approve |
| **suspended** | Temporarily stopped | ❌ No | Reactivate |

---

## Suspension vs Rejection

### Rejection
- **Used for**: New store applications with issues
- **Action**: Store never went live
- **Vendor Action**: Fix issues and wait for re-approval
- **Example**: "Business license is expired"

### Suspension
- **Used for**: Active stores with violations
- **Action**: Store was approved but now stopped
- **Vendor Action**: Resolve issue and wait for reactivation
- **Example**: "Multiple customer complaints - under investigation"

---

## API Endpoints

### 1. Suspend a Store
**POST** `/api/admin/stores/{id}/suspend`

**Permission**: `stores.approve`

**Request Body:**
```json
{
  "suspension_note": "Multiple customer complaints about late deliveries. Store is under investigation."
}
```

**Validation:**
- `suspension_note`: required, string, min 10 chars, max 1000 chars

**Response (Success - 200):**
```json
{
  "success": true,
  "message": {
    "en": "Store suspended successfully",
    "ar": "تم إيقاف المتجر بنجاح"
  },
  "data": {
    "id": 1,
    "name_en": "Fresh Market",
    "status": "suspended",
    "rejection_note": "Multiple customer complaints about late deliveries. Store is under investigation.",
    "approved_at": "2025-10-02T10:00:00.000000Z",
    "approved_by": 1
  }
}
```

**Error (Not Approved - 400):**
```json
{
  "success": false,
  "message": {
    "en": "Only approved stores can be suspended",
    "ar": "يمكن إيقاف المتاجر المعتمدة فقط"
  }
}
```

---

### 2. Reactivate a Suspended Store
**POST** `/api/admin/stores/{id}/reactivate`

**Permission**: `stores.approve`

**No Request Body Required**

**Response (Success - 200):**
```json
{
  "success": true,
  "message": {
    "en": "Store reactivated successfully",
    "ar": "تم إعادة تفعيل المتجر بنجاح"
  },
  "data": {
    "id": 1,
    "name_en": "Fresh Market",
    "status": "approved",
    "rejection_note": null,
    "approved_at": "2025-10-02T14:30:00.000000Z",
    "approved_by": 1
  }
}
```

**Error (Not Suspended - 400):**
```json
{
  "success": false,
  "message": {
    "en": "Only suspended stores can be reactivated",
    "ar": "يمكن إعادة تفعيل المتاجر الموقوفة فقط"
  }
}
```

---

### 3. Get Suspended Stores
**GET** `/api/admin/stores?status=suspended`

**Permission**: `stores.view`

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
        "status": "suspended",
        "rejection_note": "Under investigation for policy violation",
        "approved_at": "2025-10-02T10:00:00.000000Z"
      }
    ],
    "total": 3
  }
}
```

---

## Model Helper Methods

```php
// Check if store is suspended
if ($store->isSuspended()) {
    echo "Store is currently suspended";
}

// Query suspended stores
$suspendedStores = Store::suspended()->get();

// Check if store can process orders
if ($store->canProcessOrders()) {
    // Only returns true if status === 'approved'
    processOrder($order);
}
```

---

## Workflow Examples

### Scenario 1: Suspend Store for Investigation

1. **Customer files complaint** about food quality
2. **Admin reviews** complaint
3. **Admin suspends store** with note
   ```bash
   POST /api/admin/stores/1/suspend
   Body: {
     "suspension_note": "Store suspended due to multiple food quality complaints. Investigation in progress."
   }
   ```
4. **Store status** → `suspended`
5. **Vendor sees** suspension note in dashboard
6. **Admin investigates** and resolves issue
7. **Admin reactivates** store
   ```bash
   POST /api/admin/stores/1/reactivate
   ```
8. **Store status** → `approved`

### Scenario 2: Policy Violation

1. **Admin detects** policy violation (e.g., selling restricted items)
2. **Admin immediately suspends** store
   ```bash
   POST /api/admin/stores/2/suspend
   Body: {
     "suspension_note": "Store found selling restricted items. Policy violation - Section 4.2."
   }
   ```
3. **Vendor contacts** admin to resolve
4. **Vendor removes** restricted items
5. **Admin verifies** compliance
6. **Admin reactivates** with warning
   ```bash
   POST /api/admin/stores/2/reactivate
   ```

### Scenario 3: Temporary Closure

1. **Vendor requests** temporary closure (renovation, vacation)
2. **Admin suspends** store voluntarily
   ```bash
   POST /api/admin/stores/3/suspend
   Body: {
     "suspension_note": "Store temporarily closed for renovation - Vendor requested. Expected reopening: Nov 1st."
   }
   ```
3. **After renovation**, vendor notifies admin
4. **Admin reactivates** store
   ```bash
   POST /api/admin/stores/3/reactivate
   ```

---

## Vendor Experience

### When Store is Suspended

**Vendor Dashboard Shows:**
```
⚠️ Your store is currently suspended

Status: Suspended
Reason: Multiple customer complaints about late deliveries. 
        Store is under investigation.

You cannot process orders while suspended.
Please contact admin support for assistance.
```

**Vendor Can:**
- ✅ Login and access dashboard
- ✅ View store details
- ✅ See suspension reason
- ✅ Contact admin support
- ❌ Process new orders
- ❌ Update store status

**Vendor Cannot:**
- Change store status (admin-only)
- Accept new orders
- Update certain store details (depending on implementation)

---

## Database Query Examples

### Get all suspended stores with admin info
```sql
SELECT 
    s.id,
    s.name_en,
    s.status,
    s.rejection_note as suspension_reason,
    s.approved_at as suspended_at,
    a.name as suspended_by
FROM stores s
LEFT JOIN admins a ON s.approved_by = a.id
WHERE s.status = 'suspended'
ORDER BY s.approved_at DESC;
```

### Count stores by status
```sql
SELECT 
    status,
    COUNT(*) as count
FROM stores
GROUP BY status;

-- Output:
-- pending   | 5
-- approved  | 120
-- rejected  | 8
-- suspended | 3
```

### Get stores suspended in last 7 days
```sql
SELECT *
FROM stores
WHERE status = 'suspended'
AND approved_at >= DATE_SUB(NOW(), INTERVAL 7 DAY);
```

---

## Testing Checklist

- [ ] Can suspend only approved stores
- [ ] Cannot suspend pending stores
- [ ] Cannot suspend rejected stores
- [ ] Cannot suspend already suspended stores
- [ ] Suspension note is required (min 10 chars)
- [ ] Can reactivate only suspended stores
- [ ] Cannot reactivate approved stores
- [ ] Cannot reactivate pending/rejected stores
- [ ] Reactivation clears suspension note
- [ ] Reactivation updates approved_at timestamp
- [ ] Vendor sees suspension note in dashboard
- [ ] Filter by status=suspended works
- [ ] `isSuspended()` returns correct value
- [ ] `canProcessOrders()` returns false for suspended
- [ ] `suspended()` scope returns only suspended stores

---

## cURL Examples

### Suspend a Store
```bash
curl -X POST http://localhost:8000/api/admin/stores/1/suspend \
  -H "Authorization: Bearer {admin_token}" \
  -H "Content-Type: application/json" \
  -d '{
    "suspension_note": "Store suspended for repeated policy violations."
  }'
```

### Reactivate a Store
```bash
curl -X POST http://localhost:8000/api/admin/stores/1/reactivate \
  -H "Authorization: Bearer {admin_token}"
```

### Get All Suspended Stores
```bash
curl -X GET "http://localhost:8000/api/admin/stores?status=suspended" \
  -H "Authorization: Bearer {admin_token}"
```

---

## Status Transition Rules

```
pending ──────────┐
    │             │
    │ approve     │ reject
    ▼             ▼
approved      rejected
    │             │
    │ suspend     │ approve
    ▼             │
suspended ────────┘
    │
    │ reactivate
    └──> approved
```

### Allowed Transitions

| From | To | Action | Permission Required |
|------|-----|--------|-------------------|
| pending | approved | approve() | stores.approve |
| pending | rejected | reject() | stores.approve |
| approved | suspended | suspend() | stores.approve |
| suspended | approved | reactivate() | stores.approve |
| rejected | approved | approve() | stores.approve |
| approved | pending | toggleStatus() | stores.approve |

### Blocked Transitions

❌ pending → suspended (must be approved first)  
❌ rejected → suspended (must be approved first)  
❌ suspended → pending (use reactivate to go to approved)  
❌ suspended → rejected (doesn't make sense logically)

---

## Important Notes

1. **Suspension Note Reuses Field**: The `rejection_note` field is used for both rejection and suspension notes
2. **Approved Timestamp Preserved**: When suspending, `approved_at` and `approved_by` are kept to track original approval
3. **Reactivation Updates Timestamp**: When reactivating, `approved_at` is updated to current time
4. **Permission Same as Approve**: Both suspend and reactivate use `stores.approve` permission
5. **Bilingual Messages**: All responses include both English and Arabic messages

---

## Future Enhancements

### Potential Improvements

1. **Suspension History**
   - Track all suspensions/reactivations
   - Store multiple suspension reasons
   - Count number of times suspended

2. **Auto-Suspension**
   - Automatic suspension after X customer complaints
   - Automatic suspension if vendor violates policy Y times

3. **Suspension Duration**
   - Add `suspended_until` timestamp
   - Auto-reactivate after period

4. **Notification System**
   - Email vendor when suspended
   - SMS alert for suspension
   - Push notification

5. **Suspension Categories**
   - Predefined suspension reasons
   - Category-based handling
   - Different actions per category

---

## Summary

✅ **Added** `suspended` status to store enum  
✅ **Created** suspend() endpoint with required note  
✅ **Created** reactivate() endpoint  
✅ **Updated** routes with new endpoints  
✅ **Added** model helper methods (isSuspended, canProcessOrders)  
✅ **Added** suspended() scope for queries  
✅ **Bilingual** error messages throughout  

The suspension system is now ready to use! Run `php artisan migrate:fresh` to apply the updated schema.
