# RocketLMS v3.0 - Project Overview

## 📌 Project Information

**Project Name:** RocketLMS v3.0 Upgrade  
**Client:** Noor  
**Current Version:** 1.9.8  
**Target Version:** 3.0  
**Framework:** Laravel 9.x  
**PHP Version:** 8.1+  
**Database:** MySQL/MariaDB  

---

## 📋 Quick Links

- **Milestone Plan:** [PROJECT_MILESTONES.md](./PROJECT_MILESTONES.md)
- **Original Requirements:** [Noor v3.0 (Changes) Rocket LMS - Google Docs.pdf](./Noor%20v3.0%20(Changes)%20Rocket%20LMS%20-%20Google%20Docs.pdf)
- **Live Demo:** https://lms.rocket-soft.org
- **Admin Panel:** https://lms.rocket-soft.org/admin

---

## 🎯 Project Goals

Transform RocketLMS from a pure Learning Management System into a comprehensive **multi-service marketplace** supporting:

1. **Courses & Webinars** (existing)
2. **Physical & Virtual Products** (existing)
3. **Booking Services** (NEW) - Hotels, appointments, rentals, tours
4. **Advanced Geolocation** (NEW) - Nearby search with maps
5. **External Integrations** (NEW) - Calendar sync, ERP, email marketing
6. **Dynamic Pricing & Units** (NEW) - Multi-currency, measurements
7. **Enhanced Role System** (NEW) - Multi-role support with approval workflow

---

## 🏗️ Major Features (Version 3.0)

### ✅ Milestone 1: Foundation (2 weeks)
- Git setup with version control
- Upgrade v1.9 → v2.0
- Currency exchange rate API with auto-update
- Unit conversion system (km/mi, kg/lbs, etc.)

### ✅ Milestone 2: Core Features (3-4 weeks)
- Laravel Spatial + Nearby search
- Complete booking system (23 templates)
- Booking bundles
- Calendar integrations (Google, Outlook, iCal)
- Import/Export for bookings

### ✅ Milestone 3: Advanced (3 weeks)
- Modular checkout system
- Advanced search bar
- Perfex ERP integration
- MailWizz integration
- QR codes + short links
- Multi-role system with approval workflow

---

## 💻 Technology Stack

### Backend
- **Laravel 9.19** - PHP framework
- **PHP 8.1+** - Programming language
- **MySQL/MariaDB** - Database with spatial indexes
- **Redis** - Caching and queue management
- **Laravel Sanctum** - API authentication
- **JWT** - Token-based auth

### Frontend
- **Blade Templates** - Server-side rendering
- **Bootstrap 4** - CSS framework
- **jQuery 2.2.4** - JavaScript library
- **Vue.js** - For interactive components
- **Sass/SCSS** - CSS preprocessor
- **Laravel Mix / Vite** - Asset compilation

### Third-Party Integrations
- **OpenStreetMap** - Geocoding and maps
- **Google Calendar API** - Calendar sync
- **Microsoft Graph API** - Outlook sync
- **Perfex ERP** - Customer/order/invoice sync
- **MailWizz** - Email marketing
- **Premium URL Shortener** - Short links
- **ExchangeRatesAPI** - Currency rates
- **Zoom / BigBlueButton / Agora** - Video conferencing

### Development Tools
- **Git** - Version control
- **Composer** - PHP dependency manager
- **NPM** - JavaScript package manager
- **Laravel Horizon** - Queue monitoring
- **Laravel Telescope** - Debugging (optional)
- **PHPUnit** - Testing framework

---

## 📦 New Packages to Install

```bash
# Spatial queries
composer require grimzy/laravel-mysql-spatial

# Excel import/export
composer require maatwebsite/excel

# QR code generation
composer require simplesoftwareio/simple-qrcode

# Google Calendar
composer require google/apiclient

# Microsoft Graph
composer require microsoft/microsoft-graph

# HTTP client
composer require guzzlehttp/guzzle

# MailWizz SDK
composer require twisted1919/mailwizz-php-sdk
```

---

## 🗄️ Database Structure

### New Tables (60+ tables to be added)

**Currency & Units:**
- `exchange_rates`
- `unit_preferences`

**Geolocation:**
- Spatial columns added to: `users`, `webinars`, `products`, `bookings`

**Bookings:**
- `bookings`
- `booking_bundles`
- `booking_categories`
- `booking_resources`
- `booking_availability`
- `booking_reservations`
- `booking_orders`
- `booking_policies`
- `booking_templates`
- `booking_reviews`
- `booking_specifications`
- `booking_filters`

**Calendar Sync:**
- `calendar_integrations`
- `calendar_mappings`
- `calendar_logs`

**Checkout Modules:**
- `checkout_modules`
- `checkout_module_translations`
- `org_checkout_modules`
- `entity_checkout_modules`
- `order_meta`
- `order_item_meta`

**API Integrations:**
- `api_integrations`
- `api_mappings`
- `api_sync_logs`
- `api_abilities`
- `org_api_abilities`

