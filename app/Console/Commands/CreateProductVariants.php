<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\ProductAttribute;
use App\Models\ProductAttributeValue;
use App\Models\VariantAttribute;

class CreateProductVariants extends Command
{
    protected $signature = 'products:create-variants {count=5}';
    protected $description = 'Create variants for existing products';

    public function handle()
    {
        $this->info('Creating product variants...');

        $count = (int) $this->argument('count');
        $products = Product::take($count)->get();

        if ($products->isEmpty()) {
            $this->error('No products found!');
            return 1;
        }

        $colorAttr = ProductAttribute::where('slug', 'color')->first();
        $sizeAttr = ProductAttribute::where('slug', 'size')->first();

        if (!$colorAttr || !$sizeAttr) {
            $this->error('Color or Size attributes not found!');
            return 1;
        }

        $colors = $colorAttr->values()->take(3)->get();
        $sizes = $sizeAttr->values()->take(2)->get();

        if ($colors->isEmpty() || $sizes->isEmpty()) {
            $this->error('No attribute values found!');
            return 1;
        }

        $this->info("Found {$colors->count()} colors and {$sizes->count()} sizes");

        $total = 0;
        foreach ($products as $product) {
            // Skip if product already has variants
            if ($product->variants()->exists()) {
                $this->warn("Skipping {$product->name} (already has variants)");
                continue;
            }

            $this->info("Adding variants to: {$product->name}");

            foreach ($colors as $color) {
                foreach ($sizes as $size) {
                    $sku = $product->sku . '-' . strtoupper(substr($color->value, 0, 3)) . '-' . strtoupper(substr($size->value, 0, 1));

                    // Skip if SKU already exists
                    if (ProductVariant::where('sku', $sku)->exists()) {
                        continue;
                    }

                    $variant = ProductVariant::create([
                        'product_id' => $product->id,
                        'sku' => $sku,
                        'name' => $color->value . ' / ' . $size->value,
                        'price' => $product->price + rand(-5, 10),
                        'compare_at_price' => $product->price + 15,
                        'stock_quantity' => rand(10, 50),
                        'is_active' => true,
                    ]);

                    VariantAttribute::create([
                        'product_variant_id' => $variant->id,
                        'product_attribute_id' => $colorAttr->id,
                        'product_attribute_value_id' => $color->id,
                    ]);

                    VariantAttribute::create([
                        'product_variant_id' => $variant->id,
                        'product_attribute_id' => $sizeAttr->id,
                        'product_attribute_value_id' => $size->id,
                    ]);

                    $total++;
                }
            }
        }

        $this->info("✅ Created {$total} variants for {$products->count()} products");
        return 0;
    }
}
