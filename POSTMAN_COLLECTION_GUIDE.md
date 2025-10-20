# Postman Collection Guide - Stores by Category with Location

## Overview
This guide explains how to import and use the Postman collection for testing the "Stores by Category with Location" API endpoint.

---

## Import Collection

### **Method 1: Import File**
1. Open Postman
2. Click **Import** button (top left)
3. Select **File** tab
4. Browse and select: `Stores_By_Category_Location.postman_collection.json`
5. Click **Import**

### **Method 2: Drag and Drop**
1. Open Postman
2. Drag the `Stores_By_Category_Location.postman_collection.json` file
3. Drop it into Postman window
4. Collection will be imported automatically

---

## Configure Variables

Before running tests, configure these collection variables:

### **Required Variables**
1. Click on the collection name
2. Go to **Variables** tab
3. Update the following:

| Variable | Default Value | Description | Action Required |
|----------|---------------|-------------|-----------------|
| `base_url` | `http://localhost/api` | API base URL | ✅ Update if different |
| `main_category_id` | `1` | Main category ID to test | ✅ Update with valid ID |
| `test_latitude` | `30.0444` | Test latitude (Cairo) | ✅ Optional |
| `test_longitude` | `31.2357` | Test longitude (Cairo) | ✅ Optional |
| `user_token` | `` | User JWT token | ⚠️ Required for authenticated tests |

### **How to Get User Token**
1. Use the user login endpoint from main Postman collection
2. Copy the token from response
3. Paste into `user_token` variable
4. Click **Save**

---

## Collection Structure

The collection contains **12 test scenarios**:

### **✅ Success Scenarios**
1. **Guest User - Basic Request** - Guest with coordinates
2. **Authenticated User - Auto Location** - User with default address
3. **With Search Filter** - Search stores by name
4. **With Radius Filter** - Filter by distance (5km)
5. **With Pagination** - Custom page size (5 per page)
6. **Combined Filters** - Search + radius + pagination
7. **Arabic Language Response** - Test Arabic localization
8. **Different Location - Cairo** - Cairo coordinates
9. **Different Location - Alexandria** - Alexandria coordinates

### **❌ Error Scenarios**
10. **Error - Invalid Category ID** - 404 error test
11. **Error - Missing Location** - 400 error test
12. **Error - Invalid Coordinates** - 422 validation error test

---

## Running Tests

### **Run Single Request**
1. Select a request from the collection
2. Click **Send** button
3. View response in the bottom panel
4. Check **Test Results** tab

### **Run All Tests (Collection Runner)**
1. Right-click on collection name
2. Select **Run collection**
3. Select all requests (or specific ones)
4. Click **Run [Collection Name]**
5. View test results summary

### **Run with Different Data**
1. Click on a request
2. Modify query parameters in **Params** tab
3. Click **Send**
4. Tests will run automatically

---

## Understanding Test Results

### **Green Checkmarks** ✅
- Test passed successfully
- Expected behavior confirmed

### **Red X Marks** ❌
- Test failed
- Check response or adjust test expectations

### **Common Test Validations**

#### **Status Code Tests**
```javascript
pm.test("Status code is 200", function () {
    pm.response.to.have.status(200);
});
```

#### **Response Structure Tests**
```javascript
pm.test("Response has stores array", function () {
    var jsonData = pm.response.json();
    pm.expect(jsonData.data.stores).to.be.an('array');
});
```

#### **Distance Sorting Tests**
```javascript
pm.test("Stores are sorted by distance", function () {
    var jsonData = pm.response.json();
    var stores = jsonData.data.stores;
    for (let i = 0; i < stores.length - 1; i++) {
        pm.expect(stores[i].distance_km).to.be.at.most(stores[i + 1].distance_km);
    }
});
```

---

## Test Scenarios Explained

### **1. Guest User - Basic Request**
**Purpose**: Test basic functionality with guest user

**Request**:
```
GET /user/stores/by-category/1?latitude=30.0444&longitude=31.2357
```

