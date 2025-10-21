# Stores by Main Category with Location-Based Sorting - Implementation Plan

## Overview
Create an endpoint for users to get stores filtered by main category ID, ordered by proximity to their location (closest stores first).

---

## Endpoint Specification

### **Route**
```
GET /api/user/stores/by-category/{main_category_id}
```

### **Authentication**
- Optional (works for both guests and authenticated users)
- Guests must provide latitude/longitude
- Authenticated users can use their default address automatically

### **Request Parameters**

#### Path Parameters
- `main_category_id` (required) - The ID of the main category to filter stores

#### Query Parameters
- `latitude` (optional) - User's latitude coordinate (required if not authenticated or no default address)
- `longitude` (optional) - User's longitude coordinate (required if not authenticated or no default address)
- `search` (optional) - Search stores by name (supports both English and Arabic)
- `per_page` (optional) - Number of results per page (default: 15)
- `radius` (optional) - Maximum distance in kilometers (optional filter)

### **Response Format**
```json
{
  "success": true,
  "message": "Stores retrieved successfully",
  "data": {
    "stores": [
      {
        "id": 1,
        "name_en": "Pizza Palace",
        "name_ar": "قصر البيتزا",
        "name": "Pizza Palace",
        "description_en": "Best pizza in town",
        "description_ar": "أفضل بيتزا في المدينة",
        "description": "Best pizza in town",
        "logo_url": "https://...",
        "status": "approved",
        "closest_branch": {
          "id": 5,
          "name_en": "Downtown Branch",
          "name_ar": "فرع وسط المدينة",
          "name": "Downtown Branch",
          "address": "123 Main St",
          "phone": "+1234567890",
          "latitude": 30.0444,
          "longitude": 31.2357,
          "is_main": true,
          "is_active": true,
          "opening_time": "09:00:00",
          "closing_time": "22:00:00",
          "distance_km": 2.5
        },
        "main_categories": [
          {
            "id": 1,
            "name_en": "Restaurants",
            "name_ar": "مطاعم",
            "name": "Restaurants"
          }
        ]
      }
    ],
    "pagination": {
      "current_page": 1,
      "per_page": 15,
      "total": 25,
      "last_page": 2,
      "from": 1,
      "to": 15
    },
    "user_location": {
      "latitude": 30.0444,
      "longitude": 31.2357,
      "source": "provided" // or "default_address"
    }
  }
}
```

---

## Implementation Steps

### **Step 1: Create Store Controller for Users**
**File**: `app/Http/Controllers/Api/User/StoreController.php`

**Key Methods**:
- `getStoresByMainCategory($mainCategoryId, Request $request)`

**Responsibilities**:
- Validate main_category_id exists
- Get user location (from params or default address)
- Query stores by main category
- Calculate distances for each store's branches
- Sort stores by closest branch distance
- Return paginated results

---

### **Step 2: Implement Distance Calculation Logic**

**Approach**:
1. Query stores that belong to the main category
2. Load active branches for each store with coordinates
3. For each branch, calculate distance using existing `getDistanceFrom()` method in Branch model
4. Select the closest branch for each store
5. Sort stores by closest branch distance (ascending)
6. Apply optional radius filter if provided

**Distance Calculation**:
- Use existing `Branch::getDistanceFrom($lat, $lng)` method (Haversine formula)
- Returns distance in kilometers

---

### **Step 3: Add Validation**

**Validation Rules**:
```php
'latitude' => 'required_without:auth|nullable|numeric|between:-90,90',
'longitude' => 'required_without:auth|nullable|numeric|between:-180,180',
'search' => 'nullable|string|max:255',
'per_page' => 'nullable|integer|min:1|max:100',
'radius' => 'nullable|numeric|min:0'
```

**Location Source Priority**:
1. If latitude/longitude provided in request → use them
2. Else if user is authenticated → use default address coordinates
3. Else → return validation error

---

### **Step 4: Database Queries**

**Query Structure**:
```php
Store::whereHas('mainCategories', function ($query) use ($mainCategoryId) {
    $query->where('main_categories.id', $mainCategoryId);
})
->with(['mainCategories', 'activeBranches' => function ($query) {
    $query->whereNotNull('latitude')
          ->whereNotNull('longitude');
}])
->approved() // Only approved stores
->get();
```

**Distance Calculation**:
- Loop through each store
- For each store, calculate distance to all active branches
- Select the minimum distance branch
- Add distance to store data
- Sort collection by distance

---

### **Step 5: Response Formatting**

