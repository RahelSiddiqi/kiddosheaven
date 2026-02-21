<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CurrencyService
{
    // Base currency for the store
    public const BASE = 'BDT';

    // TTL for cached live rates (6 hours)
    protected const CACHE_TTL = 21600;

    // Static fallback rates (BDT base) — updated periodically via RefreshCurrencyRates command
    protected array $fallbackRates = [
        'BDT' => 1.0,
        'USD' => 0.0091,
        'EUR' => 0.0084,
        'GBP' => 0.0071,
        'CAD' => 0.0124,
        'AUD' => 0.0139,
        'INR' => 0.7593,
        'SGD' => 0.0122,
        'MYR' => 0.0426,
        'AED' => 0.0333,
        'SAR' => 0.0341,
    ];

    protected array $meta = [
        'BDT' => ['name' => 'Bangladeshi Taka',  'symbol' => '৳',    'decimals' => 0],
        'USD' => ['name' => 'US Dollar',          'symbol' => '$',    'decimals' => 2],
        'EUR' => ['name' => 'Euro',               'symbol' => '€',    'decimals' => 2],
        'GBP' => ['name' => 'British Pound',      'symbol' => '£',    'decimals' => 2],
        'CAD' => ['name' => 'Canadian Dollar',    'symbol' => 'CA$',  'decimals' => 2],
        'AUD' => ['name' => 'Australian Dollar',  'symbol' => 'A$',   'decimals' => 2],
        'INR' => ['name' => 'Indian Rupee',       'symbol' => '₹',    'decimals' => 0],
        'SGD' => ['name' => 'Singapore Dollar',   'symbol' => 'S$',   'decimals' => 2],
        'MYR' => ['name' => 'Malaysian Ringgit',  'symbol' => 'RM',   'decimals' => 2],
        'AED' => ['name' => 'UAE Dirham',         'symbol' => 'د.إ',  'decimals' => 2],
        'SAR' => ['name' => 'Saudi Riyal',        'symbol' => '﷼',    'decimals' => 2],
    ];

    // -------------------------------------------------- rates

    /**
     * Get live rates, cached or from open.er-api.com (free, no key).
     */
    public function rates(): array
    {
        return Cache::remember('currency_rates', self::CACHE_TTL, function () {
            return $this->fetchLiveRates();
        });
    }

    /**
     * Fetch live rates from the free Exchange Rate API.
     * Falls back to hardcoded rates on failure.
     */
    public function fetchLiveRates(): array
    {
        try {
            $response = Http::timeout(8)->get(
                'https://open.er-api.com/v6/latest/' . self::BASE
            );

            if ($response->successful()) {
                $data = $response->json();
                if (!empty($data['rates'])) {
                    $rates = [];
                    foreach (array_keys($this->meta) as $code) {
                        $rates[$code] = $data['rates'][$code] ?? ($this->fallbackRates[$code] ?? 1.0);
                    }
                    return $rates;
                }
            }
        } catch (\Throwable $e) {
            Log::warning('CurrencyService: failed to fetch live rates — ' . $e->getMessage());
        }

        return $this->fallbackRates;
    }

    /**
     * Force-refresh rates and store in cache.
     */
    public function refreshRates(): array
    {
        $rates = $this->fetchLiveRates();
        Cache::put('currency_rates', $rates, self::CACHE_TTL);
        return $rates;
    }

    // -------------------------------------------------- session helpers

    public function all(): array
    {
        $rates = $this->rates();
        return collect($this->meta)
            ->mapWithKeys(fn ($info, $code) => [$code => array_merge($info, ['rate' => $rates[$code] ?? 1.0])])
            ->all();
    }

    public function current(): string
    {
        return session('currency', self::BASE);
    }

    public function setCurrency(string $code): void
    {
        if (isset($this->meta[$code])) {
            session(['currency' => $code]);
        }
    }

    // -------------------------------------------------- conversion / formatting

    public function convert(float $amount, ?string $toCurrency = null): float
    {
        $code  = $toCurrency ?? $this->current();
        $rates = $this->rates();
        $rate  = $rates[$code] ?? 1.0;
        return round($amount * $rate, 8);
    }

    public function format(float $amount, ?string $code = null): string
    {
        $code    = $code ?? $this->current();
        $info    = $this->meta[$code] ?? $this->meta[self::BASE];
        $rates   = $this->rates();
        $rate    = $rates[$code] ?? 1.0;
        $converted = $amount * $rate;
        return $info['symbol'] . number_format($converted, $info['decimals']);
    }

    public function symbol(?string $code = null): string
    {
        $code = $code ?? $this->current();
        return $this->meta[$code]['symbol'] ?? '৳';
    }

    /**
     * Map a shipping country code (ISO 3166-1 alpha-2) to a currency code.
     */
    public function currencyForCountry(string $countryCode): string
    {
        return match (strtoupper($countryCode)) {
            'US'            => 'USD',
            'GB'            => 'GBP',
            'CA'            => 'CAD',
            'AU'            => 'AUD',
            'IN'            => 'INR',
            'SG'            => 'SGD',
            'MY'            => 'MYR',
            'AE'            => 'AED',
            'SA'            => 'SAR',
            'EU', 'DE', 'FR', 'IT', 'ES', 'NL', 'BE', 'PT', 'AT', 'CH', 'SE', 'NO', 'DK', 'FI' => 'EUR',
            default         => self::BASE,  // BDT for Bangladesh + unknown
        };
    }
}

