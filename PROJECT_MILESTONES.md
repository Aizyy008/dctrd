# RocketLMS v3.0 - Project Milestones

**Project Duration:** 8-10 Weeks  
**Total Budget:** PKR 100,000  
**Start Date:** TBD  
**Client:** Noor

---

## Budget Distribution

| Milestone | Duration | Budget (PKR) | Percentage |
|-----------|----------|--------------|------------|
| Milestone 1 | 2 weeks | 30,000 | 30% |
| Milestone 2 | 3-4 weeks | 45,000 | 45% |
| Milestone 3 | 3 weeks | 25,000 | 25% |
| **TOTAL** | **8-10 weeks** | **100,000** | **100%** |

---

## MILESTONE 1: Foundation & Core Infrastructure
**Duration:** 2 Weeks  
**Budget:** PKR 30,000  
**Priority:** Critical Foundation

### Tasks

#### 1.1 Project Setup & Version Control
- Initialize Git repository with proper structure
- Implement .gitignore for Laravel project
- Create development, staging, and production branches
- Set up commit message standards
- Document current codebase (v1.9)

#### 1.2 Version Upgrade (Task 3.1)
- Analyze RocketLMS v2.0 changes
- Merge v1.9 → v2.0 updates
- Test existing features after upgrade
- Create detailed changelog with comments
- Run all migrations
- Fix compatibility issues

#### 1.3 Currency & Exchange Rates (Task 3.2.1)
- Create `config/exchange.php` configuration
- Create `exchange_rates` database table
- Implement ExchangeRateService with API integration
- Connect to exchangeratesapi.io (Primary)
- Connect to exchangerate.host (Backup)
- Implement 12-hour auto-update via Laravel Scheduler
- Add fallback mechanism for API failures
- Store historical rates in database
- Add Redis/cache layer for performance
- Admin toggle in Financial Settings

#### 1.4 Units of Measurement (Task 3.2.2)
- Create `config/units.php` configuration
- Add unit preference fields to users table
- Implement UnitConversionService
- Support: Length (km, mi, m, ft), Mass (kg, lbs), Area (sqm, sqft)
- Add unit selection in user profile (Admin & Panel)
- Apply conversions in checkout, reports, listings
- Store all values in base units (km, kg, sqm)

### Deliverables

| # | Deliverable | Format |
|---|-------------|--------|
| 1 | Git repository with full project history | GitHub/GitLab |
| 2 | RocketLMS v2.0 merged and tested | Live codebase |
| 3 | Exchange rate system fully functional | Code + DB |
| 4 | Unit conversion system operational | Code + DB |
| 5 | Admin financial settings updated | UI Screenshots |
| 6 | Cron job configured for auto-updates | Server config |
| 7 | Unit tests for conversions | PHPUnit tests |
| 8 | Documentation (setup guide) | PDF/MD file |

### ✅ Acceptance Criteria
- [ ] Git repository is clean and well-organized
- [ ] v2.0 upgrade completed without breaking existing features
- [ ] Currency rates update automatically every 12 hours
- [ ] API fallback works when primary API fails
- [ ] Users can select preferred currency and units
- [ ] All prices display in user's preferred currency
- [ ] Unit conversions work in checkout and reports
- [ ] Admin can enable/disable auto-update
- [ ] Historical rates are stored and accessible

---

## MILESTONE 2: Geolocation, Bookings & Integrations
**Duration:** 3-4 Weeks  
**Budget:** PKR 45,000  
**Priority:** High - Core Features

### 📋 Tasks

#### 2.1 Laravel Spatial + Nearby Filter (Task 3.4)
- Install grimzy/laravel-mysql-spatial package
- Create migrations for spatial columns (lat, lng, location POINT)
- Update tables: users, webinars, products, vendors, bookings
- Implement GeolocationService
- Integrate OpenStreetMap API for address autocomplete
- Add map widget with draggable marker
- Update SearchController with distance queries
- Add "Nearby" filter in search form (radius selector)
- Order results by distance from user location
- Add address fields: address_line, city, country, postal_code
- Implement in Admin user edit, Panel profile, Checkout

#### 2.2 Comprehensive Booking System (Task 3.5.1)
- Create 20+ database tables for booking system
  - bookings, booking_categories, booking_resources
  - booking_availability, booking_reservations, booking_orders
  - booking_bundles, booking_policies, booking_resources