**Include**:
- Store details (id, names, descriptions, logo_url, status)
- Closest branch with distance_km field
- Main categories relationship
- User location and source (provided/default_address)
- Pagination metadata

**Exclude**:
- Stores with no active branches
- Stores with branches that don't have coordinates
- Rejected/pending/suspended stores

---

### **Step 6: Add Route**

**File**: `routes/api/user.php`

**Route Definition**:
```php
Route::prefix('user')->group(function () {
    // ... existing routes
    
    // Store routes (public + authenticated)
    Route::controller(\App\Http\Controllers\Api\User\StoreController::class)->prefix('stores')->group(function () {
        Route::get('/by-category/{mainCategoryId}', 'getStoresByMainCategory')->name('user.stores.byCategory');
    });
});
```

---

### **Step 7: Error Handling**

**Error Scenarios**:
1. **Main category not found**: Return 404 with message
2. **No location provided**: Return 400 validation error
3. **Invalid coordinates**: Return 422 validation error
4. **User has no default address**: Return 400 with helpful message
5. **No stores found**: Return 200 with empty array

---

## Additional Features (Optional Enhancements)

### **Feature 1: Currently Open Stores Filter**
Add query parameter: `only_open=true`
- Filter stores based on branch opening/closing times
- Use `Branch::isCurrentlyOpen()` method

### **Feature 2: Sort Options**
Add query parameter: `sort_by=distance|name`
- Default: distance (ascending)
- Allow sorting by name alphabetically

### **Feature 3: Include All Branches**
Add query parameter: `include_all_branches=true`
- Return all branches with distances instead of just closest
- Useful for showing multiple locations on map

---

## Testing Scenarios

### **Test Case 1: Guest User with Coordinates**
```
GET /api/user/stores/by-category/1?latitude=30.0444&longitude=31.2357
Expected: List of stores ordered by distance
```

### **Test Case 2: Authenticated User (Auto Location)**
```
GET /api/user/stores/by-category/1
Authorization: Bearer {token}
Expected: List of stores using user's default address
```

### **Test Case 3: Search Filter**
```
GET /api/user/stores/by-category/1?latitude=30.0444&longitude=31.2357&search=pizza
Expected: Filtered stores matching "pizza"
```

### **Test Case 4: Radius Filter**
```
GET /api/user/stores/by-category/1?latitude=30.0444&longitude=31.2357&radius=5
Expected: Only stores within 5km
```

### **Test Case 5: Invalid Main Category**
```
GET /api/user/stores/by-category/999?latitude=30.0444&longitude=31.2357
Expected: 404 error
```

### **Test Case 6: Missing Location Data**
```
GET /api/user/stores/by-category/1
Expected: 400 validation error (if not authenticated or no default address)
```

---

## Files to Create/Modify

### **New Files**
1. `app/Http/Controllers/Api/User/StoreController.php` - Main controller

### **Modified Files**
1. `routes/api/user.php` - Add new route
2. `app/Models/MainCategory.php` - Add stores() relationship if not exists

---

## Database Requirements

### **Required Relationships**
- `Store` → `mainCategories()` (belongsToMany via main_category_store pivot)
- `Store` → `branches()` (hasMany)
- `Store` → `activeBranches()` (hasMany with is_active = true)
- `Branch` → `store()` (belongsTo)

### **Required Fields**
- `stores.status` - Must be 'approved'
- `branches.latitude` - Required for distance calculation
- `branches.longitude` - Required for distance calculation
- `branches.is_active` - Must be true
- `user_addresses.latitude` - For authenticated users
- `user_addresses.longitude` - For authenticated users

---

## Success Criteria

✅ Endpoint returns stores filtered by main category
✅ Stores are ordered by distance (closest first)
✅ Distance is calculated accurately using Haversine formula
✅ Works for both guests (with coordinates) and authenticated users
✅ Only approved stores with active branches are shown
✅ Pagination works correctly
✅ Search filter works for store names
✅ Optional radius filter works
✅ Proper error handling for all edge cases
✅ Response format matches specification

---

## Notes

- The existing `Branch::getDistanceFrom()` method already implements the Haversine formula
- UserAddress model has latitude/longitude fields for authenticated users
- Store model has `approved()` scope for filtering
- Branch model has `active()` scope for filtering
- All models support bilingual names (en/ar)

---

**Status**: Ready for implementation
**Estimated Time**: 1-2 hours
**Priority**: Medium
**Dependencies**: None (all required models and relationships exist)
