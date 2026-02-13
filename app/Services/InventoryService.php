<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\PurchaseBatch;
use App\Models\InventoryMovement;
use App\Models\Setting;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

class InventoryService
{
    const METHOD_FIFO = 'fifo';
    const METHOD_LIFO = 'lifo';

    /**
     * Get the configured costing method from settings.
     */
    public function getCostingMethod(): string
    {
        try {
            $method = Setting::where('key', 'inventory_costing_method')->value('value');
            return $method ?: self::METHOD_FIFO;
        } catch (\Throwable) {
            return self::METHOD_FIFO;
        }
    }

    // ─────────────────────────────────────────────────────────────
    //  BATCH QUERIES
    // ─────────────────────────────────────────────────────────────

    /**
     * Get available batches for a product (optionally variant), ordered by costing method.
     * Uses SELECT ... FOR UPDATE to prevent concurrent oversell.
     */
    public function getAvailableBatches(
        int $productId,
        ?int $variantId = null,
        string $method = self::METHOD_FIFO
    ): Collection {
        $query = PurchaseBatch::where('product_id', $productId)
            ->where('remaining_quantity', '>', 0)
            ->whereNotIn('status', [PurchaseBatch::STATUS_EXPIRED, PurchaseBatch::STATUS_DAMAGED])
            ->lockForUpdate();

        if ($variantId) {
            $query->where('product_variant_id', $variantId);
        }

        if ($method === self::METHOD_FIFO) {
            return $query->orderBy('purchase_date', 'asc')
                ->orderBy('created_at', 'asc')
                ->get();
        }

        return $query->orderBy('purchase_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get total available stock for a product from batches.
     */
    public function getBatchStock(int $productId, ?int $variantId = null): int
    {
        $query = PurchaseBatch::where('product_id', $productId)
            ->where('remaining_quantity', '>', 0)
            ->whereNotIn('status', [PurchaseBatch::STATUS_EXPIRED, PurchaseBatch::STATUS_DAMAGED]);

        if ($variantId) {
            $query->where('product_variant_id', $variantId);
        }

        return (int) $query->sum(DB::raw('remaining_quantity - quantity_reserved'));
    }

    // ─────────────────────────────────────────────────────────────
    //  STOCK DEDUCTION (FIFO/LIFO)  —  called when an order is placed
    // ─────────────────────────────────────────────────────────────

    /**
     * Deduct stock using FIFO/LIFO. Returns array of consumed batches.
     *
     * Each element: ['batch_id'=>int, 'quantity'=>int, 'unit_cost'=>float]
     *
     * MUST be called inside a DB::transaction from the order pipeline.
     */
    public function deductStock(
        int $productId,
        int $quantity,
        ?int $variantId = null,
        ?string $method = null,
        ?int $userId = null,
        ?string $referenceType = null,
        ?int $referenceId = null
    ): array {
        $method = $method ?? $this->getCostingMethod();
        $batches = $this->getAvailableBatches($productId, $variantId, $method);

        $usedBatches = [];
        $remainingQty = $quantity;

        foreach ($batches as $batch) {
            if ($remainingQty <= 0) {
                break;
            }

            // Only deduct from unreserved stock
            $available = $batch->remaining_quantity - $batch->quantity_reserved;
            if ($available <= 0) {
                continue;
            }

            $deductFromBatch = min($available, $remainingQty);

            // Update batch
            $batch->remaining_quantity -= $deductFromBatch;
            if ($batch->remaining_quantity <= 0) {
                $batch->status = PurchaseBatch::STATUS_SOLD;
            } elseif ($batch->remaining_quantity < $batch->quantity_received) {
                $batch->status = PurchaseBatch::STATUS_PARTIALLY_SOLD;
            }
            $batch->save();

            // Create movement record
            InventoryMovement::create([
                'product_id'         => $productId,
                'product_variant_id' => $variantId,
                'batch_id'           => $batch->id,
                'movement_type'      => InventoryMovement::TYPE_SALE,
                'quantity'           => -$deductFromBatch,
                'unit_cost'          => $batch->unit_cost,
                'reference_type'     => $referenceType,
                'reference_id'       => $referenceId,
                'user_id'            => $userId,
                'notes'              => "FIFO sale: {$deductFromBatch} units from batch #{$batch->batch_number}",
            ]);

            $usedBatches[] = [
                'batch_id'  => $batch->id,
                'quantity'  => $deductFromBatch,
                'unit_cost' => (float) $batch->unit_cost,
            ];

            $remainingQty -= $deductFromBatch;
        }

        if ($remainingQty > 0) {
            throw new \Exception(
                "Insufficient stock for product #{$productId}. " .
                "Needed {$quantity}, available " . ($quantity - $remainingQty)
            );
        }

        return $usedBatches;
    }

    /**
     * Calculate weighted average unit cost from consumed batches.
     */
    public function weightedAverageCost(array $usedBatches): float
    {
        $totalCost = 0;
        $totalQty  = 0;

        foreach ($usedBatches as $b) {
            $totalCost += $b['unit_cost'] * $b['quantity'];
            $totalQty  += $b['quantity'];
        }

        return $totalQty > 0 ? round($totalCost / $totalQty, 2) : 0;
    }

    // ─────────────────────────────────────────────────────────────
    //  STOCK RESTORATION  —  called on order cancellation / return
    // ─────────────────────────────────────────────────────────────

    /**
     * Restore stock back to batches (reverse of deductStock).
     * $usedBatches is the same array returned by deductStock().
     */
    public function restoreStock(
        int $productId,
        array $usedBatches,
        ?int $variantId = null,
        ?int $userId = null,
        ?string $referenceType = null,
        ?int $referenceId = null
    ): void {
        foreach ($usedBatches as $item) {
            $batch = PurchaseBatch::lockForUpdate()->find($item['batch_id']);
            if (!$batch) {
                continue;
            }

            $batch->remaining_quantity += $item['quantity'];

            // Re-evaluate status
            if ($batch->remaining_quantity >= $batch->quantity_received) {
                $batch->status = PurchaseBatch::STATUS_ACTIVE;
            } elseif ($batch->remaining_quantity > 0) {
                $batch->status = PurchaseBatch::STATUS_PARTIALLY_SOLD;
            }
            $batch->save();

            InventoryMovement::create([
                'product_id'         => $productId,
                'product_variant_id' => $variantId,
                'batch_id'           => $batch->id,
                'movement_type'      => InventoryMovement::TYPE_RETURN,
                'quantity'           => $item['quantity'],
                'unit_cost'          => $item['unit_cost'],
                'reference_type'     => $referenceType,
                'reference_id'       => $referenceId,
                'user_id'            => $userId,
                'notes'              => "Return/cancel: {$item['quantity']} units to batch #{$batch->batch_number}",
            ]);
        }
    }

    // ─────────────────────────────────────────────────────────────
    //  ADD STOCK (PURCHASE)
    // ─────────────────────────────────────────────────────────────

    /**
     * Add stock (new purchase batch).
     */
    public function addStock(
        Product $product,
        int $quantity,
        float $unitCost,
        array $details = []
    ): PurchaseBatch {
        return DB::transaction(function () use ($product, $quantity, $unitCost, $details) {
            $batch = PurchaseBatch::create([
                'product_id'              => $product->id,
                'product_variant_id'      => $details['product_variant_id'] ?? null,
                'partner_id'              => $details['partner_id'] ?? null,
                    'product_variant_id'      => $details['product_variant_id'] ?? null,
                'unit_cost'               => $unitCost,
                'quantity_received'       => $quantity,
                'remaining_quantity'      => $quantity,
                'status'                  => PurchaseBatch::STATUS_ACTIVE,
                'supplier'                => $details['supplier'] ?? null,
                'supplier_invoice_number' => $details['supplier_invoice_number'] ?? null,
                'purchase_date'           => $details['purchase_date'] ?? now()->toDateString(),
                'manufacture_date'        => $details['manufacture_date'] ?? null,
                'expiry_date'             => $details['expiry_date'] ?? null,
                'notes'                   => $details['notes'] ?? null,
            ]);

            InventoryMovement::create([

                $variantId = $details['product_variant_id'] ?? null;

                'product_id'         => $product->id,
                'product_variant_id' => $details['product_variant_id'] ?? null,
                    'product_variant_id' => $variantId,
                'movement_type'      => InventoryMovement::TYPE_PURCHASE,
                'quantity'           => $quantity,
                'unit_cost'          => $unitCost,
                'user_id'            => $details['user_id'] ?? null,
                'notes'              => "Purchase: {$quantity} units @ {$unitCost}",
            ]);


                // Increment product and variant counters to keep summaries in sync
                $product->increment('stock_quantity', $quantity);

                if ($variantId) {
                    if ($variant = ProductVariant::lockForUpdate()->find($variantId)) {
                        $variant->increment('stock_quantity', $quantity);
                    }
                }
            // Sync the product-level stock counter
            $product->increment('stock_quantity', $quantity);

            // If variant, sync variant stock too
            if (!empty($details['product_variant_id'])) {
                ProductVariant::where('id', $details['product_variant_id'])
                    ->increment('stock_quantity', $quantity);
            }

            return $batch;
        });
    }

    // ─────────────────────────────────────────────────────────────
    //  COSTING / REPORTING
    // ─────────────────────────────────────────────────────────────

    /**
     * Calculate weighted average cost for a product's remaining stock.
     */
    public function getAverageCost(int $productId, ?int $variantId = null): float
    {
        $query = PurchaseBatch::where('product_id', $productId)
            ->where('remaining_quantity', '>', 0)
            ->whereNotIn('status', [PurchaseBatch::STATUS_EXPIRED, PurchaseBatch::STATUS_DAMAGED]);

        if ($variantId) {
            $query->where('product_variant_id', $variantId);
        }

        $batches = $query->get();

        if ($batches->isEmpty()) {
            return 0;
        }

        $totalCost = $batches->sum(fn($b) => $b->remaining_quantity * $b->unit_cost);
        $totalQty  = $batches->sum('remaining_quantity');

        return $totalQty > 0 ? round($totalCost / $totalQty, 2) : 0;
    }

    /**
     * Calculate total stock valuation.
     */
    public function getStockValuation(int $productId, ?int $variantId = null): float
    {
        $query = PurchaseBatch::where('product_id', $productId)
            ->where('remaining_quantity', '>', 0);

        if ($variantId) {
            $query->where('product_variant_id', $variantId);
        }

        return $query->get()->sum(fn($b) => $b->remaining_quantity * $b->unit_cost);
    }

    /**
     * Get batch stock report for a product.
     */
    public function getBatchStockReport(Product $product): array
    {
        $batches = PurchaseBatch::where('product_id', $product->id)
            ->orderBy('purchase_date', 'desc')
            ->get();

        return [
            'product'            => $product,
            'total_batches'      => $batches->count(),
            'total_quantity'     => $batches->sum('quantity_received'),
            'remaining_quantity' => $batches->sum('remaining_quantity'),
            'sold_quantity'      => $batches->sum('quantity_received') - $batches->sum('remaining_quantity'),
            'total_value'        => $batches->sum(fn($b) => $b->remaining_quantity * $b->unit_cost),
            'average_cost'       => $this->getAverageCost($product->id),
            'batches'            => $batches,
        ];
    }

    /**
     * Get expiring batches.
     */
    public function getExpiringBatches(int $days = 30): Collection
    {
        return PurchaseBatch::whereNotNull('expiry_date')
            ->where('expiry_date', '>=', now())
            ->where('expiry_date', '<=', now()->addDays($days))
            ->where('remaining_quantity', '>', 0)
            ->orderBy('expiry_date', 'asc')
            ->get();
    }

    // ─────────────────────────────────────────────────────────────
    //  RESERVATION  —  optional reserve-then-confirm flow
    // ─────────────────────────────────────────────────────────────

    /**
     * Reserve stock across batches (FIFO).
     */
    public function reserveStock(
        int $productId,
        int $quantity,
        ?int $variantId = null,
        ?string $method = null
    ): array {
        $method  = $method ?? $this->getCostingMethod();
        $batches = $this->getAvailableBatches($productId, $variantId, $method);

        $reserved    = [];
        $remainingQty = $quantity;

        foreach ($batches as $batch) {
            if ($remainingQty <= 0) {
                break;
            }

            $available       = $batch->remaining_quantity - $batch->quantity_reserved;
            $reserveFromBatch = min($available, $remainingQty);

            if ($reserveFromBatch <= 0) {
                continue;
            }

            $batch->quantity_reserved += $reserveFromBatch;
            $batch->save();

            $reserved[] = [
                'batch_id' => $batch->id,
                'quantity' => $reserveFromBatch,
                'unit_cost' => (float) $batch->unit_cost,
            ];

            $remainingQty -= $reserveFromBatch;
        }

        if ($remainingQty > 0) {
            // Rollback
            foreach ($reserved as $item) {
                $batch = PurchaseBatch::find($item['batch_id']);
                if ($batch) {
                    $batch->quantity_reserved -= $item['quantity'];
                    $batch->save();
                }
            }
            throw new \Exception("Insufficient stock to reserve for product #{$productId}");
        }

        return $reserved;
    }

    /**
     * Release previously reserved stock.
     */
    public function releaseReservedStock(array $reservations): void
    {
        foreach ($reservations as $item) {
            $batch = PurchaseBatch::find($item['batch_id']);
            if ($batch) {
                $batch->quantity_reserved = max(0, $batch->quantity_reserved - $item['quantity']);
                $batch->save();
            }
        }
    }

    // ─────────────────────────────────────────────────────────────
    //  STOCK SYNC HELPERS
    // ─────────────────────────────────────────────────────────────

    /**
     * Sync a product's stock_quantity counter from batch totals.
     */
    public function syncProductStock(Product $product): void
    {
        $batchTotal = PurchaseBatch::where('product_id', $product->id)
            ->where('remaining_quantity', '>', 0)
            ->whereNotIn('status', [PurchaseBatch::STATUS_EXPIRED, PurchaseBatch::STATUS_DAMAGED])
            ->sum('remaining_quantity');

        $product->update(['stock_quantity' => $batchTotal]);
    }

    /**
     * Sync a variant's stock_quantity from its batch totals.
     */
    public function syncVariantStock(ProductVariant $variant): void
    {
        $batchTotal = PurchaseBatch::where('product_variant_id', $variant->id)
            ->where('remaining_quantity', '>', 0)
            ->whereNotIn('status', [PurchaseBatch::STATUS_EXPIRED, PurchaseBatch::STATUS_DAMAGED])
            ->sum('remaining_quantity');

        $variant->update(['stock_quantity' => $batchTotal]);
    }
}
