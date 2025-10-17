# File Upload Fix Plan - Complete Project

## Problem Overview
**Issue**: PUT/PATCH requests don't support file uploads with multipart/form-data in PHP/Laravel.
**Root Cause**: PHP doesn't parse `$_FILES` for PUT/PATCH requests, only for POST.
**Solution**: Add POST route alternatives for all update endpoints that accept file uploads.

---

## Controllers Requiring Fixes

### 1. **Admin Controllers** (4 controllers)

#### A. Admin\StoreController ✅ Priority: HIGH
**File**: `app/Http/Controllers/Api/Admin/StoreController.php`
**Methods**: 
- `update()` - Updates store details with logo/cover
**Routes**: 
- `PUT /api/admin/stores/{id}` → Add `POST /api/admin/stores/{id}`
**Files**:
- logo
- cover_image

---

#### B. Admin\MainCategoryController ✅ Priority: HIGH
**File**: `app/Http/Controllers/Api/Admin/MainCategoryController.php`
**Methods**: 
- `update()` - Updates main category with image
**Routes**: 
- `PUT /api/admin/main-categories/{id}` → Add `POST /api/admin/main-categories/{id}`
**Files**:
- image

---

#### C. Admin\CategoryController ✅ Priority: HIGH
**File**: `app/Http/Controllers/Api/Admin/CategoryController.php`
**Methods**: 
- `update()` - Updates store category with image
**Routes**: 
- `PUT /api/admin/categories/{id}` → Add `POST /api/admin/categories/{id}`
**Files**:
- image

---

#### D. Admin\ProductController ✅ Priority: HIGH
**File**: `app/Http/Controllers/Api/Admin/ProductController.php`
**Methods**: 
- `update()` - Updates product details
- `uploadImages()` - Already POST, no fix needed
**Routes**: 
- `PUT /api/admin/products/{id}` → Add `POST /api/admin/products/{id}`
**Files**:
- image (thumbnail)

---

### 2. **Vendor Controllers** (4 controllers)

#### A. Vendor\StoreController ✅ Priority: HIGH
**File**: `app/Http/Controllers/Api/Vendor/StoreController.php`
**Methods**: 
- `update()` - Updates store with logo/cover
**Routes**: 
- `PUT /api/vendor/store` → Add `POST /api/vendor/store`
**Files**:
- logo
- cover_image

---

#### B. Vendor\CategoryController ❌ TO DO
**File**: `app/Http/Controllers/Api/Vendor/CategoryController.php`
**Status**: ❌ Needs to be fixed (changes were rejected)
**Routes**: 
- `PUT /api/vendor/categories/{id}` → Add `POST /api/vendor/categories/{id}`
**Files**:
- image

---

#### C. Vendor\ProductController ✅ Priority: HIGH
**File**: `app/Http/Controllers/Api/Vendor/ProductController.php`
**Methods**: 
- `update()` - Updates product with image
**Routes**: 
- `PUT /api/vendor/products/{id}` → Add `POST /api/vendor/products/{id}`
**Files**:
- image

---

#### D. Vendor\AuthController ✅ Priority: MEDIUM
**File**: `app/Http/Controllers/Api/Vendor/AuthController.php`
**Methods**: 
- `updateProfile()` - Updates vendor profile with avatar
**Routes**: 
- `PUT /api/vendor/auth/profile` → Add `POST /api/vendor/auth/profile`
**Files**:
- avatar

---

### 3. **User Controllers** (1 controller)

#### User\AddressController ❌ No files
**File**: `app/Http/Controllers/Api/User/AddressController.php`
**Status**: ✅ No file uploads - no fix needed

---

## Implementation Steps

### Phase 1: Admin Controllers (Priority: HIGH)
1. ✅ Fix Admin\StoreController
2. ✅ Fix Admin\MainCategoryController
3. ✅ Fix Admin\CategoryController
4. ✅ Fix Admin\ProductController

### Phase 2: Vendor Controllers (Priority: HIGH)
5. ✅ Fix Vendor\StoreController
6. ✅ Fix Vendor\CategoryController
7. ✅ Fix Vendor\ProductController
8. ✅ Fix Vendor\AuthController

### Phase 3: Update Postman Collection (Priority: HIGH)
9. ✅ Update Postman collection endpoints to POST for file uploads

