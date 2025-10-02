# Platform Specification: Makkok Multi-Vendor Food Delivery System

**Platform Name**: Makkok  
**Created**: 2025-10-02  
**Status**: Active Development  
**Input**: Multi-vendor delivery app mainly for food

---

## ⚡ Platform Overview

Makkok is a multi-vendor food delivery platform connecting customers with local food vendors through a centralized marketplace. The system enables vendors to manage their stores, customers to browse and order food, delivery personnel to fulfill orders, and administrators to oversee the entire platform.

**Core Value Proposition**: Enable multiple food vendors to operate independent stores on a shared platform while maintaining centralized quality control and customer experience.

---

## User Scenarios & Testing

### Primary User Stories

#### 1. Vendor Onboarding Journey
**As a** restaurant owner  
**I want to** register my store on the platform  
**So that** I can reach customers and start selling food online

**Journey**:
1. Vendor completes registration form with business details
2. Vendor uploads store logo and verification documents
3. Vendor selects relevant food categories (e.g., Fast Food, Desserts)
4. System creates vendor account and store profile
5. Admin reviews and approves store
6. Vendor receives approval notification and gains access to store management

#### 2. Customer Food Ordering Journey
**As a** hungry customer  
**I want to** browse nearby restaurants and order food  
**So that** I can have meals delivered to my location

**Journey**:
1. Customer browses stores by category or location
2. Customer views store menu and adds items to cart
3. Customer proceeds to checkout and confirms delivery address
4. Customer completes payment
5. Order is assigned to available delivery personnel
6. Customer tracks order status in real-time
7. Customer receives order and confirms delivery

#### 3. Delivery Personnel Journey
**As a** delivery driver  
**I want to** receive and fulfill delivery requests  
**So that** I can earn income from deliveries

**Journey**:
1. Delivery personnel registers with vehicle information
2. Admin approves delivery account
3. Delivery personnel sets availability status online
4. System assigns orders based on location and availability
5. Delivery personnel accepts order and navigates to pickup location
6. Delivery personnel picks up order from vendor
7. Delivery personnel delivers to customer and confirms completion

#### 4. Admin Platform Management Journey
**As a** platform administrator  
**I want to** oversee all platform operations  
**So that** I can ensure quality service and resolve issues

**Journey**:
1. Admin reviews pending vendor applications
2. Admin approves/rejects stores based on verification
3. Admin manages food categories and platform settings
4. Admin monitors user activities and handles disputes
5. Admin manages system configurations and permissions

### Acceptance Scenarios

#### Vendor Registration
1. **Given** a new vendor wants to join the platform, **When** they submit complete registration form with valid documents, **Then** system creates vendor account, store profile, and sends for admin approval
2. **Given** a vendor submits incomplete registration, **When** required fields are missing, **Then** system rejects submission with specific validation errors in English and Arabic
3. **Given** a vendor tries to register with duplicate email, **When** email already exists in system, **Then** system prevents registration and notifies vendor
4. **Given** a store is pending approval, **When** vendor logs in, **Then** vendor can access dashboard but cannot process orders until approved

#### Customer Browsing & Ordering
1. **Given** a customer is browsing stores, **When** filtering by category, **Then** system displays only stores belonging to selected category
2. **Given** a customer views a store, **When** store is inactive/unapproved, **Then** store does not appear in customer-facing listings
3. **Given** a customer is placing an order, **When** cart is empty, **Then** system prevents checkout
4. **Given** a customer completes order, **When** payment succeeds, **Then** system notifies vendor and assigns delivery personnel

#### Multi-Vendor Store Management
1. **Given** a store has multiple staff members, **When** any authorized vendor logs in, **Then** they can manage the same store
2. **Given** a store owner wants to add staff, **When** creating new vendor account with existing store ID, **Then** system associates new vendor with store
3. **Given** multiple vendors manage one store, **When** one vendor updates store details, **Then** changes are visible to all vendors of that store

#### Delivery Assignment
1. **Given** a new order is placed, **When** delivery personnel are available, **Then** system assigns order to nearest available driver
2. **Given** a delivery personnel is offline, **When** new orders are created, **Then** system does not assign orders to offline drivers
3. **Given** a delivery is in progress, **When** delivery personnel marks as completed, **Then** customer and vendor receive confirmation

#### Admin Control & Permissions
1. **Given** an admin with super_admin role, **When** accessing any admin function, **Then** full access is granted
2. **Given** an admin with moderator role, **When** attempting to delete stores, **Then** system denies access based on permission rules
3. **Given** an admin creates new category, **When** vendors register, **Then** new category appears in available selections

