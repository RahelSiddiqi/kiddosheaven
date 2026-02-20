<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Product;
use App\Models\PurchaseBatch;
use App\Models\InventoryMovement;
use App\Services\InventoryService;
use Illuminate\Foundation\Testing\DatabaseTransactions;

/**
 * Regression test for the addStock() double-increment bug.
 *
 * Bug: duplicate array keys in PurchaseBatch::create() and
 * InventoryMovement::create() plus two stock increment blocks
 * caused stock_quantity to be incremented twice per addStock() call.
 *
 * Covered fix: app/Domains/Inventory/Services/InventoryService.php
 *              app/Services/InventoryService.php (flat alias used by OrderService)
 */
class InventoryServiceAddStockTest extends TestCase
{
    use DatabaseTransactions;

    protected function connectionsToTransact(): array
    {
        return ['mysql'];
    }

    // ── addStock() creates exactly one batch and one movement ────

    public function test_add_stock_creates_exactly_one_batch(): void
    {
        $service = app(InventoryService::class);

        $product = $this->makeProduct();
        $batchCountBefore = PurchaseBatch::where('product_id', $product->id)->count();

        $service->addStock($product, 10, 100.00, [
            'purchase_date' => '2026-01-01',
            'supplier'      => 'Test Supplier',
        ]);

        $this->assertEquals(
            $batchCountBefore + 1,
            PurchaseBatch::where('product_id', $product->id)->count(),
            'Expected exactly one new batch record'
        );
    }

    public function test_add_stock_creates_exactly_one_movement(): void
    {
        $service = app(InventoryService::class);

        $product           = $this->makeProduct();
        $movementCountBefore = InventoryMovement::where('product_id', $product->id)->count();

        $service->addStock($product, 10, 100.00, [
            'purchase_date' => '2026-01-01',
        ]);

        $this->assertEquals(
            $movementCountBefore + 1,
            InventoryMovement::where('product_id', $product->id)->count(),
            'Expected exactly one new movement record'
        );
    }

    // ── stock_quantity incremented by the correct amount (not doubled) ──

    public function test_add_stock_increments_product_quantity_once(): void
    {
        $service = app(InventoryService::class);

        $product = $this->makeProduct(startingStock: 0);

        $service->addStock($product, 25, 50.00, ['purchase_date' => '2026-01-01']);

        $product->refresh();
        $this->assertEquals(
            25,
            $product->stock_quantity,
            'stock_quantity must be 25, not 50 — double-increment bug must not recur'
        );
    }

    public function test_add_stock_accumulates_correctly_across_multiple_batches(): void
    {
        $service = app(InventoryService::class);

        $product = $this->makeProduct(startingStock: 0);

        $service->addStock($product, 10, 100.00, ['purchase_date' => '2026-01-01']);
        $service->addStock($product, 15, 110.00, ['purchase_date' => '2026-01-15']);
        $service->addStock($product, 5,  120.00, ['purchase_date' => '2026-02-01']);

        $product->refresh();
        $this->assertEquals(
            30,
            $product->stock_quantity,
            'stock_quantity must be 30 (10 + 15 + 5), not 60 from double-increment'
        );
    }

    // ── batch fields are persisted correctly ─────────────────────

    public function test_add_stock_batch_has_correct_unit_cost_and_quantities(): void
    {
        $service = app(InventoryService::class);

        $product = $this->makeProduct();
        $batch   = $service->addStock($product, 20, 75.50, [
            'purchase_date'           => '2026-03-01',
            'supplier'                => 'ABC Imports',
            'supplier_invoice_number' => 'INV-001',
        ]);

        $this->assertEquals(20, $batch->quantity_received);
        $this->assertEquals(20, $batch->remaining_quantity);
        $this->assertEquals(75.50, (float) $batch->unit_cost);
        $this->assertEquals('active', $batch->status);
        $this->assertEquals('ABC Imports', $batch->supplier);
        $this->assertEquals('INV-001', $batch->supplier_invoice_number);
    }

    // ── movement record has correct type and quantity ─────────────

    public function test_add_stock_movement_is_type_purchase_with_positive_quantity(): void
    {
        $service = app(InventoryService::class);

        $product = $this->makeProduct();
        $service->addStock($product, 8, 200.00, ['purchase_date' => '2026-01-01']);

        $movement = InventoryMovement::where('product_id', $product->id)
            ->where('movement_type', 'purchase')
            ->latest()
            ->first();

        $this->assertNotNull($movement);
        $this->assertEquals(8, $movement->quantity);
        $this->assertEquals(200.00, (float) $movement->unit_cost);
    }

    // ── variant stock increment (if variant provided) ─────────────

    public function test_add_stock_for_variant_increments_variant_stock_once(): void
    {
        $service = app(InventoryService::class);

        $product = $this->makeProduct(startingStock: 0);

        // Create a minimal variant
        $variant = $product->variants()->create([
            'sku'            => 'VAR-TST-' . uniqid(),
            'price'          => 150.00,
            'stock_quantity' => 0,
            'is_active'      => true,
        ]);

        $service->addStock($product, 12, 90.00, [
            'purchase_date'      => '2026-01-01',
            'product_variant_id' => $variant->id,
        ]);

        $variant->refresh();
        $this->assertEquals(
            12,
            $variant->stock_quantity,
            'Variant stock_quantity must be 12, not 24 — double-increment must not recur'
        );
    }

    // ─────────────────────────────────────────────────────────────
    //  Helpers
    // ─────────────────────────────────────────────────────────────

    protected function makeProduct(int $startingStock = 0): Product
    {
        return Product::create([
            'name'           => 'Test Product ' . uniqid(),
            'slug'           => 'test-product-' . uniqid(),
            'price'          => 200.00,
            'cost_price'     => 100.00,
            'stock_quantity' => $startingStock,
            'sku'            => 'TEST-' . uniqid(),
        ]);
    }
}
