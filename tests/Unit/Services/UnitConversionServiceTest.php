<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Services\UnitConversionService;

class UnitConversionServiceTest extends TestCase
{
    protected $unitService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->unitService = new UnitConversionService();
    }

    /** @test */
    public function it_can_convert_length_units()
    {
        // Kilometers to Miles
        $result = $this->unitService->convert(10, 'km', 'mi', 'length');
        $this->assertEqualsWithDelta(6.21371, $result, 0.0001);

        // Miles to Kilometers
        $result = $this->unitService->convert(10, 'mi', 'km', 'length');
        $this->assertEqualsWithDelta(16.0934, $result, 0.001);

        // Kilometers to Meters
        $result = $this->unitService->convert(5, 'km', 'm', 'length');
        $this->assertEquals(5000, $result);
    }

    /** @test */
    public function it_can_convert_mass_units()
    {
        // Kilograms to Pounds
        $result = $this->unitService->convert(10, 'kg', 'lb', 'mass');
        $this->assertEqualsWithDelta(22.0462, $result, 0.001);

        // Pounds to Kilograms
        $result = $this->unitService->convert(100, 'lb', 'kg', 'mass');
        $this->assertEqualsWithDelta(45.3592, $result, 0.001);

        // Kilograms to Grams
        $result = $this->unitService->convert(2.5, 'kg', 'g', 'mass');
        $this->assertEquals(2500, $result);
    }

    /** @test */
    public function it_can_convert_area_units()
    {
        // Square meters to Square feet
        $result = $this->unitService->convert(100, 'sqm', 'sqft', 'area');
        $this->assertEqualsWithDelta(1076.39, $result, 0.01);

        // Square feet to Square meters
        $result = $this->unitService->convert(1000, 'sqft', 'sqm', 'area');
        $this->assertEqualsWithDelta(92.903, $result, 0.001);

        // Square meters to Hectares
        $result = $this->unitService->convert(10000, 'sqm', 'hectare', 'area');
        $this->assertEquals(1, $result);
    }

    /** @test */
    public function it_can_convert_temperature_units()
    {
        // Celsius to Fahrenheit
        $result = $this->unitService->convert(0, 'C', 'F', 'temperature');
        $this->assertEqualsWithDelta(32, $result, 0.01);

        $result = $this->unitService->convert(100, 'C', 'F', 'temperature');
        $this->assertEqualsWithDelta(212, $result, 0.01);

        // Fahrenheit to Celsius
        $result = $this->unitService->convert(32, 'F', 'C', 'temperature');
        $this->assertEqualsWithDelta(0, $result, 0.01);

        // Celsius to Kelvin
        $result = $this->unitService->convert(0, 'C', 'K', 'temperature');
        $this->assertEqualsWithDelta(273.15, $result, 0.01);
    }

    /** @test */
    public function it_returns_same_value_for_same_unit()
    {
        $result = $this->unitService->convert(100, 'km', 'km', 'length');
        $this->assertEquals(100, $result);

        $result = $this->unitService->convert(50, 'C', 'C', 'temperature');
        $this->assertEquals(50, $result);
    }

    /** @test */
    public function it_can_get_available_units_for_type()
    {
        $lengthUnits = $this->unitService->getAvailableUnits('length');
        
        $this->assertIsArray($lengthUnits);
        $this->assertArrayHasKey('km', $lengthUnits);
        $this->assertArrayHasKey('mi', $lengthUnits);
        $this->assertEquals('Kilometers', $lengthUnits['km']);
    }

    /** @test */
    public function it_can_format_value_with_unit()
    {
        $formatted = $this->unitService->formatValue(1234.567, 'km', 2);
        $this->assertEquals('1,234.57 km', $formatted);

        $formatted = $this->unitService->formatValue(98.6, 'F', 1);
        $this->assertEquals('98.6 F', $formatted);
    }

    /** @test */
    public function it_can_convert_to_base_unit()
    {
        // Convert miles to base unit (km)
        $result = $this->unitService->convertToBase(10, 'mi', 'length');
        $this->assertEqualsWithDelta(16.0934, $result, 0.001);

        // Convert pounds to base unit (kg)
        $result = $this->unitService->convertToBase(220, 'lb', 'mass');
        $this->assertEqualsWithDelta(99.79, $result, 0.01);
    }

    /** @test */
    public function it_can_convert_from_base_unit()
    {
        // Convert from base unit (km) to miles
        $result = $this->unitService->convertFromBase(16.0934, 'mi', 'length');
        $this->assertEqualsWithDelta(10, $result, 0.001);

        // Convert from base unit (kg) to pounds
        $result = $this->unitService->convertFromBase(100, 'lb', 'mass');
        $this->assertEqualsWithDelta(220.462, $result, 0.001);
    }

    /** @test */
    public function it_handles_invalid_type_gracefully()
    {
        $result = $this->unitService->convert(100, 'km', 'mi', 'invalid_type');
        $this->assertEquals(100, $result);
    }

    /** @test */
    public function it_handles_invalid_units_gracefully()
    {
        $result = $this->unitService->convert(100, 'xyz', 'abc', 'length');
        $this->assertEquals(100, $result);
    }
}
