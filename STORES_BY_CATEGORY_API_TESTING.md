# Stores by Main Category API - Testing Guide

## Endpoint
```
GET /api/user/stores/by-category/{main_category_id}
```

---

## Test Scenarios

### **Scenario 1: Guest User with Coordinates**
**Purpose**: Test endpoint with guest user providing coordinates

**Request**:
```http
GET /api/user/stores/by-category/1?latitude=30.0444&longitude=31.2357
```

**Expected Response** (200 OK):
```json
{
  "success": true,
  "message": "Data retrieved successfully",
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
        "logo": "stores/logo.jpg",
        "logo_url": "http://localhost/storage/stores/logo.jpg",
        "status": "approved",
        "distance_km": 2.5,
        "closest_branch": {
          "id": 1,
          "store_id": 1,
          "name_en": "Downtown Branch",
          "name_ar": "فرع وسط المدينة",
          "name": "Downtown Branch",
          "address": "123 Main St",
          "latitude": "30.0500",
          "longitude": "31.2400",
          "phone": "+201234567890",
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
            "image": "categories/restaurant.jpg",
            "image_url": "http://localhost/storage/categories/restaurant.jpg"
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
      "source": "provided"
    }
  }
}
```

---

### **Scenario 2: Authenticated User (Auto Location)**
**Purpose**: Test with authenticated user using default address

**Request**:
```http
GET /api/user/stores/by-category/1
Authorization: Bearer {user_token}
```

**Expected Response** (200 OK):
```json
{
  "success": true,
  "message": "Data retrieved successfully",
  "data": {
    "stores": [...],
    "pagination": {...},
    "user_location": {
      "latitude": 30.0444,
      "longitude": 31.2357,
      "source": "default_address"
    }
  }
}
```

---

### **Scenario 3: Search Filter**
**Purpose**: Test search by store name (English or Arabic)

**Request**:
```http
GET /api/user/stores/by-category/1?latitude=30.0444&longitude=31.2357&search=pizza
```

**Expected Behavior**:
- Returns only stores matching "pizza" in name_en or name_ar
- Maintains distance-based sorting

---

### **Scenario 4: Radius Filter**
**Purpose**: Test filtering stores within specific radius

**Request**:
```http
GET /api/user/stores/by-category/1?latitude=30.0444&longitude=31.2357&radius=5
```

**Expected Behavior**:
- Returns only stores within 5km radius
- Stores beyond 5km are excluded

---

### **Scenario 5: Pagination**
**Purpose**: Test pagination with custom per_page

**Request**:
```http
GET /api/user/stores/by-category/1?latitude=30.0444&longitude=31.2357&per_page=5&page=2
```

**Expected Response**:
```json
{
  "data": {
    "stores": [...], // 5 stores
    "pagination": {
      "current_page": 2,
      "per_page": 5,
      "total": 25,
      "last_page": 5,
      "from": 6,
      "to": 10
    }
  }
}
```

---

### **Scenario 6: Invalid Main Category**
**Purpose**: Test with non-existent category ID

**Request**:
```http
GET /api/user/stores/by-category/9999?latitude=30.0444&longitude=31.2357
```

**Expected Response** (404 Not Found):
```json
{
  "success": false,
  "message": "Resource not found"
}
```

---

### **Scenario 7: Missing Location Data**
**Purpose**: Test guest user without coordinates

**Request**:
```http
GET /api/user/stores/by-category/1
```

**Expected Response** (400 Bad Request):
```json
{
  "success": false,
  "message": "Location is required. Please provide latitude and longitude, or login to use your default address"
}
```

---

### **Scenario 8: Invalid Coordinates**
**Purpose**: Test with invalid latitude/longitude

**Request**:
```http
GET /api/user/stores/by-category/1?latitude=200&longitude=500
```

**Expected Response** (422 Unprocessable Entity):
```json
{
  "success": false,
  "message": "Validation failed. Please check your input",
  "errors": {
    "latitude": ["The latitude must be between -90 and 90."],
    "longitude": ["The longitude must be between -180 and 180."]
  }
}
```

---

### **Scenario 9: User Without Default Address**
**Purpose**: Test authenticated user with no default address

**Request**:
```http
GET /api/user/stores/by-category/1
Authorization: Bearer {user_token_without_default_address}
```

**Expected Response** (400 Bad Request):
```json
{
  "success": false,
  "message": "No default address found. Please provide latitude and longitude, or set a default address"
}
```

---

### **Scenario 10: No Stores Found**
**Purpose**: Test when no stores match criteria

**Request**:
```http
GET /api/user/stores/by-category/1?latitude=30.0444&longitude=31.2357&search=nonexistent
```

**Expected Response** (200 OK):
```json
{
  "success": true,
  "message": "Data retrieved successfully",
  "data": {
    "stores": [],
    "pagination": {
      "current_page": 1,
      "per_page": 15,
      "total": 0,
      "last_page": 0,
      "from": 0,
      "to": 0
    },
    "user_location": {
      "latitude": 30.0444,
      "longitude": 31.2357,
      "source": "provided"
    }
  }
}
```

---

## Postman Collection Examples

