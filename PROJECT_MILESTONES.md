# RocketLMS v3.0 - Project Milestones

**Project:** RocketLMS Upgrade to v3.0  
**Total Budget:** PKR 1,00,000 (1 Lac)  
**Total Timeline:** 8.5 weeks (2 months)  
**Start Date:** TBD  

---

## Budget & Timeline Overview

| Milestone | Features | Duration | Budget | % of Total |
|-----------|----------|----------|--------|------------|
| **Milestone 1** | Foundation & Core Updates | 2 weeks | PKR 20,000 | 20% |
| **Milestone 2** | Spatial & Roles System | 2 weeks | PKR 20,000 | 20% |
| **Milestone 3** | Booking System (Complete) | 2.5 weeks | PKR 35,000 | 35% |
| **Milestone 4** | Integrations & Advanced Features | 2 weeks | PKR 25,000 | 25% |

---

# Milestone 1: Foundation & Core Updates

**Duration:** 2 weeks  
**Budget:** PKR 20,000  
**Completion Criteria:** 40+ acceptance tests passed

## Tasks Included

### 3.1 - Paste RocketLMS v2.0 Changes
**Effort:** 1-1.5 weeks

#### Deliverables:
- [ ] Git repository setup with version control
- [ ] Complete comparison of v1.9 vs v2.0
- [ ] File-by-file migration with comments
- [ ] All v2.0 features integrated
- [ ] No conflicts with existing customizations
- [ ] Migration documented in CHANGELOG.md

#### Technical Steps:
1. **Week 1, Day 1-2:** Setup
   - Create new git branch: `upgrade/v2.0-merge`
   - Download official RocketLMS v2.0
   - Create comparison report (file diff)
   
2. **Week 1, Day 3-5:** Core Files
   - Merge `app/` directory changes
   - Update controllers, models, middleware
   - Preserve existing customizations
   
3. **Week 1, Day 6-7:** Resources & Config
   - Merge `resources/views/` updates
   - Update `config/` files
   - Merge `routes/` changes

4. **Week 2, Day 1-3:** Testing & Fixes
   - Run all existing tests
   - Fix conflicts and errors
   - Test major features (courses, products, checkout)
   
5. **Week 2, Day 4-5:** Documentation
   - Document all changes with comments
   - Create migration guide
   - Update README with new features

#### Acceptance Criteria:
- All v2.0 features working
- No breaking changes to existing features
- All migrations run successfully
- Zero critical bugs
- Git history shows detailed commit messages

---

### 3.2 - Common Units & Conversions
**Effort:** 1.5-2 weeks

## Part A: Currency Exchange API (3.2.1)

#### Deliverables:
- [ ] Currency rate API integration (Primary + Backup)
- [ ] 12-hour auto-update with caching
- [ ] Fallback mode for API failures
- [ ] Historical rates storage
- [ ] Admin toggle in Financial Settings
- [ ] Rate conversion service

#### Technical Implementation:

**Database Tables:**
```sql
currency_rates:
- id, currency_code, rate, base_currency
- last_updated_at, source (API name), is_fallback
```

**Files to Create:**
- `config/exchange.php` - API configuration
- `app/Services/CurrencyService.php` - Core service
- `app/Console/Commands/UpdateCurrencyRates.php` - Update command
- `database/migrations/xxxx_create_currency_rates_table.php`
- Update: `app/Http/Controllers/Admin/FinancialController.php`
- Update: `resources/views/admin/settings/financial/currency.blade.php`

**Week-by-Week Tasks:**

**Week 3, Day 1-2:** Setup & Config
- Install HTTP client dependencies
- Create config file with API credentials
- Setup environment variables (.env)
- Register with exchangeratesapi.io (primary)
- Setup backup API (exchangerate.host)

**Week 3, Day 3-4:** Service Layer
- Create CurrencyService class
- Implement `fetchRates()` method (primary API)
- Implement `fallbackFetch()` method (backup API)
- Implement `storeRates()` method (save to DB)
- Implement `convert($amount, $from, $to)` method
- Add caching layer (Redis/DB)

**Week 3, Day 5:** Artisan Command
- Create `currency:update` command
- Add error handling and logging
- Schedule in `app/Console/Kernel.php` (every 12 hours)
- Test command execution

**Week 3, Day 6-7:** Admin Panel Integration
- Add toggle "Auto-update currency via API" in Financial Settings
- Store setting in `settings` table
- Show last update time and source
- Add manual "Update Now" button
- Display all available currencies with rates

#### API Configuration:
```php
// .env additions
EXCHANGE_API_PRIMARY=exchangeratesapi.io
EXCHANGE_API_KEY=your_api_key_here
EXCHANGE_API_BACKUP=exchangerate.host
BASE_CURRENCY=USD
CURRENCY_UPDATE_INTERVAL=12
```

#### Acceptance Criteria:
- Rates auto-update every 12 hours
- Fallback works when primary API fails
- Historical rates stored for reporting
- Conversion accurate across all currencies
- Admin can enable/disable auto-update
- Cache improves performance (sub-100ms queries)

---

## Part B: Unit Conversion System (3.2.2)

#### Deliverables:
- [ ] User preference system (length, mass, area, temperature)
- [ ] Cookie-based preferences for guests
- [ ] Central conversion service
- [ ] Frontend dropdowns for unit selection
- [ ] Auto-conversion in displays
- [ ] Base unit storage in database

#### Technical Implementation:

**Database Changes:**
```sql
ALTER TABLE users ADD:
- preferred_length_unit (km/mi/m/ft/yd)
- preferred_mass_unit (kg/lb/g/oz/ton)
- preferred_area_unit (sqm/sqft/acre/hectare)
- preferred_temperature_unit (C/F/K)
- preferred_currency (USD/EUR/GBP...)
```

**Files to Create:**
- `config/units.php` - Unit definitions & conversion factors
- `app/Services/UnitConversionService.php` - Conversion logic
- Update: `resources/views/web/default/panel/setting/index.blade.php`
- Update: `resources/views/admin/users/editTabs/general.blade.php`
- Create: `resources/js/parts/unit-selector.js` - Frontend logic

**Week 4, Day 1-2:** Configuration & Database
- Create `config/units.php` with all units
- Define conversion factors (km→mi, kg→lb, etc.)
- Create migration for user preference fields
- Run migration and test

**Week 4, Day 3-4:** Service Layer
- Create UnitConversionService class
- Implement `convert($value, $from, $to, $type)` method
- Implement `getUserPreferredUnit($userId, $type)` method
- Implement `displayValue($value, $unit, $userId)` method
- Add caching for frequent conversions

**Week 4, Day 4-5:** Frontend - Admin
- Add unit preference fields in Admin → User Edit
- Add section "Common Units & Conversions"
- Create dropdowns for each unit type
- Save via AJAX or form submission

**Week 4, Day 5-6:** Frontend - User Panel
- Add unit preference in Panel → Settings
- Create user-friendly UI with icons
- Implement cookie storage for guests
- AJAX save for logged-in users

**Week 4, Day 7:** Integration
- Apply conversions in checkout
- Apply conversions in product/course displays
- Apply conversions in reports
- Test with different unit combinations

#### Conversion Examples:
```php
// In Blade templates
{{ displayValue($distance, 'km', auth()->user()) }}
// Output: "50 km" or "31.07 mi" based on user preference

// In Controllers
$convertedValue = UnitConversionService::convert(50, 'km', 'mi', 'length');
// Returns: 31.0686
```

#### Acceptance Criteria:
- Users can select preferred units in profile
- Guests can set preferences (stored in cookies)
- All displays auto-convert to user preference
- Database stores only base units (consistency)
- Conversion accurate (±0.01% tolerance)
- Works across checkout, reports, listings
- Existing localization fields unchanged

---

## Milestone 1 - Acceptance Testing Checklist

### Currency System Tests:
- [ ] Rates update successfully from API
- [ ] Backup API activates on primary failure
- [ ] Fallback uses last stored rates when both APIs fail
- [ ] Admin can toggle auto-update on/off
- [ ] Manual "Update Now" button works
- [ ] Historical rates visible in admin panel
- [ ] Conversion accurate for 20+ currency pairs
- [ ] Cache reduces API calls (verify logs)

### Unit Conversion Tests:
- [ ] User can save preferences in profile
- [ ] Guest preferences stored in cookies
- [ ] Preferences persist across sessions
- [ ] Length conversion works (km ↔ mi)
- [ ] Mass conversion works (kg ↔ lb)
- [ ] Area conversion works (sqm ↔ sqft)
- [ ] Temperature conversion works (C ↔ F)
- [ ] Checkout displays user's preferred currency
- [ ] Product listings show converted units
- [ ] Reports respect user preferences
- [ ] Base units stored correctly in database

