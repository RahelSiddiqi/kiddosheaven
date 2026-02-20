<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;

class TaxService
{
    public function getRate(): float
    {
        return (float) Cache::remember('tax_rate', 600, function () {
            try {
                return \App\Models\Setting::where('key', 'tax_rate')->value('value') ?? 0;
            } catch (\Throwable $e) {
                return 0;
            }
        });
    }

    public function calculate(float $subtotal): float
    {
        $rate = $this->getRate();
        if ($rate <= 0) return 0.0;
        return round($subtotal * ($rate / 100), 2);
    }

    public function withTax(float $subtotal): float
    {
        return round($subtotal + $this->calculate($subtotal), 2);
    }

    public function ratePercent(): float
    {
        return $this->getRate();
    }

    public function label(): string
    {
        $rate = $this->getRate();
        return $rate > 0 ? "VAT ({$rate}%)" : '';
    }
}
