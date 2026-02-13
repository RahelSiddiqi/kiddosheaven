<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class PricingTemplate extends Model
{
    protected $fillable = [
        'name',
        'description',
        'strategy_type',
        'config',
        'is_active',
        'is_global',
    ];

    protected $casts = [
        'config' => 'array',
        'is_active' => 'boolean',
        'is_global' => 'boolean',
    ];

    /**
     * Categories using this template
     */
    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'category_pricing_template');
    }

    /**
     * Apply this template to calculate price from cost
     */
    public function calculatePrice(float $costPrice, array $attributes = []): float
    {
        $config = $this->config ?? [];

        return match ($this->strategy_type) {
            'percentage_markup' => $this->applyPercentageMarkup($costPrice, $config),
            'fixed_markup' => $this->applyFixedMarkup($costPrice, $config),
            'tiered' => $this->applyTiered($costPrice, $config),
            'attribute_based' => $this->applyAttributeBased($costPrice, $attributes, $config),
            default => $costPrice,
        };
    }

    /**
     * Percentage Markup: cost * (1 + percentage/100)
     */
    private function applyPercentageMarkup(float $costPrice, array $config): float
    {
        $percentage = $config['percentage'] ?? 50;
        return round($costPrice * (1 + ($percentage / 100)), 2);
    }

    /**
     * Fixed Markup: cost + fixed_amount
     */
    private function applyFixedMarkup(float $costPrice, array $config): float
    {
        $fixedAmount = $config['fixed_amount'] ?? 10;
        return round($costPrice + $fixedAmount, 2);
    }

    /**
     * Tiered: Different percentages based on cost ranges
     */
    private function applyTiered(float $costPrice, array $config): float
    {
        $tiers = $config['tiers'] ?? [];

        // Sort tiers by min_cost descending to match highest tier first
        usort($tiers, fn($a, $b) => ($b['min_cost'] ?? 0) <=> ($a['min_cost'] ?? 0));

        foreach ($tiers as $tier) {
            if ($costPrice >= ($tier['min_cost'] ?? 0)) {
                $percentage = $tier['percentage'] ?? 50;
                return round($costPrice * (1 + ($percentage / 100)), 2);
            }
        }

        // Default markup if no tier matches
        return round($costPrice * 1.5, 2);
    }

    /**
     * Attribute-Based: Different markups based on variant attributes
     */
    private function applyAttributeBased(float $costPrice, array $attributes, array $config): float
    {
        $rules = $config['rules'] ?? [];

        foreach ($rules as $rule) {
            $matches = true;

            // Check if all conditions match
            foreach ($rule['conditions'] ?? [] as $condition) {
                $attrName = $condition['attribute'] ?? '';
                $attrValue = $condition['value'] ?? '';

                if (!isset($attributes[$attrName]) || $attributes[$attrName] !== $attrValue) {
                    $matches = false;
                    break;
                }
            }

            if ($matches) {
                $percentage = $rule['percentage'] ?? 50;
                return round($costPrice * (1 + ($percentage / 100)), 2);
            }
        }

        // Default markup
        $defaultPercentage = $config['default_percentage'] ?? 50;
        return round($costPrice * (1 + ($defaultPercentage / 100)), 2);
    }

    /**
     * Get human-readable strategy name
     */
    public function getStrategyNameAttribute(): string
    {
        return match ($this->strategy_type) {
            'percentage_markup' => 'Percentage Markup',
            'fixed_markup' => 'Fixed Markup',
            'tiered' => 'Tiered Pricing',
            'attribute_based' => 'Attribute-Based',
            default => 'Custom',
        };
    }

    /**
     * Get config summary for display
     */
    public function getConfigSummaryAttribute(): string
    {
        $config = $this->config ?? [];

        return match ($this->strategy_type) {
            'percentage_markup' => ($config['percentage'] ?? 50) . '% markup',
            'fixed_markup' => '$' . ($config['fixed_amount'] ?? 10) . ' fixed markup',
            'tiered' => count($config['tiers'] ?? []) . ' pricing tiers',
            'attribute_based' => count($config['rules'] ?? []) . ' attribute rules',
            default => 'No configuration',
        };
    }
}
