<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\PurchaseBatch;
use Illuminate\Support\Facades\DB;

class SyncStockCounters extends Command
{
    protected $signature = 'inventory:sync-stock {--dry-run : Show drift without fixing}';
    protected $description = 'Sync products.stock_quantity from purchase_batches.remaining_quantity totals';

    public function handle(): int
    {
        $dryRun = $this->option('dry-run');

        $this->info($dryRun ? '🔍 Dry-run mode — showing drift only' : '🔧 Syncing stock counters...');

        // ── Products ────────────────────────────────────────
        $products = Product::all();
        $fixed = 0;

        foreach ($products as $product) {
            $batchStock = (int) PurchaseBatch::where('product_id', $product->id)
                ->where('remaining_quantity', '>', 0)
                ->whereNotIn('status', ['expired', 'damaged'])
                ->sum('remaining_quantity');

            $drift = $product->stock_quantity - $batchStock;

            if ($drift !== 0) {
                if ($dryRun) {
                    $this->warn("  Product #{$product->id} '{$product->name}': counter={$product->stock_quantity}, batches={$batchStock}, drift={$drift}");
                } else {
                    $product->update(['stock_quantity' => $batchStock]);
                    $this->line("  ✅ Product #{$product->id} '{$product->name}': {$product->stock_quantity} → {$batchStock} (drift was {$drift})");
                }
                $fixed++;
            }
        }

        // ── Variants ────────────────────────────────────────
        $variants = ProductVariant::all();
        $variantFixed = 0;

        foreach ($variants as $variant) {
            $batchStock = (int) PurchaseBatch::where('product_variant_id', $variant->id)
                ->where('remaining_quantity', '>', 0)
                ->whereNotIn('status', ['expired', 'damaged'])
                ->sum('remaining_quantity');

            $drift = $variant->stock_quantity - $batchStock;

            if ($drift !== 0) {
                if ($dryRun) {
                    $this->warn("  Variant #{$variant->id} (SKU: {$variant->sku}): counter={$variant->stock_quantity}, batches={$batchStock}, drift={$drift}");
                } else {
                    $variant->update(['stock_quantity' => $batchStock]);
                    $this->line("  ✅ Variant #{$variant->id}: {$variant->stock_quantity} → {$batchStock}");
                }
                $variantFixed++;
            }
        }

        $this->newLine();
        $action = $dryRun ? 'drifted' : 'fixed';
        $this->info("Products {$action}: {$fixed}/{$products->count()}");
        $this->info("Variants {$action}: {$variantFixed}/{$variants->count()}");

        return self::SUCCESS;
    }
}
