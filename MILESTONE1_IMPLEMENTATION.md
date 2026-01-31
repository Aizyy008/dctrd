# Milestone 1 Implementation Guide

## Overview
This document provides a complete guide for implementing Milestone 1 features: Currency Exchange API and Unit Conversion System.

## Prerequisites

### System Requirements
- PHP 8.1+
- MySQL 5.7+ or MariaDB 10.3+
- Composer
- Laravel 9.x

### Database Setup
Ensure your database credentials are correct in `.env` file:
```env
DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=your_database
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

## Installation Steps

### Step 1: Run Migrations

```bash
cd /home/dev/Documents/noor/dctrd

# Run currency rates table migration
php artisan migrate --path=database/migrations/2026_01_31_230123_create_currency_rates_table.php

# Run user unit preferences migration
php artisan migrate --path=database/migrations/2026_01_31_230124_add_unit_preferences_to_users_table.php
```

Expected output:
```
Migrating: 2026_01_31_230123_create_currency_rates_table
Migrated:  2026_01_31_230123_create_currency_rates_table (XX.XXms)

Migrating: 2026_01_31_230124_add_unit_preferences_to_users_table
Migrated:  2026_01_31_230124_add_unit_preferences_to_users_table (XX.XXms)
```

### Step 2: Configure Currency API

1. Get API key from https://exchangeratesapi.io/
   - Sign up for free account
   - Get your API key from dashboard

2. Update `.env` file:
```env
CURRENCY_AUTO_UPDATE_ENABLED=true
EXCHANGE_API_KEY=your_api_key_here
BASE_CURRENCY=USD
```

### Step 3: Test Currency Service

```bash
# Manually update currency rates
php artisan currency:update

# Force update even if not stale
php artisan currency:update --force

# Use different base currency
php artisan currency:update --base=EUR
```

Expected output:
```
Fetching currency rates (base: USD)...
✓ Successfully updated 30 currency rates

┌──────────┬─────────┐
│ Currency │ Rate    │
├──────────┼─────────┤
│ EUR      │ 0.8500  │
│ GBP      │ 0.7300  │
│ PKR      │ 278.500 │
│ ...      │ ...     │
└──────────┴─────────┘
```

### Step 4: Set up Scheduler (Production)

Add to your server's crontab:
```bash
* * * * * cd /home/dev/Documents/noor/dctrd && php artisan schedule:run >> /dev/null 2>&1
```

This will run `currency:update` command automatically every 12 hours.

### Step 5: Run Tests

```bash
# Run all tests
php artisan test

# Run specific test suites
php artisan test --testsuite=Unit

# Run specific test file
php artisan test tests/Unit/Services/CurrencyServiceTest.php
php artisan test tests/Unit/Services/UnitConversionServiceTest.php
```

Expected output:
```
PASS  Tests\Unit\Services\CurrencyServiceTest
✓ it can store currency rates
✓ it can convert between currencies
✓ it returns same amount for same currency
✓ it can get stored rates
✓ it can format amount with currency
✓ it handles missing rates gracefully

PASS  Tests\Unit\Services\UnitConversionServiceTest
✓ it can convert length units
✓ it can convert mass units
✓ it can convert area units
✓ it can convert temperature units
✓ it returns same value for same unit
✓ it can get available units for type
✓ it can format value with unit
✓ it can convert to base unit
✓ it can convert from base unit
✓ it handles invalid type gracefully
✓ it handles invalid units gracefully

Tests:  17 passed
Time:   2.34s
```

## Usage Examples

### Currency Conversion in Code

```php
use App\Services\CurrencyService;

$currencyService = new CurrencyService();

// Convert amount
$amountInEUR = $currencyService->convert(100, 'USD', 'EUR');
echo $amountInEUR; // 85.00

// Format amount
$formatted = $currencyService->formatAmount(1234.56, 'PKR', 2);
echo $formatted; // 1,234.56 PKR

// Get current rate
$rate = $currencyService->getRate('PKR');
echo $rate; // 278.50
```

### Unit Conversion in Code

```php
use App\Services\UnitConversionService;

