<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Product;
use App\Models\Order;
use App\Models\InventoryMovement;
use App\Services\Cart\CartService;
use App\Services\InventoryService;
use App\Services\Order\OrderService;
use App\Events\OrderPlaced;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Session;

/**
 * End-to-end regression test for the checkout domain bypass.
 *
 * Bug: Livewire Checkout.php directly created Order and OrderItem
 * models. It bypassed OrderService and InventoryService entirely.
 * Consequences:
 *   - No FIFO batch deduction ran
 *   - inventory_movements were not created
 *   - order_items had null unit_cost and null purchase_batch_id
 *   - OrderPlaced event was not fired (no confirmation email)
 *   - Stock counters were not decremented
 *
 * Fix: Checkout.php now delegates entirely to OrderService::create().
 * These tests verify the full pipeline that OrderService executes.
 *
 * Column mapping (real schema):
 *   user_id        — auth user FK (nullable for guest)
 *   address_line   — shipping street address
 *   city           — shipping city
 *   postal_code    — shipping ZIP
 *   (no shipping_state, shipping_amount, payment_status columns)
 */
class CheckoutOrderPipelineTest extends TestCase
{
    use DatabaseTransactions;

    protected function connectionsToTransact(): array
    {
        return ['mysql'];
    }

    // ─────────────────────────────────────────────────────────────
    //  OrderService creates order with FIFO cost data
    // ─────────────────────────────────────────────────────────────

    public function test_order_service_creates_order_with_fifo_cost_on_items(): void
    {
        $inventoryService = app(InventoryService::class);
        $orderService     = app(OrderService::class);

        $product = $this->makeProduct();

        $batch = $inventoryService->addStock($product, 20, 85.00, [
            'purchase_date' => '2026-01-01',
            'supplier'      => 'Test Supplier',
        ]);

        $product->refresh();
        $this->assertEquals(20, $product->stock_quantity);

        $order = DB::transaction(fn() => $orderService->create(
            $this->orderPayload(
                product: $product,
                qty: 5,
                price: 200.00,
                name: 'Jane Doe',
                email: 'jane@test.com',
            )
        ));

        // ── Order record ──────────────────────────────────────────
        $this->assertInstanceOf(Order::class, $order);
        $this->assertNotEmpty($order->order_number);
        $this->assertEquals('pending', $order->status);

        // ── OrderItem has FIFO cost data ──────────────────────────
        $this->assertCount(1, $order->items);
        $item = $order->items->first();

        $this->assertEquals($product->id, $item->product_id);
        $this->assertEquals(5, $item->quantity);
        $this->assertEquals(85.00, (float) $item->unit_cost,
            'unit_cost must be the batch cost (85), not null — FIFO must have run');
        $this->assertEquals($batch->id, $item->purchase_batch_id,
            'purchase_batch_id must reference the batch consumed by FIFO');

        // ── Batch decremented ─────────────────────────────────────
        $batch->refresh();
        $this->assertEquals(15, $batch->remaining_quantity,
            'Batch must have 15 remaining (20 − 5)');
        $this->assertEquals('partially_sold', $batch->status);

        // ── inventory_movements created ───────────────────────────
        $movement = InventoryMovement::where('product_id', $product->id)
            ->where('movement_type', 'sale')
            ->latest()
            ->first();

        $this->assertNotNull($movement, 'A sale movement record must be created');
        $this->assertEquals(-5, $movement->quantity);
        $this->assertEquals($batch->id, $movement->batch_id);
        $this->assertEquals(Order::class, $movement->reference_type);
        $this->assertEquals($order->id, $movement->reference_id);

        // ── Product stock_quantity decremented ────────────────────
        $product->refresh();
        $this->assertEquals(15, $product->stock_quantity,
            'Product stock_quantity must drop from 20 to 15');
    }

    // ─────────────────────────────────────────────────────────────
    //  OrderPlaced event is fired
    // ─────────────────────────────────────────────────────────────

    public function test_order_service_fires_order_placed_event(): void
    {
        Event::fake([OrderPlaced::class]);

        $inventoryService = app(InventoryService::class);
        $orderService     = app(OrderService::class);

        $product = $this->makeProduct();
        $inventoryService->addStock($product, 10, 100.00, ['purchase_date' => '2026-01-01']);

        DB::transaction(fn() => $orderService->create(
            $this->orderPayload(
                product: $product,
                qty: 2,
                price: 150.00,
                email: 'event-check@test.com',
            )
        ));

        Event::assertDispatched(OrderPlaced::class, fn($event) =>
            $event->order->customer_email === 'event-check@test.com'
        );
    }

    // ─────────────────────────────────────────────────────────────
    //  Order number uses structured format (not old random hex)
    // ─────────────────────────────────────────────────────────────

    public function test_order_service_generates_structured_order_number(): void
    {
        $inventoryService = app(InventoryService::class);
        $orderService     = app(OrderService::class);

        $product = $this->makeProduct();
        $inventoryService->addStock($product, 10, 50.00, ['purchase_date' => '2026-01-01']);

        $order = DB::transaction(fn() => $orderService->create(
            $this->orderPayload(product: $product, qty: 1, price: 100.00)
        ));

        $this->assertStringStartsWith('ORD', $order->order_number,
            'Order number must start with ORD prefix, not the old Str::random hex format');
    }

