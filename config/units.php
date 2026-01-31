<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Unit Conversion Configuration
    |--------------------------------------------------------------------------
    |
    | Configure the available units and conversion factors.
    |
    */

    'length' => [
        'units' => ['km', 'mi', 'm', 'ft', 'yd'],
        'base_unit' => 'km',
        'conversions' => [
            'km_to_mi' => 0.621371,
            'km_to_m' => 1000,
            'km_to_ft' => 3280.84,
            'km_to_yd' => 1093.61,
            'mi_to_km' => 1.60934,
            'm_to_km' => 0.001,
            'ft_to_km' => 0.0003048,
            'yd_to_km' => 0.0009144,
        ],
    ],

    'mass' => [
        'units' => ['kg', 'lb', 'g', 'oz', 'ton'],
        'base_unit' => 'kg',
        'conversions' => [
            'kg_to_lb' => 2.20462,
            'kg_to_g' => 1000,
            'kg_to_oz' => 35.274,
            'kg_to_ton' => 0.001,
            'lb_to_kg' => 0.453592,
            'g_to_kg' => 0.001,
            'oz_to_kg' => 0.0283495,
            'ton_to_kg' => 1000,
        ],
    ],

    'area' => [
        'units' => ['sqm', 'sqft', 'acre', 'hectare'],
        'base_unit' => 'sqm',
        'conversions' => [
            'sqm_to_sqft' => 10.7639,
            'sqm_to_acre' => 0.000247105,
            'sqm_to_hectare' => 0.0001,
            'sqft_to_sqm' => 0.092903,
            'acre_to_sqm' => 4046.86,
            'hectare_to_sqm' => 10000,
        ],
    ],

    'temperature' => [
        'units' => ['C', 'F', 'K'],
        'base_unit' => 'C',
        // Temperature uses formulas, not simple multiplication
        'formulas' => [
            'C_to_F' => ['multiply' => 1.8, 'add' => 32],
            'C_to_K' => ['add' => 273.15],
            'F_to_C' => ['subtract' => 32, 'multiply' => 0.5556],
            'K_to_C' => ['subtract' => 273.15],
        ],
    ],

    'defaults' => [
        'length' => 'km',
        'mass' => 'kg',
        'area' => 'sqm',
        'temperature' => 'C',
    ],

    'display_labels' => [
        // Length
        'km' => 'Kilometers',
        'mi' => 'Miles',
        'm' => 'Meters',
        'ft' => 'Feet',
        'yd' => 'Yards',
        
        // Mass
        'kg' => 'Kilograms',
        'lb' => 'Pounds',
        'g' => 'Grams',
        'oz' => 'Ounces',
        'ton' => 'Metric Tons',
        
        // Area
        'sqm' => 'Square Meters',
        'sqft' => 'Square Feet',
        'acre' => 'Acres',
        'hectare' => 'Hectares',
        
        // Temperature
        'C' => 'Celsius',
        'F' => 'Fahrenheit',
        'K' => 'Kelvin',
    ],
];
