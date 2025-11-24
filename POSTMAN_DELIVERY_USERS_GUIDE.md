# Postman Collection - Admin Delivery Users CRUD

## Overview

This Postman collection contains all the endpoints for managing delivery users from the admin panel. **All requests use form-data format** instead of JSON raw.

## Collection File

📦 **File:** `Admin_Delivery_Users.postman_collection.json`

## How to Import

1. Open Postman
2. Click **Import** button (top left)
3. Select the `Admin_Delivery_Users.postman_collection.json` file
4. Click **Import**

## Setup Environment Variables

Before using the collection, set up these environment variables:

1. Create a new environment in Postman (e.g., "Makook Local")
2. Add these variables:

| Variable | Initial Value | Current Value |
|----------|--------------|---------------|
| `base_url` | `http://localhost` | Your API base URL |
| `admin_token` | *(leave empty)* | Auto-filled after login |

**Note:** The `admin_token` will be automatically saved when you run the "Admin Login" request.

## Collection Structure

### 1. Authentication
- **Admin Login** - Login and auto-save token to environment

### 2. Delivery Users (Main CRUD)
- **List Delivery Users** - Get paginated list with filters
- **Create Delivery User** - Create new delivery user (form-data)
- **Get Single Delivery User** - View details of one user
- **Update Delivery User** - Update user information (form-data)
- **Toggle Delivery User Status** - Activate/deactivate user
- **Delete Delivery User** - Permanently delete user

### 3. Test Scenarios
- **Search Delivery Users** - Test search functionality
- **Filter by Active Status** - Test status filter
- **Filter by Available** - Test availability filter
- **Filter by Vehicle Type** - Test vehicle type filter
- **Combined Filters** - Test multiple filters together

## Quick Start Guide

### Step 1: Login as Admin

1. Open "Authentication" folder
2. Run "Admin Login" request
3. Update the credentials in the form-data:
   ```
   email: your-admin-email@example.com
   password: your-admin-password
   ```
4. Send the request
5. ✅ Token will be automatically saved to environment

### Step 2: Test CRUD Operations

#### Create a Delivery User
1. Go to "Delivery Users" → "Create Delivery User"
2. Review/modify the form-data fields:
   - `name`: "John Doe"
   - `email`: "john.delivery@example.com"
   - `password`: "password123"
   - `phone`: "+1234567890"
   - `vehicle_type`: "motorcycle" (optional)
   - `vehicle_number`: "ABC123" (optional)
   - `license_number`: "DL12345" (optional)
   - `address`: "123 Main Street" (optional)
   - `status`: "true" (optional)
   - `availability`: "true" (optional)
3. Send the request
4. ✅ User created successfully

#### List All Delivery Users
1. Go to "Delivery Users" → "List Delivery Users"
2. Optionally add query parameters:
   - `per_page`: 15
   - `search`: Search term
   - `status`: true/false
   - `availability`: true/false
   - `vehicle_type`: motorcycle/car/bike
3. Send the request
4. ✅ See paginated list of users

#### Update a Delivery User
1. Go to "Delivery Users" → "Update Delivery User"
2. Change the `:id` in the URL to the user ID you want to update
3. **Important:** Enable only the fields you want to update by unchecking "disabled"
4. Modify the values of enabled fields
5. Send the request
6. ✅ User updated successfully

#### Toggle Status
1. Go to "Delivery Users" → "Toggle Delivery User Status"
2. Change the `:id` in the URL
3. Send the request
4. ✅ Status toggled (active ↔ inactive)

#### Delete a User
1. Go to "Delivery Users" → "Delete Delivery User"
2. Change the `:id` in the URL
3. Send the request
4. ✅ User deleted permanently

## Form-Data Format

All POST and PUT requests use **form-data** format. Here's how to use it:

### Creating a User (POST)
```
Method: POST
URL: {{base_url}}/api/admin/delivery-users
Headers:
  - Authorization: Bearer {{admin_token}}
  - Accept: application/json
  - Accept-Language: en

Body (form-data):
  name: John Doe
  email: john@example.com
  password: password123
  phone: +1234567890
  vehicle_type: motorcycle
  vehicle_number: ABC123
  license_number: DL12345
  address: 123 Main Street
  status: true
  availability: true
```

