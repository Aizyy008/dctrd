<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;

class UnitConversionService
{
    protected $config;

    public function __construct()
    {
        $this->config = config('units');
    }

    /**
     * Convert value from one unit to another
     */
    public function convert($value, $fromUnit, $toUnit, $type)
    {
        // Same unit, no conversion needed
        if ($fromUnit === $toUnit) {
            return $value;
        }

        // Check if type exists
        if (!isset($this->config[$type])) {
            return $value;
        }

        $typeConfig = $this->config[$type];
        
        // Handle temperature separately (uses formulas)
        if ($type === 'temperature') {
            return $this->convertTemperature($value, $fromUnit, $toUnit);
        }

        // For other units, use conversion factors
        $conversions = $typeConfig['conversions'];
        $conversionKey = "{$fromUnit}_to_{$toUnit}";

        if (isset($conversions[$conversionKey])) {
            return $value * $conversions[$conversionKey];
        }

        // If direct conversion not found, try converting through base unit
        $baseUnit = $typeConfig['base_unit'];
        
        if ($fromUnit !== $baseUnit && $toUnit !== $baseUnit) {
            // Convert from -> base -> to
            $toBaseKey = "{$fromUnit}_to_{$baseUnit}";
            $fromBaseKey = "{$baseUnit}_to_{$toUnit}";
            
            if (isset($conversions[$toBaseKey]) && isset($conversions[$fromBaseKey])) {
                $valueInBase = $value * $conversions[$toBaseKey];
                return $valueInBase * $conversions[$fromBaseKey];
            }
        }

        // If all else fails, return original value
        return $value;
    }

    /**
     * Convert temperature (uses formulas, not simple multiplication)
     */
    protected function convertTemperature($value, $fromUnit, $toUnit)
    {
        if ($fromUnit === $toUnit) {
            return $value;
        }

        $formulas = $this->config['temperature']['formulas'];
        $conversionKey = "{$fromUnit}_to_{$toUnit}";

        if (isset($formulas[$conversionKey])) {
            $formula = $formulas[$conversionKey];
            $result = $value;

            // Apply formula operations in order
            if (isset($formula['subtract'])) {
                $result -= $formula['subtract'];
            }
            if (isset($formula['multiply'])) {
                $result *= $formula['multiply'];
            }
            if (isset($formula['add'])) {
                $result += $formula['add'];
            }

            return $result;
        }

        // If direct formula not found, try converting through Celsius
        if ($fromUnit !== 'C' && $toUnit !== 'C') {
            $toCelsius = $this->convertTemperature($value, $fromUnit, 'C');
            return $this->convertTemperature($toCelsius, 'C', $toUnit);
        }

        return $value;
    }

    /**
     * Get user's preferred unit for a type
     */
    public function getUserPreferredUnit($userId, $type)
    {
        if (!$userId) {
            return $this->config['defaults'][$type] ?? null;
        }

        // Cache the user preferences
        $cacheKey = "user_unit_prefs_{$userId}";
        
        $preferences = Cache::remember($cacheKey, 3600, function () use ($userId) {
            $user = \App\User::find($userId);
            if (!$user) {
                return [];
            }

            return [
                'length' => $user->preferred_length_unit,
                'mass' => $user->preferred_mass_unit,
                'area' => $user->preferred_area_unit,
                'temperature' => $user->preferred_temperature_unit,
            ];
        });

        return $preferences[$type] ?? $this->config['defaults'][$type];
    }

    /**
     * Display value with automatic conversion based on user preference
     */
    public function displayValue($value, $unit, $type, $userId = null, $decimals = 2)
    {
        if ($userId) {
            $preferredUnit = $this->getUserPreferredUnit($userId, $type);
        } else {
            // For guests, check session/cookie
            $preferredUnit = session("preferred_{$type}_unit", $this->config['defaults'][$type]);
        }

        $convertedValue = $this->convert($value, $unit, $preferredUnit, $type);
        
        return $this->formatValue($convertedValue, $preferredUnit, $decimals);
    }

    /**
     * Format value with unit label
     */
    public function formatValue($value, $unit, $decimals = 2)
    {
        $formattedValue = number_format($value, $decimals);
        $label = $this->config['display_labels'][$unit] ?? $unit;
        
        return "{$formattedValue} {$unit}";
    }

    /**
     * Get available units for a type
     */
    public function getAvailableUnits($type)
    {
        if (!isset($this->config[$type])) {
            return [];
        }

        $units = $this->config[$type]['units'];
        
        return collect($units)->mapWithKeys(function ($unit) {
            return [$unit => $this->config['display_labels'][$unit] ?? $unit];
        })->toArray();
    }

    /**
     * Get all unit types
     */
    public function getUnitTypes()
    {
        return ['length', 'mass', 'area', 'temperature'];
    }

    /**
     * Clear user preferences cache
     */
    public function clearUserCache($userId)
    {
        Cache::forget("user_unit_prefs_{$userId}");
    }

    /**
     * Convert to base unit (for storage)
     */
    public function convertToBase($value, $fromUnit, $type)
    {
        if (!isset($this->config[$type])) {
            return $value;
        }

        $baseUnit = $this->config[$type]['base_unit'];
        return $this->convert($value, $fromUnit, $baseUnit, $type);
    }

    /**
     * Convert from base unit (for display)
     */
    public function convertFromBase($value, $toUnit, $type)
    {
        if (!isset($this->config[$type])) {
            return $value;
        }

        $baseUnit = $this->config[$type]['base_unit'];
        return $this->convert($value, $baseUnit, $toUnit, $type);
    }
}