### v2.0 Migration Tests:
- [ ] All existing courses load correctly
- [ ] Product pages functional
- [ ] Checkout process works end-to-end
- [ ] Payment gateways functional (test 5+ gateways)
- [ ] User registration/login works
- [ ] Admin panel accessible
- [ ] Instructor panel functional
- [ ] No JavaScript errors in console
- [ ] No PHP errors in logs
- [ ] Mobile responsive (test on 3+ devices)

### Performance Tests:
- [ ] Page load time < 2 seconds (homepage)
- [ ] Currency conversion < 100ms
- [ ] Unit conversion < 50ms
- [ ] Database queries optimized (< 50 queries/page)

### Translation Tests:
- [ ] All new features have translations (EN, AR, ES)
- [ ] Currency names translated
- [ ] Unit names translated
- [ ] Admin labels translated

---

## Milestone 1 - Deliverables Summary

**Code Deliverables:**
- v2.0 fully merged and functional
- 5 new migration files
- 2 new service classes (Currency, UnitConversion)
- 1 new artisan command
- 2 new config files
- 15+ view files updated
- Full test suite (40+ tests)

**Documentation Deliverables:**
- CHANGELOG.md updated with v2.0 changes
- API documentation for Currency service
- API documentation for Unit Conversion service
- Admin guide for currency settings
- User guide for unit preferences

**Budget Breakdown:**
- Developer hours: 80 hours @ PKR 250/hour = PKR 20,000
- API subscription: Included
- Testing & QA: Included
- **Total:** PKR 20,000

---

# Milestone 2: Spatial & Roles System

**Duration:** 2 weeks  
**Budget:** PKR 20,000  
**Completion Criteria:** 35+ acceptance tests passed

## Tasks Included

### 3.4 - Laravel Spatial + Nearby Filter
**Effort:** 1 week

#### Deliverables:
- [ ] Geolocation database schema (lat/lng/POINT)
- [ ] OpenStreetMap API integration
- [ ] Map picker UI (registration, profiles, admin)
- [ ] Address autocomplete functionality
- [ ] Nearby/Distance filter in search
- [ ] Spatial queries (Grimzy package)
- [ ] Mobile-friendly map interface

#### Technical Implementation:

**Week 1: Database & Backend**

**Day 1-2: Package Installation & Setup**
```bash
composer require grimzy/laravel-mysql-spatial
```

Files to create:
- `app/Services/GeocodingService.php` - OpenStreetMap API wrapper
- `app/Traits/HasLocation.php` - Spatial trait for models

**Day 3-4: Database Migrations**

Create migrations for adding spatial fields to:
- `users` table
- `webinars` table (courses)
- `products` table
- `vendors` table
- Future: `bookings` table (milestone 3)

Migration structure:
```php
$table->string('address_line')->nullable();
$table->string('city')->nullable();
$table->string('country')->nullable();
$table->string('postal_code')->nullable();
$table->decimal('lat', 10, 7)->nullable();
$table->decimal('lng', 10, 7)->nullable();
$table->point('location')->nullable(); // SPATIAL
$table->spatialIndex('location'); // Critical for performance
```

**Day 5-7: Service Layer**

GeocodingService methods:
- `geocode($address)` - Get lat/lng from address
- `reverseGeocode($lat, $lng)` - Get address from coordinates
- `autocomplete($query)` - Address suggestions
- Rate limiting (OpenStreetMap: 1 req/sec)
- Caching results (Redis)

**Week 2: Frontend & Integration**

**Day 1-3: Map Picker UI**

JavaScript libraries:
- Leaflet.js (free, lightweight)
- OpenStreetMap tiles

Create components:
- `resources/js/components/MapPicker.vue` or vanilla JS
- Draggable marker for precise location
- Search box with autocomplete
- "Use My Location" button (browser geolocation)

**Day 4-5: Forms Integration**

Update views:
1. **Admin - User Edit:**
   - `resources/views/admin/users/editTabs/general.blade.php`
   - Add "Location" section with map
   
2. **Panel - User Settings:**
   - `resources/views/web/default/panel/setting/index.blade.php`
   - Add "Location" tab or section
   
3. **Registration:**
   - `resources/views/web/default/auth/register.blade.php`
   - Optional location input during signup
   
4. **Product/Course Create:**
   - `resources/views/admin/store/products/create.blade.php`
   - `resources/views/admin/webinars/create.blade.php`
   - Add location section

**Day 6-7: Search & Filters**

Update SearchController:
```php
public function index(Request $request) {
    $lat = $request->get('lat');
    $lng = $request->get('lng');
    $radius = $request->get('radius', 50); // km
    
    $results = Webinar::query();
    
    if ($lat && $lng) {
        $results->selectRaw(
            '*, ST_Distance_Sphere(location, POINT(?, ?)) as distance_km',
            [$lng, $lat]
        )
        ->whereRaw(
            'ST_Distance_Sphere(location, POINT(?, ?)) <= ?',
            [$lng, $lat, $radius * 1000]
        )
        ->orderBy('distance_km');
    }
    
    return $results->paginate(20);
}
```

Search page UI:
- Add "Nearby" filter with address input
- Radius slider (5km - 100km)
- Display distance in results: "2.5 km away"
- Show on map view (optional)

#### Files to Create/Update:
- **New:**
  - `app/Services/GeocodingService.php` (250 lines)
  - `app/Traits/HasLocation.php` (100 lines)
  - `resources/js/parts/map-picker.js` (400 lines)
  - `database/migrations/xxxx_add_location_to_users.php`
  - `database/migrations/xxxx_add_location_to_webinars.php`
  - `database/migrations/xxxx_add_location_to_products.php`
  
- **Update:**
  - `app/Models/User.php` (add SpatialTrait)
  - `app/Models/Webinar.php` (add SpatialTrait)
  - `app/Models/Product.php` (add SpatialTrait)
  - `app/Http/Controllers/Web/SearchController.php` (+100 lines)
  - 10+ view files

#### Acceptance Criteria:
- Map picker works on all forms
- Address autocomplete shows suggestions
- Geocoding accurate (±10m)
- Reverse geocoding works
- Nearby search returns correct results
- Distance calculation accurate (±1%)
- Spatial queries performant (< 200ms with 10k records)
- Mobile-friendly map interface
- Existing localization fields unchanged

---

### 3.9 - Roles & Access Levels
**Effort:** 1 week

#### Deliverables:
- [ ] Multi-role user system
- [ ] Role catalog (Instructor/Organization/Customer sub-roles)
- [ ] Dynamic role addition post-registration
- [ ] Approval workflow for role requests
- [ ] Form Builder integration for role verification
- [ ] Customer group restrictions on products/courses
- [ ] Admin approval queue

#### Technical Implementation:

**Week 1: Database & Core Logic**

**Day 1-2: Database Schema**

Create tables:
```sql
user_roles:
- id, user_id, role_key, status (pending/active/rejected/revoked)
- requested_at, approved_at, approved_by, rejection_reason, notes

role_definitions:
- id, role_key, name, level (instructor/organization/customer)
- requires_approval, visible_in_registration, is_active
- permissions (JSON), is_superset_of (JSON array)

role_forms:
- role_key, form_slug, is_required

user_role_forms:
- user_id, role_key, submission_id, status

entity_customer_groups:
- entity_type (webinar/product/booking), entity_id
- allowed_customer_groups (JSON array)
```

**Day 3-4: Seeder & Role Definitions**

Create seeder: `database/seeders/RoleDefinitionsSeeder.php`

Define roles:

**Instructors (5 roles):**
- `instructor_seller` - Individual seller
- `instructor_operator_tour` - Tour operator
- `instructor_event_organizer` - Event organizer
- `instructor_agent` - Agent/Broker
- `instructor_trainer` - Trainer/Coach

**Organizations (5 roles):**
- `org_business_seller` - Business seller
- `org_business_producer` - Producer/Manufacturer
- `org_business_services` - Service provider
- `org_tour_operator` - Tour operator company
- `org_agency_ota` - Online Travel Agency

**Customers (9 roles):**
- `customer_individual` - Individual buyer
- `customer_student` - Student
- `customer_store` - Retail store
- `customer_wholesaler` - Wholesaler
- `customer_importer` - Importer
- `customer_advertiser` - Advertiser
- `customer_promoter` - Promoter/Affiliate
- `customer_travel_agency` - Travel agency
- `customer_tour_operator` - Tour operator