**QR & Short Links:**
- `qr_scan_logs`
- QR fields added to: `webinars`, `products`, `bookings`, `certificates`

**Roles:**
- `user_roles` (multi-role support)
- `role_forms`
- `user_role_forms`
- `role_eligibility` (JSON config)

---

## 📁 Project Structure

```
/home/dev/Documents/noor/dctrd/
├── app/
│   ├── Http/Controllers/
│   │   ├── Admin/
│   │   │   ├── BookingController.php (NEW)
│   │   │   ├── BookingBundleController.php (NEW)
│   │   │   ├── CheckoutModuleController.php (NEW)
│   │   │   ├── ApiIntegrationController.php (NEW)
│   │   │   └── RoleRequestController.php (NEW)
│   │   ├── Panel/
│   │   │   ├── BookingController.php (NEW)
│   │   │   ├── CalendarIntegrationController.php (NEW)
│   │   │   └── ProfileRoleController.php (NEW)
│   │   └── Web/
│   │       ├── SearchController.php (UPDATE)
│   │       └── CartController.php (UPDATE)
│   ├── Models/
│   │   ├── Booking.php (NEW)
│   │   ├── BookingBundle.php (NEW)
│   │   ├── CheckoutModule.php (NEW)
│   │   └── UserRole.php (NEW)
│   ├── Services/ (NEW)
│   │   ├── ExchangeRateService.php
│   │   ├── UnitConversionService.php
│   │   ├── GeolocationService.php
│   │   ├── BookingAvailabilityService.php
│   │   ├── BookingPricingService.php
│   │   ├── CalendarSyncService.php
│   │   ├── PerfexSyncService.php
│   │   ├── MailWizzService.php
│   │   └── QrCodeService.php
│   ├── Jobs/ (NEW)
│   │   ├── SyncCalendarJob.php
│   │   └── SyncToPerfexJob.php
│   ├── Exports/ (NEW)
│   │   └── BookingsExport.php
│   └── Imports/ (NEW)
│       └── BookingsImport.php
├── config/
│   ├── exchange.php (NEW)
│   ├── units.php (NEW)
│   ├── perfex.php (NEW)
│   ├── mailwizz.php (NEW)
│   └── qrcode.php (NEW)
├── database/
│   ├── migrations/ (60+ NEW files)
│   └── seeders/
│       ├── BookingTemplatesSeeder.php (NEW)
│       ├── CheckoutModulesSeeder.php (NEW)
│       └── RolesSeeder.php (UPDATE)
├── resources/
│   ├── views/
│   │   ├── admin/
│   │   │   ├── bookings/ (NEW)
│   │   │   ├── booking_bundles/ (NEW)
│   │   │   └── users/editTabs/ (UPDATE)
│   │   └── web/default/
│   │       ├── bookings/ (NEW)
│   │       ├── booking_bundles/ (NEW)
│   │       ├── cart/modules/ (NEW)
│   │       └── panel/bookings/ (NEW)
│   └── js/
│       └── components/
│           └── AdvancedSearch.js (NEW)
├── routes/
│   ├── admin.php (UPDATE - 50+ new routes)
│   ├── panel.php (UPDATE - 30+ new routes)
│   └── web.php (UPDATE - search, QR redirect)
├── .gitignore (NEW)
├── PROJECT_MILESTONES.md (NEW)
├── PROJECT_OVERVIEW.md (THIS FILE)
└── Noor v3.0 (Changes) Rocket LMS - Google Docs.pdf
```

---

## 🚀 Getting Started

### Prerequisites
- PHP 8.1 or higher
- Composer 2.x
- Node.js 16+ & NPM
- MySQL 8.0+ or MariaDB 10.5+
- Redis (recommended)
- Git

### Initial Setup

```bash
# 1. Clone repository (after Git init)
git clone <repository-url>
cd dctrd

# 2. Install PHP dependencies
composer install

# 3. Install JavaScript dependencies
npm install

# 4. Copy environment file
cp .env.example .env

# 5. Generate application key
php artisan key:generate

# 6. Configure database in .env
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=rocket_lms
# DB_USERNAME=root
# DB_PASSWORD=

# 7. Run migrations
php artisan migrate

# 8. Seed database (optional)
php artisan db:seed

# 9. Create storage link
php artisan storage:link

# 10. Compile assets
npm run dev

# 11. Start development server
php artisan serve
```

---

## 🔑 API Keys Required

Add these to your `.env` file:

```env
# Exchange Rate API
EXCHANGE_API_KEY=your_key_here

# Google Calendar
GOOGLE_CLIENT_ID=your_client_id
GOOGLE_CLIENT_SECRET=your_client_secret

# Microsoft Outlook
MICROSOFT_CLIENT_ID=your_client_id
MICROSOFT_CLIENT_SECRET=your_client_secret

# Perfex ERP
PERFEX_API_URL=https://yourdomain.com/_ERP/api/v1
PERFEX_API_KEY=your_key_here

# MailWizz
MAILWIZZ_API_URL=https://yourmailwizz.com/api
MAILWIZZ_API_KEY=your_key_here

# Premium URL Shortener
PUS_API_URL=https://s.yourdomain.com/api
PUS_API_TOKEN=your_token_here

# OpenStreetMap (no key needed, but optional for custom setup)
OPENSTREETMAP_API_URL=https://nominatim.openstreetmap.org
```

