# Multi-Guard JWT Authentication API Documentation

## Overview
This Laravel application implements a multi-guard authentication system using JWT (JSON Web Tokens) for four different user types:
- **Users** (Regular users)
- **Store Users** (Store owners/managers)
- **Admins** (System administrators)
- **Deliveries** (Delivery personnel)

Each guard has its own authentication endpoints, models, and protected routes. All responses include bilingual messages (English and Arabic).

## Setup Instructions

### 1. Install Dependencies
```bash
composer install
```

### 2. Configure Environment
Add the following to your `.env` file:
```env
JWT_SECRET=your-secret-key-here
JWT_TTL=60
JWT_REFRESH_TTL=20160
JWT_ALGO=HS256
```

### 3. Generate JWT Secret
```bash
php artisan jwt:secret
```

### 4. Run Migrations
```bash
php artisan migrate
```

## API Endpoints

### Base URL
```
http://your-domain.com/api
```

---

## 1. User Authentication (`/api/user`)

### Register
- **URL:** `/api/user/register`
- **Method:** `POST`
- **Body:**
```json
{
    "name": "John Doe",
    "email": "user@example.com",
    "password": "password123",
    "password_confirmation": "password123"
}
```

### Login
- **URL:** `/api/user/login`
- **Method:** `POST`
- **Body:**
```json
{
    "email": "user@example.com",
    "password": "password123"
}
```

### Profile
- **URL:** `/api/user/profile`
- **Method:** `GET`
- **Headers:** `Authorization: Bearer {token}`

### Logout
- **URL:** `/api/user/logout`
- **Method:** `POST`
- **Headers:** `Authorization: Bearer {token}`

### Refresh Token
- **URL:** `/api/user/refresh`
- **Method:** `POST`
- **Headers:** `Authorization: Bearer {token}`

### Protected Routes
- **Dashboard:** `/api/user/dashboard` (GET)

---

## 2. Store User Authentication (`/api/store`)

### Register
- **URL:** `/api/store/register`
- **Method:** `POST`
- **Body:**
```json
{
    "name": "Store Owner",
    "email": "store@example.com",
    "password": "password123",
    "password_confirmation": "password123",
    "store_name": "My Store",
    "phone": "+1234567890",
    "address": "123 Store Street"
}
```

### Login
- **URL:** `/api/store/login`
- **Method:** `POST`
- **Body:**
```json
{
    "email": "store@example.com",
    "password": "password123"
}
```

### Profile
- **URL:** `/api/store/profile`
- **Method:** `GET`
- **Headers:** `Authorization: Bearer {token}`

### Logout
- **URL:** `/api/store/logout`
- **Method:** `POST`
- **Headers:** `Authorization: Bearer {token}`

### Refresh Token
- **URL:** `/api/store/refresh`
- **Method:** `POST`
- **Headers:** `Authorization: Bearer {token}`

### Protected Routes
- **Dashboard:** `/api/store/dashboard` (GET)
- **Products:** `/api/store/products` (GET)

---

## 3. Admin Authentication (`/api/admin`)

### Register
- **URL:** `/api/admin/register`
- **Method:** `POST`
- **Body:**
```json
{
    "name": "Admin User",
    "email": "admin@example.com",
    "password": "password123",
    "password_confirmation": "password123",
    "role": "admin",
    "phone": "+1234567890"
}
```
**Note:** Role can be: `admin`, `super_admin`, `manager`

### Login
- **URL:** `/api/admin/login`
- **Method:** `POST`
- **Body:**
```json
{
    "email": "admin@example.com",
    "password": "password123"
}
```

### Profile
- **URL:** `/api/admin/profile`
- **Method:** `GET`
- **Headers:** `Authorization: Bearer {token}`

### Logout
- **URL:** `/api/admin/logout`
- **Method:** `POST`
- **Headers:** `Authorization: Bearer {token}`

### Refresh Token
- **URL:** `/api/admin/refresh`
- **Method:** `POST`
- **Headers:** `Authorization: Bearer {token}`

### Protected Routes
- **Dashboard:** `/api/admin/dashboard` (GET)
- **Users Management:** `/api/admin/users` (GET)
- **Settings:** `/api/admin/settings` (GET)

