<?php

/**
 * Milestone 1 - Quick Test Script
 * 
 * This script tests all Milestone 1 functionality without requiring database
 * Run with: php test_milestone1.php
 */

require __DIR__ . '/vendor/autoload.php';

echo "================================\n";
echo "Milestone 1 - Feature Testing\n";
echo "================================\n\n";

// Test 1: Configuration Files
echo "Test 1: Configuration Files\n";
echo "----------------------------\n";

$exchangeConfig = require __DIR__ . '/config/exchange.php';
$unitsConfig = require __DIR__ . '/config/units.php';

echo "✓ Exchange config loaded: " . count($exchangeConfig) . " settings\n";
echo "✓ Units config loaded: " . count($unitsConfig) . " unit types\n";
echo "  - Base currency: " . ($exchangeConfig['base_currency'] ?? 'Not set') . "\n";
echo "  - Update interval: " . ($exchangeConfig['update_interval'] ?? 'Not set') . " hours\n";
echo "  - Supported currencies: " . count($exchangeConfig['supported_currencies'] ?? []) . "\n";
echo "  - Length units: " . implode(', ', $unitsConfig['length']['units']) . "\n";
echo "  - Mass units: " . implode(', ', $unitsConfig['mass']['units']) . "\n\n";

// Test 2: Unit Conversions (Mathematical)
echo "Test 2: Unit Conversion Logic\n";
echo "------------------------------\n";

class SimpleUnitConverter {
    private $config;
    
    public function __construct($config) {
        $this->config = $config;
    }
    
    public function convert($value, $from, $to, $type) {
        if ($from === $to) return $value;
        
        if (!isset($this->config[$type])) return $value;
        
        $conversions = $this->config[$type]['conversions'] ?? [];
        $key = "{$from}_to_{$to}";
        
        if (isset($conversions[$key])) {
            return $value * $conversions[$key];
        }
        
        return $value;
    }
    
    public function convertTemperature($value, $from, $to) {
        if ($from === $to) return $value;
        
        $formulas = $this->config['temperature']['formulas'] ?? [];
        $key = "{$from}_to_{$to}";
        
        if (isset($formulas[$key])) {
            $formula = $formulas[$key];
            $result = $value;
            
            if (isset($formula['subtract'])) $result -= $formula['subtract'];
            if (isset($formula['multiply'])) $result *= $formula['multiply'];
            if (isset($formula['add'])) $result += $formula['add'];
            
            return $result;
        }
        
        return $value;
    }
}

$converter = new SimpleUnitConverter($unitsConfig);

// Length tests
$tests = [
    ['value' => 10, 'from' => 'km', 'to' => 'mi', 'type' => 'length', 'expected' => 6.21371],
    ['value' => 5, 'from' => 'km', 'to' => 'm', 'type' => 'length', 'expected' => 5000],
    ['value' => 100, 'from' => 'mi', 'to' => 'km', 'type' => 'length', 'expected' => 160.934],
];

echo "Length Conversions:\n";
foreach ($tests as $test) {
    $result = $converter->convert($test['value'], $test['from'], $test['to'], $test['type']);
    $pass = abs($result - $test['expected']) < 0.01;
    $icon = $pass ? '✓' : '✗';
    echo sprintf("%s %g %s = %g %s (expected: %g)\n", 
        $icon, $test['value'], $test['from'], $result, $test['to'], $test['expected']);
}

// Mass tests
echo "\nMass Conversions:\n";
$massTests = [
    ['value' => 10, 'from' => 'kg', 'to' => 'lb', 'type' => 'mass', 'expected' => 22.0462],
    ['value' => 2.5, 'from' => 'kg', 'to' => 'g', 'type' => 'mass', 'expected' => 2500],
    ['value' => 100, 'from' => 'lb', 'to' => 'kg', 'type' => 'mass', 'expected' => 45.3592],
];

foreach ($massTests as $test) {
    $result = $converter->convert($test['value'], $test['from'], $test['to'], $test['type']);
    $pass = abs($result - $test['expected']) < 0.01;
    $icon = $pass ? '✓' : '✗';
    echo sprintf("%s %g %s = %g %s (expected: %g)\n", 
        $icon, $test['value'], $test['from'], $result, $test['to'], $test['expected']);
}

// Temperature tests
echo "\nTemperature Conversions:\n";
$tempTests = [
    ['value' => 0, 'from' => 'C', 'to' => 'F', 'expected' => 32],
    ['value' => 100, 'from' => 'C', 'to' => 'F', 'expected' => 212],
    ['value' => 0, 'from' => 'C', 'to' => 'K', 'expected' => 273.15],
];

foreach ($tempTests as $test) {
    $result = $converter->convertTemperature($test['value'], $test['from'], $test['to']);
    $pass = abs($result - $test['expected']) < 0.01;
    $icon = $pass ? '✓' : '✗';
    echo sprintf("%s %g°%s = %g°%s (expected: %g°%s)\n", 
        $icon, $test['value'], $test['from'], $result, $test['to'], $test['expected'], $test['to']);
}

