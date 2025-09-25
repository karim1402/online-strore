# API Testing Guide

## 🌍 **Localization System Implemented**

Your API now supports dynamic language switching based on HTTP headers and returns consistent JSON responses.

### **Language Detection**
The API detects language from these headers (in priority order):
1. `X-Language: en` or `X-Language: ar`
2. `Accept-Language: en` or `Accept-Language: ar`
3. Default: `en` (English)

### **Response Format**
All API responses now follow this consistent structure:
```json
{
    "success": true|false,
    "message": "Localized message based on request language",
    "data": {...} // Always present, null if no data
}
```

## 🧪 **Test the API**

### **1. Test API Info (English)**
```bash
curl -H "X-Language: en" http://localhost:8000/api/
```
**Expected Response:**
```json
{
    "success": true,
    "message": "Makkok API is running",
    "data": {
        "version": "1.0.0",
        "guards": {...},
        "endpoints": {...}
    }
}
```

### **2. Test API Info (Arabic)**
```bash
curl -H "X-Language: ar" http://localhost:8000/api/
```
**Expected Response:**
```json
{
    "success": true,
    "message": "واجهة برمجة التطبيقات مكوك تعمل",
    "data": {
        "version": "1.0.0",
        "guards": {...},
        "endpoints": {...}
    }
}
```

### **3. Test Health Check (English)**
```bash
curl -H "X-Language: en" http://localhost:8000/api/health
```

### **4. Test Health Check (Arabic)**
```bash
curl -H "X-Language: ar" http://localhost:8000/api/health
```

### **5. Test User Registration (English)**
```bash
curl -X POST -H "Content-Type: application/json" -H "X-Language: en" \
  -d '{"name":"Test User","email":"test@example.com","password":"password123"}' \
  http://localhost:8000/api/user/register
```

### **6. Test User Registration (Arabic)**
```bash
curl -X POST -H "Content-Type: application/json" -H "X-Language: ar" \
  -d '{"name":"Test User","email":"test2@example.com","password":"password123"}' \
  http://localhost:8000/api/user/register
```

### **7. Test Validation Error (English)**
```bash
curl -X POST -H "Content-Type: application/json" -H "X-Language: en" \
  -d '{"name":"Test"}' \
  http://localhost:8000/api/user/register
```

### **8. Test Validation Error (Arabic)**
```bash
curl -X POST -H "Content-Type: application/json" -H "X-Language: ar" \
  -d '{"name":"Test"}' \
  http://localhost:8000/api/user/register
```

### **9. Test Authentication Error (English)**
```bash
curl -H "X-Language: en" http://localhost:8000/api/user/dashboard
```

### **10. Test Authentication Error (Arabic)**
```bash
curl -H "X-Language: ar" http://localhost:8000/api/user/dashboard
```

### **11. Test 404 Error (English)**
```bash
curl -H "X-Language: en" http://localhost:8000/api/nonexistent
```

### **12. Test 404 Error (Arabic)**
```bash
curl -H "X-Language: ar" http://localhost:8000/api/nonexistent
```

## ✅ **Features Implemented**

1. **✅ JSON Language Files**: English and Arabic messages in separate JSON files
2. **✅ Language Detection**: Automatic detection from HTTP headers
3. **✅ Consistent Response Format**: All responses have `success`, `message`, and `data` keys
4. **✅ Localization Service**: Centralized message handling with dot notation
5. **✅ ApiResponse Trait**: Standardized response methods for controllers
6. **✅ Exception Handling**: All errors return localized JSON responses
7. **✅ Middleware Integration**: Language detection runs on every API request
8. **✅ Fallback Support**: Defaults to English if language not supported

## 🎯 **Key Benefits**

- **Single Message**: Only one message field per response (language-specific)
- **Header-Based**: Language switching via HTTP headers
- **Consistent Structure**: All responses follow the same format
- **Centralized Management**: All messages in JSON files
- **Dot Notation**: Easy message organization (e.g., 'success.user_registered')
- **Fallback System**: Graceful handling of missing translations

Your API is now fully internationalized and ready for production use!
