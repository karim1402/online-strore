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
    *   A sub-category must belong to the same **Module** as its parent.
*   **Admin/Vendor API**:
    *   `GET /categories`: Added `parent_id` and `module_id` filters.
    *   `POST /categories`: Requires `module_id`, optional `parent_id`.
*   **User API**:
    *   `GET /stores/{id}/categories-with-products`: Now returns a **nested tree structure**.
    *   Top-level categories have a `children` array containing their sub-categories.
    *   Products are distributed: products in a sub-category appear inside that sub-category; products in a main category (with no sub-category) appear at the top level.

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
    *   **Note**: This field is currently for data collection only and will be used for advanced search features (like Elasticsearch) in the future. Please include this field in the Product forms.
*   **API Responses**: Product objects now include a `subcategory` relationship.

## 4. User Account Management
*   **Delete Account**: New endpoint `DELETE /api/user/delete-account` (requires authentication).
*   **Localization**: Added `account_deleted` message in English and Arabic.

## 5. Localization Updates
New error and success messages added to `messages.json`:
*   `invalid_parent`: Category cannot be its own parent.
*   `invalid_parent_category`: Parent must be in the same module.
*   `subcategory_not_found_in_category`: The selected sub-category doesn't belong to the main category.
*   `account_deleted`: Success message for account deletion.

## 6. Postman Collection
The Postman collection `Makook v2.postman_collection.json` has been fully updated:
*   All "Main Category" references renamed to "Module".
*   Routes updated to `/modules`.
*   New `subcategory_id` and `parent_id` fields added to relevant requests.
*   Added the `delete-account` request.

---
**Note for Portal Developers**: If you are working on the Admin Portal (Makook Owner) or the Vendor Portal, please ensure you update your form data and API calls to use `module_id` and handle the optional `parent_id` for categories and `subcategory_id` for products.