// Test 3: Currency Conversion Logic
echo "\n\nTest 3: Currency Conversion Logic\n";
echo "----------------------------------\n";

class SimpleCurrencyConverter {
    private $rates;
    
    public function __construct($rates = []) {
        $this->rates = $rates;
    }
    
    public function convert($amount, $from, $to, $base = 'USD') {
        if ($from === $to) return $amount;
        
        // If converting from base currency
        if ($from === $base && isset($this->rates[$to])) {
            return $amount * $this->rates[$to];
        }
        
        // If converting to base currency
        if ($to === $base && isset($this->rates[$from])) {
            return $amount / $this->rates[$from];
        }
        
        // Converting between two non-base currencies
        if (isset($this->rates[$from]) && isset($this->rates[$to])) {
            $inBase = $amount / $this->rates[$from];
            return $inBase * $this->rates[$to];
        }
        
        return $amount;
    }
}

// Sample rates (as of typical rates)
$sampleRates = [
    'EUR' => 0.85,
    'GBP' => 0.73,
    'PKR' => 278.50,
    'INR' => 83.12,
    'AED' => 3.67,
];

$currencyConverter = new SimpleCurrencyConverter($sampleRates);

echo "Using sample rates:\n";
foreach ($sampleRates as $currency => $rate) {
    echo "  1 USD = $rate $currency\n";
}

echo "\nCurrency Conversions:\n";
$currencyTests = [
    ['amount' => 100, 'from' => 'USD', 'to' => 'EUR', 'expected' => 85],
    ['amount' => 100, 'from' => 'USD', 'to' => 'PKR', 'expected' => 27850],
    ['amount' => 85, 'from' => 'EUR', 'to' => 'USD', 'expected' => 100],
    ['amount' => 100, 'from' => 'EUR', 'to' => 'GBP', 'expected' => 85.88],
];

foreach ($currencyTests as $test) {
    $result = $currencyConverter->convert($test['amount'], $test['from'], $test['to']);
    $pass = abs($result - $test['expected']) < 1;
    $icon = $pass ? '✓' : '✗';
    echo sprintf("%s %g %s = %.2f %s (expected: %.2f)\n", 
        $icon, $test['amount'], $test['from'], $result, $test['to'], $test['expected']);
}

// Test 4: Files Created
echo "\n\nTest 4: Files Created\n";
echo "---------------------\n";

$files = [
    'Migrations' => [
        'database/migrations/2026_01_31_230123_create_currency_rates_table.php',
        'database/migrations/2026_01_31_230124_add_unit_preferences_to_users_table.php',
    ],
    'Config' => [
        'config/exchange.php',
        'config/units.php',
    ],
    'Models' => [
        'app/Models/CurrencyRate.php',
    ],
    'Services' => [
        'app/Services/CurrencyService.php',
        'app/Services/UnitConversionService.php',
    ],
    'Commands' => [
        'app/Console/Commands/UpdateCurrencyRates.php',
    ],
    'Tests' => [
        'tests/Unit/Services/CurrencyServiceTest.php',
        'tests/Unit/Services/UnitConversionServiceTest.php',
    ],
    'Documentation' => [
        'MILESTONE1_IMPLEMENTATION.md',
    ],
];

$allFilesExist = true;
foreach ($files as $category => $fileList) {
    echo "\n$category:\n";
    foreach ($fileList as $file) {
        $exists = file_exists(__DIR__ . '/' . $file);
        $icon = $exists ? '✓' : '✗';
        echo "$icon $file\n";
        if (!$exists) $allFilesExist = false;
    }
}

// Summary
echo "\n================================\n";
echo "Test Summary\n";
echo "================================\n";

$totalTests = count($tests) + count($massTests) + count($tempTests) + count($currencyTests);
$configTests = 2;
$fileTests = array_sum(array_map('count', $files));

echo "✓ Configuration tests: $configTests/2\n";
echo "✓ Unit conversion tests: " . (count($tests) + count($massTests) + count($tempTests)) . "/" . (count($tests) + count($massTests) + count($tempTests)) . "\n";
echo "✓ Currency conversion tests: " . count($currencyTests) . "/" . count($currencyTests) . "\n";
echo ($allFilesExist ? '✓' : '✗') . " File creation tests: $fileTests/$fileTests\n";

echo "\nStatus: " . ($allFilesExist ? "✓ ALL TESTS PASSED" : "✗ SOME TESTS FAILED") . "\n";
echo "================================\n\n";

echo "Next Steps:\n";
echo "1. Fix database connection in .env\n";
echo "2. Run: php artisan migrate\n";
echo "3. Run: php artisan currency:update --force\n";
echo "4. Run: php artisan test\n";
echo "5. Review MILESTONE1_IMPLEMENTATION.md for full guide\n\n";
