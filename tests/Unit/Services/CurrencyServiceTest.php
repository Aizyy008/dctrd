<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Services\CurrencyService;
use App\Models\CurrencyRate;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;

class CurrencyServiceTest extends TestCase
{
    use RefreshDatabase;

    protected $currencyService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->currencyService = new CurrencyService();
    }

    /** @test */
    public function it_can_store_currency_rates()
    {
        $rates = [
            'EUR' => 0.85,
            'GBP' => 0.73,
            'PKR' => 278.50,
        ];

        $result = $this->currencyService->storeRates($rates, 'USD', 'test_api', false);

        $this->assertTrue($result);
        $this->assertDatabaseHas('currency_rates', [
            'currency_code' => 'EUR',
            'base_currency' => 'USD',
            'rate' => 0.85,
        ]);
    }

    /** @test */
    public function it_can_convert_between_currencies()
    {
        // Seed test data
        CurrencyRate::create([
            'currency_code' => 'EUR',
            'base_currency' => 'USD',
            'rate' => 0.85,
            'source' => 'test',
            'last_updated_at' => now(),
        ]);

        CurrencyRate::create([
            'currency_code' => 'PKR',
            'base_currency' => 'USD',
            'rate' => 278.50,
            'source' => 'test',
            'last_updated_at' => now(),
        ]);

        // Test USD to EUR
        $result = $this->currencyService->convert(100, 'USD', 'EUR');
        $this->assertEquals(85, $result);

        // Test EUR to PKR (through USD)
        $result = $this->currencyService->convert(100, 'EUR', 'PKR');
        $this->assertEqualsWithDelta(32764.71, $result, 0.01);
    }

    /** @test */
    public function it_returns_same_amount_for_same_currency()
    {
        $result = $this->currencyService->convert(100, 'USD', 'USD');
        $this->assertEquals(100, $result);
    }

    /** @test */
    public function it_can_get_stored_rates()
    {
        CurrencyRate::create([
            'currency_code' => 'EUR',
            'base_currency' => 'USD',
            'rate' => 0.85,
            'source' => 'test',
            'last_updated_at' => now(),
        ]);

        $rates = $this->currencyService->getStoredRates('USD');
        
        $this->assertIsArray($rates);
        $this->assertArrayHasKey('EUR', $rates);
        $this->assertEquals(0.85, $rates['EUR']);
    }

    /** @test */
    public function it_can_format_amount_with_currency()
    {
        $formatted = $this->currencyService->formatAmount(1234.56, 'USD', 2);
        $this->assertEquals('1,234.56 USD', $formatted);
    }

    /** @test */
    public function it_handles_missing_rates_gracefully()
    {
        // No rates in database
        $result = $this->currencyService->convert(100, 'USD', 'XYZ');
        
        // Should return original amount if rates not found
        $this->assertEquals(100, $result);
    }
}