- Create Booking models with relationships
- Create Admin controllers (CRUD + Reports)
- Create Panel controllers (My Bookings, Calendar, etc.)
- Implement BookingAvailabilityService
- Implement BookingPricingService (dynamic pricing)
- Create booking categories (Real Estate, Healthcare, Automotive, etc.)
- Add booking menu in Admin & Panel sidebars
- Support hourly/daily/weekly bookings
- Implement resource management (rooms, vehicles, staff)
- Create booking calendar view
- Implement cancellation & reschedule policies
- Add booking reviews and comments

#### 2.3 Booking Bundles
- Create booking_bundles table and model
- CRUD operations for booking bundles
- Allow multiple bookings in one bundle
- Special bundle pricing
- Bundle availability checks

#### 2.4 Import/Export for Bookings
- Install maatwebsite/excel package
- Create BookingsExport class
- Create BookingsImport class
- Admin interface for import/export
- Validation for imported data
- Support CSV and Excel formats

#### 2.5 Booking Templates (Task 3.5.2)
- Create booking_templates table
- Design 23 industry-specific templates:
  1. Hotel/Accommodation
  2. Restaurant
  3. Beauty Salon
  4. Car Rental
  5. Real Estate
  6. Healthcare Clinic
  7. Fitness Center
  8. Event Space
  9. Photography Studio
  10. Consulting Services
  11. Repair Services
  12. Tours & Travel
  13. Spa & Wellness
  14. Automotive Services
  15. Home Services
  16. Pet Services
  17. Education/Tutoring
  18. Legal Consultation
  19. Financial Advisory
  20. Equipment Rental
  21. Venue Booking
  22. Appointment Services
  23. General Services
- Create Blade templates for each type
- Dynamic field rendering based on template
- Template seeder with default configurations

#### 2.6 External Calendar Connections (Task 3.3)
- Install Google Calendar & Microsoft Graph APIs
- Create calendar_integrations table
- Create calendar_mappings table
- Create calendar_logs table
- Implement OAuth flows for Google & Outlook
- Create CalendarSyncService
- Two-way sync: Rocket ↔ External Calendar
- Event templates with placeholders
- iCalendar export (.ics download)
- Add External Connections tab in Admin & Panel
- Create SyncCalendarJob for queued sync
- Debug logging for troubleshooting

### 📦 Deliverables

| # | Deliverable | Format |
|---|-------------|--------|
| 1 | Geolocation system with map interface | Code + UI |
| 2 | Nearby search filter operational | Live feature |
| 3 | Complete booking module (Admin + Panel + Frontend) | Full CRUD |
| 4 | 23 booking templates implemented | Blade files |
| 5 | Booking calendar with availability | Interactive UI |
| 6 | Import/Export functionality for bookings | Excel/CSV |
| 7 | Google Calendar integration working | Live sync |
| 8 | Microsoft Outlook integration working | Live sync |
| 9 | iCalendar export (.ics) functional | Download link |
| 10 | Booking policies (cancel/reschedule) | Business logic |
| 11 | Resource management system | Code + DB |
| 12 | Database schema with 25+ new tables | SQL migrations |
| 13 | API documentation for bookings | Postman/MD |

### ✅ Acceptance Criteria
- [ ] Users can search courses/products/bookings by distance
- [ ] Map displays correctly on user/product/course forms
- [ ] Address autocomplete works with OpenStreetMap
- [ ] Bookings can be created with date/time slots
- [ ] Booking calendar shows availability accurately
- [ ] All 23 templates render correctly
- [ ] Import/export works without data loss
- [ ] Google Calendar syncs bookings automatically
- [ ] Outlook Calendar syncs bookings automatically
- [ ] Cancellation policies are enforced
- [ ] Resource conflicts are prevented (double-booking)
- [ ] Booking notifications sent via email/SMS
- [ ] Admin can view booking reports

---

## MILESTONE 3: Advanced Features & Polish
**Duration:** 3 Weeks  
**Budget:** PKR 25,000  
**Priority:** Medium - Enhancement & Completion

### 📋 Tasks