### Edge Cases

#### Registration & Authentication
- What happens when vendor uploads invalid file format for logo/document?
  - System validates file types (images: jpeg, jpg, png, webp; documents: pdf) and size limits (logo: 2MB, document: 5MB), rejects invalid uploads with clear error message

- What happens when user forgets password?
  - System provides password reset flow via email verification [NEEDS CLARIFICATION: password reset flow not yet implemented]

- What happens when JWT token expires during active session?
  - System provides token refresh endpoint; client can refresh token without re-authentication

#### Store & Category Management
- What happens when admin deletes a category with associated stores?
  - System prevents deletion due to foreign key constraint; admin must reassign stores first or use cascade policy [NEEDS CLARIFICATION: deletion policy preference]

- What happens when vendor tries to register with inactive category?
  - System validates category is active before allowing registration; rejects with appropriate error

- What happens when store has no categories assigned?
  - System requires at least one category during registration; validation prevents zero categories

#### Multi-Vendor Scenarios
- What happens when two vendors from same store update details simultaneously?
  - Database handles concurrent updates; last write wins (standard optimistic locking)

- What happens when store owner wants to remove vendor access?
  - [NEEDS CLARIFICATION: vendor removal/deactivation flow not specified in current implementation]

#### Geolocation & Delivery
- What happens when customer location is outside delivery range?
  - [NEEDS CLARIFICATION: delivery range/zone logic not specified]

- What happens when no delivery personnel are available?
  - [NEEDS CLARIFICATION: order queueing and fallback handling not specified]

#### Data & File Management
- What happens when vendor uploads corrupted image file?
  - File validation occurs during upload; corrupted files fail validation and return error

- What happens when storage quota is exceeded?
  - [NEEDS CLARIFICATION: storage limits and quota management not specified]

#### Localization
- What happens when client sends invalid Accept-Language header?
  - System defaults to English for messages; all responses include both English and Arabic fields

---

## Requirements

### Functional Requirements

#### FR-001: Multi-Guard Authentication System
- **FR-001.1**: System MUST support four distinct user types: Customers, Vendors, Delivery Personnel, Admins
- **FR-001.2**: System MUST use separate authentication guards for each user type (api, vendors, deliveries, admins)
- **FR-001.3**: System MUST issue JWT tokens with 60-minute expiration
- **FR-001.4**: System MUST allow token refresh without re-authentication
- **FR-001.5**: System MUST include user role and permissions in JWT token claims
- **FR-001.6**: System MUST validate user status before allowing login (disabled accounts cannot login)

#### FR-002: Vendor Registration & Store Creation
- **FR-002.1**: System MUST allow vendor registration with complete store information in single transaction
- **FR-002.2**: System MUST require: vendor name, email, password, phone, personal address
- **FR-002.3**: System MUST require bilingual store information: name (English/Arabic), description (English/Arabic)
- **FR-002.4**: System MUST require store address with precise geolocation (latitude, longitude)
- **FR-002.5**: System MUST require at least one main category selection (multiple categories allowed)
- **FR-002.6**: System MUST require store logo image upload (max 2MB, formats: jpeg, jpg, png, webp)
- **FR-002.7**: System MUST require verification document upload (max 5MB, formats: pdf, jpeg, jpg, png)
- **FR-002.8**: System MUST store all new stores in pending status (status = false) awaiting admin approval
- **FR-002.9**: System MUST rollback entire registration if any step fails (transaction safety)
- **FR-002.10**: System MUST auto-login vendor after successful registration with JWT token

#### FR-003: Multi-Vendor Store Access
- **FR-003.1**: System MUST allow one store to have multiple vendors (one-to-many relationship)
- **FR-003.2**: System MUST allow any authorized vendor to manage their associated store
- **FR-003.3**: System MUST track which vendors belong to which store via store_id foreign key
- **FR-003.4**: System MUST support future vendor roles (owner, manager, staff) [NEEDS CLARIFICATION: role implementation timeline]

#### FR-004: Store-Category Relationship
- **FR-004.1**: System MUST allow stores to belong to multiple main categories (many-to-many)
- **FR-004.2**: System MUST prevent duplicate category assignments to same store
- **FR-004.3**: System MUST require at least one category per store
- **FR-004.4**: System MUST validate all selected categories exist and are active before registration
- **FR-004.5**: System MUST allow vendors to update category assignments [NEEDS CLARIFICATION: update flow not fully specified]