**Tests**:
- ✅ Status code is 200
- ✅ Response has success flag
- ✅ Response has stores array
- ✅ Response has pagination
- ✅ Location source is "provided"
- ✅ Stores are sorted by distance
- ✅ Each store has required fields

---

### **2. Authenticated User - Auto Location**
**Purpose**: Test authenticated user using default address

**Request**:
```
GET /user/stores/by-category/1
Authorization: Bearer {token}
```

**Tests**:
- ✅ Status code is 200 or 400
- ✅ Location source is "default_address" (if 200)
- ✅ Error message about address (if 400)

**Note**: Requires valid `user_token` in variables

---

### **3. With Search Filter**
**Purpose**: Test store name search

**Request**:
```
GET /user/stores/by-category/1?latitude=30.0444&longitude=31.2357&search=pizza
```

**Tests**:
- ✅ Status code is 200
- ✅ Stores match search term

---

### **4. With Radius Filter**
**Purpose**: Test distance filtering

**Request**:
```
GET /user/stores/by-category/1?latitude=30.0444&longitude=31.2357&radius=5
```

**Tests**:
- ✅ Status code is 200
- ✅ All stores within 5km radius

---

### **5. With Pagination**
**Purpose**: Test pagination functionality

**Request**:
```
GET /user/stores/by-category/1?latitude=30.0444&longitude=31.2357&per_page=5&page=1
```

**Tests**:
- ✅ Status code is 200
- ✅ Pagination matches request (per_page=5)
- ✅ Stores array length ≤ 5

---

### **6. Combined Filters**
**Purpose**: Test multiple filters together

**Request**:
```
GET /user/stores/by-category/1?latitude=30.0444&longitude=31.2357&search=restaurant&radius=10&per_page=10
```

**Tests**:
- ✅ Status code is 200
- ✅ Response structure is correct

---

### **7. Arabic Language Response**
**Purpose**: Test multilingual support

**Request**:
```
GET /user/stores/by-category/1?latitude=30.0444&longitude=31.2357
Accept-Language: ar
```

**Tests**:
- ✅ Status code is 200
- ✅ Response has Arabic content

---

### **8. Error - Invalid Category ID**
**Purpose**: Test 404 error handling

**Request**:
```
GET /user/stores/by-category/99999?latitude=30.0444&longitude=31.2357
```

**Tests**:
- ✅ Status code is 404
- ✅ Response has error message

---

### **9. Error - Missing Location**
**Purpose**: Test 400 error for missing coordinates

**Request**:
```
GET /user/stores/by-category/1
```

**Tests**:
- ✅ Status code is 400
- ✅ Error message mentions "location"

---

### **10. Error - Invalid Coordinates**
**Purpose**: Test validation error (422)

**Request**:
```
GET /user/stores/by-category/1?latitude=200&longitude=500
```

**Tests**:
- ✅ Status code is 422
- ✅ Response has validation errors

---

## Customizing Tests

### **Modify Test Coordinates**
Edit the request URL parameters:
```
?latitude=YOUR_LAT&longitude=YOUR_LNG
```

### **Change Search Term**
Edit the pre-request script:
```javascript
pm.variables.set('search_term', 'YOUR_SEARCH_TERM');
```

### **Adjust Radius**
Edit the query parameter:
```
&radius=YOUR_RADIUS_IN_KM
```

### **Change Pagination**
Edit query parameters:
```
&per_page=10&page=2
```

---

## Global Test Scripts

### **Response Time Test**
Runs on every request:
```javascript
pm.test("Response time is less than 2000ms", function () {
    pm.expect(pm.response.responseTime).to.be.below(2000);
});
```

### **Content Type Test**
Runs on every request:
```javascript
pm.test("Response has correct content type", function () {
    pm.expect(pm.response.headers.get('Content-Type')).to.include('application/json');
});
```

---

## Troubleshooting

### **Problem: All tests fail**
**Solution**: 
- Check if API server is running
- Verify `base_url` variable is correct
- Ensure database has test data

### **Problem: "Invalid Category ID" even with valid ID**
**Solution**: 
- Update `main_category_id` variable
- Verify category exists in database
- Check category status is active

