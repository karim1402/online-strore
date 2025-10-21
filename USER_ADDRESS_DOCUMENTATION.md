# User Address Management System

## Overview
Simplified address management system for users where each user can have multiple addresses with address type (villa/apartment/office), building details, location coordinates, and a default address selection.

---

## Database Schema

### Table: `user_addresses`

| Field | Type | Description |
|-------|------|-------------|
| id | bigint | Primary key |
| user_id | bigint | Foreign key to users table |
| address_name | string | Optional custom name for the address (nullable) |
| address_type | enum | Type of address: 'villa', 'apartment', or 'office' |
| building_name | string | Name of the building |
| apartment_number | string | Apartment number (nullable for villas) |
| floor_number | string | Floor number (nullable) |
| street_name | string | Name of the street |
| landmark | string | Nearby landmark/tombstone (nullable) |
| phone | string | Contact phone for this address |
| latitude | decimal(10,8) | GPS latitude (nullable) |
| longitude | decimal(11,8) | GPS longitude (nullable) |
| is_default | boolean | Default shipping address (default: false) |
| created_at | timestamp | Creation timestamp |
| updated_at | timestamp | Last update timestamp |

**Indexes:**
- `user_id` - For faster user-based queries
- `is_default` - For default address lookups
- `address_type` - For filtering by address type

**Constraints:**
- Foreign key on `user_id` with cascade delete

---

## Model Features

### UserAddress Model (`app/Models/UserAddress.php`)

**Relationships:**
- `user()` - BelongsTo User relationship

**Attributes:**
- `full_address` - Computed attribute that returns formatted full address

**Scopes:**
- `default()` - Filter only default addresses
- `ofType($type)` - Filter addresses by type (villa, apartment, office)

**Activity Logging:**
- Automatically logs all create, update, and delete operations
- Uses `user_address` log name
- Tracks all address fields including location and status changes

**Automatic Behavior:**
- When an address is set as default, all other user addresses are automatically unmarked as default
- Ensures only one default address per user at all times

### User Model Updates (`app/Models/User.php`)

**New Relationships:**
- `addresses()` - HasMany relationship to UserAddress
- `defaultAddress()` - HasOne relationship to get the default address

---

## API Endpoints

Base URL: `/api/user/addresses`

All endpoints require authentication with `auth:api` middleware.

### 1. Get All User Addresses
```http
GET /api/user/addresses
```

**Query Parameters:**
- `address_type` (string) - Filter by address type (villa, apartment, office)
- `is_default` (boolean) - Filter by default status
- `search` (string) - Search in address_name, building_name, street_name, or landmark

**Response:**
```json
{
  "success": true,
  "message": "Data retrieved successfully",
  "data": [
    {
      "id": 1,
      "user_id": 1,
      "address_name": "My Home",
      "address_type": "apartment",
      "building_name": "Tower A",
      "apartment_number": "405",
      "floor_number": "4",
      "street_name": "Tahrir Street",
      "landmark": "Near Cairo Tower",
      "phone": "+201234567890",
      "latitude": "30.0444196",
      "longitude": "31.2357116",
      "is_default": true,
      "created_at": "2024-10-17T06:33:00.000000Z",
      "updated_at": "2024-10-17T06:33:00.000000Z"
    }
  ]
}
```

### 2. Get Single Address
```http
GET /api/user/addresses/{id}
```

**Response:** Single address object (same format as above)

### 3. Create New Address
```http
POST /api/user/addresses
```

**Request Body:**
```json
{
  "address_name": "My Home",
  "address_type": "apartment",
  "building_name": "Tower A",
  "apartment_number": "405",
  "floor_number": "4",
  "street_name": "Tahrir Street",
  "landmark": "Near Cairo Tower",
  "phone": "+201234567890",
  "latitude": 30.0444196,
  "longitude": 31.2357116,
  "is_default": true
}
```

**Validation Rules:**
- `address_name`: optional, string, max 255
- `address_type`: required, must be one of: 'villa', 'apartment', 'office'
- `building_name`: required, string, max 255
- `apartment_number`: optional, string, max 50
- `floor_number`: optional, string, max 50
- `street_name`: required, string, max 255
- `landmark`: optional, string, max 255 (nearby landmark/tombstone)
- `phone`: required, string, max 20
- `latitude`: optional, numeric, between -90 and 90
- `longitude`: optional, numeric, between -180 and 180
- `is_default`: optional, boolean

**Notes:**
- If this is the user's first address, it will automatically be set as default
- If `is_default` is true, all other addresses will be unmarked as default

**Response:** Created address object with 201 status code