#### FR-005: Admin Store Approval Workflow
- **FR-005.1**: System MUST require admin approval before stores can accept orders
- **FR-005.2**: System MUST allow admins to view pending stores list
- **FR-005.3**: System MUST allow admins to approve stores (change status to true)
- **FR-005.4**: System MUST allow admins to reject stores [NEEDS CLARIFICATION: rejection workflow and notification]
- **FR-005.5**: System MUST notify vendors when store status changes [NEEDS CLARIFICATION: notification mechanism]

#### FR-006: Role-Based Access Control (Admin Panel)
- **FR-006.1**: System MUST implement hierarchical admin roles: super_admin, admin, manager, moderator
- **FR-006.2**: System MUST enforce 17 granular permissions across 4 categories:
  - Admin Users: view, create, update, delete, manage roles
  - Stores: view, create, update, delete, approve
  - Categories: view, create, update, delete
  - System Settings: view, update, maintenance
- **FR-006.3**: System MUST use Spatie Permission package for role/permission management
- **FR-006.4**: System MUST allow super_admin to manage all roles and permissions
- **FR-006.5**: System MUST restrict route access based on permissions (middleware-enforced)
- **FR-006.6**: System MUST allow role assignment to admin users

#### FR-007: Customer Account Management
- **FR-007.1**: System MUST allow customer registration with name, email, password
- **FR-007.2**: System MUST validate email uniqueness across customer accounts
- **FR-007.3**: System MUST hash passwords before storage
- **FR-007.4**: System MUST provide customer profile access and updates
- **FR-007.5**: System MUST provide customer dashboard [NEEDS CLARIFICATION: dashboard features not specified]

#### FR-008: Delivery Personnel Management
- **FR-008.1**: System MUST allow delivery personnel registration with vehicle information
- **FR-008.2**: System MUST capture vehicle type (bike, car, van, truck), vehicle number, license number
- **FR-008.3**: System MUST track delivery personnel availability status (boolean)
- **FR-008.4**: System MUST allow delivery personnel to toggle availability on/off
- **FR-008.5**: System MUST store delivery personnel status (admin-approved/disabled)
- **FR-008.6**: System MUST provide delivery dashboard showing assigned orders [NEEDS CLARIFICATION: order assignment logic]

#### FR-009: Main Category Management
- **FR-009.1**: System MUST allow admins to create main categories with bilingual names/descriptions
- **FR-009.2**: System MUST allow category image uploads
- **FR-009.3**: System MUST support category activation/deactivation
- **FR-009.4**: System MUST support category sorting via sort_order field
- **FR-009.5**: System MUST allow category updates and deletion (with constraints)
- **FR-009.6**: System MUST display active categories in vendor registration flow

#### FR-010: File Storage & Management
- **FR-010.1**: System MUST store uploaded files in organized directories:
  - Store logos: `storage/app/public/stores/logos/`
  - Store documents: `storage/app/public/stores/documents/`
  - Category images: [NEEDS CLARIFICATION: storage path not specified]
- **FR-010.2**: System MUST generate unique filenames to prevent conflicts
- **FR-010.3**: System MUST provide public URLs for accessing uploaded files
- **FR-010.4**: System MUST clean up files when related records are deleted [NEEDS CLARIFICATION: file cleanup strategy]

#### FR-011: Bilingual Support
- **FR-011.1**: System MUST support English and Arabic languages
- **FR-011.2**: System MUST return all user-facing messages in both languages
- **FR-011.3**: System MUST store bilingual content for stores and categories (name, description)
- **FR-011.4**: System MUST detect language preference from Accept-Language header
- **FR-011.5**: System MUST provide locale-aware accessors returning correct language variant

#### FR-012: API Response Standardization
- **FR-012.1**: System MUST return consistent JSON response format with success flag, message, data
- **FR-012.2**: System MUST use appropriate HTTP status codes (200, 201, 400, 401, 403, 404, 422, 500)
- **FR-012.3**: System MUST return validation errors in structured format
- **FR-012.4**: System MUST include guard information in authentication responses

#### FR-013: Geolocation & Mapping
- **FR-013.1**: System MUST capture store location as latitude/longitude coordinates
- **FR-013.2**: System MUST validate latitude range (-90 to 90)
- **FR-013.3**: System MUST validate longitude range (-180 to 180)
- **FR-013.4**: System MUST store coordinates with 7 decimal precision
- **FR-013.5**: System MUST support proximity-based store searches [NEEDS CLARIFICATION: search radius and algorithm]

#### FR-014: Vendor Store Management
- **FR-014.1**: System MUST allow vendors to view their store details
- **FR-014.2**: System MUST allow vendors to update store information
- **FR-014.3**: System MUST validate vendor owns/belongs to store before allowing updates
- **FR-014.4**: System MUST support logo and document updates [NEEDS CLARIFICATION: file replacement flow]
- **FR-014.5**: System MUST reload store for admin approval after certain updates [NEEDS CLARIFICATION: which updates trigger re-approval]

