# Product API Documentation

## Overview
Product endpoints for the user side, including detailed product information and random product suggestions.

## Endpoints

### 1. Get Random Products
**GET** `/api/user/products/random`

Retrieves a random selection of products with basic information (name, price, and primary image). Perfect for "You may also like" sections, featured products, or product suggestions.

#### Authentication
- **Not Required** - Public endpoint

#### Query Parameters
| Parameter | Type | Required | Default | Description |
|-----------|------|----------|---------|-------------|
| count | integer | No | 4 | Number of random products to return (min: 1, max: 20) |

#### Headers
| Header | Value | Required | Description |
|--------|-------|----------|-------------|
| Accept-Language | en/ar | No | Language preference (default: en) |

#### Success Response (200 OK)
```json
{
  "success": true,
  "message": "Data retrieved successfully",
  "data": {
    "products": [
      {
        "id": 5,
        "name": "Deluxe Burger",
        "base_price": "15.99",
        "image": "https://example.com/storage/products/burger.jpg"
      },
      {
        "id": 8,
        "name": "Chicken Pizza",
        "base_price": "22.50",
        "image": "https://example.com/storage/products/pizza.jpg"
      },
      {
        "id": 12,
        "name": "Caesar Salad",
        "base_price": "12.00",
        "image": "https://example.com/storage/products/salad.jpg"
      },
      {
        "id": 3,
        "name": "Chocolate Cake",
        "base_price": "8.99",
        "image": null
      }
    ],
    "count": 4
  }
}
```

#### Features
- Returns only active products from approved stores
- Truly random selection on each request
- Lightweight response (only essential data)
- Automatic localization based on Accept-Language header
- Configurable count (1-20 products)

#### Usage Examples

**Get 4 random products (default):**
```bash
curl -X GET "https://api.example.com/api/user/products/random" \
  -H "Accept: application/json"
```

**Get 10 random products:**
```bash
curl -X GET "https://api.example.com/api/user/products/random?count=10" \
  -H "Accept: application/json"
```

**Get random products in Arabic:**
```bash
curl -X GET "https://api.example.com/api/user/products/random?count=6" \
  -H "Accept: application/json" \
  -H "Accept-Language: ar"
```

---

### 2. Get Product Details
**GET** `/api/user/products/{productId}`

Retrieves complete product information including:
- Basic product details (name, description, price, etc.)
- Store information
- Category information
- All product images (sorted by primary first, then by sort order)
- Available addons with pricing
- Product options with their values and pricing
- Stock availability for option values

## Authentication
- **Not Required** - Public endpoint accessible to both guests and authenticated users
- View count is incremented automatically on each request

## Request Parameters

### Path Parameters
| Parameter | Type | Required | Description |
|-----------|------|----------|-------------|
| productId | integer | Yes | The ID of the product to retrieve |

### Headers
| Header | Value | Required | Description |
|--------|-------|----------|-------------|
| Accept-Language | en/ar | No | Language preference (default: en) |

## Response Structure

### Success Response (200 OK)

