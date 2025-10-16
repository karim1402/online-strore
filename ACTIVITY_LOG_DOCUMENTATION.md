# Activity Log Implementation Documentation

## Overview
This project now includes comprehensive activity logging using the `spatie/laravel-activitylog` package. All important model changes and administrative actions are automatically tracked and stored in the database.

## Installation
The package has been installed and configured:
- Package: `spatie/laravel-activitylog` v4.10.2
- Configuration file: `config/activitylog.php`
- Database table: `activity_log`

## Models with Activity Logging

The following models automatically log their changes:

### 1. **Admin** (`app/Models/Admin.php`)
- **Log Name**: `admin`
- **Tracked Fields**: name, email, phone, status
- **Events**: created, updated, deleted

### 2. **User** (`app/Models/User.php`)
- **Log Name**: `user`
- **Tracked Fields**: name, email
- **Events**: created, updated, deleted

### 3. **Vendor** (`app/Models/Vendor.php`)
- **Log Name**: `vendor`
- **Tracked Fields**: name, email, phone, address, store_id, status
- **Events**: created, updated, deleted

### 4. **Delivery** (`app/Models/Delivery.php`)
- **Log Name**: `delivery`
- **Tracked Fields**: name, email, phone, vehicle_type, vehicle_number, status, availability
- **Events**: created, updated, deleted

### 5. **Store** (`app/Models/Store.php`)
- **Log Name**: `store`
- **Tracked Fields**: name_en, name_ar, description_en, description_ar, status, rejection_note, approved_at, approved_by
- **Events**: created, updated, deleted
- **Custom Logging**: Store approval/rejection actions

### 6. **Branch** (`app/Models/Branch.php`)
- **Log Name**: `branch`
- **Tracked Fields**: name_en, name_ar, address, phone, is_main, is_active, latitude, longitude
- **Events**: created, updated, deleted

### 7. **Category** (`app/Models/Category.php`)
- **Log Name**: `category`
- **Tracked Fields**: name_en, name_ar, description_en, description_ar, is_active, sort_order
- **Events**: created, updated, deleted

### 8. **MainCategory** (`app/Models/MainCategory.php`)
- **Log Name**: `main_category`
- **Tracked Fields**: name_en, name_ar, description_en, description_ar, status, sort_order
- **Events**: created, updated, deleted

### 9. **Product** (`app/Models/Product.php`)
- **Log Name**: `product`
- **Tracked Fields**: name_en, name_ar, description_en, description_ar, base_price, is_active, category_id
- **Events**: created, updated, deleted

## Manual Activity Logging

### Controller Actions with Custom Logging

#### 1. **Admin Authentication** (`app/Http/Controllers/Api/Admin/AuthController.php`)
- **Login**: Logs IP address and user agent
- **Logout**: Logs logout event

#### 2. **Vendor Authentication** (`app/Http/Controllers/Api/Vendor/AuthController.php`)
- **Registration**: Logs vendor registration with store creation, IP address
- **Login**: Logs IP address and user agent
- **Logout**: Logs logout event

#### 3. **Delivery Authentication** (`app/Http/Controllers/Api/Delivery/AuthController.php`)
- **Registration**: Logs delivery user registration with vehicle type, IP address
- **Login**: Logs IP address and user agent
- **Logout**: Logs logout event

#### 4. **User Authentication** (`app/Http/Controllers/Api/User/AuthController.php`)
- **Registration**: Logs IP address
- **Login**: Logs IP address and user agent
- **Logout**: Logs logout event

#### 5. **Store Management** (`app/Http/Controllers/Api/Admin/StoreController.php`)
- **Store Approval**: Logs admin details and store name
- **Store Rejection**: Logs admin details, store name, and rejection note

#### 6. **Admin User Management** (`app/Http/Controllers/Api/Admin/AdminUserController.php`)
- **Create Admin**: Logs creator admin details, new admin name/email, assigned role
- **Delete Admin**: Logs deleting admin details, deleted admin name/email
- **Toggle Status**: Logs status change with old and new status values

### How to Add Manual Logging

```php
use Spatie\Activitylog\Facades\LogActivity;

// Simple log
activity('log_name')
    ->log('Description of the activity');

// Log with causer (who performed the action)
activity('log_name')
    ->causedBy(auth()->user())
    ->log('User performed an action');

// Log with subject (what was affected)
activity('log_name')
    ->performedOn($model)
    ->log('Action performed on model');

// Log with properties (additional data)
activity('log_name')
    ->causedBy($admin)
    ->performedOn($store)
    ->withProperties([
        'action' => 'approved',
        'admin_name' => $admin->name,
        'custom_data' => 'value',
    ])
    ->log('Store approved by admin');
```

## API Endpoints

### Activity Log Endpoints (Admin Only)

All endpoints are prefixed with `/api/admin/activity-logs` and require admin authentication.

#### 1. **Get All Activity Logs**
```
GET /api/admin/activity-logs
```