#### 3.1 Modular Checkout System (Task 3.6)
- Create checkout_modules table
- Create checkout_module_translations table
- Create org_checkout_modules table
- Create entity_checkout_modules table
- Create order_meta and order_item_meta tables
- Implement CheckoutModule model
- Admin CRUD for checkout modules
- Predefined modules:
  - Days (date picker)
  - Hours (time slot selector)
  - Staff Member (dropdown)
  - Persons + Children (quantity with age)
  - Extra Services (checkboxes with price)
  - Cancellation Policy (info + checkbox)
- Create Blade partials for each module
- Organization/Instructor can enable/disable modules
- Dynamic rendering on checkout page
- Validation for required modules
- Store module data in order_meta

#### 3.2 Advanced Search Bar (Task 3.7)
- Update SearchController with advanced filters
- Add category dropdown in search
- Add type filter (Courses, Products, Bookings, Bundles)
- Integrate nearby/distance filter
- AJAX autocomplete functionality
- Create AdvancedSearch.js component
- Update navbar with enhanced search UI
- Search results page with filter panel
- Sort by: relevance, distance, price, rating

#### 3.3 API Integrations (Task 3.8)
- Perfex ERP Integration
  - Create api_integrations table
  - Create api_mappings table
  - Create api_sync_logs table
  - Implement PerfexSyncService
  - Map: Customer → Client, Order → Invoice, Payment → Payment
  - Create SyncToPerfexJob
  - Admin UI for API settings
  - Real-time sync via webhooks
- MailWizz Integration
  - Install mailwizz-php-sdk
  - Create MailWizzService
  - Sync user subscriptions
  - Campaign management
- QR Codes & Short Links
  - Install simplesoftwareio/simple-qrcode
  - Add qr_enabled, short_url, qr_image_path to models
  - Create qr_scan_logs table
  - Implement QrCodeService
  - Premium URL Shortener integration
  - Generate QR for products, courses, bookings, certificates
  - QR embed in invoices and emails
  - Track scans and check-ins
  - Analytics dashboard for QR scans
- Dynamic Import/Export Abilities
  - Create api_abilities table
  - Create org_api_abilities table
  - Admin can create "+ New Ability"
  - Configurable field mappings (JSON)
  - Enable/disable per vendor
  - Export/Import classes for extensibility

#### 3.4 Multi-Role System (Task 3.9)
- Expand role system with sub-roles:
  - **Instructor:** Seller, Operator, Event Organizer, Agent
  - **Organization:** Business Seller, Producer, Services, Tour Operator, Agency
  - **Customer:** Individual, Student, Store, Wholesaler, Importer, Advertiser, Promoter
- Create user_roles table (multi-role support)
- Create role_forms mapping table
- Create user_role_forms table
- Update roles table with new fields
- Implement ProfileRoleController
- "Add Role" button in user profile
- Confirmation popup for role request
- Form-based role approval workflow
- Admin review queue for role requests
- Role eligibility rules (JSON config)
- Customer group-based access control
- Restrict products/courses by customer group
- Denial message on unauthorized purchase
- Role badges in profile
- Multi-role permissions

#### 3.5 UI/UX Enhancements
- Update admin menu structure
- Update panel menu structure
- Add Featured Products/Bookings sections
- Improve search results layout
- Mobile responsive design for new features
- Add tooltips and help texts
- Improve form validation messages
- Add loading spinners for async operations

#### 3.6 Testing & QA
- Unit tests for critical services
- Integration tests for API flows
- End-to-end testing for booking flow
- Load testing for spatial queries
- Security audit (XSS, CSRF, SQL injection)
- Browser compatibility testing
- Mobile device testing

#### 3.7 Documentation & Training
- Admin user manual
- Vendor/Instructor guide
- API documentation (Postman collection)
- Database schema diagram
- Deployment guide
- Video tutorials (screen recordings)

### 📦 Deliverables

| # | Deliverable | Format |
|---|-------------|--------|
| 1 | Modular checkout system operational | Live feature |
| 2 | Advanced search with filters | Live feature |
| 3 | Perfex ERP integration working | Live sync |
| 4 | MailWizz integration working | Email campaigns |
| 5 | QR codes generated for all entities | Images + URLs |
| 6 | Short links working with analytics | Dashboard |
| 7 | Multi-role system fully functional | Code + UI |
| 8 | Role request approval workflow | Admin panel |
| 9 | Customer group access control | Middleware |
| 10 | Complete test suite | PHPUnit tests |
| 11 | Admin user manual | PDF (30+ pages) |
| 12 | API documentation | Postman + MD |
| 13 | Video tutorials | MP4 files (5-10 videos) |
| 14 | Final codebase on GitHub | Repository |
| 15 | Production deployment | Live server |