#### FR-015: Order Management [NEEDS CLARIFICATION: Not yet implemented]
- **FR-015.1**: System MUST allow customers to create orders
- **FR-015.2**: System MUST assign orders to delivery personnel
- **FR-015.3**: System MUST track order status through lifecycle
- **FR-015.4**: System MUST calculate delivery fees
- **FR-015.5**: System MUST process payments
- **FR-015.6**: System MUST provide order tracking

#### FR-016: Product/Menu Management [NEEDS CLARIFICATION: Not yet implemented]
- **FR-016.1**: System MUST allow vendors to create products/menu items
- **FR-016.2**: System MUST support product categorization
- **FR-016.3**: System MUST support product pricing
- **FR-016.4**: System MUST support product availability status
- **FR-016.5**: System MUST support product images

### Non-Functional Requirements

#### NFR-001: Security
- **NFR-001.1**: System MUST hash all passwords using bcrypt
- **NFR-001.2**: System MUST use HTTPS for all API communications in production
- **NFR-001.3**: System MUST validate and sanitize all user inputs
- **NFR-001.4**: System MUST protect against SQL injection via ORM parameterized queries
- **NFR-001.5**: System MUST implement CORS policies to restrict API access
- **NFR-001.6**: System MUST protect file uploads against malicious content
- **NFR-001.7**: System MUST use JWT secret key with strong randomness

#### NFR-002: Performance
- **NFR-002.1**: System SHOULD respond to API requests within 500ms under normal load [NEEDS CLARIFICATION: load definition and benchmarks]
- **NFR-002.2**: System SHOULD handle concurrent vendor registrations without data corruption
- **NFR-002.3**: System SHOULD optimize database queries with proper indexing
- **NFR-002.4**: System SHOULD use eager loading to prevent N+1 query problems

#### NFR-003: Scalability
- **NFR-003.1**: System SHOULD support horizontal scaling via stateless API design
- **NFR-003.2**: System SHOULD support multiple concurrent users per guard
- **NFR-003.3**: System SHOULD handle file storage growth through cloud storage integration [NEEDS CLARIFICATION: cloud provider preference]

#### NFR-004: Reliability
- **NFR-004.1**: System MUST use database transactions for critical operations
- **NFR-004.2**: System MUST rollback transactions on failure
- **NFR-004.3**: System MUST log all errors for debugging
- **NFR-004.4**: System SHOULD implement automated backups [NEEDS CLARIFICATION: backup frequency and retention]

#### NFR-005: Usability
- **NFR-005.1**: System MUST provide clear, actionable error messages
- **NFR-005.2**: System MUST return bilingual messages for Arabic-speaking users
- **NFR-005.3**: API MUST follow RESTful conventions for predictable endpoint structure

#### NFR-006: Maintainability
- **NFR-006.1**: Code MUST follow PSR-12 coding standards
- **NFR-006.2**: Code MUST use Laravel best practices and conventions
- **NFR-006.3**: Code SHOULD include inline documentation for complex logic
- **NFR-006.4**: Database migrations MUST be reversible

#### NFR-007: Compliance
- **NFR-007.1**: System MUST comply with data protection regulations [NEEDS CLARIFICATION: specific regulations - GDPR, local laws]
- **NFR-007.2**: System MUST allow users to access their personal data [NEEDS CLARIFICATION: data export format]
- **NFR-007.3**: System MUST allow users to request account deletion [NEEDS CLARIFICATION: deletion workflow and data retention]

### Key Entities

#### User
Represents customer accounts who browse stores and place orders
- Attributes: name, email, password, verification status, timestamps
- Relationships: has many orders, has many addresses, has many reviews

#### Vendor
Represents restaurant/store staff who manage stores and fulfill orders
- Attributes: name, email, password, phone, address, status, store_id
- Relationships: belongs to store, has roles/permissions (future), processes orders
- Notes: Multiple vendors can manage same store

#### Store
Represents a restaurant or food business on the platform
- Attributes: bilingual name, bilingual description, address, latitude, longitude, logo, document, approval status
- Relationships: has many vendors, belongs to many categories, has many products, has many orders
- Notes: Requires admin approval before going live

#### Admin
Represents platform administrators who manage system operations
- Attributes: name, email, password, phone, status
- Relationships: has roles (super_admin, admin, manager, moderator), has permissions
- Notes: Role-based access control with 17 granular permissions