**Query Parameters:**
- `log_name`: Filter by log name (admin, user, store, product, etc.)
- `event`: Filter by event type (created, updated, deleted)
- `causer_type`: Filter by user type (App\Models\Admin, App\Models\User, etc.)
- `causer_id`: Filter by specific user ID
- `subject_type`: Filter by model type (App\Models\Store, App\Models\Product, etc.)
- `subject_id`: Filter by specific model ID
- `description`: Search in description (partial match)
- `start_date`: Filter logs from this date
- `end_date`: Filter logs up to this date
- `per_page`: Results per page (default: 15)

**Example:**
```bash
GET /api/admin/activity-logs?log_name=store&event=updated&per_page=20
```

#### 2. **Get Single Activity Log**
```
GET /api/admin/activity-logs/{id}
```

#### 3. **Get Activity Logs for a Specific Model**
```
GET /api/admin/activity-logs/model/{modelType}/{modelId}
```

**Example:**
```bash
GET /api/admin/activity-logs/model/App\Models\Store/5
```

#### 4. **Get Activity Logs by a Specific User**
```
GET /api/admin/activity-logs/user/{userType}/{userId}
```

**Example:**
```bash
GET /api/admin/activity-logs/user/App\Models\Admin/1
```

#### 5. **Get Activity Statistics**
```
GET /api/admin/activity-logs/stats
```

Returns:
- Total activities count
- Today's activities count
- This week's activities count
- This month's activities count
- Activities grouped by log name
- Activities grouped by event type
- Recent 10 activities

#### 6. **Get Available Log Names**
```
GET /api/admin/activity-logs/log-names
```

Returns a list of all unique log names in the system.

#### 7. **Get Available Event Types**
```
GET /api/admin/activity-logs/event-types
```

Returns a list of all unique event types (created, updated, deleted, etc.).

#### 8. **Cleanup Old Logs** (Super Admin Only)
```
DELETE /api/admin/activity-logs/cleanup?days=365
```

Deletes activity logs older than the specified number of days (default: 365).

## Activity Log Structure

Each activity log entry contains:

```json
{
  "id": 1,
  "log_name": "admin",
  "description": "Admin logged in",
  "subject_type": "App\\Models\\Admin",
  "subject_id": 2,
  "causer_type": "App\\Models\\Admin",
  "causer_id": 2,
  "properties": {
    "ip_address": "192.168.1.1",
    "user_agent": "Mozilla/5.0...",
    "attributes": {
      "name": "John Doe",
      "email": "john@example.com"
    },
    "old": {
      "name": "Jane Doe",
      "email": "jane@example.com"
    }
  },
  "event": "updated",
  "batch_uuid": null,
  "created_at": "2025-10-15T12:30:00.000000Z",
  "updated_at": "2025-10-15T12:30:00.000000Z",
  "causer": {
    "id": 2,
    "name": "Admin User",
    "email": "admin@example.com"
  },
  "subject": {
    "id": 2,
    "name": "John Doe"
  }
}
```

## Configuration

The activity log configuration is located at `config/activitylog.php`:

- **enabled**: Enable/disable activity logging (default: true)
- **delete_records_older_than_days**: Auto-cleanup period (default: 365 days)
- **default_log_name**: Default log name when not specified (default: 'default')
- **table_name**: Database table name (default: 'activity_log')

## Usage Examples

### Example 1: View All Store Activities
```bash
GET /api/admin/activity-logs?log_name=store&per_page=50
```

### Example 2: View All Actions by a Specific Admin
```bash
GET /api/admin/activity-logs/user/App\Models\Admin/2
```

### Example 3: View All Activities for a Specific Store
```bash
GET /api/admin/activity-logs/model/App\Models\Store/10
```

### Example 4: View Today's Activities
```bash
GET /api/admin/activity-logs?start_date=2025-10-15&end_date=2025-10-15
```

### Example 5: Search for Store Approvals
```bash
GET /api/admin/activity-logs?description=approved&log_name=store
```

## Best Practices

1. **Use Descriptive Log Names**: Group related activities with consistent log names
2. **Include Relevant Properties**: Add context-specific data in properties
3. **Log Important Actions**: Focus on security-sensitive and business-critical operations
4. **Regular Cleanup**: Schedule periodic cleanup of old logs to maintain performance
5. **Monitor Logs**: Regularly review activity logs for unusual patterns

## Security Considerations

- Activity logs are only accessible to authenticated admin users
- The cleanup endpoint is restricted to super admins only
- Logs include IP addresses and user agents for security tracking
- All sensitive actions (login, approval, rejection) are automatically logged

## Maintenance

### Automatic Cleanup Command
You can schedule the cleanup command in `app/Console/Kernel.php`:

```php
protected function schedule(Schedule $schedule)
{
    // Clean up activity logs older than 1 year daily
    $schedule->command('activitylog:clean')->daily();
}
```

### Manual Cleanup
Use the API endpoint or run the Artisan command:
```bash
php artisan activitylog:clean
```

## Support

For more information about the package, visit:
- [Spatie Activity Log Documentation](https://spatie.be/docs/laravel-activitylog)
- [GitHub Repository](https://github.com/spatie/laravel-activitylog)
