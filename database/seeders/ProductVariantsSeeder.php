<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\ProductAttribute;
use App\Models\ProductAttributeValue;
use App\Models\VariantAttribute;
use Illuminate\Support\Str;

class ProductVariantsSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🚀 Creating products with variants...');

        // Get or create Color and Size attributes
        $colorAttr = ProductAttribute::firstOrCreate(
            ['slug' => 'color'],
            ['name' => 'Color', 'type' => 'select', 'use_for_variants' => true]
        );

        $sizeAttr = ProductAttribute::firstOrCreate(
            ['slug' => 'size'],
            ['name' => 'Size', 'type' => 'select', 'use_for_variants' => true]
        );

        // Create color values
        $colors = ['Red', 'Blue', 'Green', 'Yellow', 'Pink', 'Black', 'White'];
        $colorValues = [];
        foreach ($colors as $color) {
            $colorValues[] = ProductAttributeValue::firstOrCreate(
                ['product_attribute_id' => $colorAttr->id, 'value' => $color],
                ['sort_order' => 0]
            );
        }

        // Create size values
        $sizes = ['Small', 'Medium', 'Large', 'XL'];
        $sizeValues = [];
        foreach ($sizes as $size) {
            $sizeValues[] = ProductAttributeValue::firstOrCreate(
                ['product_attribute_id' => $sizeAttr->id, 'value' => $size],
                ['sort_order' => 0]
            );
        }

        // Get some existing products or create new ones
        $products = Product::take(5)->get();

        if ($products->isEmpty()) {
            $this->command->warn('No products found to add variants to.');
            return;
        }

        $totalVariants = 0;

        foreach ($products as $product) {
            $this->command->info("Adding variants to: {$product->name}");

            // Select random colors and sizes
            $selectedColors = collect($colorValues)->random(rand(2, 3));
            $selectedSizes = collect($sizeValues)->random(rand(2, 3));

            foreach ($selectedColors as $colorValue) {
                foreach ($selectedSizes as $sizeValue) {
                    // Create the variant
                    $variant = ProductVariant::create([
                        'product_id' => $product->id,
                        'sku' => $product->sku . '-' . strtoupper(substr($colorValue->value, 0, 3)) . '-' . strtoupper(substr($sizeValue->value, 0, 1)),
                        'name' => $colorValue->value . ' / ' . $sizeValue->value,
                        'price' => $product->price + rand(-5, 10),
                        'compare_at_price' => $product->price + rand(5, 20),
                        'stock_quantity' => rand(10, 100),
                        'is_active' => true,
                        'is_default' => false,
                    ]);

                    // Link color attribute
                    VariantAttribute::create([
                        'product_variant_id' => $variant->id,
                        'product_attribute_id' => $colorAttr->id,
                        'product_attribute_value_id' => $colorValue->id,
                    ]);

                    // Link size attribute
                    VariantAttribute::create([
                        'product_variant_id' => $variant->id,
                        'product_attribute_id' => $sizeAttr->id,
                        'product_attribute_value_id' => $sizeValue->id,
                    ]);

                    $totalVariants++;
                }
            }

            // Set first variant as default
            $firstVariant = $product->variants()->first();
            if ($firstVariant) {
                $firstVariant->update(['is_default' => true]);
            }
        }

        $this->command->info("✅ Created {$totalVariants} variants for {$products->count()} products");
    }
}
