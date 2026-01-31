<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Currency Exchange API Configuration
    |--------------------------------------------------------------------------
    |
    | Configure the currency exchange rate API providers and settings.
    |
    */

    'enabled' => env('CURRENCY_AUTO_UPDATE_ENABLED', true),

    'primary_api' => [
        'provider' => env('EXCHANGE_API_PRIMARY', 'exchangeratesapi.io'),
        'url' => env('EXCHANGE_API_URL', 'https://api.exchangeratesapi.io/v1/latest'),
        'key' => env('EXCHANGE_API_KEY', ''),
    ],

    'backup_api' => [
        'provider' => env('EXCHANGE_API_BACKUP', 'exchangerate.host'),
        'url' => env('EXCHANGE_API_BACKUP_URL', 'https://api.exchangerate.host/latest'),
        'key' => env('EXCHANGE_API_BACKUP_KEY', ''), // Most backup APIs are free
    ],

    'base_currency' => env('BASE_CURRENCY', 'USD'),

    'update_interval' => env('CURRENCY_UPDATE_INTERVAL', 12), // hours

    'cache' => [
        'enabled' => env('CURRENCY_CACHE_ENABLED', true),
        'duration' => env('CURRENCY_CACHE_DURATION', 720), // minutes (12 hours)
        'key' => 'currency_rates',
    ],

    'fallback' => [
        'enabled' => true,
        'max_age_hours' => 168, // 7 days - use fallback if rates older than this
    ],

    'supported_currencies' => [
        'USD', 'EUR', 'GBP', 'PKR', 'INR', 'AED', 'SAR', 'JPY', 'CNY', 'AUD', 
        'CAD', 'CHF', 'SEK', 'NOK', 'DKK', 'NZD', 'SGD', 'HKD', 'KRW', 'MXN',
        'BRL', 'ZAR', 'TRY', 'RUB', 'THB', 'IDR', 'MYR', 'PHP', 'VND', 'EGP',
    ],

    'retry' => [
        'max_attempts' => 3,
        'delay_seconds' => 5,
    ],
];