### **Test 1: Basic Request**
```javascript
// Pre-request Script
pm.environment.set("main_category_id", 1);
pm.environment.set("latitude", 30.0444);
pm.environment.set("longitude", 31.2357);

// Test
pm.test("Status code is 200", function () {
    pm.response.to.have.status(200);
});

pm.test("Response has stores array", function () {
    var jsonData = pm.response.json();
    pm.expect(jsonData.data).to.have.property('stores');
    pm.expect(jsonData.data.stores).to.be.an('array');
});

pm.test("Stores are sorted by distance", function () {
    var jsonData = pm.response.json();
    var stores = jsonData.data.stores;
    for (let i = 0; i < stores.length - 1; i++) {
        pm.expect(stores[i].distance_km).to.be.at.most(stores[i + 1].distance_km);
    }
});
```

---

## cURL Examples

### **Guest User Request**
```bash
curl -X GET "http://localhost/api/user/stores/by-category/1?latitude=30.0444&longitude=31.2357" \
  -H "Accept: application/json" \
  -H "Accept-Language: en"
```

### **Authenticated User Request**
```bash
curl -X GET "http://localhost/api/user/stores/by-category/1" \
  -H "Accept: application/json" \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "Accept-Language: en"
```

### **With Search and Radius**
```bash
curl -X GET "http://localhost/api/user/stores/by-category/1?latitude=30.0444&longitude=31.2357&search=pizza&radius=10&per_page=5" \
  -H "Accept: application/json" \
  -H "Accept-Language: en"
```

### **Arabic Response**
```bash
curl -X GET "http://localhost/api/user/stores/by-category/1?latitude=30.0444&longitude=31.2357" \
  -H "Accept: application/json" \
  -H "Accept-Language: ar"
```

---

## Important Notes

### **Distance Calculation**
- Uses Haversine formula (implemented in `Branch::getDistanceFrom()`)
- Returns distance in kilometers
- Earth radius: 6371 km
- Accuracy: ±0.5% for typical distances

### **Performance Considerations**
- Distance calculation happens in PHP (not MySQL)
- For large datasets (10,000+ stores), consider:
  - Adding Redis caching for popular queries
  - Using MySQL spatial indexes (ST_Distance)
  - Implementing Elasticsearch for better performance

### **Location Data**
- Latitude range: -90 to 90
- Longitude range: -180 to 180
- Default address must have coordinates
- Branches without coordinates are excluded

### **Filtering Logic**
1. Filter stores by main category
2. Filter only approved stores
3. Load active branches with coordinates
4. Calculate distance to each branch
5. Select closest branch per store
6. Apply optional radius filter
7. Apply optional search filter
8. Sort by distance (ascending)
9. Paginate results

### **Multilingual Support**
- Use `Accept-Language: en` header for English
- Use `Accept-Language: ar` header for Arabic
- All store/branch names return localized versions
- Errors and messages are localized

---

## Testing Checklist

- [ ] Guest user with valid coordinates
- [ ] Guest user without coordinates (error)
- [ ] Authenticated user with default address
- [ ] Authenticated user without default address (error)
- [ ] Invalid main category ID (404)
- [ ] Invalid coordinates (validation error)
- [ ] Search filter works correctly
- [ ] Radius filter works correctly
- [ ] Pagination works correctly
- [ ] Stores are sorted by distance
- [ ] Only approved stores are returned
- [ ] Only active branches are included
- [ ] Branches without coordinates are excluded
- [ ] Arabic language response
- [ ] English language response
- [ ] Empty result handling
- [ ] Distance calculation is accurate

---

## Sample Test Data Setup

### **Required Data**
1. **Main Category**: ID=1, name="Restaurants", status=active
2. **Store**: ID=1, approved, linked to main category
3. **Branch**: ID=1, store_id=1, is_active=true, has coordinates
4. **User**: With JWT token
5. **User Address**: is_default=true, has coordinates

### **SQL Sample Data**
```sql
-- Main Category
INSERT INTO main_categories (id, name_en, name_ar, status, sort_order) 
VALUES (1, 'Restaurants', 'مطاعم', 1, 1);

-- Store
INSERT INTO stores (id, name_en, name_ar, description_en, description_ar, status, approved_at) 
VALUES (1, 'Pizza Palace', 'قصر البيتزا', 'Best pizza in town', 'أفضل بيتزا في المدينة', 'approved', NOW());

-- Link Store to Category
INSERT INTO main_category_store (main_category_id, store_id) 
VALUES (1, 1);

-- Branch
INSERT INTO branches (id, store_id, name_en, name_ar, address, latitude, longitude, phone, is_main, is_active, opening_time, closing_time)
VALUES (1, 1, 'Downtown Branch', 'فرع وسط المدينة', '123 Main St', 30.0500, 31.2400, '+201234567890', 1, 1, '09:00', '22:00');

-- User
INSERT INTO users (id, name, email, password) 
VALUES (1, 'Test User', 'test@example.com', '$2y$10$...');

-- User Address
INSERT INTO user_addresses (user_id, address_type, building_name, street_name, phone, latitude, longitude, is_default)
VALUES (1, 'villa', 'Building A', 'Main Street', '+201234567890', 30.0444, 31.2357, 1);
```

---

## Success Criteria

✅ All test scenarios pass
✅ Distance calculation is accurate
✅ Stores are properly sorted by distance
✅ Only approved stores with active branches are shown
✅ Pagination works correctly
✅ Search and radius filters work
✅ Both guest and authenticated flows work
✅ Proper error handling for all edge cases
✅ Multilingual support works (EN/AR)
✅ Response format matches specification