---

## 4. Delivery Authentication (`/api/delivery`)

### Register
- **URL:** `/api/delivery/register`
- **Method:** `POST`
- **Body:**
```json
{
    "name": "Delivery Person",
    "email": "delivery@example.com",
    "password": "password123",
    "password_confirmation": "password123",
    "phone": "+1234567890",
    "vehicle_type": "bike",
    "vehicle_number": "ABC-123",
    "license_number": "DL-123456",
    "address": "123 Delivery Street"
}
```
**Note:** Vehicle type can be: `bike`, `car`, `van`, `truck`

### Login
- **URL:** `/api/delivery/login`
- **Method:** `POST`
- **Body:**
```json
{
    "email": "delivery@example.com",
    "password": "password123"
}
```

### Profile
- **URL:** `/api/delivery/profile`
- **Method:** `GET`
- **Headers:** `Authorization: Bearer {token}`

### Logout
- **URL:** `/api/delivery/logout`
- **Method:** `POST`
- **Headers:** `Authorization: Bearer {token}`

### Refresh Token
- **URL:** `/api/delivery/refresh`
- **Method:** `POST`
- **Headers:** `Authorization: Bearer {token}`

### Protected Routes
- **Dashboard:** `/api/delivery/dashboard` (GET)
- **Orders:** `/api/delivery/orders` (GET)
- **Update Availability:** `/api/delivery/update-availability` (POST)

---

## Response Format

### Success Response
```json
{
    "success": true,
    "message_en": "Operation successful",
    "message_ar": "العملية ناجحة",
    "data": { ... }
}
```

### Authentication Response
```json
{
    "success": true,
    "access_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...",
    "token_type": "bearer",
    "expires_in": 3600,
    "guard": "api",
    "message_en": "Login successful",
    "message_ar": "تم تسجيل الدخول بنجاح",
    "user": {
        "id": 1,
        "name": "John Doe",
        "email": "user@example.com",
        ...
    }
}
```

### Error Response
```json
{
    "success": false,
    "message_en": "Error message in English",
    "message_ar": "رسالة الخطأ بالعربية"
}
```

---

## HTTP Status Codes

- `200` - OK: Request successful
- `201` - Created: Resource created successfully
- `400` - Bad Request: Invalid request data
- `401` - Unauthorized: Authentication required or failed
- `403` - Forbidden: Access denied (e.g., disabled account)
- `404` - Not Found: Resource not found
- `422` - Unprocessable Entity: Validation errors
- `500` - Internal Server Error: Server error

---

## Security Notes

1. **JWT Secret**: Always use a strong, unique JWT secret in production
2. **HTTPS**: Always use HTTPS in production to protect tokens in transit
3. **Token Storage**: Store tokens securely on the client side (avoid localStorage for sensitive apps)
4. **Token Expiry**: Tokens expire after 60 minutes by default (configurable via JWT_TTL)
5. **Refresh Tokens**: Use refresh endpoint to get new tokens without re-authentication

---

## Testing with Postman

1. **Import Collection**: Create a new collection for each guard
2. **Set Base URL**: Use environment variable for base URL
3. **Authentication**: Save token from login response as environment variable
4. **Headers**: Add `Authorization: Bearer {{token}}` to protected endpoints

---

## Troubleshooting

### Common Issues

1. **"Token not provided"**
   - Ensure Authorization header is included
   - Format: `Authorization: Bearer {token}`

2. **"Token has expired"**
   - Use refresh endpoint to get new token
   - Or login again

3. **"Invalid credentials"**
   - Check email and password
   - Ensure user account exists and is active

4. **"Account is disabled"**
   - User status is set to false
   - Contact admin to enable account

---

## Database Schema

### Users Table
- id, name, email, password, email_verified_at, remember_token, created_at, updated_at

### Store Users Table
- id, name, email, password, store_name, phone, address, status, email_verified_at, remember_token, created_at, updated_at

### Admins Table
- id, name, email, password, role, phone, status, email_verified_at, remember_token, created_at, updated_at

### Deliveries Table
- id, name, email, password, phone, vehicle_type, vehicle_number, license_number, address, status, availability, email_verified_at, remember_token, created_at, updated_at
