# 🚀 Quick Start Guide - Milestone 1

## ⚡ 5-Minute Setup

### Step 1: Get API Key (2 minutes)
1. Visit https://exchangeratesapi.io/
2. Click "Get Free API Key"
3. Sign up (free tier: 250 requests/month)
4. Copy your API key

### Step 2: Configure Environment (1 minute)
Edit `.env`:
```env
EXCHANGE_RATES_API_KEY=your_api_key_here
```

### Step 3: Run Initial Update (1 minute)
```bash
php artisan exchange:update
```

### Step 4: Setup Cron (1 minute)
```bash
# Linux/Mac
crontab -e
# Add: * * * * * cd /path/to/project && php artisan schedule:run >> /dev/null 2>&1

# Windows (PowerShell as Admin)
schtasks /create /tn "Laravel" /tr "php C:\path\to\project\artisan schedule:run" /sc minute /mo 1
```

### Step 5: Verify (30 seconds)
```bash
php artisan schedule:list
```

✅ **Done!** Your system is now updating exchange rates automatically every 12 hours.

---

## 📖 Usage Examples

### In Blade Templates:
```blade
{{-- Display price in user's currency --}}
<p>Price: {{ formatCurrency($price) }}</p>

{{-- Display distance --}}
<p>Distance: {{ formatUnit($distance, 'length', 'km') }}</p>
```

### In Controllers:
```php
// Convert currency
$converted = convertCurrency(100, 'USD');

// Convert units
$miles = convertUnit(10, 'length', 'km');
```

### Admin API:
```javascript
// Manual update
POST /admin/exchange-rates/update

// Get settings
GET /admin/exchange-rates/settings
```

---

## 🔍 Quick Checks

### Is it working?
```bash
# Check scheduler
php artisan schedule:list

# Check database
php artisan tinker
>>> App\Models\ExchangeRate::count()

# Check last update
>>> App\Models\ExchangeRate::latest('fetched_at')->first()
```

### Troubleshooting:
```bash
# Clear caches
php artisan config:clear
php artisan cache:clear

# Check logs
tail -f storage/logs/laravel.log

# Manual update
php artisan exchange:update
```

---

## 📚 Full Documentation

- **Complete Setup**: `MILESTONE_1_SETUP_GUIDE.md`
- **Technical Docs**: `CODEBASE_DOCUMENTATION.md`
- **Changes**: `CHANGELOG.md`
- **Summary**: `IMPLEMENTATION_SUMMARY.md`

---

## ✅ Checklist

- [ ] API key added to `.env`
- [ ] Migrations run successfully
- [ ] Initial exchange rate update completed
- [ ] Cron job configured
- [ ] Scheduler verified
- [ ] Caches cleared

---

**Need Help?** Check `MILESTONE_1_SETUP_GUIDE.md` for detailed instructions.