```json
{
  "success": true,
  "message": "Data retrieved successfully",
  "data": {
    "product": {
      "id": 1,
      "name": "Deluxe Burger",
      "description": "A delicious burger with premium ingredients",
      "search_keywords": "burger, food, deluxe",
      "base_price": "15.99",
      "is_active": true,
      "view_count": 125,
      "sales_count": 45,
      "sort_order": 1,
      "created_at": "2024-10-15T10:30:00.000000Z",
      "updated_at": "2024-10-20T14:20:00.000000Z",
      
      "store": {
        "id": 5,
        "name": "Burger House",
        "description": "Best burgers in town",
        "logo_url": "https://example.com/storage/stores/logo.jpg",
        "status": "active"
      },
      
      "category": {
        "id": 3,
        "name": "Burgers",
        "description": "All types of burgers",
        "image_url": "https://example.com/storage/categories/burgers.jpg",
        "is_active": true
      },
      
      "primary_image": {
        "id": 10,
        "image_url": "https://example.com/storage/products/burger-main.jpg"
      },
      
      "images": [
        {
          "id": 10,
          "image_url": "https://example.com/storage/products/burger-main.jpg",
          "is_primary": true,
          "sort_order": 1
        },
        {
          "id": 11,
          "image_url": "https://example.com/storage/products/burger-side.jpg",
          "is_primary": false,
          "sort_order": 2
        }
      ],
      
      "addons": [
        {
          "id": 1,
          "name": "Extra Cheese",
          "description": "Add extra cheese to your burger",
          "price": "2.50",
          "addon_category": "toppings",
          "is_active": true,
          "is_available": true,
          "sort_order": 1
        },
        {
          "id": 2,
          "name": "Bacon",
          "description": "Crispy bacon strips",
          "price": "3.00",
          "addon_category": "toppings",
          "is_active": true,
          "is_available": true,
          "sort_order": 2
        }
      ],
      
      "options": [
        {
          "id": 5,
          "is_required": true,
          "sort_order": 1,
          "option_group": {
            "id": 2,
            "name": "Size",
            "type": "radio",
            "is_active": true
          },
          "values": [
            {
              "id": 15,
              "option_value_id": 8,
              "value": "Small",
              "price_type": "fixed",
              "price_value": "15.99",
              "calculated_price": "15.99",
              "stock_quantity": 50,
              "is_available": true,
              "in_stock": true
            },
            {
              "id": 16,
              "option_value_id": 9,
              "value": "Medium",
              "price_type": "additional",
              "price_value": "3.00",
              "calculated_price": "18.99",
              "stock_quantity": 30,
              "is_available": true,
              "in_stock": true
            },
            {
              "id": 17,
              "option_value_id": 10,
              "value": "Large",
              "price_type": "additional",
              "price_value": "5.00",
              "calculated_price": "20.99",
              "stock_quantity": 20,
              "is_available": true,
              "in_stock": true
            }
          ]
        },
        {
          "id": 6,
          "is_required": false,
          "sort_order": 2,
          "option_group": {
            "id": 3,
            "name": "Extras",
            "type": "checkbox",
            "is_active": true
          },
          "values": [
            {
              "id": 18,
              "option_value_id": 11,
              "value": "Extra Sauce",
              "price_type": "additional",
              "price_value": "1.50",
              "calculated_price": "17.49",
              "stock_quantity": 100,
              "is_available": true,
              "in_stock": true
            }
          ]
        }
      ]
    }
  }
}
```

### Error Responses

#### Product Not Found (404)
```json
{
  "success": false,
  "message": "Product not found"
}
```

**Reasons for 404:**
- Product ID doesn't exist
- Product is not active (is_active = false)
- Product's store is not approved
- Product's store doesn't exist

## Data Structure Details

### Product Object
| Field | Type | Description |
|-------|------|-------------|
| id | integer | Product unique identifier |
| name | string | Localized product name |
| description | string | Localized product description |
| search_keywords | string | Keywords for search functionality |
| base_price | decimal | Base price before options/addons |
| is_active | boolean | Product availability status |
| view_count | integer | Number of times product was viewed |
| sales_count | integer | Number of times product was sold |
| sort_order | integer | Display order in listings |
| created_at | timestamp | Product creation date |
| updated_at | timestamp | Last update date |

### Store Object
| Field | Type | Description |
|-------|------|-------------|
| id | integer | Store unique identifier |
| name | string | Localized store name |
| description | string | Localized store description |
| logo_url | string | Full URL to store logo |
| status | string | Store status (active/inactive) |

### Category Object
| Field | Type | Description |
|-------|------|-------------|
| id | integer | Category unique identifier |
| name | string | Localized category name |
| description | string | Localized category description |
| image_url | string | Full URL to category image |
| is_active | boolean | Category availability status |

### Image Object
| Field | Type | Description |
|-------|------|-------------|
| id | integer | Image unique identifier |
| image_url | string | Full URL to image file |
| is_primary | boolean | Whether this is the main product image |
| sort_order | integer | Display order |

### Addon Object
| Field | Type | Description |
|-------|------|-------------|
| id | integer | Addon unique identifier |
| name | string | Localized addon name |
| description | string | Localized addon description |
| price | decimal | Additional price for this addon |
| addon_category | string | Category/type of addon |
| is_active | boolean | Addon availability status |
| is_available | boolean | Whether addon is available for this product |
| sort_order | integer | Display order |

### Option Object
| Field | Type | Description |
|-------|------|-------------|
| id | integer | Product option unique identifier |
| is_required | boolean | Whether customer must select this option |
| sort_order | integer | Display order |
| option_group | object | Option group details (see below) |
| values | array | Available values for this option |

### Option Group Object
| Field | Type | Description |
|-------|------|-------------|
| id | integer | Option group unique identifier |
| name | string | Localized option group name (e.g., "Size", "Color") |
| type | string | Input type (radio, checkbox, select, etc.) |
| is_active | boolean | Option group availability status |