Superset logic:
```json
{
  "org_business_seller": ["instructor_seller", "customer_individual"],
  "instructor_seller": ["customer_individual"]
}
```

**Day 5-7: Models & Business Logic**

Create models:
- `app/Models/UserRole.php`
- `app/Models/RoleDefinition.php`

Add methods to User model:
```php
public function activeRoles() {
    return $this->hasMany(UserRole::class)
                ->where('status', 'active');
}

public function canAddRole($roleKey) {
    // Check supersets, existing roles, eligibility
}

public function hasRole($roleKey) {
    return $this->activeRoles()
                ->where('role_key', $roleKey)
                ->exists();
}
```

**Week 2: Frontend & Approval System**

**Day 1-2: Registration Flow**

Update: `resources/views/web/default/auth/register.blade.php`
- Step 1: "Choose Your Role" - dropdown (data-driven)
- Step 2: Load role-specific form
- On submit: Create account + user_role record

**Day 3-4: Profile - Add Role**

Update: `resources/views/web/default/panel/setting/index.blade.php`

Add section: "Regulatory & Badges"
- Display current roles as badges (Active/Pending)
- Button: "Add Role"
- On click: Confirmation modal
- Show dropdown of eligible roles (filtered)
- On select: Load form (Form Builder integration)
- Submit creates user_role record (pending status)

**Day 5-6: Admin Approval Queue**

Create: `app/Http/Controllers/Admin/RoleRequestController.php`

Route: `/admin/role-requests`

View: `resources/views/admin/role-requests/index.blade.php`
- List all pending role requests
- Filters: role, user, date
- View submission details inline
- Actions: Approve / Reject (with reason textarea)
- On approve: Update status, assign permissions, send notification

**Day 7: Customer Group Restrictions**

Update product/course/booking create forms:
- Add field: "Allowed Customer Groups" (multi-select)
- Options: All defined customer roles

Middleware or Controller check:
```php
// In CheckoutController
if ($item->hasCustomerGroupRestrictions()) {
    $userRoles = auth()->user()
                      ->activeRoles
                      ->pluck('role_key')
                      ->toArray();
    
    $allowedGroups = $item->allowed_customer_groups;
    
    if (!array_intersect($userRoles, $allowedGroups)) {
        return response()->json([
            'error' => 'Purchase denied. You must be: ' . 
                       implode(' or ', $allowedGroups)
        ], 403);
    }
}
```

Denial modal on frontend with clear message.

#### Files to Create/Update:
- **New:**
  - `app/Models/UserRole.php` (150 lines)
  - `app/Models/RoleDefinition.php` (100 lines)
  - `app/Http/Controllers/Admin/RoleRequestController.php` (300 lines)
  - `database/seeders/RoleDefinitionsSeeder.php` (500 lines)
  - 4 migration files
  - `resources/views/admin/role-requests/` (5 files)
  
- **Update:**
  - `app/Models/User.php` (+150 lines)
  - `resources/views/web/default/auth/register.blade.php` (+100 lines)
  - `resources/views/web/default/panel/setting/index.blade.php` (+200 lines)
  - `resources/views/admin/webinars/create.blade.php` (+50 lines)
  - `resources/views/admin/store/products/create.blade.php` (+50 lines)

#### Acceptance Criteria:
- 19 roles defined and seeded
- User can register with any role
- "Add Role" button shows only eligible roles
- Confirmation popup works
- Forms load correctly per role
- Admin can approve/reject requests
- Permissions assigned instantly on approval
- Restricted products block unauthorized users
- Clear denial message shown
- Email notifications sent (request/approved/rejected)

---

## Milestone 2 - Acceptance Testing Checklist

### Spatial System Tests:
- [ ] Map picker loads on all forms
- [ ] Address autocomplete returns suggestions
- [ ] Geocoding converts address → lat/lng accurately
- [ ] Reverse geocoding converts lat/lng → address
- [ ] User can drag marker to adjust location
- [ ] "Use My Location" button works (browser permission)
- [ ] Location saved correctly in database (POINT field)
- [ ] Nearby search returns results within radius
- [ ] Distance calculation accurate (test with known locations)
- [ ] Results sorted by distance (closest first)
- [ ] Works for users, products, courses
- [ ] Mobile map interface responsive

