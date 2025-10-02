# Store Status Enum System

## Overview
The store `status` field is now an enum with three possible values instead of a boolean:

```php
enum('status', ['pending', 'approved', 'rejected'])
```

---

## Status States

### 1. `pending` (Default)
- **Meaning**: Store is awaiting admin review
- **When**: Set automatically on vendor registration
- **Vendor Can**: Login and view store, cannot process orders
- **Next Actions**: Admin can approve or reject

### 2. `approved`  
- **Meaning**: Store is active and can operate
- **When**: Admin approves the store
- **Vendor Can**: Full access, process orders
- **Tracking**: 
  - `approved_at` = timestamp
  - `approved_by` = admin_id
  - `rejection_note` = null

### 3. `rejected`
- **Meaning**: Store application was rejected
- **When**: Admin rejects with a note
- **Vendor Can**: See rejection reason, update store details
- **Fields Set**:
  - `status` = 'rejected'
  - `rejection_note` = admin's reason (10-1000 chars)
  - `approved_at` = null
  - `approved_by` = null

---

## Database Schema

```sql
stores table:
  status          ENUM('pending', 'approved', 'rejected') DEFAULT 'pending'
  rejection_note  TEXT NULLABLE
  approved_at     TIMESTAMP NULLABLE
  approved_by     BIGINT UNSIGNED FK -> admins.id NULLABLE
```

---

## Status Flow

```
Registration
     ↓
  pending ──→ approved ──┐
     ↓              ↑     │
  rejected ────────┘     │
                         │
                      toggle
```

### Vendor Registration
```php
Store::create([
    // ... other fields
    'status' => 'pending'  // Default
]);
```

### Admin Approves
```php
$store->update([
    'status' => 'approved',
    'approved_at' => now(),
    'approved_by' => $adminId,
    'rejection_note' => null
]);
```

### Admin Rejects
```php
$store->update([
    'status' => 'rejected',
    'rejection_note' => 'Business license expired',
    'approved_at' => null,
    'approved_by' => null
]);
```

### Toggle Status
```php
// Toggles between 'approved' and 'pending'
// Rejected stores must be explicitly re-approved
$newStatus = $store->status === 'approved' ? 'pending' : 'approved';
```

---

## Model Scopes

```php
// Get only approved stores
Store::approved()->get();

// Get only pending stores
Store::pending()->get();

// Get only rejected stores  
Store::rejected()->get();
```

## Model Helper Methods

```php
if ($store->isApproved()) {
    // Store can process orders
}

if ($store->isPending()) {
    // Show "awaiting approval" message
}

if ($store->isRejected()) {
    // Show rejection reason
    echo $store->rejection_note;
}
```

---

## API Endpoints

### Admin: Filter by Status
```bash
# Get all pending stores
GET /api/admin/stores?status=pending

# Get all approved stores
GET /api/admin/stores?status=approved

# Get all rejected stores
GET /api/admin/stores?status=rejected
```

### Admin: Approve Store
```bash
POST /api/admin/stores/{id}/approve

Response:
{
  "status": "approved",
  "approved_at": "2025-10-02T12:00:00.000000Z",
  "approved_by": 1,
  "rejection_note": null
}
```

### Admin: Reject Store
```bash
POST /api/admin/stores/{id}/reject
Body: {
  "rejection_note": "License document is expired"
}

Response:
{
  "status": "rejected",
  "rejection_note": "License document is expired",
  "approved_at": null,
  "approved_by": null
}
```

### Vendor: View Store
```bash
GET /api/vendor/store

Response (if pending):
{
  "status": "pending",
  "rejection_note": null,
  "approved_at": null
}

Response (if rejected):
{
  "status": "rejected",
  "rejection_note": "License document is expired",
  "approved_at": null
}

Response (if approved):
{
  "status": "approved",
  "rejection_note": null,
  "approved_at": "2025-10-02T12:00:00.000000Z"
}
```

---

## Validation Examples

### Valid Status Values
✅ `'pending'`  
✅ `'approved'`  
✅ `'rejected'`

### Invalid Status Values
❌ `'active'`  
❌ `'inactive'`  
❌ `true` / `false`  
❌ `0` / `1`

---

## Frontend Integration

### Status Display