### **Problem: "Location required" error**
**Solution**: 
- Add latitude/longitude to query params
- Or provide valid `user_token` for authenticated request
- Ensure user has default address with coordinates

### **Problem: "No stores found"**
**Solution**: 
- Check if stores exist for this category
- Verify stores are approved
- Ensure stores have active branches
- Verify branches have coordinates

### **Problem: Authenticated tests fail**
**Solution**: 
- Get fresh JWT token from login endpoint
- Update `user_token` collection variable
- Ensure token hasn't expired

---

## Sample Test Data

### **Recommended Test Setup**

#### **Main Categories**
```sql
INSERT INTO main_categories (id, name_en, name_ar, status, sort_order) VALUES
(1, 'Restaurants', 'مطاعم', 1, 1),
(2, 'Cafes', 'مقاهي', 1, 2),
(3, 'Supermarkets', 'سوبر ماركت', 1, 3);
```

#### **Stores**
```sql
INSERT INTO stores (id, name_en, name_ar, description_en, description_ar, status) VALUES
(1, 'Pizza Palace', 'قصر البيتزا', 'Best pizza in town', 'أفضل بيتزا', 'approved'),
(2, 'Burger House', 'بيت البرجر', 'Tasty burgers', 'برجر لذيذ', 'approved');
```

#### **Category-Store Link**
```sql
INSERT INTO main_category_store (main_category_id, store_id) VALUES
(1, 1),
(1, 2);
```

#### **Branches**
```sql
INSERT INTO branches (store_id, name_en, name_ar, address, latitude, longitude, phone, is_main, is_active) VALUES
(1, 'Downtown', 'وسط البلد', '123 Main St', 30.0500, 31.2400, '+20123456789', 1, 1),
(2, 'Nasr City', 'مدينة نصر', '456 Nasr St', 30.0600, 31.3200, '+20123456788', 1, 1);
```

---

## Export Results

### **Export Test Results**
1. Run collection in Collection Runner
2. Click **Export Results** button
3. Save as JSON file
4. Share with team or attach to bug reports

### **Generate Newman Report**
For CI/CD integration:
```bash
newman run Stores_By_Category_Location.postman_collection.json \
  --environment your-environment.json \
  --reporters cli,json,html
```

---

## Tips for Success

### **Best Practices**
1. ✅ Always update variables before running
2. ✅ Start with basic requests, then move to complex
3. ✅ Check response body before running tests
4. ✅ Run collection regularly to catch regressions
5. ✅ Keep test data consistent

### **Common Coordinates for Egypt**
- **Cairo**: lat=30.0444, lng=31.2357
- **Alexandria**: lat=31.2001, lng=29.9187
- **Giza**: lat=30.0131, lng=31.2089
- **Sharm El Sheikh**: lat=27.9158, lng=34.3300
- **Hurghada**: lat=27.2579, lng=33.8116

---

## Integration with CI/CD

### **Using Newman (Postman CLI)**

#### **Install Newman**
```bash
npm install -g newman
```

#### **Run Collection**
```bash
newman run Stores_By_Category_Location.postman_collection.json \
  --env-var "base_url=http://your-api-url.com/api" \
  --env-var "main_category_id=1" \
  --env-var "test_latitude=30.0444" \
  --env-var "test_longitude=31.2357"
```

#### **Generate HTML Report**
```bash
newman run Stores_By_Category_Location.postman_collection.json \
  --reporters html \
  --reporter-html-export test-report.html
```

---

## Support & Documentation

### **Related Files**
- `STORES_BY_CATEGORY_LOCATION_PLAN.md` - Implementation plan
- `STORES_BY_CATEGORY_API_TESTING.md` - Testing guide
- `STORES_BY_CATEGORY_IMPLEMENTATION_SUMMARY.md` - Summary

### **API Endpoint**
```
GET /api/user/stores/by-category/{main_category_id}
```

### **Controller**
```
app/Http/Controllers/Api/User/StoreController.php
```

---

**Happy Testing! 🚀**
