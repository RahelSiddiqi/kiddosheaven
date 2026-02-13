<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\VariantAttribute;
use App\Models\ProductAttribute;
use App\Models\ProductAttributeValue;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

class VariantGeneratorService
{
    /**
     * Generate all possible variants from selected attributes.
     *
     * @param Product $product
     * @param array $attributeData Format: [attribute_id => [value_id1, value_id2, ...]]
     * @param array $defaults Default values for price, cost_price, stock
     * @return Collection Generated variants
     */
    public function generateVariants(Product $product, array $attributeData, array $defaults = []): Collection
    {
        $combinations = $this->generateCombinations($attributeData);

        $variants = collect();

        DB::transaction(function () use ($product, $combinations, $defaults, &$variants) {
            foreach ($combinations as $combination) {
                $variant = $this->createVariant($product, $combination, $defaults);
                $variants->push($variant);
            }
        });

        return $variants;
    }

    /**
     * Generate all combinations of attribute values.
     * Uses recursive algorithm to handle N attributes.
     *
     * @param array $attributeData
     * @return array
     */
    protected function generateCombinations(array $attributeData): array
    {
        if (empty($attributeData)) {
            return [[]];
        }

        $attributeId = array_key_first($attributeData);
        $values = array_shift($attributeData);

        $subCombinations = $this->generateCombinations($attributeData);
        $combinations = [];

        foreach ($values as $valueId) {
            foreach ($subCombinations as $subCombo) {
                $combinations[] = array_merge(
                    [$attributeId => $valueId],
                    $subCombo
                );
            }
        }

        return $combinations;
    }

    /**
     * Create a single variant with attributes.
     *
     * @param Product $product
     * @param array $combination [attribute_id => value_id]
     * @param array $defaults
     * @return ProductVariant
     */
    protected function createVariant(Product $product, array $combination, array $defaults): ProductVariant
    {
        // Get attribute values for naming
        $attributeValueIds = array_values($combination);
        $attributeValues = ProductAttributeValue::whereIn('id', $attributeValueIds)->get();

        // Generate variant name
        $variantName = $attributeValues->pluck('value')->implode(' - ');

        // Generate SKU
        $skuParts = $attributeValues->pluck('value')
            ->map(fn($v) => strtoupper(substr($v, 0, 3)))
            ->implode('-');
        $sku = $product->sku ? "{$product->sku}-{$skuParts}" : "VAR-{$skuParts}-" . time();

        // Calculate price (base price + modifiers)
        $priceModifiers = $attributeValues->sum('price_modifier');
        $price = ($defaults['price'] ?? $product->price) + $priceModifiers;

        // Create variant
        $variant = ProductVariant::create([
            'product_id' => $product->id,
            'sku' => $sku,
            'name' => $variantName,
            'price' => $price,
            'cost_price' => $defaults['cost_price'] ?? $product->cost_price ?? 0,
            'stock_quantity' => $defaults['stock_quantity'] ?? 0,
            'low_stock_alert' => $defaults['low_stock_alert'] ?? $product->low_stock_alert ?? 5,
            'weight' => $defaults['weight'] ?? $product->weight,
            'is_active' => true,
            'is_default' => false,
        ]);

        // Link variant to attributes
        foreach ($combination as $attributeId => $valueId) {
            VariantAttribute::create([
                'product_variant_id' => $variant->id,
                'product_attribute_id' => $attributeId,
                'product_attribute_value_id' => $valueId,
            ]);
        }

        return $variant;
    }

    /**
     * Update existing variants based on new attribute selection.
     * Adds new combinations, removes obsolete ones.
     *
     * @param Product $product
     * @param array $attributeData
     * @param array $defaults
     * @return array ['added' => Collection, 'removed' => Collection, 'kept' => Collection]
     */
    public function syncVariants(Product $product, array $attributeData, array $defaults = []): array
    {
        $newCombinations = $this->generateCombinations($attributeData);
        $existingVariants = $product->variants()->with('variantAttributes')->get();

        $toAdd = collect();
        $toRemove = collect();
        $toKeep = collect();

        // Convert existing variants to combination format for comparison
        $existingCombos = $existingVariants->map(function ($variant) {
            return [
                'variant' => $variant,
                'combo' => $variant->variantAttributes->pluck('product_attribute_value_id', 'product_attribute_id')->toArray()
            ];
        });

        // Find variants to add
        foreach ($newCombinations as $combo) {
            $exists = $existingCombos->first(function ($existing) use ($combo) {
                return $this->combinationsMatch($existing['combo'], $combo);
            });

            if (!$exists) {
                $toAdd->push($combo);
            } else {
                $toKeep->push($exists['variant']);
            }
        }

        // Find variants to remove
        foreach ($existingCombos as $existing) {
            $stillNeeded = collect($newCombinations)->first(function ($combo) use ($existing) {
                return $this->combinationsMatch($existing['combo'], $combo);
            });

            if (!$stillNeeded) {
                $toRemove->push($existing['variant']);
            }
        }

        // Execute changes
        DB::transaction(function () use ($product, $toAdd, $toRemove, $defaults, &$result) {
            // Delete obsolete variants
            foreach ($toRemove as $variant) {
                $variant->delete();
            }

            // Add new variants
            $added = collect();
            foreach ($toAdd as $combo) {
                $added->push($this->createVariant($product, $combo, $defaults));
            }

            $result = $added;
        });

        return [
            'added' => $result ?? collect(),
            'removed' => $toRemove,
            'kept' => $toKeep,
        ];
    }

    /**
     * Check if two combinations match.
     *
     * @param array $combo1
     * @param array $combo2
     * @return bool
     */
    protected function combinationsMatch(array $combo1, array $combo2): bool
    {
        if (count($combo1) !== count($combo2)) {
            return false;
        }

        foreach ($combo1 as $key => $value) {
            if (!isset($combo2[$key]) || $combo2[$key] != $value) {
                return false;
            }
        }

        return true;
    }

    /**
     * Get variant attributes formatted for selection.
     * Returns attributes that have multiple values (suitable for variants).
     *
     * @param Product $product
     * @return Collection
     */
    public function getVariantAttributeOptions(Product $product): Collection
    {
        if (!$product->category_id) {
            return collect();
        }

        // Get category attributes with multiple values
        return $product->category->attributes()
            ->with('values')
            ->get()
            ->filter(function ($attribute) {
                // Only attributes with 2+ values can create variants
                return $attribute->values->count() >= 2;
            })
            ->map(function ($attribute) {
                return [
                    'id' => $attribute->id,
                    'name' => $attribute->name,
                    'type' => $attribute->type,
                    'values' => $attribute->values->map(function ($value) {
                        return [
                            'id' => $value->id,
                            'value' => $value->value,
                            'price_modifier' => $value->price_modifier ?? 0,
                        ];
                    }),
                ];
            });
    }

    /**
     * Calculate total possible variant count.
     *
     * @param array $attributeData
     * @return int
     */
    public function calculateVariantCount(array $attributeData): int
    {
        if (empty($attributeData)) {
            return 0;
        }

        $count = 1;
        foreach ($attributeData as $values) {
            $count *= count($values);
        }

        return $count;
    }

    /**
     * Set a variant as default.
     *
     * @param ProductVariant $variant
     * @return void
     */
    public function setDefaultVariant(ProductVariant $variant): void
    {
        DB::transaction(function () use ($variant) {
            // Remove default from other variants
            ProductVariant::where('product_id', $variant->product_id)
                ->where('id', '!=', $variant->id)
                ->update(['is_default' => false]);

            // Set this as default
            $variant->update(['is_default' => true]);
        });
    }
}