### Option Value Object
| Field | Type | Description |
|-------|------|-------------|
| id | integer | Product option value unique identifier |
| option_value_id | integer | Reference to the base option value |
| value | string | Localized value name (e.g., "Small", "Large") |
| price_type | string | How price is calculated: "fixed", "additional", "percentage" |
| price_value | decimal | Price value based on price_type |
| calculated_price | decimal | Final calculated price for this option |
| stock_quantity | integer | Available stock for this option |
| is_available | boolean | Whether this value is available |
| in_stock | boolean | Whether there's stock available (quantity > 0) |

## Price Calculation Types

### Fixed Price
- `price_type`: "fixed"
- `calculated_price` = `price_value`
- Example: Small size costs exactly $15.99

### Additional Price
- `price_type`: "additional"
- `calculated_price` = `base_price` + `price_value`
- Example: Medium size adds $3.00 to base price

### Percentage Price
- `price_type`: "percentage"
- `calculated_price` = `base_price` * (1 + `price_value` / 100)
- Example: Premium option adds 20% to base price

## Features

### Automatic View Tracking
- View count is automatically incremented each time the endpoint is called
- No authentication required for tracking
- Useful for analytics and popular products

### Localization
- All text fields are automatically localized based on `Accept-Language` header
- Supports English (en) and Arabic (ar)
- Fallback to English if translation not available

### Smart Filtering
- Only active products are returned
- Only approved stores are included
- Only active addons are shown
- Only active option groups and values are included
- Only available option values are returned

### Stock Management
- Each option value includes stock information
- `in_stock` field indicates immediate availability
- `stock_quantity` shows exact available quantity

## Usage Examples

### Basic Request
```bash
curl -X GET "https://api.example.com/api/user/products/1" \
  -H "Accept: application/json"
```

### With Arabic Language
```bash
curl -X GET "https://api.example.com/api/user/products/1" \
  -H "Accept: application/json" \
  -H "Accept-Language: ar"
```

### With Authentication (Optional)
```bash
curl -X GET "https://api.example.com/api/user/products/1" \
  -H "Accept: application/json" \
  -H "Authorization: Bearer YOUR_JWT_TOKEN"
```

## Integration Notes

### For Frontend Developers

1. **Display Primary Image First**: Use the `primary_image` field for the main product display, or the first image in the `images` array.

2. **Calculate Total Price**: 
   - Start with `base_price`
   - Add selected addon prices
   - Use `calculated_price` from selected option values

3. **Handle Required Options**: 
   - Check `is_required` field
   - Force user selection before adding to cart
   - Validate that at least one value is selected

4. **Stock Validation**:
   - Check `in_stock` field before allowing selection
   - Display stock quantity if low
   - Disable out-of-stock options

5. **Option Types**:
   - `radio`: Single selection (e.g., size)
   - `checkbox`: Multiple selection (e.g., extras)
   - `select`: Dropdown selection

### For Mobile Apps

- Cache product details for offline viewing
- Pre-load images for better performance
- Show loading states during API calls
- Handle 404 errors gracefully (product may be deleted)

## Related Endpoints

- `GET /api/user/stores/{storeId}/categories` - Get all products in a store
- `GET /api/user/main-categories` - Browse main categories
- `GET /api/user/stores/by-category/{mainCategoryId}` - Find stores by category

## Technical Details

### Models Used
- `Product` - Main product model
- `ProductImage` - Product images
- `Addon` - Product addons
- `ProductOption` - Product options configuration
- `ProductOptionValue` - Option values with pricing
- `OptionGroup` - Option group definitions
- `OptionValue` - Base option values
- `Store` - Store information
- `Category` - Product category

### Performance Considerations
- Uses eager loading to minimize database queries
- Efficient relationship loading with constraints
- Automatic caching recommended for frequently viewed products
- Consider implementing Redis cache for popular products

### Security
- Only approved stores are accessible
- Only active products are returned
- No sensitive store/vendor data exposed
- View tracking doesn't require authentication

## Changelog

### Version 1.1.0 (2024-10-29)
- Added random products endpoint
- Configurable product count (1-20)
- Lightweight response for suggestions
- Full localization for all nested data

### Version 1.0.0 (2024-10-29)
- Initial release
- Complete product details endpoint
- Support for images, addons, and options
- Automatic view tracking
- Localization support
- Stock management integration