    // ─────────────────────────────────────────────────────────────
    //  Insufficient stock raises exception (no order created)
    // ─────────────────────────────────────────────────────────────

    public function test_order_service_throws_on_insufficient_stock(): void
    {
        $inventoryService = app(InventoryService::class);
        $orderService     = app(OrderService::class);

        $product = $this->makeProduct();
        $inventoryService->addStock($product, 2, 50.00, ['purchase_date' => '2026-01-01']);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessageMatches('/insufficient stock/i');

        DB::transaction(fn() => $orderService->create(
            $this->orderPayload(product: $product, qty: 10, price: 100.00)
        ));
    }

    // ─────────────────────────────────────────────────────────────
    //  Cancel restores stock back to batches
    // ─────────────────────────────────────────────────────────────

    public function test_cancel_restores_stock_to_original_batches(): void
    {
        $inventoryService = app(InventoryService::class);
        $orderService     = app(OrderService::class);

        $product = $this->makeProduct();
        $batch   = $inventoryService->addStock($product, 20, 100.00, [
            'purchase_date' => '2026-01-01',
        ]);

        $order = DB::transaction(fn() => $orderService->create(
            $this->orderPayload(product: $product, qty: 8, price: 200.00)
        ));

        $batch->refresh();
        $this->assertEquals(12, $batch->remaining_quantity);

        DB::transaction(fn() => $orderService->cancel($order->id));

        $batch->refresh();
        $this->assertEquals(20, $batch->remaining_quantity,
            'All 8 units must be restored to the batch on cancellation');

        $returnMovement = InventoryMovement::where('product_id', $product->id)
            ->where('movement_type', 'return')
            ->where('reference_id', $order->id)
            ->first();

        $this->assertNotNull($returnMovement, 'A return movement must be recorded');
        $this->assertEquals(8, $returnMovement->quantity);
    }

    // ─────────────────────────────────────────────────────────────
    //  CartService + OrderService integration (session key end-to-end)
    // ─────────────────────────────────────────────────────────────

    public function test_cart_to_order_pipeline_uses_correct_session_key_and_fifo(): void
    {
        $inventoryService = app(InventoryService::class);
        $cartService      = app(CartService::class);
        $orderService     = app(OrderService::class);

        $product = $this->makeProduct(price: 250.00);
        $inventoryService->addStock($product, 15, 120.00, ['purchase_date' => '2026-01-01']);

        $cartService->addItem($product->id, 4);

        // Verify the correct session key is used
        $this->assertEquals(4, $cartService->getItemCount());
        $this->assertNotNull(Session::get('shopping_cart'),
            'CartService must write to shopping_cart, not cart');
        $this->assertNull(Session::get('cart'),
            'The stale cart key must remain empty');

        $cartData = $cartService->prepareForOrder();

        $order = DB::transaction(fn() => $orderService->create([
            'customer_name'  => 'Cart User',
            'customer_email' => 'cart@test.com',
            'customer_phone' => '01400000000',
            'address_line'   => '99 Cart Lane',
            'city'           => 'Dhaka',
            'postal_code'    => '1200',
            'payment_method' => 'cod',
            'items'          => $cartData['items'],
            'subtotal'       => 1000.00,
            'tax_amount'     => 150.00,
            'total_amount'   => 1150.00,
        ]));

        $cartService->clear();

        // Order has FIFO cost data
        $this->assertCount(1, $order->items);
        $this->assertEquals(120.00, (float) $order->items->first()->unit_cost,
            'unit_cost must be populated from the FIFO batch, not null');
        $this->assertNotNull($order->items->first()->purchase_batch_id);

        // Cart is cleared from the correct session key
        $this->assertNull(Session::get('shopping_cart'));
        $this->assertEquals(0, $cartService->getItemCount());
    }

    // ─────────────────────────────────────────────────────────────
    //  Helpers
    // ─────────────────────────────────────────────────────────────

    protected function makeProduct(float $price = 200.00): Product
    {
        return Product::create([
            'name'           => 'Checkout Test Product ' . uniqid(),
            'slug'           => 'checkout-test-' . uniqid(),
            'price'          => $price,
            'cost_price'     => 100.00,
            'stock_quantity' => 0,
            'sku'            => 'CHK-' . uniqid(),
        ]);
    }

    /**
     * Build a minimal but valid orderService::create() payload using real column names.
     */
    protected function orderPayload(
        Product $product,
        int $qty,
        float $price,
        string $name = 'Test Customer',
        string $email = 'test@pipeline.com',
        string $phone = '01700000000',
    ): array {
        $subtotal = $price * $qty;
        $tax      = round($subtotal * 0.15, 2);

        return [
            'customer_name'  => $name,
            'customer_email' => $email,
            'customer_phone' => $phone,
            'address_line'   => '1 Pipeline Road',
            'city'           => 'Dhaka',
            'postal_code'    => '1200',
            'payment_method' => 'cod',
            'items'          => [
                ['product_id' => $product->id, 'quantity' => $qty, 'price' => $price],
            ],
            'subtotal'     => $subtotal,
            'tax_amount'   => $tax,
            'total_amount' => $subtotal + $tax,
        ];
    }
}
