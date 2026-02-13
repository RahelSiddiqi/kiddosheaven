<?php

namespace App\Services\Product;

use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\ProductAttribute;
use App\Models\ProductAttributeValue;
use App\Models\VariantAttribute;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class ProductVariantService
{
    /**
     * Generate variants from attribute combinations
     *
     * @param Product $product
     * @param array $attributeData Format: [['attribute_id' => 1, 'value_ids' => [1,2,3]], ...]
     * @return array
     */
    public function generateVariants(Product $product, array $attributeData): array
    {
        DB::beginTransaction();

        try {
            // Get attribute details with values
            $attributes = $this->prepareAttributeData($attributeData);

            // Generate all combinations
            $combinations = $this->createCombinations($attributes);

            $createdVariants = [];
            $skippedVariants = [];

            foreach ($combinations as $combination) {
                // Check if variant with these attributes already exists
                if ($this->variantExists($product, $combination['value_ids'])) {
                    $skippedVariants[] = $combination['name'];
                    continue;
                }

                // Create variant
                $variant = $this->createVariant($product, $combination);

                // Attach attribute values to variant
                $this->attachAttributeValues($variant, $combination['attributes']);

                $createdVariants[] = $variant;
            }

            DB::commit();

            return [
                'success' => true,
                'created_count' => count($createdVariants),
                'skipped_count' => count($skippedVariants),
                'created_variants' => $createdVariants,
                'skipped_variants' => $skippedVariants
            ];

        } catch (\Exception $e) {
            DB::rollBack();

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Prepare attribute data with full details
     */
    protected function prepareAttributeData(array $attributeData): array
    {
        $prepared = [];

        foreach ($attributeData as $data) {
            $attribute = ProductAttribute::find($data['attribute_id']);

            if (!$attribute) {
                continue;
            }

            $values = ProductAttributeValue::whereIn('id', $data['value_ids'])
                ->where('product_attribute_id', $attribute->id)
                ->get();

            $prepared[] = [
                'id' => $attribute->id,
                'name' => $attribute->name,
                'values' => $values->map(function($value) {
                    return [
                        'id' => $value->id,
                        'value' => $value->value
                    ];
                })->toArray()
            ];
        }

        return $prepared;
    }

    /**
     * Create all possible combinations from attributes
     */
    protected function createCombinations(array $attributes, int $index = 0, array $current = []): array
    {
        if ($index >= count($attributes)) {
            return [$current];
        }

        $results = [];
        $currentAttribute = $attributes[$index];

        foreach ($currentAttribute['values'] as $value) {
            $newCurrent = $current;
            $newCurrent['attributes'][] = [
                'attribute_id' => $currentAttribute['id'],
                'attribute_name' => $currentAttribute['name'],
                'value_id' => $value['id'],
                'value' => $value['value']
            ];

            // Build variant name
            $names = array_column($newCurrent['attributes'], 'value');
            $newCurrent['name'] = implode(' / ', $names);
            $newCurrent['value_ids'] = array_column($newCurrent['attributes'], 'value_id');

            $results = array_merge(
                $results,
                $this->createCombinations($attributes, $index + 1, $newCurrent)
            );
        }

        return $results;
    }

    /**
     * Check if variant with these attribute values already exists
     */
    protected function variantExists(Product $product, array $valueIds): bool
    {
        $variants = $product->variants;

        foreach ($variants as $variant) {
            $variantValueIds = $variant->variantAttributes()
                ->pluck('product_attribute_value_id')
                ->sort()
                ->values()
                ->toArray();

            sort($valueIds);

            if ($variantValueIds === $valueIds) {
                return true;
            }
        }

        return false;
    }

    /**
     * Create a single variant
     */
    protected function createVariant(Product $product, array $combination): ProductVariant
    {
        // Generate unique SKU
        $sku = $this->generateSku($product, $combination);

        return ProductVariant::create([
            'product_id' => $product->id,
            'name' => $combination['name'],
            'sku' => $sku,
            'price' => $product->price, // Inherit from product
            'cost_price' => $product->cost_price ?? null,
            'stock_quantity' => 0,
            'is_active' => true,
            'is_default' => false
        ]);
    }

    /**
     * Attach attribute values to variant
     */
    protected function attachAttributeValues(ProductVariant $variant, array $attributes): void
    {
        foreach ($attributes as $attr) {
            VariantAttribute::create([
                'product_variant_id' => $variant->id,
                'product_attribute_id' => $attr['attribute_id'],
                'product_attribute_value_id' => $attr['value_id']
            ]);
        }
    }

    /**
     * Generate unique SKU for variant
     */
    public function generateSku(Product $product, array $combination): string
    {
        // Get product SKU or generate from name
        $baseSku = $product->sku ?? $this->generateProductSku($product);

        // Create suffix from attribute values (first 3 letters of each)
        $suffix = '';
        foreach ($combination['attributes'] as $attr) {
            $suffix .= '-' . strtoupper(substr($attr['value'], 0, 3));
        }

        $sku = $baseSku . $suffix;

        // Ensure uniqueness
        $counter = 1;
        $originalSku = $sku;

        while (ProductVariant::where('sku', $sku)->exists()) {
            $sku = $originalSku . '-' . $counter;
            $counter++;
        }

        return $sku;
    }

    /**
     * Generate SKU from product name
     */
    protected function generateProductSku(Product $product): string
    {
        // Take first letters of each word, max 8 chars
        $words = explode(' ', $product->name);
        $sku = '';

        foreach ($words as $word) {
            $sku .= strtoupper(substr($word, 0, 1));
            if (strlen($sku) >= 8) break;
        }

        // Add random suffix if too short
        if (strlen($sku) < 4) {
            $sku .= strtoupper(Str::random(4 - strlen($sku)));
        }

        return 'SKU-' . $sku;
    }

    /**
     * Bulk update variants
     */
    public function bulkUpdate(array $variantIds, string $field, $value, bool $asPercentage = false): int
    {
        $variants = ProductVariant::whereIn('id', $variantIds)->get();
        $updated = 0;

        foreach ($variants as $variant) {
            switch ($field) {
                case 'cost_price':
                    $variant->cost_price = $value;
                    break;

                case 'price':
                    $variant->price = $value;
                    break;

                case 'sale':
                    if ($asPercentage) {
                        $variant->compare_at_price = $variant->price;
                        $variant->price = $variant->price * (1 - $value / 100);
                    } else {
                        $variant->compare_at_price = $variant->price;
                        $variant->price = $value;
                    }
                    break;

                case 'stock':
                    $variant->stock_quantity = (int) $value;
                    break;
            }

            if ($variant->save()) {
                $updated++;
            }
        }

        return $updated;
    }

    /**
     * Delete variant and its attributes
     */
    public function deleteVariant(ProductVariant $variant): bool
    {
        DB::beginTransaction();

        try {
            // Delete variant attributes
            VariantAttribute::where('product_variant_id', $variant->id)->delete();

            // Delete variant
            $variant->delete();

            DB::commit();
            return true;

        } catch (\Exception $e) {
            DB::rollBack();
            return false;
        }
    }

    /**
     * Clone variants from one product to another
     */
    public function cloneVariants(Product $sourceProduct, Product $targetProduct): array
    {
        $variants = $sourceProduct->variants;
        $cloned = [];

        DB::beginTransaction();

        try {
            foreach ($variants as $variant) {
                $newVariant = $variant->replicate();
                $newVariant->product_id = $targetProduct->id;
                $newVariant->sku = $this->generateSku($targetProduct, [
                    'attributes' => $variant->variantAttributes->map(function($va) {
                        return [
                            'value' => $va->attributeValue->value ?? 'VAR'
                        ];
                    })->toArray()
                ]);
                $newVariant->save();

                // Clone variant attributes
                foreach ($variant->variantAttributes as $va) {
                    VariantAttribute::create([
                        'product_variant_id' => $newVariant->id,
                        'product_attribute_id' => $va->product_attribute_id,
                        'product_attribute_value_id' => $va->product_attribute_value_id
                    ]);
                }

                $cloned[] = $newVariant;
            }

            DB::commit();

            return [
                'success' => true,
                'cloned_count' => count($cloned)
            ];

        } catch (\Exception $e) {
            DB::rollBack();

            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
}