$unitService = new UnitConversionService();

// Convert length
$miles = $unitService->convert(10, 'km', 'mi', 'length');
echo $miles; // 6.21371

// Convert mass
$pounds = $unitService->convert(50, 'kg', 'lb', 'mass');
echo $pounds; // 110.231

// Convert temperature
$fahrenheit = $unitService->convert(25, 'C', 'F', 'temperature');
echo $fahrenheit; // 77

// Format with unit
$formatted = $unitService->formatValue(1234.5, 'km', 2);
echo $formatted; // 1,234.50 km

// Display value with user preference
$userId = auth()->id();
$displayed = $unitService->displayValue(100, 'km', 'length', $userId);
echo $displayed; // Automatically converts based on user preference
```

### Using in Blade Templates

```blade
{{-- Currency conversion --}}
@inject('currencyService', 'App\Services\CurrencyService')

<p>Price: {{ $currencyService->formatAmount($product->price, 'USD', 2) }}</p>
<p>Price in PKR: {{ $currencyService->formatAmount(
    $currencyService->convert($product->price, 'USD', 'PKR'),
    'PKR',
    2
) }}</p>

{{-- Unit conversion --}}
@inject('unitService', 'App\Services\UnitConversionService')

<p>Distance: {{ $unitService->displayValue($booking->distance, 'km', 'length', auth()->id()) }}</p>
<p>Weight: {{ $unitService->displayValue($product->weight, 'kg', 'mass', auth()->id()) }}</p>
```

### Using in Controllers

```php
namespace App\Http\Controllers;

use App\Services\CurrencyService;
use App\Services\UnitConversionService;

class ProductController extends Controller
{
    protected $currencyService;
    protected $unitService;

    public function __construct(
        CurrencyService $currencyService,
        UnitConversionService $unitService
    ) {
        $this->currencyService = $currencyService;
        $this->unitService = $unitService;
    }

    public function show($id)
    {
        $product = Product::findOrFail($id);
        
        // Convert price to user's preferred currency
        if (auth()->check() && auth()->user()->preferred_currency) {
            $product->display_price = $this->currencyService->convert(
                $product->price,
                'USD',
                auth()->user()->preferred_currency
            );
            $product->display_currency = auth()->user()->preferred_currency;
        }
        
        // Convert dimensions to user's preferred units
        if (auth()->check()) {
            $product->display_length = $this->unitService->displayValue(
                $product->length,
                'km',
                'length',
                auth()->id()
            );
        }
        
        return view('products.show', compact('product'));
    }
}
```

## Configuration Files

### config/exchange.php
- **enabled**: Enable/disable auto-update
- **primary_api**: Main API provider configuration
- **backup_api**: Fallback API configuration
- **base_currency**: Default base currency (USD)
- **update_interval**: How often to update (hours)
- **cache**: Caching configuration
- **supported_currencies**: List of currencies to fetch

### config/units.php
- **length**: Length units and conversions (km, mi, m, ft, yd)
- **mass**: Mass units and conversions (kg, lb, g, oz, ton)
- **area**: Area units and conversions (sqm, sqft, acre, hectare)
- **temperature**: Temperature units with formulas (C, F, K)
- **defaults**: Default units for each type
- **display_labels**: Human-readable labels for units

## Database Schema

### currency_rates Table
```sql
- id: bigint (primary key)
- currency_code: varchar(3) (EUR, PKR, etc.)
- base_currency: varchar(3) (default: USD)
- rate: decimal(20,8) (exchange rate)
- source: varchar(50) (API source name)
- is_fallback: boolean (is this from backup API?)
- last_updated_at: timestamp
- created_at: timestamp
- updated_at: timestamp

Indexes:
- currency_code
- (currency_code, base_currency)
- last_updated_at
```

### users Table (new columns)
```sql
- preferred_length_unit: varchar(10) (default: 'km')
- preferred_mass_unit: varchar(10) (default: 'kg')
- preferred_area_unit: varchar(10) (default: 'sqm')
- preferred_temperature_unit: varchar(10) (default: 'C')
- preferred_currency: varchar(3) (nullable)
```

## API Endpoints (if needed)

Create API endpoints for frontend:

```php
// routes/api.php

