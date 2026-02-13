<?php

namespace App\Services;

use App\Models\PricingTemplate;
use App\Models\Category;
use Illuminate\Support\Collection;

class PricingTemplateService
{
    /**
     * Get all active templates
     */
    public function getActiveTemplates(): Collection
    {
        return PricingTemplate::where('is_active', true)
            ->orderBy('name')
            ->get();
    }

    /**
     * Get template by ID
     */
    public function getTemplate(int $id): ?PricingTemplate
    {
        return PricingTemplate::find($id);
    }

    /**
     * Create a new template
     */
    public function create(array $data): PricingTemplate
    {
        return PricingTemplate::create([
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'strategy_type' => $data['strategy_type'],
            'config' => $this->buildConfig($data['strategy_type'], $data),
            'is_active' => $data['is_active'] ?? true,
            'is_global' => $data['is_global'] ?? false,
        ]);
    }

    /**
     * Update existing template
     */
    public function update(PricingTemplate $template, array $data): PricingTemplate
    {
        $template->update([
            'name' => $data['name'] ?? $template->name,
            'description' => $data['description'] ?? $template->description,
            'strategy_type' => $data['strategy_type'] ?? $template->strategy_type,
            'config' => isset($data['strategy_type'])
                ? $this->buildConfig($data['strategy_type'], $data)
                : $template->config,
            'is_active' => $data['is_active'] ?? $template->is_active,
            'is_global' => $data['is_global'] ?? $template->is_global,
        ]);

        return $template->fresh();
    }

    /**
     * Delete template
     */
    public function delete(PricingTemplate $template): bool
    {
        return $template->delete();
    }

    /**
     * Attach template to categories
     */
    public function attachToCategories(PricingTemplate $template, array $categoryIds): void
    {
        $template->categories()->sync($categoryIds);
    }

    /**
     * Get template for a specific category
     */
    public function getTemplateForCategory(Category $category): ?PricingTemplate
    {
        // Check if category has a specific template
        $template = $category->pricingTemplates()->where('is_active', true)->first();

        if ($template) {
            return $template;
        }

        // Fall back to global template
        return PricingTemplate::where('is_active', true)
            ->where('is_global', true)
            ->first();
    }

    /**
     * Calculate price using template
     */
    public function calculatePrice(
        PricingTemplate $template,
        float $costPrice,
        array $attributes = []
    ): float {
        return $template->calculatePrice($costPrice, $attributes);
    }

    /**
     * Bulk calculate prices for multiple variants
     */
    public function bulkCalculatePrices(
        PricingTemplate $template,
        array $variants
    ): array {
        $results = [];

        foreach ($variants as $variant) {
            $costPrice = $variant['cost_price'] ?? 0;
            $attributes = $variant['attributes'] ?? [];

            $results[] = [
                'variant' => $variant,
                'calculated_price' => $template->calculatePrice($costPrice, $attributes),
            ];
        }

        return $results;
    }

    /**
     * Build config array based on strategy type
     */
    private function buildConfig(string $strategyType, array $data): array
    {
        return match ($strategyType) {
            'percentage_markup' => [
                'percentage' => $data['percentage'] ?? 50,
            ],
            'fixed_markup' => [
                'fixed_amount' => $data['fixed_amount'] ?? 10,
            ],
            'tiered' => [
                'tiers' => $data['tiers'] ?? [],
            ],
            'attribute_based' => [
                'rules' => $data['rules'] ?? [],
                'default_percentage' => $data['default_percentage'] ?? 50,
            ],
            default => [],
        };
    }

    /**
     * Validate config for strategy type
     */
    public function validateConfig(string $strategyType, array $config): array
    {
        $errors = [];

        switch ($strategyType) {
            case 'percentage_markup':
                if (!isset($config['percentage']) || $config['percentage'] < 0) {
                    $errors[] = 'Percentage must be a positive number';
                }
                break;

            case 'fixed_markup':
                if (!isset($config['fixed_amount']) || $config['fixed_amount'] < 0) {
                    $errors[] = 'Fixed amount must be a positive number';
                }
                break;

            case 'tiered':
                if (empty($config['tiers'])) {
                    $errors[] = 'At least one tier is required';
                }
                break;

            case 'attribute_based':
                if (empty($config['rules'])) {
                    $errors[] = 'At least one rule is required';
                }
                break;
        }

        return $errors;
    }

    /**
     * Get example calculations for preview
     */
    public function getExampleCalculations(PricingTemplate $template): array
    {
        $examples = [
            ['cost' => 10, 'attributes' => []],
            ['cost' => 50, 'attributes' => []],
            ['cost' => 100, 'attributes' => []],
            ['cost' => 500, 'attributes' => []],
        ];

        return array_map(function ($example) use ($template) {
            return [
                'cost' => $example['cost'],
                'price' => $template->calculatePrice($example['cost'], $example['attributes']),
                'markup' => $template->calculatePrice($example['cost'], $example['attributes']) - $example['cost'],
            ];
        }, $examples);
    }
}