### Phase 4: Documentation (Priority: MEDIUM)
10. ✅ Create file upload standards documentation
11. ✅ Update API documentation

---

## Standard Fix Pattern

### 1. Routes (Add POST alternative)
```php
// Before
Route::put('/{id}', 'update');

// After
Route::put('/{id}', 'update');
Route::post('/{id}', 'update'); // POST alternative for file uploads
```

### 2. Controller (Validate file)
```php
// Before
if ($request->hasFile('image')) {
    $path = $request->file('image')->store('folder', 'public');
}

// After
if ($request->hasFile('image') && $request->file('image')->isValid()) {
    $path = $request->file('image')->store('folder', 'public');
}
```

### 3. Postman Usage
```
Method: POST (instead of PUT)
URL: {{base_url}}/path/{id}
Body: form-data
├── image (File)
├── name (Text)
└── other fields (Text)
```

---

## Files to Modify

### Route Files:
1. ✅ `routes/api/admin.php` - Add POST alternatives for admin routes
2. ✅ `routes/api/store.php` - Add POST alternatives for vendor routes

### Controller Files:
1. ✅ `app/Http/Controllers/Api/Admin/StoreController.php`
2. ✅ `app/Http/Controllers/Api/Admin/MainCategoryController.php`
3. ✅ `app/Http/Controllers/Api/Admin/CategoryController.php`
4. ✅ `app/Http/Controllers/Api/Admin/ProductController.php`
5. ✅ `app/Http/Controllers/Api/Vendor/StoreController.php`
6. ✅ `app/Http/Controllers/Api/Vendor/CategoryController.php`
7. ✅ `app/Http/Controllers/Api/Vendor/ProductController.php`
8. ✅ `app/Http/Controllers/Api/Vendor/AuthController.php`

### Postman Collection:
9. ✅ `Makook.postman_collection.json` - Update PUT to POST for file upload endpoints

---

## Testing Checklist

After fixes, test each endpoint:
- [ ] Admin Store Update (logo + cover_image)
- [ ] Admin Main Category Update (image)
- [ ] Admin Category Update (image)
- [ ] Admin Product Update (image)
- [ ] Vendor Store Update (logo + cover_image)
- [ ] Vendor Category Update (image)
- [ ] Vendor Product Update (image)
- [ ] Vendor Profile Update (avatar)

---

## Expected Outcomes

✅ All file uploads work with POST method
✅ PUT method still works for non-file updates
✅ Consistent API behavior across all endpoints
✅ Proper file validation
✅ Old images properly deleted before new upload
✅ Clear documentation for frontend developers

---

## Postman Collection Updates Required

The following PUT endpoints in `Makook.postman_collection.json` need to be changed to POST:

### Admin Endpoints:
1. **Update Main Category** - `/admin/main-categories/{id}` (has image field)
2. **Update Store** - `/admin/stores/{id}` (has logo/cover_image fields)
3. **Update Category** - `/admin/categories/{id}` (has image field)
4. **Update Product** - `/admin/products/{id}` (has image field)

### Vendor Endpoints:
5. **Update Profile** - `/vendor/profile` (has avatar field)
6. **Update Store** - `/vendor/store` (has logo/cover_image fields)
7. **Update Category** - `/vendor/categories/{id}` (has image field)
8. **Update Category with Image** - `/vendor/categories/{id}` (duplicate endpoint with image)
9. **Update Product** - `/vendor/products/{id}` (has image field)

### User Endpoints:
- **Update Address** - `/user/addresses/{id}` (NO file uploads - keep as PUT)

**Total Postman Requests to Update**: 9 endpoints

---

## Timeline Estimate

- **Phase 1 (Admin Controllers)**: 20 minutes
- **Phase 2 (Vendor Controllers)**: 15 minutes
- **Phase 3 (Postman Collection)**: 10 minutes
- **Phase 4 (Documentation)**: 10 minutes
- **Testing**: 15 minutes
- **Total**: ~70 minutes

---

## Notes

- The issue affects ONLY update endpoints with file uploads
- Create/Store endpoints already use POST (no fix needed)
- Delete endpoints don't have files (no fix needed)
- This is a known PHP/Laravel limitation, not a bug
- Solution is standard practice in Laravel community
