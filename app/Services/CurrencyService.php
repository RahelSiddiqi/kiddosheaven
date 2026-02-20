<?php

namespace App\Services;

class CurrencyService
{
    protected array $currencies = [
        'BDT' => ['name' => 'Bangladeshi Taka', 'symbol' => '৳', 'rate' => 1.0],
        'USD' => ['name' => 'US Dollar', 'symbol' => '$', 'rate' => 0.0091],
        'EUR' => ['name' => 'Euro', 'symbol' => '€', 'rate' => 0.0084],
        'GBP' => ['name' => 'British Pound', 'symbol' => '£', 'rate' => 0.0071],
        'SAR' => ['name' => 'Saudi Riyal', 'symbol' => '﷼', 'rate' => 0.034],
        'AED' => ['name' => 'UAE Dirham', 'symbol' => 'د.إ', 'rate' => 0.033],
    ];

    public function all(): array
    {
        return $this->currencies;
    }

    public function current(): string
    {
        return session('currency', 'BDT');
    }

    public function setCurrency(string $code): void
    {
        if (isset($this->currencies[$code])) {
            session(['currency' => $code]);
        }
    }

    public function format(float $amount, ?string $code = null): string
    {
        $code = $code ?? $this->current();
        $currency = $this->currencies[$code] ?? $this->currencies['BDT'];
        $converted = $amount * $currency['rate'];
        return $currency['symbol'] . number_format($converted, 2);
    }

    public function symbol(?string $code = null): string
    {
        $code = $code ?? $this->current();
        return $this->currencies[$code]['symbol'] ?? '৳';
    }

    public function convert(float $amount, ?string $code = null): float
    {
        $code = $code ?? $this->current();
        $rate = $this->currencies[$code]['rate'] ?? 1.0;
        return round($amount * $rate, 2);
    }
}
