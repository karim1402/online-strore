# Store Approval System - Implementation Summary

## ✅ What Has Been Implemented

### 1. Database Migration
**File**: `database/migrations/2025_10_02_000001_add_approval_fields_to_stores_table.php`

Added three new fields to stores table:
- `rejection_note` (text, nullable) - Admin's rejection reason
- `approved_at` (timestamp, nullable) - Approval timestamp
- `approved_by` (foreign key to admins table) - Admin who approved

### 2. Admin Store Controller
**File**: `app/Http/Controllers/Api/Admin/StoreController.php`

Methods implemented:
- `index()` - Get all stores with filtering (pending/approved)
- `getPendingStores()` - Get only pending stores
- `show($id)` - Get single store details
- `approve($id)` - Approve a store
- `reject($id)` - Reject with mandatory note (10-1000 chars)
- `toggleStatus($id)` - Activate/deactivate store
- `destroy($id)` - Delete store (only if no vendors)

### 3. Admin Routes
**File**: `routes/api/admin.php`

New routes added:
```
GET    /api/admin/stores                  # All stores
GET    /api/admin/stores/pending          # Pending only
GET    /api/admin/stores/{id}             # Store details
POST   /api/admin/stores/{id}/approve     # Approve
POST   /api/admin/stores/{id}/reject      # Reject
PATCH  /api/admin/stores/{id}/toggle-status  # Toggle
DELETE /api/admin/stores/{id}             # Delete
```

**Permissions**:
- `stores.view` - View stores
- `stores.approve` - Approve/reject/toggle
- `stores.delete` - Delete stores

### 4. Store Model Updates
**File**: `app/Models/Store.php`

Added to fillable:
- `rejection_note`
- `approved_at`
- `approved_by`

Added casts:
- `approved_at` => 'datetime'

Added relationship:
- `approvedBy()` - belongsTo Admin

### 5. Vendor Store View Updates
**File**: `app/Http/Controllers/Api/Vendor/StoreController.php`

Both `show()` and `update()` methods now return:
- `rejection_note` - Vendor can see why store was rejected
- `approved_at` - Approval timestamp

### 6. Documentation
**File**: `STORE_APPROVAL_SYSTEM.md`

Complete API documentation with:
- All endpoints with examples
- Request/response formats
- Workflow scenarios
- cURL examples
- Permission requirements

---

## 🚀 Next Steps to Deploy

### Step 1: Run Migration
```bash
php artisan migrate
```

This will add the three new columns to the stores table.

### Step 2: Test Admin Endpoints

#### Login as Admin
```bash
curl -X POST http://localhost:8000/api/admin/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "superadmin@test.com",
    "password": "your_password"
  }'
```

Save the token from response.

#### Get Pending Stores
```bash
curl -X GET http://localhost:8000/api/admin/stores/pending \
  -H "Authorization: Bearer YOUR_TOKEN"
```

#### Approve a Store
```bash
curl -X POST http://localhost:8000/api/admin/stores/1/approve \
  -H "Authorization: Bearer YOUR_TOKEN"
```

#### Reject a Store
```bash
curl -X POST http://localhost:8000/api/admin/stores/1/reject \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "rejection_note": "Business license document is expired. Please upload a valid license."
  }'
```

### Step 3: Test Vendor View

#### Login as Vendor
```bash
curl -X POST http://localhost:8000/api/vendor/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "vendor@example.com",
    "password": "password"
  }'
```

#### View Store (with rejection note if rejected)
```bash
curl -X GET http://localhost:8000/api/vendor/store \
  -H "Authorization: Bearer VENDOR_TOKEN"
```

The response will include `rejection_note` and `approved_at` fields.

---

## 📋 Validation Rules

### Rejection Note
- **Required**: Yes (when rejecting)
- **Type**: String
- **Min Length**: 10 characters
- **Max Length**: 1000 characters
- **Error Messages**: Bilingual (English/Arabic)

---

## 🔐 Permission Requirements

Based on your existing permission system:

| Action | Required Permission | Roles with Access |
|--------|-------------------|-------------------|
| View all stores | `stores.view` | super_admin, admin, manager |
| View pending stores | `stores.view` | super_admin, admin, manager |
| Approve store | `stores.approve` | super_admin, admin |
| Reject store | `stores.approve` | super_admin, admin |
| Toggle status | `stores.approve` | super_admin, admin |
| Delete store | `stores.delete` | super_admin |

---

## 🎯 Key Features

### ✅ Atomic Approval
- Approval sets: `status = true`, `approved_at = now()`, `approved_by = admin_id`
- Clears any previous rejection note

### ✅ Detailed Rejection
- Requires meaningful note (minimum 10 characters)
- Vendor sees rejection reason when viewing store
- Sets: `status = false`, clears approval data

### ✅ Status Toggle
- Quick activate/deactivate for approved stores
- Activating sets approval information
- Deactivating keeps history