### 4. Update Address
```http
PUT /api/user/addresses/{id}
```

**Request Body:** Same as create (all fields optional with `sometimes` validation)

**Response:** Updated address object

### 5. Delete Address
```http
DELETE /api/user/addresses/{id}
```

**Notes:**
- If the deleted address was the default, another active address will automatically be set as default

**Response:**
```json
{
  "success": true,
  "message": "Data deleted successfully",
  "data": null
}
```

### 6. Set Address as Default
```http
PATCH /api/user/addresses/{id}/set-default
```

**Notes:**
- Automatically unsets other default addresses for the user

**Response:** Updated address object with is_default = true

### 7. Get Default Address
```http
GET /api/user/addresses/default
```

**Response:** Default address object (or 404 if no default address exists)

---

## Activity Logging

All address operations are automatically logged to the activity log system:

**Logged Events:**
- Address creation
- Address updates (with old and new values)
- Address deletion
- Setting address as default

**Logged Properties:**
- User information (name, email)
- Address details
- IP address and user agent
- Old and new values (for updates)

**Log Name:** `user_address`

**Query Activity Logs:**
```http
GET /api/admin/activity-logs?log_name=user_address
```

---

## Usage Examples

### Creating a User's First Address (Apartment)
```javascript
// POST /api/user/addresses
{
  "address_type": "apartment",
  "building_name": "Tower A",
  "apartment_number": "405",
  "floor_number": "4",
  "street_name": "Tahrir Street",
  "landmark": "Near Cairo Tower",
  "phone": "+201234567890",
  "latitude": 30.0444196,
  "longitude": 31.2357116
}
// This will automatically become the default address
```

### Adding an Office Address
```javascript
// POST /api/user/addresses
{
  "address_name": "Work Office",
  "address_type": "office",
  "building_name": "Smart Village B2",
  "floor_number": "5",
  "street_name": "26th of July Corridor",
  "landmark": "Next to Mall entrance",
  "phone": "+201234567890"
}
```

### Adding a Villa Address
```javascript
// POST /api/user/addresses
{
  "address_name": "Family Villa",
  "address_type": "villa",
  "building_name": "Villa 45",
  "street_name": "Palm Hills",
  "landmark": "Near clubhouse",
  "phone": "+201234567890"
}
```

### Searching for Addresses
```javascript
// GET /api/user/addresses?search=tower&address_type=apartment
// Returns all apartment addresses containing "tower" in address_name, building_name, street_name, or landmark
```

### Getting User with Addresses in Other Controllers
```php
$user = auth('api')->user();
$addresses = $user->addresses; // All addresses
$defaultAddress = $user->defaultAddress; // Default address only
$apartments = $user->addresses()->ofType('apartment')->get(); // Only apartments
$villas = $user->addresses()->ofType('villa')->get(); // Only villas
```

---

## Migration

Run the migration to create the table:
```bash
php artisan migrate
```

The migration file: `database/migrations/2024_10_17_063300_create_user_addresses_table.php`

---

## Security Features

1. **User Isolation**: Users can only access their own addresses
2. **Authentication Required**: All endpoints require valid JWT token
3. **Automatic Default Management**: System ensures data integrity for default addresses
4. **Activity Logging**: Full audit trail of all address operations
5. **Cascade Delete**: Addresses are automatically deleted when user is deleted

---

## Integration Tips

### For Frontend Developers
1. Always fetch addresses when showing checkout or delivery pages
2. Show the default address as pre-selected
3. Allow users to add/edit/delete addresses from their profile
4. Use the location coordinates for map integration
5. Validate phone numbers according to your region requirements

### For Backend Developers
1. Use the relationship methods on User model to access addresses
2. Check for default address before creating orders
3. Include address validation in order creation
4. Consider caching user's default address for performance
5. Use the activity log to track address-related issues

---

## Files Created

1. **Migration**: `database/migrations/2024_10_17_063300_create_user_addresses_table.php`
2. **Model**: `app/Models/UserAddress.php`
3. **Controller**: `app/Http/Controllers/Api/User/AddressController.php`
4. **Routes**: Updated `routes/api/user.php`
5. **User Model**: Updated `app/Models/User.php` with relationships
6. **Documentation**: This file

---

## Future Enhancements (Optional)

1. **Address Validation**: Integrate with Google Maps API for address verification
2. **Geocoding**: Automatically get coordinates from address
3. **Address Templates**: Pre-defined address formats per country
4. **Delivery Zones**: Check if address is within delivery coverage
5. **Address Book Sharing**: Allow sharing addresses with family members
6. **Address History**: Track when addresses were used in orders
