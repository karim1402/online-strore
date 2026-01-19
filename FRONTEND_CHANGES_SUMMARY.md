# Makook API & Architectural Changes Summary

This document summarizes the recent backend changes to help frontend developers integrate the new features and architectural updates.

## 1. Architectural Change: Modules (formerly Main Categories)
The concept of "Main Categories" has been renamed to **Modules** to better reflect the system's structure.

*   **API Routes**: All `/main-categories` routes are now `/modules`.
    *   Example: `admin/main-categories` -> `admin/modules`
*   **Parameters**:
    *   `main_category_id` is now `module_id`.
    *   `main_category_ids` is now `module_ids`.
*   **Models & Relationships**:
    *   The `MainCategory` model is now `Module`.
    *   `Store` model now has a `modules` relationship (replaces `mainCategories`).
    *   `Category` model now has a `module` relationship (replaces `mainCategory`).

## 2. Category Hierarchy (Sub-categories)
Categories now support a parent-child relationship, allowing for nested sub-categories.

*   **New Field**: `parent_id` (nullable).
*   **Validation**:
    *   A category cannot be its own parent.
*   **User API**:
    *   `GET /categories/by-module/{moduleId}`: Get all top-level categories for a specific module.
    *   `GET /categories/all-with-products`: Get all categories and their related products (optionally filter by `module_id`).
    *   `GET /categories/{id}`: Get a main category with its subcategories and products.
    *   `GET /categories/{id}/subcategories`: Get only the list of subcategories for a given main category, with their products.

## 3. Product Sub-category Support
Products can now be specifically assigned to a sub-category.

*   **New Field**: `subcategory_id` (nullable).
*   **Validation**: The `subcategory_id` must be a valid child of the selected `category_id`.
*   **Convenience Attributes (Appended)**:
    *   `image_url`: Returns the primary image URL or the first available image.
    *   `name`: Returns the localized name based on the request language.
    *   `description`: Returns the localized description based on the request language.
*   **New Input Field**: `search_keywords` (nullable string).
    *   **Purpose**: Allows Admins and Vendors to add comma-separated keywords (e.g., "pizza, spicy, italian") when creating or updating products.
*   **Option Values**: Added `image` field (nullable string/file) to option values.
    *   **Response**: `OptionValue` objects now include an `image_url` attribute.
*   **Global Addons**: Addons can now be created without a `store_id` (Global Addons).
    *   **Admin API**: `store_id` is now optional in `POST /admin/addons`.
    *   **Vendor API**: Vendors can now see and assign global addons to their products.

## 4. User Account Management
*   **Delete Account**: New endpoint `DELETE /api/user/delete-account` (requires authentication).

## 5. Postman Collection
Two Postman collections are available:
1.  `Makook v2.postman_collection.json`: Full collection updated with Modules and new fields.
2.  `Category_Management.postman_collection.json`: Focused collection for Category APIs using form-data.

---
**Note for Portal Developers**: If you are working on the Admin Portal (Makook Owner) or the Vendor Portal, please ensure you update your form data and API calls to use `module_id` and handle the optional `parent_id` for categories and `subcategory_id` for products.
