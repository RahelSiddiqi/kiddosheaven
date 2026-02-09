<?php

namespace App\Services;

use App\Models\Product;
use App\Models\PurchaseBatch;
use App\Models\InventoryMovement;
use App\Models\FinancialTransaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

class InventoryService
{
    const METHOD_FIFO = 'fifo'; // First In, First Out
    const METHOD_LIFO = 'lifo'; // Last In, First Out

    /**
     * Get available batches for a product, ordered by method.
     */
    public function getAvailableBatches(Product $product, string $method = self::METHOD_FIFO): Collection
    {
        $query = PurchaseBatch::where('product_id', $product->id)
            ->where('quantity_remaining', '>', 0)
            ->where('status', '!=', 'expired')
            ->where('status', '!=', 'damaged');

        if ($method === self::METHOD_FIFO) {
            return $query->orderBy('purchase_date', 'asc')
                ->orderBy('created_at', 'asc')
                ->get();
        } else {
            return $query->orderBy('purchase_date', 'desc')
                ->orderBy('created_at', 'desc')
                ->get();
        }
    }

    /**
     * Deduct stock using FIFO/LIFO method.
     * Returns array of batches used with quantities deducted.
     */
    public function deductStock(Product $product, int $quantity, string $method = self::METHOD_FIFO, float $sellingPrice = 0): array
    {
        $batches = $this->getAvailableBatches($product, $method);
        $usedBatches = [];
        $remainingQty = $quantity;

        foreach ($batches as $batch) {
            if ($remainingQty <= 0) {
                break;
            }

            $deductFromBatch = min($batch->quantity_remaining, $remainingQty);

            DB::transaction(function () use ($batch, $deductFromBatch, $product, $sellingPrice, &$usedBatches) {
                // Update batch
                $batch->quantity_remaining -= $deductFromBatch;
                if ($batch->quantity_remaining <= 0) {
                    $batch->status = 'sold';
                } elseif ($batch->quantity_remaining < $batch->quantity_received) {
                    $batch->status = 'partially_sold';
                }
                $batch->save();

                // Create movement record
                InventoryMovement::create([
                    'product_id' => $product->id,
                    'purchase_batch_id' => $batch->id,
                    'movement_type' => 'sale',
                    'quantity' => -$deductFromBatch,
                    'unit_cost' => $batch->unit_cost,
                    'total_cost' => $deductFromBatch * $batch->unit_cost,
                    'selling_price' => $sellingPrice,
                ]);

                $usedBatches[] = [
                    'batch_id' => $batch->id,
                    'quantity' => $deductFromBatch,
                    'unit_cost' => $batch->unit_cost,
                    'total_cost' => $deductFromBatch * $batch->unit_cost,
                ];
            });

            $remainingQty -= $deductFromBatch;
        }

        if ($remainingQty > 0) {
            throw new \Exception("Insufficient stock. Needed {$quantity}, but only have " . ($quantity - $remainingQty));
        }

        return $usedBatches;
    }

    /**
     * Add stock (purchase).
     */
    public function addStock(Product $product, int $quantity, float $unitCost, array $details = []): PurchaseBatch
    {
        return DB::transaction(function () use ($product, $quantity, $unitCost, $details) {
            $batch = PurchaseBatch::create([
                'product_id' => $product->id,
                'partner_id' => $details['partner_id'] ?? null,
                'unit_cost' => $unitCost,
                'total_cost' => $quantity * $unitCost,
                'quantity_received' => $quantity,
                'quantity_remaining' => $quantity,
                'supplier_invoice_number' => $details['supplier_invoice_number'] ?? null,
                'purchase_date' => $details['purchase_date'] ?? now(),
                'manufacture_date' => $details['manufacture_date'] ?? null,
                'expiry_date' => $details['expiry_date'] ?? null,
                'notes' => $details['notes'] ?? null,
            ]);

            // Create movement record
            InventoryMovement::create([
                'product_id' => $product->id,
                'purchase_batch_id' => $batch->id,
                'movement_type' => 'purchase',
                'quantity' => $quantity,
                'unit_cost' => $unitCost,
                'total_cost' => $quantity * $unitCost,
                'partner_id' => $details['partner_id'] ?? null,
            ]);

            // Create financial transaction for purchase
            FinancialTransaction::create([
                'transaction_type' => 'purchase',
                'amount' => $quantity * $unitCost,
                'cost_amount' => $quantity * $unitCost,
                'partner_id' => $details['partner_id'] ?? null,
                'reference_type' => PurchaseBatch::class,
                'reference_id' => $batch->id,
            ]);

            return $batch;
        });
    }