---

## 🧪 Testing

```bash
# Run all tests
php artisan test

# Run specific test suite
php artisan test --testsuite=Feature

# Run with coverage
php artisan test --coverage

# Run specific test
php artisan test tests/Feature/BookingTest.php
```

---

## 📊 Database Migrations

### Running Migrations
```bash
# Run all pending migrations
php artisan migrate

# Run migrations with seed
php artisan migrate --seed

# Rollback last migration
php artisan migrate:rollback

# Rollback all migrations
php artisan migrate:reset

# Fresh migration (drop all + migrate)
php artisan migrate:fresh

# Check migration status
php artisan migrate:status
```

### Creating New Migrations
```bash
# Create migration
php artisan make:migration create_bookings_table

# Create migration with model
php artisan make:model Booking -m

# Create all resources
php artisan make:model Booking -mcr
# -m: migration
# -c: controller
# -r: resource controller
```

---

## 🔄 Queue Management

```bash
# Start queue worker
php artisan queue:work

# Start queue with retry
php artisan queue:work --tries=3

# Start Horizon (if installed)
php artisan horizon

# Process failed jobs
php artisan queue:retry all

# Clear failed jobs
php artisan queue:flush
```

---

## 📅 Scheduled Tasks (Cron)

Add to your server crontab:

```bash
* * * * * cd /path/to/project && php artisan schedule:run >> /dev/null 2>&1
```

Scheduled tasks include:
- Exchange rate updates (every 12 hours)
- Calendar sync (every 30 minutes)
- Cleanup old logs (daily)
- Database backup (daily)

---

## 🐛 Debugging

### Enable Debug Mode
```env
APP_DEBUG=true
APP_ENV=local
```

### Clear Cache
```bash
# Clear all cache
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Clear everything
php artisan optimize:clear
```

### Generate IDE Helper
```bash
composer require --dev barryvdh/laravel-ide-helper
php artisan ide-helper:generate
php artisan ide-helper:models
php artisan ide-helper:meta
```

---

## 📖 Documentation

- **Admin Manual:** `docs/admin-manual.pdf` (to be created)
- **API Documentation:** `docs/api-documentation.md` (to be created)
- **Database Schema:** `docs/database-schema.pdf` (to be created)
- **Deployment Guide:** `docs/deployment-guide.md` (to be created)

---

## 👥 Roles & Permissions

### Super Admin
- Full system access
- Manage all users, roles, permissions
- View all reports and analytics
- Configure integrations

### Admin
- Manage content (approve/reject)
- View reports
- Manage users (limited)

### Organization
- Manage instructors under organization
- Create/edit courses, products, bookings
- View own sales reports
- Configure checkout modules
- Manage API integrations

### Instructor
- Create/edit courses, products, bookings
- View own sales
- Manage calendar

### Student/Customer
- Purchase courses, products, bookings
- Leave reviews
- Manage profile
- View purchase history

---

## 🔐 Security Checklist

- [ ] `.env` file not committed to Git
- [ ] API keys stored securely
- [ ] Database credentials encrypted
- [ ] SSL certificate installed
- [ ] CSRF protection enabled
- [ ] XSS protection enabled
- [ ] SQL injection prevention (Eloquent)
- [ ] File upload validation
- [ ] Rate limiting configured
- [ ] Session security configured
- [ ] Two-factor authentication (optional)

---

## 📞 Support & Contact

**Developer Contact:** [Your Contact Info]  
**Project Manager:** [PM Contact Info]  
**Emergency Support:** [Emergency Contact]

**Working Hours:** 9 AM - 6 PM (Monday - Friday)  
**Response Time:** Within 24 hours  
**Emergency Response:** Within 2 hours  

---

## 📝 Change Log

### Version 3.0 (In Progress)
- Added booking system with 23 templates
- Implemented geolocation and nearby search
- Integrated external calendars (Google, Outlook)
- Added Perfex ERP integration
- Implemented QR codes and short links
- Enhanced role system with multi-role support
- Added modular checkout system
- Implemented currency exchange and unit conversion

### Version 1.9.8 (Current)
- Base RocketLMS features
- Courses and webinars
- Products (physical & virtual)
- Payment gateway integrations
- Multi-language support

---

## 🎯 Success Metrics

- [ ] All 9 tasks completed (3.1 - 3.9)
- [ ] 60+ database migrations created
- [ ] 23 booking templates functional
- [ ] 100% test coverage for critical services
- [ ] Page load time < 3 seconds
- [ ] Zero critical bugs in production
- [ ] API response time < 500ms
- [ ] User satisfaction > 90%

---

**Last Updated:** 2026-01-31  
**Version:** 1.0  
**Status:** In Development  

---

*For detailed milestone breakdown, see [PROJECT_MILESTONES.md](./PROJECT_MILESTONES.md)*