```javascript
// Status badge colors
const statusConfig = {
  pending: {
    color: 'yellow',
    text: 'Pending Approval',
    textAr: 'قيد المراجعة'
  },
  approved: {
    color: 'green', 
    text: 'Approved',
    textAr: 'معتمد'
  },
  rejected: {
    color: 'red',
    text: 'Rejected',
    textAr: 'مرفوض'
  }
};
```

### Vendor Dashboard

```javascript
if (store.status === 'pending') {
  showMessage('Your store is awaiting admin approval');
}

if (store.status === 'rejected') {
  showAlert('Your store was rejected', store.rejection_note);
  enableEditButton(); // Allow vendor to fix issues
}

if (store.status === 'approved') {
  enableOrderProcessing();
}
```

---

## Migration Steps

### If Starting Fresh
```bash
php artisan migrate:fresh
```

### If You Have Existing Data
```bash
php artisan migrate
# The migration automatically converts:
# - status = false → 'pending'
# - status = true → 'approved'
```

---

## Benefits of Enum Status

### ✅ Clarity
- Clear, explicit state names
- No ambiguity about what boolean `true`/`false` means

### ✅ Extensibility
- Easy to add new states in future (e.g., 'suspended', 'archived')
- No need to add separate boolean flags

### ✅ Query Simplicity
```php
// Before (confusing)
Store::where('status', false)->whereNull('rejection_note')->get();

// After (clear)
Store::pending()->get();
```

### ✅ Type Safety
- Database enforces valid values
- Can't accidentally set status to invalid value

### ✅ Better UX
- Frontend can show status-specific messages
- Rejection reason always associated with 'rejected' status

---

## Comparison: Boolean vs Enum

### Boolean Approach (Old)
```php
status = false + rejection_note = null   → Pending
status = true  + rejection_note = null   → Approved
status = false + rejection_note = "..."  → Rejected
```
**Problem**: Status meaning depends on multiple fields

### Enum Approach (New)
```php
status = 'pending'   → Pending
status = 'approved'  → Approved  
status = 'rejected'  → Rejected (+ rejection_note)
```
**Solution**: Single field clearly defines state

---

## Important Notes

1. **Default Status**: All new stores start as `'pending'`
2. **Rejection Note**: Required only when status is `'rejected'`
3. **Toggle Behavior**: Only toggles between `'approved'` ↔ `'pending'`
4. **Rejected Stores**: Must be explicitly re-approved, not toggled
5. **Database Validation**: Enum constraint prevents invalid values

---

## Testing Checklist

- [ ] Vendor registration creates store with status = 'pending'
- [ ] Admin can approve pending store → status = 'approved'
- [ ] Admin can reject pending store → status = 'rejected'
- [ ] Rejection requires note (10-1000 chars)
- [ ] Vendor sees rejection_note when status = 'rejected'
- [ ] Approved stores show approved_at timestamp
- [ ] approved_by tracks which admin took action
- [ ] Toggle switches between 'approved' and 'pending'
- [ ] Rejected stores don't toggle (must be re-approved)
- [ ] Cannot approve already-approved store
- [ ] Filter endpoints work: ?status=pending|approved|rejected
- [ ] Scopes work: pending(), approved(), rejected()
- [ ] Helper methods work: isApproved(), isPending(), isRejected()

---

## SQL Queries

### Count Stores by Status
```sql
SELECT status, COUNT(*) as count
FROM stores
GROUP BY status;
```

### Get Rejected Stores with Notes
```sql
SELECT id, name_en, status, rejection_note, created_at
FROM stores
WHERE status = 'rejected'
ORDER BY created_at DESC;
```

### Get Recently Approved Stores
```sql
SELECT s.*, a.name as approved_by_name
FROM stores s
LEFT JOIN admins a ON s.approved_by = a.id
WHERE s.status = 'approved'
ORDER BY s.approved_at DESC
LIMIT 10;
```

---

## Future Enhancements

### Potential Additional States
- `'suspended'` - Temporarily disabled by admin
- `'archived'` - Store no longer active but kept for records
- `'under_review'` - Flagged for manual review
- `'inactive'` - Vendor voluntarily deactivated

### Implementation Example
```php
// Future migration
$table->enum('status', [
    'pending', 
    'approved', 
    'rejected',
    'suspended',  // NEW
    'archived'    // NEW
])->default('pending');
```

---

That's it! The status is now a clean, semantic enum. 🎉