### ✅ Safety Checks
- Cannot delete store with active vendors
- Cannot approve already-approved store
- Validates rejection note length

### ✅ Audit Trail
- `approved_by` tracks which admin took action
- `approved_at` timestamps the approval
- History preserved in database

---

## 🔄 Workflow Example

### Typical Approval Flow

1. **Vendor Registers**
   - Store created with `status = false`
   - All approval fields are `null`

2. **Admin Reviews**
   ```
   GET /api/admin/stores/pending
   GET /api/admin/stores/1
   ```

3. **Admin Decides**
   
   **Option A: Approve**
   ```
   POST /api/admin/stores/1/approve
   ```
   Result: Store is live, vendor can process orders
   
   **Option B: Reject**
   ```
   POST /api/admin/stores/1/reject
   Body: { "rejection_note": "License expired" }
   ```
   Result: Vendor sees rejection reason

4. **If Rejected: Vendor Fixes Issue**
   ```
   PUT /api/vendor/store
   (Upload new document or fix issue)
   ```

5. **Admin Re-reviews**
   - Store appears in pending list again
   - Admin can approve after review

---

## 📊 Response Examples

### Pending Store
```json
{
  "status": false,
  "rejection_note": null,
  "approved_at": null,
  "approved_by": null
}
```

### Rejected Store
```json
{
  "status": false,
  "rejection_note": "Business license is expired. Please upload current license.",
  "approved_at": null,
  "approved_by": null
}
```

### Approved Store
```json
{
  "status": true,
  "rejection_note": null,
  "approved_at": "2025-10-02T11:30:00.000000Z",
  "approved_by": 1
}
```

---

## 🐛 Error Handling

### Rejection Without Note (422)
```json
{
  "success": false,
  "message": "Rejection note is required / ملاحظة الرفض مطلوبة",
  "errors": {
    "rejection_note": ["Rejection note is required / ملاحظة الرفض مطلوبة"]
  }
}
```

### Note Too Short (422)
```json
{
  "success": false,
  "message": "Rejection note must be at least 10 characters / يجب أن تكون ملاحظة الرفض 10 أحرف على الأقل",
  "errors": {...}
}
```

### Store Not Found (404)
```json
{
  "success": false,
  "message": {
    "en": "Not found",
    "ar": "غير موجود"
  }
}
```

### Already Approved (400)
```json
{
  "success": false,
  "message": {
    "en": "Store is already approved",
    "ar": "المتجر معتمد بالفعل"
  }
}
```

### Cannot Delete (400)
```json
{
  "success": false,
  "message": {
    "en": "Cannot delete store with active vendors. Remove vendors first.",
    "ar": "لا يمكن حذف المتجر الذي يحتوي على موظفين نشطين. قم بإزالة الموظفين أولاً."
  }
}
```

---

## 🔮 Future Enhancements

### Recommended Additions
1. **Email Notifications**
   - Notify vendor when store is approved
   - Notify vendor when store is rejected
   - Include rejection note in email

2. **Rejection Categories**
   - Predefined rejection reasons
   - Dropdown selection for common issues
   - Custom note for specific details

3. **Re-submission Tracking**
   - Count number of rejections
   - Track resubmission history
   - Flag stores with multiple rejections

4. **Internal Admin Notes**
   - Separate from vendor-visible rejection note
   - For admin communication only
   - Track review comments

5. **Approval Analytics**
   - Average review time
   - Approval rate
   - Common rejection reasons
   - Admin performance metrics

6. **Bulk Actions**
   - Approve multiple stores at once
   - Export pending stores list
   - Filter by category/location

---

## ✅ Testing Checklist

- [ ] Run migration successfully
- [ ] Admin can view all stores
- [ ] Admin can view pending stores only
- [ ] Admin can approve a pending store
- [ ] Admin can reject with valid note (10+ chars)
- [ ] Rejection fails without note
- [ ] Rejection fails with note < 10 chars
- [ ] Cannot approve already-approved store
- [ ] Vendor sees rejection note in store view
- [ ] Vendor sees approval timestamp when approved
- [ ] Toggle status works for approved stores
- [ ] Cannot delete store with vendors
- [ ] Can delete store without vendors
- [ ] Permission checks work correctly
- [ ] Bilingual messages display properly

---

## 📝 Files Modified/Created

### Created
1. `database/migrations/2025_10_02_000001_add_approval_fields_to_stores_table.php`
2. `app/Http/Controllers/Api/Admin/StoreController.php`
3. `STORE_APPROVAL_SYSTEM.md`
4. `IMPLEMENTATION_SUMMARY.md` (this file)

### Modified
1. `app/Models/Store.php`
2. `routes/api/admin.php`
3. `app/Http/Controllers/Api/Vendor/StoreController.php`

---

## 🎉 Ready to Use!

The store approval system is now fully implemented. Run the migration and start testing!

```bash
php artisan migrate
php artisan serve
```

Then use the API endpoints documented in `STORE_APPROVAL_SYSTEM.md`.
