<?php

namespace App\Console\Commands;

use App\Services\CurrencyService;
use Illuminate\Console\Command;

class RefreshCurrencyRates extends Command
{
    protected $signature   = 'currency:refresh {--force : Bypass cache and fetch fresh rates}';
    protected $description = 'Refresh exchange rates from open.er-api.com and store in cache';

    public function handle(CurrencyService $service): int
    {
        $this->info('Fetching latest exchange rates (base: BDT)...');

        if ($this->option('force')) {
            $rates = $service->refreshRates();
        } else {
            $rates = $service->fetchLiveRates();
            \Illuminate\Support\Facades\Cache::put('currency_rates', $rates, 21600);
        }

        $this->table(
            ['Currency', 'Rate (1 BDT =)'],
            collect($rates)->map(fn ($rate, $code) => [$code, $rate])->values()->toArray()
        );

        $this->info('✅ Exchange rates updated successfully.');

        return self::SUCCESS;
    }
}
