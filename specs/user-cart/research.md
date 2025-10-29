# Research: User Shopping Cart

**Feature**: user-cart  
**Date**: 2024-10-29  
**Status**: Complete

## Research Areas

### 1. Cart Data Model Design

**Decision**: Four-table normalized structure  
**Rationale**:
- `carts` table: One cart per user with store_id constraint
- `cart_items` table: Products with quantity
- `cart_item_options` table: Selected product option values (pivot)
- `cart_item_addons` table: Selected addons with quantities (pivot)

**Alternatives Considered**:
- Single table with JSON: Rejected due to query complexity and lack of referential integrity
- Two tables (cart + items with JSON options): Rejected due to difficulty validating option/addon existence

**Benefits**:
- Referential integrity via foreign keys
- Easy to query and validate
- Supports cascade deletes
- Can add indexes for performance

### 2. Single-Store Constraint Implementation

**Decision**: Unique constraint on user_id in carts table + application-level validation  
**Rationale**:
- Database enforces one cart per user
- Application checks store_id before adding items
- Returns 409 Conflict when store mismatch detected
- Provides confirmation endpoint for cart replacement

**Alternatives Considered**:
- Store multiple carts per user: Rejected per requirements
- Soft delete old cart: Rejected, clean replacement preferred
- Merge carts: Rejected, too complex and not required

**Implementation Flow**:
1. User adds product to cart
2. Check if cart exists
3. If exists, compare store_id
4. If mismatch, return 409 with confirmation required
5. User confirms via separate endpoint
6. Delete old cart, create new cart

### 3. Price Calculation Strategy

**Decision**: Calculate prices on-the-fly, don't store totals  
**Rationale**:
- Product/option/addon prices may change
- Always show current prices
- Prevents stale price data
- Simple to implement

**Calculation Formula**:
```
item_price = product.base_price 
           + SUM(option_value.calculated_price)
           + SUM(addon.price × addon_quantity)

item_total = item_price × quantity

cart_total = SUM(item_total for all items)
```

**Alternatives Considered**:
- Store prices at add time: Rejected, prices should be current
- Cache calculations: May add later for performance if needed

**Performance Optimization**:
- Eager load all relationships in single query
- Use Laravel's `with()` for products, options, addons
- Calculate in PHP after loading (fast enough for cart sizes)

### 4. Stock Validation Approach

**Decision**: Validate on add/update, show warnings on view  
**Rationale**:
- Prevent adding out-of-stock items
- Allow viewing cart even if items became unavailable
- Block checkout if unavailable items exist
- User-friendly error messages

**Validation Points**:
- Product must be active
- Product's store must be approved
- Option values must be available and in stock
- Addons must be active

**Alternatives Considered**:
- Auto-remove unavailable items: Rejected, user should decide
- Reserve stock when added to cart: Rejected, too complex for v1

### 5. Concurrency Handling

**Decision**: Optimistic locking with last-write-wins for quantity  
**Rationale**:
- Cart conflicts are rare (single user)
- Simple implementation
- Good enough for cart operations
- Can add pessimistic locking later if needed

**Implementation**:
- Use Laravel's `updated_at` for version checking
- Atomic updates via database transactions
- Return current state on conflict

**Alternatives Considered**:
- Pessimistic locking: Rejected, overkill for cart
- Queue-based updates: Rejected, adds complexity

### 6. Activity Logging Strategy

**Decision**: Log cart creation, item additions, cart clearing  
**Rationale**:
- Audit trail for customer support
- Analytics for cart abandonment
- Debug issues
- Existing Spatie Activity Log package

**Events to Log**:
- Cart created (with store_id)
- Item added (product_id, quantity, options, addons)
- Item quantity updated (old → new)
- Item removed (product details)
- Cart cleared (item count, total value)
- Cart replaced (old store → new store)

**Alternatives Considered**:
- No logging: Rejected, valuable for support
- Custom logging: Rejected, Spatie already integrated

### 7. API Response Structure

**Decision**: Nested JSON with full product details  
**Rationale**:
- Frontend needs all info to display cart
- Minimize API calls
- Include localized names/descriptions
- Calculate and return totals

**Response Structure**:
```json
{
  "cart": {
    "id": 1,
    "store": {...},
    "items": [
      {
        "id": 1,
        "product": {...},
        "quantity": 2,
        "selected_options": [...],
        "selected_addons": [...],
        "item_price": "25.99",
        "item_total": "51.98"
      }
    ],
    "subtotal": "51.98",
    "total": "51.98",
    "item_count": 1
  }
}
```

**Alternatives Considered**:
- Minimal response with IDs only: Rejected, requires extra API calls
- Separate endpoints for details: Rejected, inefficient

### 8. Error Handling Patterns

**Decision**: HTTP status codes + localized error messages  
**Rationale**:
- RESTful conventions
- Bilingual support via LocalizationService
- Clear error types for frontend

**Status Codes**:
- 200: Success
- 201: Cart/item created
- 204: Item deleted
- 400: Validation error
- 404: Cart/item not found
- 409: Store conflict (requires confirmation)
- 422: Unprocessable (stock unavailable, etc.)

**Alternatives Considered**:
- Always 200 with error in body: Rejected, not RESTful
- Custom error codes: Rejected, HTTP standards sufficient

### 9. Testing Strategy

**Decision**: Feature tests covering all user flows  
**Rationale**:
- Test actual HTTP requests
- Validate JSON responses
- Test database state
- Cover edge cases

**Test Coverage**:
- Add item to empty cart
- Add item to existing cart (same store)
- Add item from different store (conflict)
- Confirm cart replacement
- Update item quantity
- Remove item
- Clear cart
- View cart with calculations
- Stock validation
- Product availability validation

**Alternatives Considered**:
- Unit tests only: Rejected, need integration coverage
- Manual testing: Rejected, not sustainable

### 10. Performance Considerations

**Decision**: Eager loading + query optimization  
**Rationale**:
- Cart queries can be complex (many relationships)
- N+1 query problem is common
- Laravel's eager loading solves this

**Optimization Techniques**:
- Use `with()` for all relationships
- Select only needed columns
- Index foreign keys
- Consider caching for high-traffic users (future)

**Query Example**:
```php
Cart::with([
    'store:id,name_en,name_ar,logo',
    'items.product:id,name_en,name_ar,base_price',
    'items.product.primaryImage',
    'items.options.optionValue',
    'items.addons'
])->where('user_id', $userId)->first();
```

**Alternatives Considered**:
- Lazy loading: Rejected, causes N+1 problems
- Separate queries: Rejected, slower than eager loading

## Summary

All technical decisions are made and documented. No NEEDS CLARIFICATION remain. Ready to proceed to Phase 1 (Design & Contracts).

**Key Takeaways**:
- Four-table normalized structure
- Single-store constraint enforced at DB and app level
- Real-time price calculation
- Optimistic concurrency control
- Comprehensive activity logging
- Feature test coverage
- Performance via eager loading