### Roles System Tests:
- [ ] Registration shows role dropdown
- [ ] Role-specific form loads on selection
- [ ] Account created with pending/active role
- [ ] "Add Role" button in profile works
- [ ] Confirmation modal displays correctly
- [ ] Only eligible roles shown in dropdown
- [ ] Superset roles not offered (e.g., Student can't see Org roles)
- [ ] Form Builder integration works
- [ ] Submission saved with role request
- [ ] Admin sees role requests in queue
- [ ] Approve action updates status to active
- [ ] Reject action saves reason
- [ ] User notified on approval/rejection
- [ ] Permissions assigned correctly on approval
- [ ] Product with customer group restrictions enforced
- [ ] Denial modal shows correct roles required
- [ ] Resubmit works after rejection

### Integration Tests:
- [ ] Spatial + Roles: Location-based role restrictions work
- [ ] Search with nearby + role filters combined
- [ ] Admin can filter users by location AND role
- [ ] Reports show location + role data

### Performance Tests:
- [ ] Spatial queries < 200ms (with 10k records)
- [ ] Map loads < 1 second
- [ ] Role check < 50ms
- [ ] Autocomplete responds < 300ms

### Translation Tests:
- [ ] All spatial UI translated (EN, AR, ES)
- [ ] All role names translated
- [ ] Error messages translated
- [ ] Confirmation modals translated

---

## Milestone 2 - Deliverables Summary

**Code Deliverables:**
- Spatial package integrated
- 7 new migration files
- 2 new service classes (Geocoding, Roles)
- 3 new models (UserRole, RoleDefinition, RoleForm)
- 1 new admin controller (RoleRequests)
- Map picker component (JS)
- 25+ view files updated/created
- Full test suite (35+ tests)

**Documentation Deliverables:**
- Spatial API documentation
- Roles system guide (admin)
- Roles system guide (users)
- OpenStreetMap API usage guide
- Role eligibility matrix

**Budget Breakdown:**
- Developer hours: 80 hours @ PKR 250/hour = PKR 20,000
- OpenStreetMap infrastructure: Included
- Testing & QA: Included
- **Total:** PKR 20,000

---

# Milestone 3: Booking System (Complete)

**Duration:** 2.5 weeks  
**Budget:** PKR 35,000  
**Completion Criteria:** 60+ acceptance tests passed

## Tasks Included

### 3.5.1 - Booking Module + Bundles + Import
**Effort:** 2 weeks

This is the LARGEST feature in the entire project.

#### Deliverables:
- [ ] Complete booking module (parallel to webinars/products)
- [ ] 6 booking categories (Accommodation, Services, Rental, etc.)
- [ ] Availability management (date ranges, time slots)
- [ ] Resource management (rooms, staff, vehicles)
- [ ] Pricing engine (seasonal, per-person, add-ons)
- [ ] Booking bundles
- [ ] Comments, reviews, favorites system
- [ ] Calendar views (organization/instructor)
- [ ] Import/Export functionality
- [ ] Frontend booking pages

#### Technical Implementation:

**Weeks 1-2: Database & Models (5 days)**

**Database Tables (20+ tables):**

Core tables:
```sql
bookings:
- id, creator_id, type (accommodation/service/rental/appointment)
- title, slug, description, thumbnail, video_demo
- category_id, status (active/pending/inactive/draft)
- price, capacity, duration (minutes for appointments)
- enable_waitlist, private (bool), created_at, updated_at

booking_translations:
- booking_id, locale, title, description

booking_categories:
- id, parent_id, title, icon, order

booking_category_translations:
- category_id, locale, title

booking_filters:
- id, category_id, title

booking_filter_options:
- id, filter_id, title, order

booking_specifications:
- id, booking_id, specification_id, value

booking_resources:
- id, booking_id, type (room/staff/vehicle/equipment)
- name, capacity, availability_type (shared/exclusive)

booking_availability:
- id, booking_id, resource_id
- date, start_time, end_time
- capacity, booked_count, status (available/blocked)

booking_time_slots:
- id, booking_id, day_of_week
- start_time, end_time, duration
- buffer_before, buffer_after, max_bookings

booking_rate_plans:
- id, booking_id, name, type (seasonal/dow/duration)
- price_modifier (percentage/fixed), conditions (JSON)

booking_seasons:
- id, booking_id, name, start_date, end_date
- price_multiplier, min_stay_nights

booking_policies:
- id, booking_id, type (cancellation/reschedule/no_show)
- rules (JSON), penalty_percentage

booking_variants:
- id, booking_id, name (with_breakfast, parking, guide)
- price, quantity_available

booking_sales:
- id, booking_id, buyer_id, seller_id
- start_date, end_date, persons_adults, persons_children
- selected_variants (JSON), total_price
- status (pending/confirmed/cancelled/completed)

booking_orders:
- Similar to product orders

booking_reviews:
- booking_id, user_id, rating, comment

booking_comments:
- booking_id, user_id, comment, parent_id

booking_favorites:
- user_id, booking_id

booking_features:
- Featured bookings (like featured webinars)

booking_discounts:
- Special offers for bookings

booking_bundles:
- id, creator_id, title, description, price

booking_bundle_items:
- bundle_id, booking_id, order
```

**Week 1, Day 1-3: Create Migrations**
- Create all 25+ migration files
- Define indexes, foreign keys
- Test migration up/down

**Week 1, Day 4-5: Create Models**

Main models:
- `app/Models/Booking.php` (similar to Webinar.php structure)
- `app/Models/BookingCategory.php`
- `app/Models/BookingResource.php`
- `app/Models/BookingAvailability.php`
- `app/Models/BookingSale.php`
- `app/Models/BookingBundle.php`
- 15+ additional models

Relationships:
```php
// In Booking model
public function category() {
    return $this->belongsTo(BookingCategory::class);
}

public function creator() {
    return $this->belongsTo(User::class, 'creator_id');
}

public function resources() {
    return $this->hasMany(BookingResource::class);
}

public function availability() {
    return $this->hasMany(BookingAvailability::class);
}

public function sales() {
    return $this->hasMany(BookingSale::class);
}

public function reviews() {
    return $this->hasMany(BookingReview::class);
}
```

**Week 2: Business Logic Services**

**Day 1-3: Availability Engine**

`app/Services/Booking/AvailabilityService.php`

Methods:
- `checkAvailability($bookingId, $startDate, $endDate, $persons)`
- `getAvailableSlots($bookingId, $date)`
- `blockSlot($slotId, $duration)`
- `releaseSlot($slotId)`
- `calculateCapacity($resourceId, $datetime)`

**Day 4-5: Pricing Engine**

`app/Services/Booking/PricingService.php`

Methods:
- `calculatePrice($booking, $options)` where options include:
  - start_date, end_date
  - persons (adults/children)
  - variants (add-ons)
  - promotional codes
  
Price calculation flow:
1. Base price
2. Apply seasonal multiplier
3. Apply day-of-week adjustment
4. Multiply by duration (nights/hours)
5. Multiply by persons
6. Add variant costs
7. Apply discounts/coupons
8. Calculate taxes

**Day 6-7: Policy Engine**

`app/Services/Booking/PolicyService.php`

Methods:
- `canCancel($saleId)` - Check cancellation window
- `calculateCancellationPenalty($saleId, $cancelledAt)`
- `canReschedule($saleId, $newDate)`
- `checkNoShowStatus($saleId)`

**Weeks 2-2.5: Controllers & Routes (5 days)**

**Admin Controllers:**

`app/Http/Controllers/Admin/BookingController.php`
- index() - List all bookings
- create() - Show create form
- store() - Save new booking
- edit() - Show edit form (multi-tab like webinars)
- update() - Update booking
- destroy() - Delete booking
- changeStatus() - Approve/reject/activate

Similar controllers for:
- BookingCategoryController
- BookingFilterController
- BookingSpecificationController
- BookingReviewController
- BookingBundleController
- BookingSaleController

**Panel Controllers:**

`app/Http/Controllers/Panel/BookingController.php`
- myBookings() - Instructor's bookings list
- create() - Create new booking
- store() - Save booking
- edit() - Edit booking
- myPurchases() - Student's purchased bookings
- calendar() - Calendar view
- comments() - Booking comments
- myComments() - User's comments
- favorites() - Favorite bookings
- personalNotes() - Booking notes

**Web Controllers:**

`app/Http/Controllers/Web/BookingController.php`
- index() - Browse bookings (with filters)
- show() - Booking detail page
- checkAvailability() - AJAX availability check
- addToCart() - Add booking to cart

**Routes:**

Create: `routes/admin_booking.php`
```php
Route::prefix('bookings')->group(function() {
    Route::get('/', 'BookingController@index');
    Route::get('/create', 'BookingController@create');
    Route::post('/', 'BookingController@store');
    Route::get('/{id}/edit', 'BookingController@edit');
    // ... 50+ routes
});
```

Create: `routes/panel_booking.php`
Create: `routes/web_booking.php`

Include in main route files.

**Week 2.5: Admin Views (2.5 days)**

Create: `resources/views/admin/bookings/`

**Day 1: List View**
- `list.blade.php` - Data table with filters
- Columns: ID, Thumbnail, Title, Category, Creator, Price, Status, Actions
- Actions: Edit, Delete, Change Status, View
- Bulk actions: Delete, Export

**Day 2: Create/Edit Form**

Multi-tab form (like webinar edit):

`create.blade.php` / `edit.blade.php`

Tabs:
1. **Basic Info:**
   - Title, slug, description
   - Category, subcategory
   - Thumbnail, video demo
   - Type (accommodation/service/rental/appointment)

2. **Pricing:**
   - Base price
   - Per person pricing (adults/children rates)
   - Seasonal pricing (date ranges with multipliers)
   - Day-of-week pricing
   - Minimum stay/duration

3. **Availability:**
   - Resources (add rooms/staff/vehicles)
   - Time slots (for services/appointments)
   - Blocked dates
   - Capacity settings

4. **Variants & Add-ons:**
   - Extra services (breakfast, parking, guide, etc.)
   - Each with price and quantity

5. **Specifications:**
   - Select specs (Wi-Fi, Pool, Wheelchair Access, etc.)
   - Values

6. **Policies:**
   - Cancellation policy (free until X days, penalty %)
   - Reschedule policy
   - No-show policy
   - Deposit requirements

7. **Location:** (from Milestone 2)
   - Map picker
   - Address fields

8. **SEO:**
   - Meta title, description, keywords

9. **Access:**
   - Customer groups allowed
   - Private/Public

**Day 2.5: Categories, Filters, Specifications**
- CRUD views for these entities (similar to product categories)

**Day 2.5: Settings**
- `settings.blade.php`
- General booking settings
- Commission rates for bookings
- Email templates
- Calendar sync settings

**Week 2.5 (continued): Panel Views (remaining days)**

Create: `resources/views/web/default/panel/booking/`

**My Bookings**
- `my_bookings.blade.php` - List of instructor's bookings
- Tabs: Active, Pending, Draft, Inactive
- Quick stats: Total bookings, Total revenue, Avg rating

**Calendar**
- `calendar.blade.php` - Full calendar view
- Show all bookings with time slots
- Color-coded by status
- Click to view/edit

**Week 2.5 (continued): Frontend Views (remaining days)**

Create: `resources/views/web/default/booking/`

**Booking Detail Page**

`show.blade.php` (similar to course detail page)

Sections:
- Hero section (images gallery + title + rating)
- Pricing & availability widget (sticky sidebar)
  - Date picker (check-in/check-out for accommodation)
  - Time slot picker (for services/appointments)
  - Person selector (adults/children)
  - Add-ons checkboxes
  - Price calculation live preview
  - "Check Availability" button
  - "Add to Cart" button
- Description tabs:
  - Overview
  - Specifications
  - Location (map)
  - Reviews
  - Policies (cancellation, etc.)
  - FAQs
- Related bookings
- Seller info card

**Booking List/Browse Page**

`index.blade.php`

Features:
- Filters sidebar:
  - Category
  - Price range
  - Location (nearby)
  - Specifications (Wi-Fi, Parking, etc.)
  - Rating
  - Availability (dates)
- Grid/List view toggle
- Sort: Price, Rating, Newest, Distance
- Pagination

**Booking Bundle Pages**

`bundle_show.blade.php` - Bundle detail
`bundle_index.blade.php` - Browse bundles

**Checkout Integration**

Update: `resources/views/web/default/cart/checkout.blade.php`
- Booking items show:
  - Dates/time
  - Persons
  - Add-ons
  - Policies acceptance checkbox

### Import/Export Functionality

**Final days (overlap with other tasks):**

Create: `app/Imports/BookingImport.php`
Create: `app/Exports/BookingExport.php`

Use Maatwebsite Excel package (already installed).

Import fields:
- title, description, category_id, price
- capacity, duration, type
- specifications (JSON)
- availability_rules (JSON)

Export includes all booking data + stats (total sales, revenue).

Admin buttons:
- "Import Bookings" (upload Excel)
- "Export Bookings" (download Excel with filters)

#### Acceptance Criteria (Partial - Full list in testing section):
- Admin can create/edit bookings with all fields
- Availability checked accurately
- Pricing calculated correctly (all scenarios)
- Calendar view shows all bookings
- Frontend booking page loads < 2 seconds
- Checkout process works end-to-end
- Import/Export functional
- Mobile responsive

---

### 3.5.2 - Booking Templates
**Effort:** 2 days

#### Deliverables:
- [ ] 23 booking templates
- [ ] Template selector in create form
- [ ] Auto-populate feature

**Day 1-2: Define Templates**

Create: `database/seeders/BookingTemplateSeeder.php`

Templates JSON structure:
```json
{
  "hotel": {
    "title": "Hotel Room",
    "type": "accommodation",
    "duration": 1440,
    "pricing_type": "per_night",
    "specifications": ["wifi", "tv", "ac", "breakfast"],
    "policies": {
      "cancellation": "free_until_24h"
    }
  },
  // ... 22 more templates
}
```

23 Templates:
1. Hotel Room
2. Vacation Rental
3. Spa Service
4. Hair Salon
5. Car Rental
6. Bicycle Rental
7. Doctor Appointment
8. Dentist Appointment
9. Therapy Session
10. Property Viewing
11. Restaurant Table
12. Event Venue
13. Catering Service
14. Tutoring Session
15. Fitness Training
16. Consulting Session
17. Legal Consultation
18. Financial Advisory
19. Photography Session
20. Beauty Treatment
21. Massage Therapy
22. Equipment Rental
23. Tour Package

**Day 3: Integration**

Update: `resources/views/admin/bookings/create.blade.php`
- Add dropdown: "Use Template"
- On select, AJAX load template data
- Auto-fill form fields
- User can modify before saving

---

### 3.6 - Modular Checkout System
**Effort:** 3 days

#### Deliverables:
- [ ] Checkout module definitions (Super Admin)
- [ ] Organization settings for module toggles
- [ ] Dynamic checkout rendering
- [ ] Module data storage (order_meta)

**Week 2.5: Implementation**

**Day 1: Database & Modules**

```sql
checkout_modules:
- id, name (days/hours/staff_member/persons/extras/policy)
- input_type, label, help_text, is_required, order_index

checkout_module_translations:
- module_id, locale, label, help_text

org_checkout_modules:
- org_id, module_name, enabled

entity_checkout_modules:
- entity_type, entity_id, module_id, enabled, config_override

order_item_meta:
- order_item_id, key, value (JSON)
```

Predefined modules:
1. Days (date range picker)
2. Hours (time slot selector)
3. Staff Member (dropdown)
4. Persons (adult/children counters)
5. Extra Services (checkboxes)
6. Cancellation Policy (info + checkbox)

**Day 2: Admin Panel & Organization Settings**

Admin Route: `/admin/checkout-modules`

View: Module list with:
- Toggle active/inactive
- Edit labels (translatable)
- Reorder (drag-drop)

**Organization Settings:**

Update: `/admin/users/{id}/edit` and `/panel/setting`

New tab: "Checkout Options"

Show toggle for each module:
- ☑ Days
- ☑ Hours
- ☐ Staff Member (disabled)
- ☑ Persons
- ☑ Extra Services
- Cancellation Policy

**Day 3: Checkout Rendering**

Update: `app/Http/Controllers/CartController.php`

Load enabled modules for each cart item:
```php
foreach ($cartItems as $item) {
    $seller = $item->creator;
    $enabledModules = $seller->enabledCheckoutModules();
    $item->checkoutModules = $enabledModules;
}
```

Create Blade partials:
`resources/views/web/default/cart/checkout_modules/`
- `_days.blade.php`
- `_hours.blade.php`
- `_staff_member.blade.php`
- `_persons.blade.php`
- `_extra_services.blade.php`
- `_cancellation_policy.blade.php`

Update checkout view:
```blade
@foreach($item->checkoutModules as $module)
    @include("web.default.cart.checkout_modules._{$module->name}", 
             ['module' => $module, 'item' => $item])
@endforeach
```

On submit, save to `order_item_meta`.

#### Acceptance Criteria:
- Super Admin can manage modules
- Organizations can toggle modules
- Checkout shows only enabled modules
- Module data saved to order_meta
- Modules affect pricing correctly
- Extensible (can add new modules)

---

## Milestone 3 - Acceptance Testing Checklist

### Booking System Tests (60+ tests):

**Admin - CRUD:**
- [ ] Admin can create booking (all types)
- [ ] All tabs load and save correctly
- [ ] Categories, filters, specs work
- [ ] Seasonal pricing saves correctly
- [ ] Time slots save correctly
- [ ] Resources created and assigned
- [ ] Policies save correctly
- [ ] Location integration works
- [ ] Approval workflow works
- [ ] Bulk actions functional
- [ ] Import Excel works (valid data)
- [ ] Import rejects invalid data
- [ ] Export generates correct Excel

**Panel - Instructor:**
- [ ] Instructor can create booking
- [ ] My bookings list shows all bookings
- [ ] Calendar view displays bookings
- [ ] Can edit own bookings
- [ ] Cannot edit others' bookings
- [ ] Statistics accurate (sales, revenue)
- [ ] Comments moderation works
- [ ] Favorites list works

**Frontend - User:**
- [ ] Booking list page loads with filters
- [ ] Category filter works
- [ ] Price filter works
- [ ] Location/nearby filter works (if enabled)
- [ ] Specification filters work
- [ ] Booking detail page loads
- [ ] Image gallery works
- [ ] Availability widget functional
- [ ] Date picker works (accommodation)
- [ ] Time slot picker works (services)
- [ ] Person selector works
- [ ] Add-ons checkboxes work
- [ ] Price calculates live
- [ ] "Check Availability" returns correct result
- [ ] "Add to Cart" works
- [ ] Reviews display and work
- [ ] Related bookings show

**Checkout & Purchase:**
- [ ] Booking item shows in cart
- [ ] Cart shows selected dates/times/persons
- [ ] Modular checkout modules appear
- [ ] Can select add-ons in checkout
- [ ] Pricing accurate in cart
- [ ] Can apply discount/coupon
- [ ] Commission calculated correctly
- [ ] Purchase completes successfully
- [ ] Sale record created
- [ ] Booking marked as booked
- [ ] Capacity decremented
- [ ] Confirmation email sent (with booking details)
- [ ] Invoice generated with QR code (if enabled)

**Availability Engine:**
- [ ] Accommodation: blocks dates correctly
- [ ] Services: time slots block correctly
- [ ] Capacity respected (no overbooking)
- [ ] Buffer times work (between appointments)
- [ ] Lead time enforced (can't book too soon)
- [ ] Cutoff time enforced
- [ ] Resource conflicts prevented
- [ ] Double-booking impossible

**Pricing Engine:**
- [ ] Base price correct
- [ ] Seasonal pricing applied
- [ ] Day-of-week pricing applied
- [ ] Per-person pricing correct (adults vs children)
- [ ] Duration pricing correct (nights/hours)
- [ ] Add-ons added to price
- [ ] Discounts applied
- [ ] Taxes calculated
- [ ] Total matches manual calculation

**Policy Engine:**
- [ ] Cancellation window enforced
- [ ] Penalty calculated correctly
- [ ] Reschedule rules enforced
- [ ] No-show detected
- [ ] Refunds processed per policy

**Booking Templates:**
- [ ] All 23 templates load
- [ ] Template selector works
- [ ] Auto-populate fills form correctly
- [ ] Can modify template data
- [ ] Save after template use works

**Modular Checkout:**
- [ ] Modules configurable by Super Admin
- [ ] Organizations can toggle modules
- [ ] Disabled modules don't appear
- [ ] Module data saves to order_meta
- [ ] Extensible (test adding new module)

**Integration Tests:**
- [ ] Booking + Spatial: Location shows on map
- [ ] Booking + Roles: Customer group restrictions work
- [ ] Booking + Currency: Multi-currency pricing
- [ ] Booking + Units: Distance conversions
- [ ] Booking + Calendar Sync: Events sync (if Milestone 4 done)

**Performance Tests:**
- [ ] Booking list page < 2 seconds (with 1000 bookings)
- [ ] Booking detail page < 1.5 seconds
- [ ] Availability check < 500ms
- [ ] Pricing calculation < 200ms
- [ ] Calendar view < 2 seconds (month view)
- [ ] Search with filters < 1 second

**Mobile Tests:**
- [ ] Booking list responsive
- [ ] Booking detail responsive
- [ ] Date/time pickers work on mobile
- [ ] Calendar view responsive
- [ ] Checkout modules responsive

---

## Milestone 3 - Deliverables Summary

**Code Deliverables:**
- Complete booking module (2500+ lines)
- 25+ new migration files
- 20+ new models
- 3 service classes (Availability, Pricing, Policy)
- 6 admin controllers
- 3 panel controllers
- 2 web controllers
- 50+ view files
- Import/Export functionality
- 23 booking templates
- Modular checkout system
- Full test suite (60+ tests)

**Documentation Deliverables:**
- Booking system architecture doc
- API documentation (Availability, Pricing, Policy services)
- Admin guide for bookings
- Instructor guide for bookings
- User guide for bookings
- Template customization guide
- Modular checkout developer guide

**Budget Breakdown:**
- Developer hours: 140 hours @ PKR 250/hour = PKR 35,000
- Additional testing: Included
- **Total:** PKR 35,000

---

# Milestone 4: Integrations & Advanced Features

**Duration:** 2 weeks  
**Budget:** PKR 25,000  
**Completion Criteria:** 45+ acceptance tests passed

## Tasks Included

### 3.3 - External Calendar Connections
**Effort:** 4 days

#### Deliverables:
- [ ] Google Calendar integration (OAuth)
- [ ] Microsoft Outlook integration (OAuth)
- [ ] iCalendar feed generation
- [ ] Two-way sync (outbound/inbound)
- [ ] Event templates with placeholders
- [ ] Sync logs and debugging
- [ ] Customer as attendee option

**Week 1: OAuth & Setup**

**Day 1-2: Database & Models**

```sql
calendar_integrations:
- user_id, provider (google/outlook/ical)
- access_token (encrypted), refresh_token (encrypted)
- token_expires_at, status, last_sync_at, settings (JSON)

calendar_mappings:
- user_id, rocket_event_id, rocket_event_type (booking/webinar)
- provider_event_id, provider, synced_at

calendar_logs:
- user_id, provider, action, status
- request_payload, response_payload, created_at
```

Create models:
- `app/Models/CalendarIntegration.php`
- `app/Models/CalendarMapping.php`

**Day 3-4: Google Calendar OAuth**

Use existing Spatie package (already in composer.json).

Routes:
- `/panel/calendar/google/connect` - Start OAuth
- `/panel/calendar/google/callback` - Handle callback
- `/panel/calendar/google/disconnect` - Revoke

Service: `app/Services/Calendar/GoogleCalendarService.php`

Methods:
- `authenticate($code)` - Exchange code for tokens
- `refreshToken($integration)` - Refresh expired token
- `createEvent($booking)` - Push event to Google
- `updateEvent($mapping, $booking)` - Update event
- `deleteEvent($mapping)` - Delete event
- `fetchEvents($since)` - Pull events from Google

**Day 4: Microsoft Outlook OAuth**

Similar implementation using Microsoft Graph API.

Install: `microsoft/microsoft-graph` package

Service: `app/Services/Calendar/OutlookCalendarService.php`

**Week 1 (continued): Sync Logic & UI**

**Day 5-6: Event Template System**

Settings JSON:
```json
{
  "title_template": "{CUSTOMER_NAME} - {BOOKING_STATUS}",
  "description_template": "Booking ID: {BOOKING_ID}\nStart: {START_AT}\nEnd: {END_AT}\nResource: {RESOURCE_NAME}",
  "add_customer_as_attendee": true,
  "status_filters": ["confirmed", "pending"]
}
```

Placeholders:
- {CUSTOMER_NAME}, {CUSTOMER_EMAIL}, {CUSTOMER_PHONE}
- {BOOKING_STATUS}, {BOOKING_ID}, {BOOKING_TITLE}
- {START_AT}, {END_AT}, {DURATION}
- {RESOURCE_NAME}, {SELLER_NAME}

Create: `app/Services/Calendar/TemplateService.php`
- `render($template, $booking)` - Replace placeholders

**Day 7-8: Outbound Sync (Rocket → Calendar)**

Create: `app/Jobs/SyncCalendarEvent.php`

Dispatch on:
- Booking sale created → createEvent()
- Booking sale updated → updateEvent()
- Booking sale cancelled → deleteEvent()

Logic:
```php
// On BookingSale created
dispatch(new SyncCalendarEvent($sale, 'create'));

// In Job
public function handle() {
    $integrations = $this->seller->calendarIntegrations()
                                 ->where('status', 'active')
                                 ->get();
    
    foreach ($integrations as $integration) {
        $service = CalendarServiceFactory::make($integration->provider);
        $service->createEvent($this->sale);
        
        // Log success/failure
        CalendarLog::create([...]);
    }
}
```

**Day 9: Inbound Sync (Calendar → Rocket)**

Create: `app/Jobs/PullCalendarEvents.php`

Scheduled every 15 minutes in `app/Console/Kernel.php`.

Logic:
- Fetch events updated since last sync
- Match to existing mappings
- If event deleted externally → mark booking cancelled (optional)
- If event time changed → update booking time (optional)
- Conflict resolution: Rocket is source of truth by default

**Day 10: iCalendar Feed**

Service: `app/Services/Calendar/ICalendarService.php`

Methods:
- `generateFeed($userId)` - Generate .ics file
- `signUrl($userId)` - Create secure signed URL

Route:
- `/ical/{user_id}/{signature}` - Public .ics feed URL

User can subscribe to this URL in any calendar app (Apple Calendar, Google, Outlook).

**Remaining days: UI Integration**

Update: `/admin/users/{id}/edit` and `/panel/setting`

New tab: "External Connections"

Show:
- **Google Calendar:**
  - Status: Connected/Not Connected
  - Last sync: 2 hours ago
  - Buttons: Connect / Disconnect / Force Sync
  
- **Microsoft Outlook:**
  - Same as above
  
- **iCalendar Feed:**
  - Toggle: Enable iCalendar export
  - URL: https://... (copyable)
  - Button: Download .ics

- **Settings:**
  - Event title template (textarea)
  - Event description template (textarea)
  - Add customer as attendee (checkbox)
  - Status filters (checkboxes: confirmed, pending, cancelled)
  - Debug mode (checkbox - shows detailed logs)

Create view: `resources/views/admin/users/editTabs/external_connections.blade.php`

#### Acceptance Criteria:
- OAuth flow works for Google & Outlook
- Events sync to external calendars
- Templates render correctly
- Placeholders replaced with actual data
- Customer added as attendee (if enabled)
- Two-way sync works (optional)
- iCalendar feed accessible
- Logs capture all sync attempts
- Error handling graceful (retries)

---

### 3.7 - Advanced Search Bar
**Effort:** 2 days

#### Deliverables:
- [ ] Multi-entity search (Courses, Products, Bookings)
- [ ] Advanced filters (Category, Price, Location, Rating)
- [ ] Autocomplete suggestions
- [ ] Mobile-responsive search

**Day 1: Backend Enhancement**

Update: `app/Http/Controllers/Web/SearchController.php`

New method:
```php
public function index(Request $request) {
    $type = $request->get('type', 'all'); // all/courses/products/bookings
    $query = $request->get('query');
    $category = $request->get('category');
    $priceMin = $request->get('price_min');
    $priceMax = $request->get('price_max');
    $lat = $request->get('lat');
    $lng = $request->get('lng');
    $radius = $request->get('radius', 50);
    $rating = $request->get('rating'); // minimum rating
    
    $results = [
        'courses' => [],
        'products' => [],
        'bookings' => []
    ];
    
    if ($type === 'all' || $type === 'courses') {
        $results['courses'] = Webinar::search()
            ->applyFilters($category, $priceMin, $priceMax, $rating)
            ->applyNearby($lat, $lng, $radius)
            ->where('status', 'active')
            ->paginate(20);
    }
    
    // Similar for products and bookings
    
    return view('web.default.search.results', compact('results', 'type'));
}
```

Create trait: `app/Traits/Searchable.php`

Methods:
- `scopeSearch($query, $keyword)`
- `scopeApplyFilters($query, ...)`
- `scopeApplyNearby($query, $lat, $lng, $radius)`

Add to Webinar, Product, Booking models.

**Day 2: Frontend UI**

Update: `resources/views/web/default/includes/navbar.blade.php`

New search bar structure:
```html
<div class="advanced-search">
    <div class="search-tabs">
        <button data-type="all">All</button>
        <button data-type="courses">Courses</button>
        <button data-type="products">Products</button>
        <button data-type="bookings">Bookings</button>
    </div>
    
    <input type="text" id="search-input" placeholder="Search...">
    
    <div class="search-filters-dropdown">
        <select name="category">...</select>
        <input type="number" name="price_min" placeholder="Min Price">
        <input type="number" name="price_max" placeholder="Max Price">
        <input type="text" name="location" placeholder="Location">
        <input type="range" name="radius" min="5" max="100" value="50">
        <select name="rating">...</select>
    </div>
    
    <button>Search</button>
</div>
```

JavaScript: `resources/js/parts/advanced-search.js`
- Handle tab switching
- Autocomplete as user types (debounced)
- Filter toggle
- Submit search

**Day 3: Autocomplete API**

Route: `/api/search/autocomplete?q={query}&type={type}`

Returns JSON:
```json
{
  "suggestions": [
    {"type": "course", "id": 1, "title": "...", "thumbnail": "..."},
    {"type": "product", "id": 5, "title": "...", "thumbnail": "..."},
    {"type": "booking", "id": 3, "title": "...", "thumbnail": "..."}
  ]
}
```

Controller: `app/Http/Controllers/Api/SearchController.php`

#### Acceptance Criteria:
- Search works for all entity types
- Filters apply correctly
- Nearby filter works (with spatial data)
- Autocomplete shows relevant suggestions
- Mobile responsive
- Fast (< 500ms response)

---

### 3.8 - API Integrations + QR Codes
**Effort:** 6 days (largest task in this milestone)

This combines multiple sub-features.

## Part A: ERP Integration (Perfex)

**Week 2: Implementation**

**Day 1-2: Database & Framework**

```sql
api_abilities:
- id, name (import/export/booking/dropshipping)
- description, is_active, config_schema (JSON)

org_api_abilities:
- org_id, ability_id, enabled, api_base_url
- api_key (encrypted), settings (JSON)

api_mappings:
- ability_id, rocket_entity, erp_entity, field_map (JSON)

api_sync_logs:
- org_id, ability_id, action, entity_type, entity_id
- status, request_payload, response_payload, error_message
```

Create models:
- `app/Models/ApiAbility.php`
- `app/Models/OrgApiAbility.php`

Seed predefined abilities:
1. Export Own Products (Rocket → Perfex)
2. Import Supplier Products (Perfex → Rocket)
3. Sync Bookings (Rocket → Perfex Appointly)
4. Sync Bookings (Rocket → Perfex Appointly)

**Day 3-4: Perfex ERP Service**

Create: `app/Services/ErpIntegration/PerfexErpService.php`

Methods:
- `syncCustomer($user)` - Create/update Client in Perfex
- `syncProduct($product)` - Create/update Item in Perfex
- `syncOrder($sale)` - Create Invoice in Perfex
- `syncBooking($booking)` - Create Appointment in Perfex
- `syncPayment($payment)` - Create Payment record
- `importProducts($supplierId)` - Import from supplier feed
- `exportToMarketplace($productIds)` - Push to external marketplace

Mapping examples:
```php
// Customer → Client
'rocket_to_erp' => [
    'full_name' => 'contact_firstname',
    'email' => 'contact_email',
    'mobile' => 'contact_phonenumber',
    'address' => 'address',
]

// Product → Item
'rocket_to_erp' => [
    'title' => 'description',
    'price' => 'rate',
    'quantity' => 'quantity',
    'tax' => 'tax',
]
```

**Day 5: Admin Panel - Ability Management**

Route: `/admin/api-abilities`

View: `resources/views/admin/api-abilities/index.blade.php`

Features:
- List predefined abilities
- Button: "+ New Ability"
- Form: Name, Type, Mappings (dynamic JSON editor)
- Toggle active/inactive

Assign to organizations:
- In `/admin/users/{id}/edit` → "API Integrations" tab
- Show available abilities
- Toggle on/off per organization
- Configure: API URL, API Key
- Test connection button

**Day 6: Queue Jobs**

Create:
- `app/Jobs/SyncToErp.php` - Push data to ERP
- `app/Jobs/ImportFromErp.php` - Pull data from ERP

Dispatch on:
- Sale created → SyncToErp (customer, order, payment)
- Product created → SyncToErp (item)
- Scheduled: ImportFromErp (every 6 hours for dropshipping)

## Part B: QR Codes & Short Links

**Day 7-8: Premium URL Shortener Integration**

Setup:
- Install Premium URL Shortener on subdomain: `s.yourdomain.com`
- Get API token

Create: `app/Services/QrCodeService.php`

Methods:
- `createShortLink($url, $title, $entityType, $entityId)`
- `generateQrCode($shortUrl)` - Returns image
- `getStats($shortCode)` - Analytics
- `disableLink($shortCode)`
- `refreshLink($shortCode)`

**Database:**
```sql
// Add to webinars, products, bookings, bundles
ALTER TABLE ... ADD:
- qr_enabled (boolean)
- short_url (varchar)
- short_code (varchar)
- qr_image_path (varchar)
- qr_last_refreshed_at (timestamp)

CREATE TABLE qr_scan_logs:
- entity_type, entity_id, ip, user_agent, referrer
- user_id, is_checkin, created_at
```

**Day 9-10: Business Logic Integration**

In create/edit forms, add:
- Checkbox: "Enable QR Code"
- If enabled → generate short link + QR on save
- Show QR preview with buttons:
  - Download QR
  - Copy Short Link
  - Re-Generate
  - Disable

Embed QR in:
1. **Order Confirmation Email:**
   ```blade
   @if($item->qr_enabled)
       <img src="{{ $item->qr_image_path }}" alt="QR Code">
       <p>Quick link: {{ $item->short_url }}</p>
   @endif
   ```

2. **Invoice PDF:**
   - Add QR code in footer
   - Preview & Download buttons

3. **Certificate PDF:**
   - Add QR for verification
   - Scan to verify authenticity

4. **Frontend Product/Course/Booking Pages:**
- "Share this" section with QR
- Copy short link button

**Day 11: Redirect & Logging**

Route: `/r/{code}` - Redirect handler

Controller: `app/Http/Controllers/RedirectController.php`

```php
public function handle($code) {
    // Log scan
    QrScanLog::create([
        'short_code' => $code,
        'ip' => request()->ip(),
        'user_agent' => request()->userAgent(),
        'referrer' => request()->header('referer'),
        'user_id' => auth()->id(),
    ]);
    
    // Find entity
    $entity = $this->findEntity($code);
    
    // Redirect to canonical URL
    return redirect($entity->getUrl());
}
```

**Day 12: Check-in API**

Route: `/api/checkin/{code}`

For events/bookings, allows staff to scan QR for check-in.

Validates:
- User has purchased this booking
- Check-in window is open
- Not already checked in

Marks `is_checkin = true` in scan log.

**Day 13: Analytics Dashboard**

Route: `/panel/analytics/qr-codes`

View: `resources/views/web/default/panel/analytics/qr_codes.blade.php`

Show:
- Total scans (all time, last 30 days, last 7 days)
- Unique IPs
- Top referrers
- Check-ins count
- Scan timeline graph
- Filters: Entity type, Date range, Specific entity

## Part C: Iframe Integrations

**Day 14: Menu Items & Views**

Update: `resources/views/web/default/panel/includes/sidebar.blade.php`

Add menu items:
- "CRM" (Perfex)
- "Email Marketing" (MailWizz)
- "URL Manager" (Premium URL Shortener)
- "Support" (Cloud Desk 3)

Create views:
- `resources/views/web/default/panel/integrations/perfex.blade.php`
- `resources/views/web/default/panel/integrations/mailwizz.blade.php`
- `resources/views/web/default/panel/integrations/url_shortener.blade.php`
- `resources/views/web/default/panel/integrations/cloud_desk.blade.php`

Each contains:
```html
<iframe 
    src="{{ config('integrations.perfex_url') }}?sso_token={{ auth()->user()->getSsoToken() }}" 
    width="100%" 
    height="800px" 
    frameborder="0"
    sandbox="allow-same-origin allow-scripts allow-forms"
></iframe>
```

SSO Token:
- Generate signed JWT token
- Token includes: user_id, email, role, timestamp
- External apps validate token and auto-login

#### Acceptance Criteria:
- API abilities manageable by admin
- Organizations can enable abilities
- ERP sync works (customer, product, order)
- Import products from supplier works
- Export to marketplace works
- Sync logs captured
- QR codes generate correctly
- Short links redirect correctly
- Scan logging works
- Check-in API functional
- Analytics dashboard accurate
- QR embedded in emails, invoices, certificates
- Iframe integrations load
- SSO token works

---

## Milestone 4 - Acceptance Testing Checklist

### Calendar Integration Tests:
- [ ] Google OAuth flow completes
- [ ] Outlook OAuth flow completes
- [ ] Access token stored securely (encrypted)
- [ ] Refresh token works (test with expired token)
- [ ] Event syncs to Google Calendar on booking create
- [ ] Event syncs to Outlook Calendar on booking create
- [ ] Event updates when booking updated
- [ ] Event deletes when booking cancelled
- [ ] Templates render correctly
- [ ] Placeholders replaced with actual data
- [ ] Customer added as attendee (if enabled)
- [ ] iCalendar feed accessible (.ics download)
- [ ] iCalendar URL subscribable in Apple Calendar
- [ ] Two-way sync pulls changes (if enabled)
- [ ] Sync logs visible in admin panel
- [ ] Retry works on failure

### Advanced Search Tests:
- [ ] Search bar shows entity type tabs
- [ ] Typing triggers autocomplete (< 300ms)
- [ ] Autocomplete shows relevant results
- [ ] Search returns results for courses
- [ ] Search returns results for products
- [ ] Search returns results for bookings
- [ ] Category filter works
- [ ] Price range filter works
- [ ] Location/nearby filter works
- [ ] Rating filter works
- [ ] Combined filters work (e.g., price + location)
- [ ] Mobile responsive
- [ ] Fast (< 1 second)

### ERP Integration Tests:
- [ ] Admin can create API ability
- [ ] Admin can assign ability to organization
- [ ] Organization can configure API URL & key
- [ ] Test connection button works
- [ ] Customer sync creates Client in Perfex
- [ ] Product sync creates Item in Perfex
- [ ] Order sync creates Invoice in Perfex
- [ ] Booking sync creates Appointment in Perfex
- [ ] Payment sync creates Payment in Perfex
- [ ] Import products from supplier works
- [ ] Import validates data (rejects invalid)
- [ ] Export to marketplace works
- [ ] Sync logs captured (all attempts)
- [ ] Retry on failure (exponential backoff)
- [ ] Error messages clear
- [ ] Manual sync button works

### QR Code & Short Link Tests:
- [ ] QR code generates for product
- [ ] QR code generates for course
- [ ] QR code generates for booking
- [ ] QR code generates for bundle
- [ ] Short link redirects correctly
- [ ] Scan logged in database
- [ ] QR embedded in order confirmation email
- [ ] QR embedded in invoice PDF
- [ ] QR embedded in certificate PDF
- [ ] QR displayed on product/course page
- [ ] "Copy Short Link" button works
- [ ] "Download QR" button downloads image
- [ ] "Re-Generate" creates new QR
- [ ] "Disable" revokes link
- [ ] Check-in API validates ownership
- [ ] Check-in API prevents duplicate check-ins
- [ ] Analytics dashboard shows correct stats
- [ ] Scan timeline graph renders
- [ ] Filters work (entity type, date)

### Iframe Integration Tests:
- [ ] Perfex iframe loads in panel
- [ ] MailWizz iframe loads in panel
- [ ] URL Shortener iframe loads in panel
- [ ] Cloud Desk iframe loads in panel
- [ ] SSO token authenticates user
- [ ] User data passed correctly (email, name, role)
- [ ] Iframe responsive on mobile
- [ ] No security issues (CSP headers)

### Integration Tests (Full Stack):
- [ ] Booking created → Calendar event → QR code → ERP invoice (full flow)
- [ ] Product with QR → Scanned → Logged → Analytics updated
- [ ] Order placed → ERP synced → Email sent with QR
- [ ] Certificate issued → QR embedded → Scannable → Verified

### Performance Tests:
- [ ] Calendar sync < 2 seconds
- [ ] QR generation < 1 second
- [ ] Search autocomplete < 300ms
- [ ] ERP API call < 3 seconds
- [ ] Analytics page < 2 seconds

---

## Milestone 4 - Deliverables Summary

**Code Deliverables:**
- Calendar integration (Google, Outlook, iCal)
- 3 calendar service classes
- Event template system
- Advanced search with filters
- Autocomplete API
- API abilities framework
- Perfex ERP integration service
- QR code generation service
- Premium URL Shortener integration
- Scan logging and analytics
- Check-in API
- 4 iframe integrations
- SSO token system
- 10+ new migration files
- 30+ view files
- Full test suite (45+ tests)

**Documentation Deliverables:**
- Calendar integration guide (admin)
- Calendar sync setup guide (users)
- ERP integration guide
- API mapping documentation
- QR code usage guide
- Analytics dashboard guide
- Iframe integration setup guide

**Budget Breakdown:**
- Developer hours: 100 hours @ PKR 250/hour = PKR 25,000
- Premium URL Shortener subscription: Included
- Testing & QA: Included
- **Total:** PKR 25,000

---

# Project Summary

## Total Deliverables Across All Milestones:

- **356+ existing migrations** + **60+ new migrations**
- **50+ new models**
- **20+ new service classes**
- **25+ new controllers**
- **5+ new artisan commands**
- **200+ view files** created/updated
- **15+ JavaScript components**
- **10+ config files**
- **180+ acceptance tests**
- **Comprehensive documentation** (15+ docs)

## Total Budget: PKR 1,00,000

## Total Timeline: 8.5 weeks (2 months)

## Project Success Criteria:

1. **Functionality:** All features working as specified
2. **Performance:** Pages load < 2 seconds
3. **Security:** No critical vulnerabilities
4. **Mobile:** Fully responsive on all devices
5. **Translation:** Full multi-language support (EN, AR, ES)
6. **Testing:** 180+ tests passing
7. **Documentation:** Complete admin & user guides
8. **Integration:** All third-party services connected
9. **Scalability:** Handles 10,000+ concurrent users
10. **Maintainability:** Clean, documented code

---

## Risk Management

### High-Risk Items:
1. **v2.0 Migration** - May have unexpected conflicts
   - Mitigation: Thorough testing after each file merge
   
2. **Booking System Complexity** - Largest feature with many edge cases
   - Mitigation: Break into smaller sub-tasks, test incrementally
   
3. **Third-party API Dependencies** - APIs may change or fail
   - Mitigation: Build fallback mechanisms, error handling
   
4. **Performance** - Spatial queries can be slow
   - Mitigation: Proper indexing, caching, query optimization

### Medium-Risk Items:
1. **OAuth Flows** - Complex authentication
   - Mitigation: Use well-tested packages (Spatie, Microsoft)
   
2. **Data Migration** - Existing data structure changes
   - Mitigation: Backup before migrations, rollback scripts
   
3. **Multi-currency Calculations** - Rounding and precision issues
   - Mitigation: Use decimal types, test extensively

### Low-Risk Items:
1. **UI Updates** - View changes
2. **Translation Files** - Adding keys
3. **Configuration Files** - Adding settings

---

## Post-Development Support

**Included in budget:**
- 2 weeks of bug fixes after delivery
- Documentation handover
- Code walkthrough session

**Not included (additional cost):**
- Training for admins/users (PKR 5,000/day)
- Custom feature additions (per estimate)
- Server setup & deployment (PKR 10,000)
- Ongoing maintenance (PKR 15,000/month)

---

## Payment Schedule

- **Milestone 1 Completion:** PKR 20,000 (20%)
- **Milestone 2 Completion:** PKR 20,000 (40% total)
- **Milestone 3 Completion:** PKR 35,000 (75% total)
- **Milestone 4 Completion & Handover:** PKR 25,000 (100%)

---

## Next Steps

1. **Review & Approve:** Review this document and approve the plan
2. **Environment Setup:** Provide access to:
   - Git repository
   - Development server
   - Staging server
   - Third-party API credentials
3. **Kick-off Meeting:** Schedule to discuss timeline details
4. **Milestone 1 Start:** Begin v2.0 migration

---

*Document Version: 1.0*  
*Last Updated: 2026-01-31*  
*Prepared for: Noor / RocketLMS v3.0 Upgrade*
