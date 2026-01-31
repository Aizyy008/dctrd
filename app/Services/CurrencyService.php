<?php

namespace App\Services;

use App\Models\CurrencyRate;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Exception;

class CurrencyService
{
    protected $config;

    public function __construct()
    {
        $this->config = config('exchange');
    }

    /**
     * Fetch rates from primary API
     */
    public function fetchRates($baseCurrency = null)
    {
        $baseCurrency = $baseCurrency ?? $this->config['base_currency'];
        
        try {
            $rates = $this->fetchFromPrimaryApi($baseCurrency);
            
            if ($rates) {
                $this->storeRates($rates, $baseCurrency, $this->config['primary_api']['provider'], false);
                $this->cacheRates($rates, $baseCurrency);
                Log::info('Currency rates fetched successfully from primary API', [
                    'source' => $this->config['primary_api']['provider'],
                    'base' => $baseCurrency,
                    'count' => count($rates),
                ]);
                return $rates;
            }
            
            // If primary fails, try backup
            return $this->fallbackFetch($baseCurrency);
            
        } catch (Exception $e) {
            Log::error('Error fetching currency rates', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            
            return $this->fallbackFetch($baseCurrency);
        }
    }

    /**
     * Fetch from primary API
     */
    protected function fetchFromPrimaryApi($baseCurrency)
    {
        $apiUrl = $this->config['primary_api']['url'];
        $apiKey = $this->config['primary_api']['key'];
        
        if (empty($apiKey)) {
            Log::warning('Primary API key not configured');
            return null;
        }
        
        $response = Http::timeout(10)->get($apiUrl, [
            'access_key' => $apiKey,
            'base' => $baseCurrency,
        ]);
        
        if ($response->successful()) {
            $data = $response->json();
            
            if (isset($data['success']) && $data['success'] === true && isset($data['rates'])) {
                return $data['rates'];
            }
            
            if (isset($data['rates'])) {
                return $data['rates'];
            }
        }
        
        return null;
    }

    /**
     * Fetch from backup API (fallback)
     */
    public function fallbackFetch($baseCurrency = null)
    {
        $baseCurrency = $baseCurrency ?? $this->config['base_currency'];
        
        try {
            $apiUrl = $this->config['backup_api']['url'];
            
            $response = Http::timeout(10)->get($apiUrl, [
                'base' => $baseCurrency,
            ]);
            
            if ($response->successful()) {
                $data = $response->json();
                
                if (isset($data['rates'])) {
                    $this->storeRates($data['rates'], $baseCurrency, $this->config['backup_api']['provider'], true);
                    $this->cacheRates($data['rates'], $baseCurrency);
                    
                    Log::info('Currency rates fetched from backup API', [
                        'source' => $this->config['backup_api']['provider'],
                        'base' => $baseCurrency,
                    ]);
                    
                    return $data['rates'];
                }
            }
            
            // If both APIs fail, use last stored rates
            return $this->getStoredRates($baseCurrency);
            
        } catch (Exception $e) {
            Log::error('Backup API also failed', ['error' => $e->getMessage()]);
            return $this->getStoredRates($baseCurrency);
        }
    }

    /**
     * Store rates in database
     */
    public function storeRates(array $rates, $baseCurrency, $source, $isFallback = false)
    {
        $now = now();
        
        foreach ($rates as $currencyCode => $rate) {
            CurrencyRate::create([
                'currency_code' => $currencyCode,
                'base_currency' => $baseCurrency,
                'rate' => $rate,
                'source' => $source,
                'is_fallback' => $isFallback,
                'last_updated_at' => $now,
            ]);
        }
        
        return true;
    }

    /**
     * Cache rates for performance
     */
    protected function cacheRates(array $rates, $baseCurrency)
    {
        if ($this->config['cache']['enabled']) {
            $cacheKey = $this->config['cache']['key'] . '_' . $baseCurrency;
            $cacheDuration = $this->config['cache']['duration'];
            
            Cache::put($cacheKey, $rates, now()->addMinutes($cacheDuration));
        }
    }

    /**
     * Get stored rates from cache or database
     */
    public function getStoredRates($baseCurrency = null)
    {
        $baseCurrency = $baseCurrency ?? $this->config['base_currency'];
        
        // Try cache first
        if ($this->config['cache']['enabled']) {
            $cacheKey = $this->config['cache']['key'] . '_' . $baseCurrency;
            $cached = Cache::get($cacheKey);
            
            if ($cached) {
                return $cached;
            }
        }
        
        // Get from database
        $rates = CurrencyRate::getLatestRates($baseCurrency);
        
        return $rates->mapWithKeys(function ($rate) {
            return [$rate->currency_code => $rate->rate];
        })->toArray();
    }

    /**
     * Convert amount from one currency to another
     */
    public function convert($amount, $fromCurrency, $toCurrency, $baseCurrency = null)
    {
        $baseCurrency = $baseCurrency ?? $this->config['base_currency'];
        
        // Same currency, no conversion needed
        if ($fromCurrency === $toCurrency) {
            return $amount;
        }
        
        $rates = $this->getStoredRates($baseCurrency);
        
        // If converting from base currency
        if ($fromCurrency === $baseCurrency) {
            if (isset($rates[$toCurrency])) {
                return $amount * $rates[$toCurrency];
            }
        }
        
        // If converting to base currency
        if ($toCurrency === $baseCurrency) {
            if (isset($rates[$fromCurrency])) {
                return $amount / $rates[$fromCurrency];
            }
        }
        
        // Converting between two non-base currencies
        if (isset($rates[$fromCurrency]) && isset($rates[$toCurrency])) {
            // Convert to base currency first, then to target currency
            $inBaseCurrency = $amount / $rates[$fromCurrency];
            return $inBaseCurrency * $rates[$toCurrency];
        }
        
        // If rates not found, return original amount
        Log::warning('Currency conversion failed, rates not found', [
            'from' => $fromCurrency,
            'to' => $toCurrency,
        ]);
        
        return $amount;
    }

    /**
     * Get latest rate for a specific currency
     */
    public function getRate($currencyCode, $baseCurrency = null)
    {
        $rates = $this->getStoredRates($baseCurrency);
        return $rates[$currencyCode] ?? null;
    }

    /**
     * Check if auto-update is enabled
     */
    public function isAutoUpdateEnabled()
    {
        return $this->config['enabled'] && 
               getSetting('currency_auto_update_enabled', true);
    }

    /**
     * Format amount with currency
     */
    public function formatAmount($amount, $currency, $decimals = 2)
    {
        return number_format($amount, $decimals) . ' ' . $currency;
    }
}
