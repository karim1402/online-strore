# 🎯 How to Use Existing Postman Collection for Vendor

## ✅ Option 1: Use Existing Admin Collection (Recommended)

You already have `Product_System_API.postman_collection.json` - just make these 3 simple changes!

### **Step 1: Import Environment**
```
Postman → File → Import
Select: Vendor_System_Environment.postman_environment.json
```

### **Step 2: Select Vendor Environment**
```
Top right dropdown → Select "Vendor Product System - Local"
```

### **Step 3: Change Collection Variables**
```
Collection → Variables tab:
Change base_url: http://localhost:8000/api/vendor
```

### **Step 4: Login as Vendor**
```
1. Authentication → Admin Login
2. Change request name to "Vendor Login"
3. Change credentials:
   email: vendor@example.com
   password: password
4. Change test script:
   pm.environment.set('vendor_token', pm.response.json().access_token);
```

### **Step 5: Modify Product Creation**
```
Products → Create Complete

REMOVE this line:
{"key": "store_id", "value": "{{store_id}}"} ← Delete this!

KEEP everything else the same!
```

That's it! All 47 endpoints now work for vendors! 🎉

---

## ✅ Option 2: Duplicate Collection

### **Step 1: Duplicate Collection**
```
Right-click "Product System API - Complete"
→ Duplicate
→ Rename to "Vendor Product System"
```

### **Step 2: Edit Collection**
```
Click collection → Variables tab:
base_url: http://localhost:8000/api/vendor
```

### **Step 3: Global Find & Replace**
```
1. In collection, press Ctrl+F (search)
2. Find: "store_id"
3. Review each occurrence
4. Delete these lines from POST requests:
   - Products → Create
   - Addons → Create
```

### **Step 4: Update Authentication**
```
Authentication → Login request:
1. Change credentials to vendor
2. Update test script variable name:
   vendor_token instead of admin_token
```

---

## 📝 What to Change in Each Request

### **Products - Create/Update**
```diff
POST /api/vendor/products

- store_id: 1  ← Remove this line
+ category_id: 1  ← Keep this
  name_en: Pizza
  base_price: 120
```

### **Addons - Create/Update**
```diff
POST /api/vendor/addons

- store_id: 1  ← Remove this line
+ name_en: Extra Cheese  ← Keep this
  price: 15.00
```

### **All Other Requests**
```
✅ No changes needed!
✅ Same format as admin
✅ Same field names
✅ Same validation rules
```

---

## 🔑 Authentication Changes

### **Admin Login (Old):**
```javascript
// Test Script
if (pm.response.code === 200) {
    pm.environment.set('admin_token', pm.response.json().access_token);
}
```

### **Vendor Login (New):**
```javascript
// Test Script
if (pm.response.code === 200) {
    pm.environment.set('vendor_token', pm.response.json().access_token);
}
```

---

## 🆚 Side-by-Side Comparison

### **Admin Request:**
```http
POST /api/admin/products
Authorization: Bearer {{admin_token}}

store_id: 1  ← Required
category_id: 1
name_en: Supreme Pizza
base_price: 120
option_groups[0][option_group_id]: 1
addon_ids[]: 1
```

### **Vendor Request:**
```http
POST /api/vendor/products
Authorization: Bearer {{vendor_token}}

← NO store_id field!
category_id: 1
name_en: Supreme Pizza
base_price: 120
option_groups[0][option_group_id]: 1
addon_ids[]: 1
```

**Difference:** Just remove `store_id` line and use vendor token!

---

## ✅ Quick Checklist

Before testing vendor endpoints, verify:

- [ ] Imported `Vendor_System_Environment.postman_environment.json`
- [ ] Selected "Vendor Product System - Local" environment
- [ ] Changed base_url to `/api/vendor`
- [ ] Updated login credentials to vendor account
- [ ] Removed `store_id` from product/addon creation
- [ ] Changed token variable to `vendor_token`
- [ ] Tested login to save vendor token

---

## 🎯 Testing Flow

### **1. Login**
```
POST /api/vendor/login
→ Saves vendor_token
```

### **2. Create Option Group**
```
POST /api/vendor/option-groups
name_en: Size
→ NO store_id needed!
```

### **3. Create Option Values**
```
POST /api/vendor/option-values
option_group_id: {{option_group_id}}
value_en: Small
→ Works same as admin!
```

### **4. Create Addon**
```
POST /api/vendor/addons
name_en: Extra Cheese
price: 15.00
→ NO store_id needed!
→ Auto-assigned to vendor's store
```

### **5. Create Product**
```
POST /api/vendor/products
category_id: 1
name_en: Pizza
base_price: 120
option_groups[0][option_group_id]: 1
addon_ids[]: 1
→ NO store_id needed!
→ All in ONE request!
```

---

## 📊 Environment Variables

**Vendor Environment includes:**
```
vendor_token: (saved from login)
base_url: http://localhost:8000/api/vendor
product_id: (auto-saved from create)
option_group_id: (auto-saved from create)
option_value_id: (auto-saved from create)
addon_id: (auto-saved from create)
```

**Same auto-save scripts work!** Just change token variable name.

---

## 💡 Pro Tips

### **Tip 1: Keep Both Collections**
```
✅ Admin Collection → Test all stores
✅ Vendor Collection → Test single store
```

### **Tip 2: Use Environments**
```
✅ Admin Environment → admin_token
✅ Vendor Environment → vendor_token
```

### **Tip 3: Share Requests**
```
Most requests are identical!
Just change:
- base_url variable
- token variable  
- remove store_id
```

---

## 🎊 Summary

**To convert Admin collection to Vendor:**

1. ✅ Change base URL from `/api/admin` to `/api/vendor`
2. ✅ Change token from `admin_token` to `vendor_token`
3. ✅ Remove `store_id` field from 2 requests (Product Create, Addon Create)
4. ✅ Done! All 47 endpoints work!

**That's literally all you need to change!** 🚀

The vendor API is designed to be almost identical to admin, just simpler (no store_id management needed).
