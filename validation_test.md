# 🌍 Localized Validation Testing Guide

## ✅ **Validation Localization Implemented**

Your API now supports fully localized validation messages that change based on the request language.

## 🧪 **Test Localized Validation Errors**

### **1. Test Required Field Validation (English)**
```bash
curl -X POST -H "Content-Type: application/json" -H "X-Language: en" \
  -d '{"email":"test@example.com"}' \
  http://localhost:8000/api/user/register
```
**Expected Response:**
```json
{
    "success": false,
    "message": "The name field is required",
    "data": null,
    "errors": {
        "name": ["The name field is required"],
        "password": ["The password field is required"]
    }
}
```

### **2. Test Required Field Validation (Arabic)**
```bash
curl -X POST -H "Content-Type: application/json" -H "X-Language: ar" \
  -d '{"email":"test@example.com"}' \
  http://localhost:8000/api/user/register
```
**Expected Response:**
```json
{
    "success": false,
    "message": "حقل الاسم مطلوب",
    "data": null,
    "errors": {
        "name": ["حقل الاسم مطلوب"],
        "password": ["حقل كلمة المرور مطلوب"]
    }
}
```

### **3. Test Email Validation (English)**
```bash
curl -X POST -H "Content-Type: application/json" -H "X-Language: en" \
  -d '{"name":"Test","email":"invalid-email","password":"123"}' \
  http://localhost:8000/api/user/register
```
**Expected Response:**
```json
{
    "success": false,
    "message": "The email address must be a valid email address",
    "data": null,
    "errors": {
        "email": ["The email address must be a valid email address"],
        "password": ["The password must be at least 6 characters"]
    }
}
```

### **4. Test Email Validation (Arabic)**
```bash
curl -X POST -H "Content-Type: application/json" -H "X-Language: ar" \
  -d '{"name":"Test","email":"invalid-email","password":"123"}' \
  http://localhost:8000/api/user/register
```
**Expected Response:**
```json
{
    "success": false,
    "message": "عنوان البريد الإلكتروني يجب أن يكون عنوان بريد إلكتروني صالح",
    "data": null,
    "errors": {
        "email": ["عنوان البريد الإلكتروني يجب أن يكون عنوان بريد إلكتروني صالح"],
        "password": ["كلمة المرور يجب أن يكون على الأقل 6 أحرف"]
    }
}
```

### **5. Test Minimum Length Validation (English)**
```bash
curl -X POST -H "Content-Type: application/json" -H "X-Language: en" \
  -d '{"name":"A","email":"test@example.com","password":"12"}' \
  http://localhost:8000/api/user/register
```
**Expected Response:**
```json
{
    "success": false,
    "message": "The name must be between 2 and 100 characters",
    "data": null,
    "errors": {
        "name": ["The name must be between 2 and 100 characters"],
        "password": ["The password must be at least 6 characters"]
    }
}
```

### **6. Test Minimum Length Validation (Arabic)**
```bash
curl -X POST -H "Content-Type: application/json" -H "X-Language: ar" \
  -d '{"name":"A","email":"test@example.com","password":"12"}' \
  http://localhost:8000/api/user/register
```
**Expected Response:**
```json
{
    "success": false,
    "message": "الاسم يجب أن يكون بين 2 و 100 أحرف",
    "data": null,
    "errors": {
        "name": ["الاسم يجب أن يكون بين 2 و 100 أحرف"],
        "password": ["كلمة المرور يجب أن يكون على الأقل 6 أحرف"]
    }
}
```

### **7. Test Unique Email Validation (English)**
First register a user, then try to register with the same email:
```bash
# First registration
curl -X POST -H "Content-Type: application/json" -H "X-Language: en" \
  -d '{"name":"Test User","email":"unique@example.com","password":"password123"}' \
  http://localhost:8000/api/user/register

# Try to register with same email
curl -X POST -H "Content-Type: application/json" -H "X-Language: en" \
  -d '{"name":"Another User","email":"unique@example.com","password":"password123"}' \
  http://localhost:8000/api/user/register
```
**Expected Response:**
```json
{
    "success": false,
    "message": "The email address has already been taken",
    "data": null,
    "errors": {
        "email": ["The email address has already been taken"]
    }
}
```

### **8. Test Unique Email Validation (Arabic)**
```bash
curl -X POST -H "Content-Type: application/json" -H "X-Language: ar" \
  -d '{"name":"Another User","email":"unique@example.com","password":"password123"}' \
  http://localhost:8000/api/user/register
```
**Expected Response:**
```json
{
    "success": false,
    "message": "عنوان البريد الإلكتروني مُستخدم مسبقاً",
    "data": null,
    "errors": {
        "email": ["عنوان البريد الإلكتروني مُستخدم مسبقاً"]
    }
}
```

## ✅ **Features Implemented**

1. **📝 Localized Validation Rules**: All validation rules (required, email, min, max, etc.) are localized
2. **🏷️ Localized Attribute Names**: Field names are translated (name → الاسم, email → عنوان البريد الإلكتروني)
3. **🔧 ValidationService**: Centralized validation with automatic localization
4. **📱 Smart Message Display**: First validation error is shown as the main message
5. **🌍 Language Detection**: Validation messages change based on request language
6. **📊 Detailed Errors**: Full error details in the `errors` object
7. **🎯 Consistent Format**: All validation responses follow the same structure

## 🎯 **Key Benefits**

- **Automatic Translation**: Validation messages automatically translate based on headers
- **Attribute Translation**: Field names are properly translated (e.g., "name" → "الاسم")
- **First Error Priority**: The main message shows the first validation error for better UX
- **Complete Error Details**: Full validation errors are available in the `errors` object
- **Consistent Structure**: All responses maintain the same `success`, `message`, `data` format

Your validation system is now fully internationalized and user-friendly!