Route::get('/currency/rates', function() {
    $currencyService = new \App\Services\CurrencyService();
    return response()->json($currencyService->getStoredRates());
});

Route::post('/currency/convert', function(Request $request) {
    $currencyService = new \App\Services\CurrencyService();
    $result = $currencyService->convert(
        $request->amount,
        $request->from,
        $request->to
    );
    return response()->json(['result' => $result]);
});

Route::get('/units/available/{type}', function($type) {
    $unitService = new \App\Services\UnitConversionService();
    return response()->json($unitService->getAvailableUnits($type));
});

Route::post('/units/convert', function(Request $request) {
    $unitService = new \App\Services\UnitConversionService();
    $result = $unitService->convert(
        $request->value,
        $request->from,
        $request->to,
        $request->type
    );
    return response()->json(['result' => $result]);
});
```

## Troubleshooting

### Issue: Currency rates not updating

**Check:**
1. API key is valid in `.env`
2. Internet connection working
3. Check logs: `storage/logs/laravel.log`
4. Manually run: `php artisan currency:update --force`

### Issue: Unit conversions incorrect

**Check:**
1. Config file loaded: `php artisan config:cache`
2. Units are valid (check `config/units.php`)
3. Type parameter is correct (length, mass, area, temperature)

### Issue: User preferences not saving

**Check:**
1. Migration ran successfully
2. User model fillable array includes new fields
3. Database columns exist: `DESCRIBE users;`

### Issue: Tests failing

**Check:**
1. Test database configured in `phpunit.xml`
2. Run migrations in test environment
3. Clear cache: `php artisan config:clear`

## Performance Optimization

### Caching
Currency rates are cached for 12 hours by default. Adjust in `config/exchange.php`:
```php
'cache' => [
    'enabled' => true,
    'duration' => 720, // minutes
],
```

### Database Indexes
Ensure indexes are created by migrations:
- `currency_code` - for quick lookups
- `(currency_code, base_currency)` - for specific rate queries
- `last_updated_at` - for checking stale rates

### Redis (Optional)
For better performance, use Redis for caching:

```env
CACHE_DRIVER=redis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
```

## Security Considerations

1. **API Keys**: Never commit API keys to version control
2. **Rate Limiting**: Implement rate limiting on API endpoints
3. **Validation**: Always validate user input for conversions
4. **SQL Injection**: Use Eloquent ORM (automatically protected)
5. **XSS Protection**: Use Blade's `{{ }}` syntax (automatically escapes)

## Monitoring

### Check Currency Rates Status
```bash
# Check latest rates
php artisan tinker
>>> \App\Models\CurrencyRate::latest('last_updated_at')->first();

# Check all available currencies
>>> \App\Models\CurrencyRate::getLatestRates()->keys();

# Check if rates are stale
>>> \App\Models\CurrencyRate::areRatesStale();
```

### Check Logs
```bash
# View recent logs
tail -f storage/logs/laravel.log

# Search for currency-related logs
grep -i currency storage/logs/laravel.log
```

## Next Steps

After completing Milestone 1:
1. Test all functionality thoroughly
2. Update user interface to show unit preferences
3. Add currency selector in checkout
4. Create admin panel for managing exchange settings
5. Move to Milestone 2 (Spatial & Roles System)

## Support

If you encounter issues:
1. Check logs: `storage/logs/laravel.log`
2. Run tests: `php artisan test`
3. Clear cache: `php artisan config:clear && php artisan cache:clear`
4. Check database connections
5. Verify API keys are valid

## Checklist

- [ ] Migrations run successfully
- [ ] Currency API key configured
- [ ] Scheduler configured (crontab)
- [ ] Tests passing (17/17)
- [ ] Currency conversion working
- [ ] Unit conversion working
- [ ] User preferences saving
- [ ] Cache working
- [ ] Logs clean
- [ ] Documentation reviewed
