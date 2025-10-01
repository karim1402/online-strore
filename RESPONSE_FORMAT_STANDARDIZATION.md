# Response Format Standardization

## Overview
All API responses across the project now follow a **consistent, localized format** using the `LocalizationService` with a single `message` field that adapts based on the `Accept-Language` or `X-Language` header.

## Standard Response Structure

### Success Response
```json
{
  "success": true,
  "message": "Localized success message",
  "data": {
    // Response data or null
  }
}
```

### Error Response
```json
{
  "success": false,
  "message": "Localized error message",
  "data": null,
  "error": "error_code"
}
```

### Validation Error Response
```json
{
  "success": false,
  "message": "Localized first error or validation message",
  "data": null,
  "errors": {
    "field_name": ["Error message 1", "Error message 2"]
  }
}
```

## Language Selection
The API automatically selects the response language based on:
1. **`X-Language` header** (highest priority)
2. **`Accept-Language` header** (medium priority)
3. **Default to English** (fallback)

Supported languages: `en`, `ar`

### Example Usage
```
// English response
X-Language: en
or
Accept-Language: en

// Arabic response
X-Language: ar
or
Accept-Language: ar
```

## Changes Made

### 1. Exception Handlers (`bootstrap/app.php`)
All exception handlers now use `LocalizationService::getMessage()` instead of hardcoded bilingual fields:

**Before:**
```json
{
  "success": false,
  "message_en": "Database error occurred.",
  "message_ar": "حدث خطأ في قاعدة البيانات.",
  "error": "database_error"
}
```

**After:**
```json
{
  "success": false,
  "message": "Database error occurred.", // or Arabic based on header
  "data": null,
  "error": "database_error"
}
```

### 2. Middleware (`CheckRole.php`, `CheckPermission.php`)
Enhanced middleware responses to include `data` and `error` fields for consistency:

```php
return response()->json([
    'success' => false,
    'message' => \App\Services\LocalizationService::getMessage('errors.unauthenticated'),
    'data' => null,
    'error' => 'authentication_required'
], 401);
```

### 3. Route Files
Updated test routes in:
- `routes/api/admin.php`
- `routes/api/store.php`
- `routes/api/delivery.php`

All now use `LocalizationService` for consistent messaging.

## Updated Exception Handling

| Exception Type | Error Code | HTTP Status |
|---------------|------------|-------------|
| AuthenticationException | `authentication_required` | 401 |
| AuthorizationException | `access_denied` | 403 |
| ValidationException | (first error message) | 422 |
| ModelNotFoundException | `resource_not_found` | 404 |
| MethodNotAllowedHttpException | `method_not_allowed` | 405 |
| NotFoundHttpException | `route_not_found` | 404 |
| TokenExpiredException | `token_expired` | 401 |
| TokenInvalidException | `token_invalid` | 401 |
| JWTException | `token_not_provided` | 401 |
| QueryException | `database_error` | 500 |
| ThrottleRequestsException | `too_many_requests` | 429 |
| Throwable (general) | `server_error` | 500 |

## Benefits

1. **Consistency**: All responses follow the same structure across all guards (admin, store, delivery, user)
2. **Localization**: Automatic language selection based on headers
3. **Maintainability**: Single source of truth for messages in language files
4. **Scalability**: Easy to add new languages by creating new message files
5. **Client-Friendly**: Frontend can rely on consistent response structure

## Language Files Location
- English: `resources/lang/en/messages.json`
- Arabic: `resources/lang/ar/messages.json`

## ApiResponse Trait
Controllers using the `ApiResponse` trait automatically get localized responses:

```php
// Success response
return $this->successResponse($data, 'success.operation_successful');

// Error response
return $this->errorResponse('errors.server_error', [], 500);

// Validation error
return $this->validationErrorResponse($validator);
```

## Notes
- All message keys are stored in `resources/lang/{locale}/messages.json`
- The `SetLanguage` middleware (applied globally) handles locale detection
- Database errors now return localized messages instead of exposing raw SQL errors (except in local environment)
