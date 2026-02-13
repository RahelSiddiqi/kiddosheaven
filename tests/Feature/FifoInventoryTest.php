<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Product;
use App\Models\PurchaseBatch;
use App\Models\InventoryMovement;
use App\Models\OrderItem;
use App\Services\InventoryService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;

class FifoInventoryTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * Ensure DatabaseTransactions uses the mysql connection.
     */
    protected function connectionsToTransact(): array
    {
        return ['mysql'];
    }

    /**
     * Full FIFO lifecycle test:
     *   Batch A: 10 @ ৳100 (Jan 1)
     *   Batch B: 10 @ ৳110 (Jan 15)
     *   Sell 5  → should consume from Batch A
     *   Restore 2 → should put back into Batch A
     *   Sell 12 → should consume 7 from A + 5 from B
     */
    public function test_fifo_lifecycle(): void
    {
        $service = app(InventoryService::class);

        // ── Setup: Create a test product ────────────────────
        $product = Product::create([
            'name' => 'FIFO Test Toy',
            'slug' => 'fifo-test-toy-' . uniqid(),
            'price' => 200,
            'cost_price' => 100,
            'stock_quantity' => 0,
            'sku' => 'FIFO-TEST-' . uniqid(),
        ]);

        // ── Add Batch A: 10 units @ ৳100 ───────────────────
        $batchA = $service->addStock($product, 10, 100.00, [
            'purchase_date' => '2026-01-01',
            'supplier' => 'Test Supplier A',
        ]);

        $this->assertEquals(10, $batchA->remaining_quantity);
        $this->assertEquals('active', $batchA->status);
        $product->refresh();
        $this->assertEquals(10, $product->stock_quantity);

        // ── Add Batch B: 10 units @ ৳110 ───────────────────
        $batchB = $service->addStock($product, 10, 110.00, [
            'purchase_date' => '2026-01-15',
            'supplier' => 'Test Supplier B',
        ]);

        $this->assertEquals(10, $batchB->remaining_quantity);
        $product->refresh();
        $this->assertEquals(20, $product->stock_quantity);

        // ── Verify average cost = (10×100 + 10×110) / 20 = 105 ──
        $avgCost = $service->getAverageCost($product->id);
        $this->assertEquals(105.00, $avgCost);

        // ── Step 1: Sell 5 units (FIFO → all from Batch A) ──
        $usedBatches = DB::transaction(fn() =>
            $service->deductStock(
                productId: $product->id,
                quantity: 5,
            )
        );

        $this->assertCount(1, $usedBatches);
        $this->assertEquals($batchA->id, $usedBatches[0]['batch_id']);
        $this->assertEquals(5, $usedBatches[0]['quantity']);
        $this->assertEquals(100.00, $usedBatches[0]['unit_cost']);

        // Verify weighted average cost from consumed batches
        $wac = $service->weightedAverageCost($usedBatches);
        $this->assertEquals(100.00, $wac);

        // Verify batch states
        $batchA->refresh();
        $this->assertEquals(5, $batchA->remaining_quantity);
        $this->assertEquals('partially_sold', $batchA->status);
        $batchB->refresh();
        $this->assertEquals(10, $batchB->remaining_quantity);

        // ── Step 2: Restore 2 units back to Batch A (cancel/return) ──
        DB::transaction(fn() =>
            $service->restoreStock(
                productId: $product->id,
                usedBatches: [['batch_id' => $batchA->id, 'quantity' => 2, 'unit_cost' => 100.00]],
            )
        );

        $batchA->refresh();
        $this->assertEquals(7, $batchA->remaining_quantity);
        $this->assertEquals('partially_sold', $batchA->status);

        // ── Step 3: Add Batch C: 10 @ ৳120 ─────────────────
        $batchC = $service->addStock($product, 10, 120.00, [
            'purchase_date' => '2026-02-01',
            'supplier' => 'Test Supplier C',
        ]);
        $this->assertEquals(10, $batchC->remaining_quantity);

        // ── Step 4: Sell 12 (FIFO: 7 from A + 5 from B) ────
        $usedBatches2 = DB::transaction(fn() =>
            $service->deductStock(
                productId: $product->id,
                quantity: 12,
            )
        );

        $this->assertCount(2, $usedBatches2);

        // First batch consumed: remaining 7 from Batch A @ ৳100
        $this->assertEquals($batchA->id, $usedBatches2[0]['batch_id']);
        $this->assertEquals(7, $usedBatches2[0]['quantity']);
        $this->assertEquals(100.00, $usedBatches2[0]['unit_cost']);

        // Second batch consumed: 5 from Batch B @ ৳110
        $this->assertEquals($batchB->id, $usedBatches2[1]['batch_id']);
        $this->assertEquals(5, $usedBatches2[1]['quantity']);
        $this->assertEquals(110.00, $usedBatches2[1]['unit_cost']);

        // Verify batch states
        $batchA->refresh();
        $this->assertEquals(0, $batchA->remaining_quantity);
        $this->assertEquals('sold', $batchA->status);

        $batchB->refresh();
        $this->assertEquals(5, $batchB->remaining_quantity);
        $this->assertEquals('partially_sold', $batchB->status);

        $batchC->refresh();
        $this->assertEquals(10, $batchC->remaining_quantity);
        $this->assertEquals('active', $batchC->status);

        // ── Verify COGS calculation ─────────────────────────
        // (7 × 100) + (5 × 110) = 700 + 550 = 1,250
        $wac2 = $service->weightedAverageCost($usedBatches2);
        $expectedWac = round((7 * 100 + 5 * 110) / 12, 2); // 104.17
        $this->assertEquals($expectedWac, $wac2);

        // ── Verify movement audit trail ─────────────────────
        $movements = InventoryMovement::where('product_id', $product->id)
            ->orderBy('id')
            ->get();

        // 2 purchases + 1 sale (5) + 1 return (2) + 1 purchase (C) + 2 sales (7+5) = 8
        $this->assertGreaterThanOrEqual(7, $movements->count());

        $purchaseMoves = $movements->where('movement_type', 'purchase');
        $saleMoves = $movements->where('movement_type', 'sale');
        $returnMoves = $movements->where('movement_type', 'return');

        $this->assertEquals(3, $purchaseMoves->count());
        $this->assertGreaterThanOrEqual(1, $returnMoves->count());

        // ── Verify stock valuation ──────────────────────────
        // Remaining: Batch B = 5 @ ৳110 = 550, Batch C = 10 @ ৳120 = 1200
        $valuation = $service->getStockValuation($product->id);
        $this->assertEquals(550 + 1200, $valuation);

        // No manual cleanup needed — DatabaseTransactions rolls back.
    }

    /**
     * Test insufficient stock throws exception.
     */
    public function test_insufficient_stock_throws_exception(): void
    {
        $service = app(InventoryService::class);

        $product = Product::create([
            'name' => 'Low Stock Toy',
            'slug' => 'low-stock-toy-' . uniqid(),
            'price' => 100,
            'stock_quantity' => 0,
            'sku' => 'LOW-TEST-' . uniqid(),
        ]);

        $service->addStock($product, 3, 50.00, ['purchase_date' => '2026-01-01']);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Insufficient stock');

        DB::transaction(fn() =>
            $service->deductStock(productId: $product->id, quantity: 5)
        );
    }
}
