# 🚀 Quick Start Guide - RocketLMS v3.0

## For Developers Starting on This Project

---

## 📚 Essential Reading Order

1. **THIS FILE** - Quick start guide (5 min)
2. `PROJECT_OVERVIEW.md` - Complete project context (10 min)
3. `PROJECT_MILESTONES.md` - Detailed work breakdown (15 min)
4. `Noor v3.0 (Changes) Rocket LMS - Google Docs.pdf` - Original requirements (30 min)

---

## ⚡ 5-Minute Setup

```bash
# 1. Navigate to project
cd /home/dev/Documents/noor/dctrd

# 2. Install dependencies
composer install
npm install

# 3. Configure environment
cp .env.example .env
# Edit .env with your database credentials

# 4. Generate key
php artisan key:generate

# 5. Run migrations
php artisan migrate

# 6. Start development
php artisan serve
npm run dev
```

Access: http://localhost:8000

---

## 🔑 Test Credentials

### Admin Panel
- **URL:** http://localhost:8000/admin/login
- **Email:** admin@demo.com
- **Password:** admin

### Organization Panel
- **URL:** http://localhost:8000/panel
- **Email:** organization@demo.com
- **Password:** organization

### Instructor Panel
- **URL:** http://localhost:8000/panel
- **Email:** instructor@demo.com
- **Password:** instructor

### Student Panel
- **URL:** http://localhost:8000/panel
- **Email:** student@demo.com
- **Password:** student

---

## 📁 Key Directories to Know

```
/app/Http/Controllers/
├── Admin/          → Backend admin controllers
├── Panel/          → User panel controllers (instructors, students)
├── Web/            → Frontend controllers
└── Api/            → REST API controllers

/app/Models/        → Eloquent models
/app/Services/      → Business logic services (NEW for v3.0)
/app/Jobs/          → Queue jobs (NEW for v3.0)

/resources/views/
├── admin/          → Admin panel views
└── web/default/    → Frontend theme
    ├── panel/      → User dashboard views
    └── pages/      → Public pages

/database/migrations/  → Database schema changes
/routes/
├── admin.php       → Admin routes
├── panel.php       → Panel routes
├── web.php         → Public routes
└── api.php         → API routes

/config/            → Configuration files
```

---

## 🎯 Current Status & Your Starting Point

### ✅ Already Done (v1.9.8)
- Laravel 9 setup with PHP 8.1
- User authentication & roles
- Course management (webinars)
- Product store (physical & virtual)
- Payment gateway integrations (50+)
- Multi-language support (ar, en, es)
- Live video sessions (Zoom, BigBlueButton, Agora)

### 🚧 Your Work Starts Here (v3.0)
See `PROJECT_MILESTONES.md` for full breakdown:
- **Milestone 1:** Currency API, Unit conversion (2 weeks)
- **Milestone 2:** Bookings, Geolocation, Calendars (3-4 weeks)
- **Milestone 3:** Modular checkout, APIs, Multi-roles (3 weeks)

---

## 🔧 Development Workflow

### Daily Workflow
```bash
# 1. Pull latest changes
git pull origin development

# 2. Create feature branch
git checkout -b feature/currency-exchange

# 3. Make changes, test locally
php artisan test

# 4. Commit with clear message
git add .
git commit -m "feat: implement currency exchange API integration"

# 5. Push to remote
git push origin feature/currency-exchange

# 6. Create Pull Request on GitHub
```

### Commit Message Format
```
feat: add new feature
fix: fix bug in existing feature
docs: update documentation
style: format code
refactor: refactor code structure
test: add tests
chore: update dependencies
```

---

## 🧪 Testing Your Work

```bash
# Run all tests
php artisan test

# Run specific test file
php artisan test tests/Feature/ExchangeRateTest.php

# Run with coverage
php artisan test --coverage

# Clear cache before testing
php artisan config:clear && php artisan cache:clear
```

---

## 🐛 Common Issues & Solutions

### Issue 1: Migration Error
```bash
# Error: "Base table or view already exists"
# Solution:
php artisan migrate:rollback
php artisan migrate
```

### Issue 2: Class Not Found
```bash
# Solution:
composer dump-autoload
```

### Issue 3: Permission Denied on Storage
```bash
# Solution:
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

### Issue 4: NPM Build Errors
```bash
# Solution:
rm -rf node_modules package-lock.json
npm install
npm run dev
```

### Issue 5: Redis Connection Error
```bash
# Solution: Check .env
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

