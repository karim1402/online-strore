# Branch Working Hours Documentation

## Overview
Simple working hours system for store branches with opening and closing times stored directly in the branches table.

---

## Database Changes

### Migration
**File:** `database/migrations/2024_10_17_112600_add_opening_closing_times_to_branches_table.php`

**Added Columns to `branches` table:**
- `opening_time` - TIME (nullable) - Branch opening time (e.g., "09:00")
- `closing_time` - TIME (nullable) - Branch closing time (e.g., "22:00")

### Run Migration
```bash
php artisan migrate
```

---

## Model Updates

### Branch Model (`app/Models/Branch.php`)

**New Fillable Fields:**
- `opening_time`
- `closing_time`

**New Methods:**

#### 1. `isCurrentlyOpen(): bool`
Check if the branch is currently open based on current time.

```php
$branch = Branch::find(1);
if ($branch->isCurrentlyOpen()) {
    echo "Branch is open now!";
}
```

**Logic:**
- Returns `false` if branch is inactive
- Returns `true` if no opening/closing times are set (24/7 operation)
- Compares current time with opening/closing times

#### 2. `isOpenAt(string $time): bool`
Check if the branch is open at a specific time.

```php
$branch = Branch::find(1);
if ($branch->isOpenAt('14:30')) {
    echo "Branch is open at 2:30 PM";
}
```

**Parameters:**
- `$time` - Time in H:i or H:i:s format (e.g., "14:30" or "14:30:00")

**Activity Logging:**
- Opening and closing times are now tracked in activity logs

---

## API Endpoints

### 1. Create Branch (with working hours)
```http
POST /api/vendor/branches
```

**Form-data Fields:**
```
name_en: "Main Branch"
name_ar: "الفرع الرئيسي"
address: "123 Main Street, Cairo"
latitude: 30.0444196
longitude: 31.2357116
phone: "+201234567890"
description_en: "Our main branch"
description_ar: "فرعنا الرئيسي"
opening_time: "09:00"
closing_time: "22:00"
is_main: 1
is_active: 1
```

**Validation:**
- `opening_time`: optional, must be in H:i format (e.g., "09:00", "14:30")
- `closing_time`: optional, must be in H:i format (e.g., "22:00", "18:45")

### 2. Update Branch (with working hours)
```http
PUT /api/vendor/branches/{id}
```

**Form-data Fields (all optional):**
```
opening_time: "08:00"
closing_time: "23:00"
```

### 3. Get Branch
```http
GET /api/vendor/branches/{id}
```

**Response includes:**
```json
{
  "success": true,
  "message": "Data retrieved successfully",
  "data": {
    "id": 1,
    "store_id": 1,
    "name_en": "Main Branch",
    "name_ar": "الفرع الرئيسي",
    "address": "123 Main Street, Cairo",
    "latitude": "30.0444196",
    "longitude": "31.2357116",
    "phone": "+201234567890",
    "description_en": "Our main branch",
    "description_ar": "فرعنا الرئيسي",
    "opening_time": "09:00:00",
    "closing_time": "22:00:00",
    "is_main": true,
    "is_active": true,
    "created_at": "2024-10-17T12:00:00.000000Z",
    "updated_at": "2024-10-17T12:00:00.000000Z"
  }
}
```

---

## Usage Examples

### Example 1: Create Branch with Working Hours
```javascript
// POST /api/vendor/branches
FormData:
{
  name_en: "Downtown Branch",
  name_ar: "فرع وسط البلد",
  address: "15 Tahrir Square, Cairo",
  latitude: 30.0444,
  longitude: 31.2357,
  phone: "+201234567890",
  opening_time: "09:00",
  closing_time: "22:00",
  is_active: 1
}
```

### Example 2: Update Only Working Hours
```javascript
// PUT /api/vendor/branches/1
FormData:
{
  opening_time: "08:30",
  closing_time: "23:30"
}
```

### Example 3: Check if Branch is Open (in PHP)
```php
// Get branch
$branch = Branch::find(1);

// Check if currently open
if ($branch->isCurrentlyOpen()) {
    echo "Branch is open now!";
} else {
    echo "Branch is closed";
}

// Check if open at specific time
if ($branch->isOpenAt('15:00')) {
    echo "Branch is open at 3 PM";
}

// Get opening hours
echo "Opens at: " . $branch->opening_time;
echo "Closes at: " . $branch->closing_time;
```

### Example 4: Query Open Branches
```php
// Get all active branches
$branches = Branch::active()->get();

// Filter currently open branches
$openBranches = $branches->filter(function($branch) {
    return $branch->isCurrentlyOpen();
});
```

### Example 5: 24/7 Operation
If you want a branch to operate 24/7, simply leave `opening_time` and `closing_time` as `null`:

```javascript
// POST /api/vendor/branches
FormData:
{
  name_en: "24/7 Branch",
  address: "Airport Road",
  latitude: 30.0444,
  longitude: 31.2357,
  // Don't include opening_time and closing_time
  is_active: 1
}
```

---

## Time Format

**Required Format:** `H:i` (24-hour format)

**Valid Examples:**
- `"09:00"` - 9:00 AM
- `"14:30"` - 2:30 PM
- `"22:45"` - 10:45 PM
- `"00:00"` - Midnight
- `"23:59"` - One minute before midnight

**Invalid Examples:**
- `"9:00"` - Missing leading zero
- `"09:00 AM"` - Contains AM/PM
- `"9"` - Missing minutes
- `"25:00"` - Invalid hour

---

## Validation Rules

### Creating Branch:
```php
'opening_time' => 'nullable|date_format:H:i'
'closing_time' => 'nullable|date_format:H:i'
```

### Updating Branch:
```php
'opening_time' => 'nullable|date_format:H:i'
'closing_time' => 'nullable|date_format:H:i'
```

---

## Activity Logging

All changes to opening and closing times are automatically logged in the activity log system:

**Logged Fields:**
- `opening_time`
- `closing_time`

**Query Activity Logs:**
```http
GET /api/admin/activity-logs?log_name=branch
```

---

## Integration Tips

### For Frontend Developers:
1. Use time picker input for opening/closing times
2. Format must be HH:MM (24-hour format)
3. Both fields are optional - null means 24/7 operation
4. Display "24/7" when both times are null
5. Show "Open Now" badge when `isCurrentlyOpen()` is true

### For Backend Developers:
1. Use `$branch->isCurrentlyOpen()` to check real-time status
2. Use `$branch->isOpenAt($time)` for specific time checks
3. Times are stored as TIME type in database
4. Activity logging tracks all time changes
5. Validation ensures proper H:i format

---

## Files Modified

1. **Migration**: `database/migrations/2024_10_17_112600_add_opening_closing_times_to_branches_table.php`
2. **Model**: `app/Models/Branch.php`
   - Added fillable fields
   - Added helper methods
   - Updated activity logging
3. **Controller**: `app/Http/Controllers/Api/Vendor/BranchController.php`
   - Added validation rules
   - Added fields to create/update operations

---

## Future Enhancements (Optional)

1. **Multiple Time Slots**: Support for break times (e.g., closed 2-4 PM)
2. **Day-Specific Hours**: Different hours for different days of the week
3. **Holiday Hours**: Special hours for holidays
4. **Seasonal Hours**: Different hours for different seasons
5. **Automatic Status Updates**: Cron job to automatically update branch status

---

## Notes

- Times are stored in 24-hour format in the database
- Null values for both times mean the branch operates 24/7
- The `is_active` flag takes precedence - inactive branches are always considered closed
- Times are compared as strings, so ensure proper H:i:s format
- Activity logs track all changes to working hours for audit purposes
