<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\CurrencyService;

class UpdateCurrencyRates extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'currency:update 
                            {--base= : Base currency (default: USD)}
                            {--force : Force update even if not stale}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update currency exchange rates from API';

    protected $currencyService;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(CurrencyService $currencyService)
    {
        parent::__construct();
        $this->currencyService = $currencyService;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // Check if auto-update is enabled
        if (!$this->currencyService->isAutoUpdateEnabled() && !$this->option('force')) {
            $this->warn('Currency auto-update is disabled in settings.');
            $this->info('Use --force to update anyway.');
            return 1;
        }

        $baseCurrency = $this->option('base') ?? config('exchange.base_currency', 'USD');

        $this->info("Fetching currency rates (base: {$baseCurrency})...");
        
        try {
            $rates = $this->currencyService->fetchRates($baseCurrency);
            
            if ($rates && count($rates) > 0) {
                $this->info("✓ Successfully updated " . count($rates) . " currency rates");
                
                // Display some sample rates
                $this->table(
                    ['Currency', 'Rate'],
                    collect($rates)->take(10)->map(function ($rate, $currency) {
                        return [$currency, number_format($rate, 4)];
                    })->toArray()
                );
                
                if (count($rates) > 10) {
                    $this->line('... and ' . (count($rates) - 10) . ' more currencies');
                }
                
                return 0;
            } else {
                $this->error('✗ Failed to fetch currency rates');
                $this->warn('Using fallback rates from database (if available)');
                return 1;
            }
            
        } catch (\Exception $e) {
            $this->error('✗ Error: ' . $e->getMessage());
            return 1;
        }
    }
}