# Or disable queue in .env
QUEUE_CONNECTION=sync
```

---

## 📊 Understanding the Database

### Main Tables (Existing)
- `users` - All users (admin, instructors, students)
- `webinars` - Courses and webinars
- `products` - Physical and virtual products
- `sales` - Purchase transactions
- `orders` - Shopping orders
- `carts` - Shopping cart items
- `bundles` - Course bundles

### New Tables (v3.0 - You'll Create)
- `bookings` - Booking services
- `booking_bundles` - Booking packages
- `exchange_rates` - Currency rates
- `calendar_integrations` - Calendar sync
- `checkout_modules` - Modular checkout
- `user_roles` - Multi-role support
- `qr_scan_logs` - QR tracking
- ...and 50+ more

View full schema: `php artisan migrate:status`

---

## 🌐 API Endpoints to Know

### Existing APIs
```
GET  /api/v1/courses          → List courses
GET  /api/v1/products         → List products
POST /api/v1/auth/login       → Login
POST /api/v1/cart/add         → Add to cart
```

### New APIs (You'll Create)
```
GET  /api/v1/bookings         → List bookings
POST /api/v1/bookings/search  → Search with nearby filter
GET  /api/v1/exchange-rates   → Get currency rates
POST /api/v1/calendar/sync    → Sync calendar
```

Test with Postman: Import from `docs/postman-collection.json` (to be created)

---

## 📦 Important Packages Used

```bash
# Laravel Core
laravel/framework: ^9.19
laravel/sanctum: ^2.14
tymon/jwt-auth: ^2.0

# Database
astrotomic/laravel-translatable: ^11.8
cviebrock/eloquent-sluggable: ^9.0

# Payments (50+ gateways)
omnipay/common: ^3.0
stripe/stripe-php: ^7.0
razorpay/razorpay: ^2.0

# Media
intervention/image: ^2.7
maatwebsite/excel: ^3.1 (NEW in v3.0)
simplesoftwareio/simple-qrcode: ^4.2 (NEW in v3.0)

# Spatial (NEW in v3.0)
grimzy/laravel-mysql-spatial: ^5.0

# APIs (NEW in v3.0)
google/apiclient: ^2.0
microsoft/microsoft-graph: ^1.0
guzzlehttp/guzzle: ^7.0
```

---

## 🔍 Useful Artisan Commands

```bash
# Development
php artisan serve                    # Start dev server
php artisan tinker                   # Laravel REPL
php artisan route:list              # List all routes
php artisan migrate:status          # Check migrations

# Code Generation
php artisan make:model Booking -mcr  # Model + migration + controller
php artisan make:controller BookingController --resource
php artisan make:migration create_bookings_table
php artisan make:seeder BookingSeeder
php artisan make:request BookingRequest
php artisan make:job SyncBookingJob

# Cache Management
php artisan cache:clear             # Clear application cache
php artisan config:clear            # Clear config cache
php artisan route:clear             # Clear route cache
php artisan view:clear              # Clear compiled views
php artisan optimize:clear          # Clear all cache

# Queue
php artisan queue:work              # Start queue worker
php artisan queue:restart           # Restart workers
php artisan queue:failed            # List failed jobs

# Database
php artisan db:seed                 # Run seeders
php artisan migrate:fresh --seed    # Fresh migration + seed
```

---

## 📱 Frontend Development

### Compiling Assets
```bash
# Development (watch for changes)
npm run dev

# Production (minified)
npm run build

# Watch mode
npm run watch
```

### Main Asset Files
```
/resources/js/app.js           → Main JavaScript entry
/resources/sass/app.scss       → Main stylesheet
/resources/js/components/      → Vue components (NEW)
```

### Adding New JS Component
```javascript
// resources/js/components/AdvancedSearch.js
export default {
    mounted() {
        console.log('Advanced Search Loaded');
    }
}

