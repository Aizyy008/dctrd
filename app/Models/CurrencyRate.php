<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CurrencyRate extends Model
{
    protected $fillable = [
        'currency_code',
        'base_currency',
        'rate',
        'source',
        'is_fallback',
        'last_updated_at',
    ];

    protected $casts = [
        'rate' => 'decimal:8',
        'is_fallback' => 'boolean',
        'last_updated_at' => 'datetime',
    ];

    /**
     * Get the latest rate for a currency
     */
    public static function getRate($currencyCode, $baseCurrency = null)
    {
        $baseCurrency = $baseCurrency ?? config('exchange.base_currency', 'USD');
        
        return self::where('currency_code', $currencyCode)
            ->where('base_currency', $baseCurrency)
            ->orderBy('last_updated_at', 'desc')
            ->first();
    }

    /**
     * Get all latest rates
     */
    public static function getLatestRates($baseCurrency = null)
    {
        $baseCurrency = $baseCurrency ?? config('exchange.base_currency', 'USD');
        
        return self::where('base_currency', $baseCurrency)
            ->whereIn('id', function($query) use ($baseCurrency) {
                $query->selectRaw('MAX(id)')
                    ->from('currency_rates')
                    ->where('base_currency', $baseCurrency)
                    ->groupBy('currency_code');
            })
            ->get()
            ->keyBy('currency_code');
    }

    /**
     * Check if rates are stale (need update)
     */
    public static function areRatesStale()
    {
        $updateInterval = config('exchange.update_interval', 12); // hours
        $latestRate = self::orderBy('last_updated_at', 'desc')->first();
        
        if (!$latestRate) {
            return true;
        }
        
        return $latestRate->last_updated_at->diffInHours(now()) >= $updateInterval;
    }
}
