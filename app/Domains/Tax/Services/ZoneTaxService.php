<?php

namespace App\Domains\Tax\Services;

use App\Domains\Tax\Models\TaxZone;
use App\Domains\Tax\Models\TaxRate;
use App\Services\TaxService as LegacyTaxService;

/**
 * Zone-aware tax calculation service.
 * Falls back to the flat-rate TaxService if no zone matches.
 */
class ZoneTaxService
{
    public function __construct(
        private readonly LegacyTaxService $legacyTaxService
    ) {}

    /**
     * Calculate tax amount for a given subtotal, country, and product type.
     */
    public function calculate(float $subtotal, string $countryCode = 'BD', string $type = 'all'): float
    {
        $zone = TaxZone::forCountry($countryCode);

        if (!$zone) {
            // Fall back to flat rate from settings
            return $this->legacyTaxService->calculate($subtotal);
        }

        $rates = $zone->rates()
            ->active()
            ->where(function ($q) use ($type) {
                $q->where('applies_to', 'all')->orWhere('applies_to', $type);
            })
            ->orderBy('is_compound')
            ->get();

        $base  = $subtotal;
        $total = 0.0;

        foreach ($rates as $rate) {
            $taxBase = $rate->is_compound ? $base + $total : $base;
            $tax     = round($taxBase * ($rate->rate / 100), 2);
            $total  += $tax;
        }

        return $total;
    }

    /**
     * Get effective total rate percentage for a country (sum of all rates).
     */
    public function effectiveRate(string $countryCode = 'BD'): float
    {
        $zone = TaxZone::forCountry($countryCode);
        if (!$zone) {
            return (float) $this->legacyTaxService->ratePercent();
        }

        return (float) $zone->rates()->active()->sum('rate');
    }
}