// Register in app.js
import AdvancedSearch from './components/AdvancedSearch';
```

---

## 🎨 UI/UX Guidelines

### Admin Panel
- Bootstrap 4 + Custom Admin Theme
- Color scheme: Blue (#2196F3)
- Icons: Feather Icons
- Tables: DataTables.js

### Frontend
- Bootstrap 4 Responsive
- RTL Support for Arabic
- Mobile-first design
- Dark mode toggle (optional)

### Creating New Admin Page
1. Create controller in `app/Http/Controllers/Admin/`
2. Create view in `resources/views/admin/`
3. Add route in `routes/admin.php`
4. Add menu item in `resources/views/admin/includes/sidebar.blade.php`

---

## 🔐 Security Best Practices

### Always Do
✅ Use Eloquent (prevents SQL injection)
✅ Validate all inputs with FormRequest
✅ Use CSRF protection (@csrf in forms)
✅ Sanitize HTML output ({{ }} not {!! !!})
✅ Hash passwords (bcrypt/Hash facade)
✅ Use middleware for authentication
✅ Log sensitive operations

### Never Do
❌ Don't use raw SQL queries
❌ Don't commit .env file
❌ Don't expose API keys in frontend
❌ Don't trust user input
❌ Don't use MD5 for passwords
❌ Don't disable CSRF on production

---

## 📞 Who to Contact

### Technical Issues
- Check `PROJECT_OVERVIEW.md` for developer contact

### Business/Requirements Questions
- Check original PDF document
- Refer to `PROJECT_MILESTONES.md`

### Stuck? Follow This Order
1. Check documentation files
2. Search Laravel docs: https://laravel.com/docs/9.x
3. Check existing codebase for similar features
4. Ask team lead
5. Create GitHub issue (if approved)

---

## 🎯 Your First Task Checklist

- [ ] Read all documentation files
- [ ] Set up local development environment
- [ ] Login to admin panel and explore
- [ ] Browse existing code structure
- [ ] Run existing tests to ensure setup works
- [ ] Review Milestone 1 tasks in detail
- [ ] Create your first feature branch
- [ ] Ask questions if anything unclear

---

## 💡 Pro Tips

1. **Use Laravel Debugbar** (dev environment)
   ```bash
   composer require barryvdh/laravel-debugbar --dev
   ```

2. **Use Telescope for debugging** (optional)
   ```bash
   composer require laravel/telescope --dev
   php artisan telescope:install
   php artisan migrate
   ```

3. **Use IDE Helper for autocomplete**
   ```bash
   composer require --dev barryvdh/laravel-ide-helper
   php artisan ide-helper:generate
   ```

4. **Keep a local test database**
   - Create separate DB for testing
   - Use `.env.testing` file

5. **Write tests as you code**
   - Don't leave testing for the end
   - Test business logic first

6. **Comment your code**
   - Especially for complex logic
   - Use PHPDoc blocks

7. **Follow Laravel conventions**
   - Model: Booking (singular)
   - Table: bookings (plural)
   - Controller: BookingController
   - Migration: create_bookings_table

---

## 📚 Recommended Learning Resources

### Laravel
- Official docs: https://laravel.com/docs/9.x
- Laracasts: https://laracasts.com
- Laravel News: https://laravel-news.com

### APIs to Integrate
- Google Calendar: https://developers.google.com/calendar
- Microsoft Graph: https://docs.microsoft.com/graph
- OpenStreetMap: https://wiki.openstreetmap.org/wiki/API

### Spatial Queries
- Laravel MySQL Spatial: https://github.com/grimzy/laravel-mysql-spatial
- MySQL Spatial: https://dev.mysql.com/doc/refman/8.0/en/spatial-types.html

---

## ⏱️ Estimated Time per Milestone

| Milestone | Duration | Your Workload |
|-----------|----------|---------------|
| Milestone 1 | 2 weeks | ~60-70 hours |
| Milestone 2 | 3-4 weeks | ~120-150 hours |
| Milestone 3 | 3 weeks | ~90-100 hours |
| **Total** | **8-10 weeks** | **~280-320 hours** |

**Daily Time:** ~4-5 hours/day (if full-time)

---

## ✅ Daily Standup Questions

Answer these daily:
1. What did I complete yesterday?
2. What will I work on today?
3. Any blockers or issues?
4. Do I need help or clarification?

---

## 🎉 Ready to Start?

1. ✅ Environment set up
2. ✅ Documentation read
3. ✅ Test credentials work
4. ✅ First branch created
5. ✅ Team notified

**Let's build something amazing!** 🚀

---

**Last Updated:** 2026-01-31  
**Next Review:** Start of Milestone 1  
**Version:** 1.0

---

*For detailed technical specifications, see `PROJECT_OVERVIEW.md` and `PROJECT_MILESTONES.md`*