### Updating a User (PUT)
```
Method: PUT
URL: {{base_url}}/api/admin/delivery-users/1
Headers:
  - Authorization: Bearer {{admin_token}}
  - Accept: application/json

Body (form-data):
  name: John Updated
  phone: +9876543210
  availability: false
```

**Tip:** In the "Update Delivery User" request, all fields are pre-filled but disabled. Enable only the fields you want to update.

## Testing Filters

The collection includes pre-configured test scenarios:

1. **Search by Name/Email/Phone**
   ```
   GET {{base_url}}/api/admin/delivery-users?search=john
   ```

2. **Filter by Active Users**
   ```
   GET {{base_url}}/api/admin/delivery-users?status=true
   ```

3. **Filter by Available Users**
   ```
   GET {{base_url}}/api/admin/delivery-users?availability=true
   ```

4. **Filter by Vehicle Type**
   ```
   GET {{base_url}}/api/admin/delivery-users?vehicle_type=motorcycle
   ```

5. **Combined Filters**
   ```
   GET {{base_url}}/api/admin/delivery-users?search=john&status=true&availability=true&vehicle_type=motorcycle&per_page=10
   ```

## Response Format

All responses follow this format:

### Success Response
```json
{
  "success": true,
  "message": "Delivery user created successfully",
  "data": {
    "id": 1,
    "name": "John Doe",
    "email": "john@example.com",
    "phone": "+1234567890",
    "vehicle_type": "motorcycle",
    "vehicle_number": "ABC123",
    "license_number": "DL12345",
    "address": "123 Main Street",
    "status": true,
    "availability": true,
    "created_at": "2024-01-01T00:00:00.000000Z",
    "updated_at": "2024-01-01T00:00:00.000000Z"
  }
}
```

### Error Response
```json
{
  "success": false,
  "message": "The email has already been taken",
  "data": null,
  "errors": {
    "email": ["The email has already been taken"]
  }
}
```

## Permissions Required

Make sure your admin account has these permissions:

- ✅ `delivery-users.view` - For listing and viewing users
- ✅ `delivery-users.create` - For creating users
- ✅ `delivery-users.update` - For updating users and toggling status
- ✅ `delivery-users.delete` - For deleting users

If you get a 403 Forbidden error, ask your super admin to assign these permissions to your role.

## Troubleshooting

### 401 Unauthorized
- **Problem:** Token is missing or expired
- **Solution:** Run "Admin Login" again to get a fresh token

### 403 Forbidden
- **Problem:** Your admin account doesn't have the required permissions
- **Solution:** Contact super admin to assign delivery-users permissions to your role

### 422 Validation Error
- **Problem:** Invalid data in form-data
- **Solution:** Check the error response for specific field errors and fix them

### 404 Not Found
- **Problem:** Delivery user ID doesn't exist
- **Solution:** Verify the ID exists by listing all users first

## Language Support

You can change the response language by modifying the `Accept-Language` header:

- **English:** `Accept-Language: en`
- **Arabic:** `Accept-Language: ar`

All success and error messages support both languages.

## Tips

1. **Auto Token Management:** The login request automatically saves the token to your environment
2. **Disabled Fields:** In update requests, disable fields you don't want to change
3. **Boolean Values:** Use "true" or "false" as strings in form-data
4. **Search is Flexible:** Search works across name, email, and phone fields
5. **Combine Filters:** You can use multiple filters together for precise results
6. **Pagination:** Use `per_page` parameter to control how many results you get

## Testing Workflow

Recommended testing order:

1. ✅ Admin Login
2. ✅ List Delivery Users (empty initially)
3. ✅ Create Delivery User
4. ✅ Get Single Delivery User
5. ✅ Update Delivery User
6. ✅ Toggle Status
7. ✅ Test Search & Filters
8. ✅ Delete Delivery User

---

**Collection Ready!** 🚀

Import the collection and start testing your delivery user management endpoints.