### ✅ Acceptance Criteria
- [ ] Checkout modules render dynamically per vendor settings
- [ ] Order meta stores all module data correctly
- [ ] Advanced search returns relevant results
- [ ] Perfex ERP syncs customers and orders
- [ ] QR codes are scannable and trackable
- [ ] Short links redirect correctly
- [ ] Users can request multiple roles
- [ ] Admin can approve/reject role requests
- [ ] Role-based access control works on checkout
- [ ] Unauthorized users see denial message
- [ ] All tests pass successfully
- [ ] Documentation is clear and comprehensive
- [ ] System is deployed to production
- [ ] Performance is acceptable (page load < 3s)
- [ ] No critical bugs remain

---

## Timeline Overview

```
Week 1-2:   Milestone 1 (Foundation)
Week 3-6:   Milestone 2 (Bookings & Integrations)
Week 7-9:   Milestone 3 (Advanced Features)
Week 10:    Buffer & Final QA
```

---

## Payment Schedule

| Payment | Milestone | Amount (PKR) | Timing |
|---------|-----------|--------------|--------|
| 1st | Upon Milestone 1 completion | 30,000 | End of Week 2 |
| 2nd | Upon Milestone 2 completion | 45,000 | End of Week 6 |
| 3rd | Upon Milestone 3 & final delivery | 25,000 | End of Week 9 |
| **TOTAL** | | **100,000** | |

---

## Change Request Process

- Minor changes within scope: No additional cost
- Major scope changes: Require new quote and timeline adjustment
- Bug fixes: Covered for 30 days post-delivery
- Emergency support: PKR 5,000/day (if needed)

---

## Risk Management

| Risk | Impact | Mitigation |
|------|--------|------------|
| API downtime (Exchange/Maps) | Medium | Fallback APIs + caching |
| Database migration issues | High | Backup before each migration |
| Third-party integration failures | Medium | Error handling + retries |
| Timeline delays | Medium | 1-week buffer built in |
| Scope creep | High | Strict change management |

---

## Technology Stack

- **Backend:** Laravel 9, PHP 8.1
- **Database:** MySQL/MariaDB with spatial indexes
- **Frontend:** Blade, Bootstrap 4, jQuery, Vue.js (components)
- **APIs:** OpenStreetMap, Google Calendar, Microsoft Graph, Perfex, MailWizz
- **Tools:** Git, Composer, NPM, Redis, Laravel Horizon
- **Packages:** 
  - grimzy/laravel-mysql-spatial
  - maatwebsite/excel
  - simplesoftwareio/simple-qrcode
  - google/apiclient
  - microsoft/microsoft-graph
  - guzzlehttp/guzzle

---

## 📞 Communication & Reporting

- **Daily Updates:** Progress via chat/email
- **Weekly Meetings:** Video call for review and planning
- **Demo Sessions:** End of each milestone
- **Issue Tracking:** GitHub Issues or Trello
- **Code Reviews:** Pull requests for major features
- **Documentation:** Maintained throughout development

---

## Final Delivery Checklist

- [ ] All features from 3 milestones completed
- [ ] Code deployed to production server
- [ ] Database migrations applied
- [ ] Cron jobs configured
- [ ] Environment variables set
- [ ] SSL certificate installed
- [ ] Backup system configured
- [ ] All tests passing
- [ ] Documentation delivered
- [ ] Training videos provided
- [ ] Admin trained on new features
- [ ] 30-day support period starts
- [ ] Final payment received

---

## Notes

1. **Database Backups:** Automated daily backups required before starting
2. **Staging Environment:** Recommended for testing before production
3. **API Keys:** Client to provide API keys for external services
4. **Server Access:** SSH and database access required
5. **Third-party Costs:** External API subscription costs not included in budget
6. **Post-Launch Support:** 30 days bug fixes included, then separate maintenance contract

---

**Document Version:** 1.0  
**Last Updated:** 2026-01-31  
**Prepared By:** Development Team  
**Approved By:** [Client Name]

---

*This milestone plan is subject to change based on client requirements and technical discoveries during development. Any major changes will be communicated and approved before implementation.*