#### Delivery
Represents delivery personnel who fulfill customer orders
- Attributes: name, email, password, phone, vehicle type, vehicle number, license number, availability status
- Relationships: has many deliveries/orders
- Notes: Can toggle availability; admin approval required

#### MainCategory
Represents top-level food categories for store classification
- Attributes: bilingual name, bilingual description, image, status, sort_order
- Relationships: belongs to many stores
- Notes: Used for store discovery and filtering

#### Store-Category Association (Pivot)
Represents many-to-many relationship between stores and categories
- Attributes: store_id, main_category_id, timestamps
- Notes: Unique constraint prevents duplicate assignments

#### Order [NEEDS CLARIFICATION: Not yet implemented]
Represents a customer food order
- Expected attributes: customer_id, store_id, delivery_id, items, total, status, delivery address, timestamps
- Expected relationships: belongs to user, belongs to store, belongs to delivery, has many order items

#### Product [NEEDS CLARIFICATION: Not yet implemented]
Represents food items/menu items offered by stores
- Expected attributes: store_id, name, description, price, image, availability, category
- Expected relationships: belongs to store, belongs to product category

#### OrderItem [NEEDS CLARIFICATION: Not yet implemented]
Represents individual items within an order
- Expected attributes: order_id, product_id, quantity, price, customizations
- Expected relationships: belongs to order, belongs to product

---

## Technical Context (Current Implementation)

*Note: This section documents existing implementation details for reference, not requirements*

**Stack**:
- Framework: Laravel 12
- PHP Version: 8.2+
- Authentication: Tymon JWT Auth 2.2
- Permissions: Spatie Laravel Permission 6.21
- Database: [configured via env - MySQL/PostgreSQL supported]

**Multi-Guard Configuration**:
- `api` (users table) - Customers
- `vendors` (vendors table) - Store staff
- `admins` (admins table) - Platform administrators
- `deliveries` (deliveries table) - Delivery personnel

**Current API Structure**:
- `/api/user/*` - Customer endpoints
- `/api/vendor/*` - Vendor/store endpoints
- `/api/admin/*` - Admin panel endpoints
- `/api/delivery/*` - Delivery personnel endpoints

---

## Review & Acceptance Checklist

### Content Quality
- [x] Focused on user value and business needs
- [x] Written for non-technical stakeholders (with technical context separated)
- [x] All mandatory sections completed
- [x] Implementation details segregated to Technical Context section

### Requirement Completeness
- [ ] Some [NEEDS CLARIFICATION] markers remain - **requires stakeholder input**:
  - Password reset flow implementation
  - Category deletion policy with existing stores
  - Vendor removal from store workflow
  - Delivery range/zone logic
  - Order queueing when no drivers available
  - Storage quota management
  - Notification mechanisms (email, push, SMS)
  - Order management complete workflow
  - Product/menu management system
  - Payment processing integration
  - Cloud storage provider preference
  - Backup strategy
  - Compliance regulations (GDPR, local)
  - Data export/deletion workflows
- [x] Requirements are testable and unambiguous (where specified)
- [x] Success criteria are measurable
- [x] Scope is clearly bounded (food delivery focus, extensible to other categories)
- [x] Dependencies identified (admin approval, category assignment, etc.)

### Known Gaps Requiring Clarification
1. **Order Management System**: Complete order lifecycle, payment processing, order tracking
2. **Product/Menu Management**: Full CRUD for vendor products and menu items
3. **Notification System**: Email, SMS, push notifications for status changes
4. **Search & Discovery**: Advanced filtering, proximity search, ratings/reviews
5. **Payment Integration**: Payment gateway, refunds, commission calculation
6. **Delivery Zone Management**: Geofencing, delivery radius, zone pricing
7. **Analytics & Reporting**: Sales reports, performance metrics, dashboards
8. **Customer Support**: Dispute resolution, refunds, chat support

---

## Execution Status

- [x] Platform description parsed
- [x] Key concepts extracted (multi-vendor, food delivery, role-based access)
- [x] Ambiguities marked with [NEEDS CLARIFICATION]
- [x] User scenarios defined (4 primary journeys)
- [x] Requirements generated (16 functional requirement groups, 7 non-functional)
- [x] Entities identified (9 core entities)
- [ ] Review checklist passed (pending clarifications)
- [x] Existing codebase analyzed and documented

---

**Next Steps**:
1. Review and resolve [NEEDS CLARIFICATION] items with stakeholders
2. Prioritize remaining feature development (orders, products, payments)
3. Run `/clarify` workflow to systematically address ambiguities
4. Generate implementation plan with `/plan` once spec is finalized
5. Break down into tasks with `/tasks` workflow