    /**
     * Calculate average cost for a product.
     */
    public function getAverageCost(Product $product): float
    {
        $batches = PurchaseBatch::where('product_id', $product->id)
            ->where('quantity_remaining', '>', 0)
            ->get();

        if ($batches->isEmpty()) {
            return 0;
        }

        $totalCost = $batches->sum(fn($batch) => $batch->quantity_remaining * $batch->unit_cost);
        $totalQty = $batches->sum('quantity_remaining');

        return $totalQty > 0 ? $totalCost / $totalQty : 0;
    }

    /**
     * Calculate total stock valuation.
     */
    public function getStockValuation(Product $product): float
    {
        $batches = PurchaseBatch::where('product_id', $product->id)
            ->where('quantity_remaining', '>', 0)
            ->get();

        return $batches->sum(fn($batch) => $batch->quantity_remaining * $batch->unit_cost);
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
            'product' => $product,
            'total_batches' => $batches->count(),
            'total_quantity' => $batches->sum('quantity_received'),
            'remaining_quantity' => $batches->sum('quantity_remaining'),
            'sold_quantity' => $batches->sum('quantity_received') - $batches->sum('quantity_remaining'),
            'total_value' => $batches->sum(fn($b) => $b->quantity_remaining * $b->unit_cost),
            'average_cost' => $this->getAverageCost($product),
            'batches' => $batches,
        ];
    }

    /**
     * Calculate profit from sold batches.
     */
    public function calculateProfitFromBatches(array $batchIds, float $totalRevenue): float
    {
        $totalCost = PurchaseBatch::whereIn('id', $batchIds)
            ->get()
            ->sum(fn($batch) => $batch->quantity_received * $batch->unit_cost);

        return $totalRevenue - $totalCost;
    }

    /**
     * Get expiring batches.
     */
    public function getExpiringBatches(int $days = 30): Collection
    {
        return PurchaseBatch::whereNotNull('expiry_date')
            ->where('expiry_date', '>=', now())
            ->where('expiry_date', '<=', now()->addDays($days))
            ->where('quantity_remaining', '>', 0)
            ->orderBy('expiry_date', 'asc')
            ->get();
    }

    /**
     * Reserve stock (for pending orders).
     */
    public function reserveStock(Product $product, int $quantity, string $method = self::METHOD_FIFO): array
    {
        $batches = $this->getAvailableBatches($product, $method);
        $reserved = [];
        $remainingQty = $quantity;

        foreach ($batches as $batch) {
            if ($remainingQty <= 0) {
                break;
            }

            $reserveFromBatch = min($batch->quantity_remaining - $batch->quantity_reserved, $remainingQty);

            $batch->quantity_reserved += $reserveFromBatch;
            $batch->save();

            $reserved[] = [
                'batch_id' => $batch->id,
                'quantity' => $reserveFromBatch,
            ];

            $remainingQty -= $reserveFromBatch;
        }

        if ($remainingQty > 0) {
            // Rollback reservations
            foreach ($reserved as $item) {
                $batch = PurchaseBatch::find($item['batch_id']);
                $batch->quantity_reserved -= $item['quantity'];
                $batch->save();
            }
            throw new \Exception("Insufficient stock to reserve");
        }

        return $reserved;
    }

    /**
     * Release reserved stock.
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
}
