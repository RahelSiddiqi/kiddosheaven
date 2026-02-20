<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Product;
use App\Services\Cart\CartService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Session;

/**
 * Regression test for the cart session key mismatch.
 *
 * Bug: CartService wrote to session key 'shopping_cart' but
 * the old Checkout component read from 'cart' — a different key.
 * After fix, Checkout delegates to CartService which uses the
 * single canonical key 'shopping_cart'.
 */
class CartServiceTest extends TestCase
{
    use DatabaseTransactions;

    protected function connectionsToTransact(): array
    {
        return ['mysql'];
    }

    // ── Session key is 'shopping_cart', NOT 'cart' ───────────────

    public function test_add_item_writes_to_shopping_cart_session_key(): void
    {
        $product = $this->makeProduct(stock: 10);

        $service = app(CartService::class);
        $service->addItem($product->id, 1);

        $this->assertNotNull(
            Session::get('shopping_cart'),
            'CartService must write to shopping_cart, not cart'
        );

        $this->assertNull(
            Session::get('cart'),
            'CartService must NOT write to the cart key — that was the stale session key'
        );
    }

    public function test_clear_removes_shopping_cart_session_key(): void
    {
        $product = $this->makeProduct(stock: 10);

        $service = app(CartService::class);
        $service->addItem($product->id, 2);
        $this->assertNotNull(Session::get('shopping_cart'));

        $service->clear();

        $this->assertNull(Session::get('shopping_cart'), 'clear() must remove shopping_cart key');
        $this->assertEquals(0, $service->getItemCount());
    }

    // ── Core cart operations ──────────────────────────────────────

    public function test_add_item_stores_correct_quantity_and_price(): void
    {
        $product = $this->makeProduct(price: 150.00, stock: 20);

        $service = app(CartService::class);
        $result  = $service->addItem($product->id, 3);

        $this->assertTrue($result['success']);
        $this->assertEquals(3, $service->getItemCount());
        $this->assertEquals(3, $service->getItemQuantity($product->id));

        $items = $service->getItems();
        $this->assertCount(1, $items);
        $this->assertEquals(150.00, (float) $items->first()['price']);
    }

    public function test_add_item_accumulates_quantity_on_repeat_call(): void
    {
        $product = $this->makeProduct(stock: 20);

        $service = app(CartService::class);
        $service->addItem($product->id, 2);
        $service->addItem($product->id, 3);

        $this->assertEquals(5, $service->getItemQuantity($product->id));
        $this->assertEquals(5, $service->getItemCount());
    }

    public function test_add_item_rejects_when_stock_is_insufficient(): void
    {
        $product = $this->makeProduct(stock: 2);

        $service = app(CartService::class);
        $result  = $service->addItem($product->id, 5);

        $this->assertFalse($result['success']);
        $this->assertStringContainsString('stock', strtolower($result['message']));
        $this->assertEquals(0, $service->getItemCount());
    }

    public function test_add_item_rejects_inactive_product(): void
    {
        $product = $this->makeProduct(stock: 10, active: false);

        $service = app(CartService::class);
        $result  = $service->addItem($product->id, 1);

        $this->assertFalse($result['success']);
    }

    public function test_update_item_changes_quantity(): void
    {
        $product = $this->makeProduct(stock: 20);

        $service = app(CartService::class);
        $service->addItem($product->id, 2);
        $service->updateItem($product->id, 7);

        $this->assertEquals(7, $service->getItemQuantity($product->id));
    }

    public function test_update_item_to_zero_removes_it(): void
    {
        $product = $this->makeProduct(stock: 10);

        $service = app(CartService::class);
        $service->addItem($product->id, 3);
        $service->updateItem($product->id, 0);

        $this->assertFalse($service->hasItem($product->id));
        $this->assertEquals(0, $service->getItemCount());
    }

    public function test_remove_item_removes_from_cart(): void
    {
        $product = $this->makeProduct(stock: 10);

        $service = app(CartService::class);
        $service->addItem($product->id, 4);
        $this->assertTrue($service->hasItem($product->id));

        $service->removeItem($product->id);
        $this->assertFalse($service->hasItem($product->id));
    }

    // ── prepareForOrder() produces correct structure for OrderService ──

    public function test_prepare_for_order_returns_correct_structure(): void
    {
        $product = $this->makeProduct(price: 200.00, stock: 10);

        $service = app(CartService::class);
        $service->addItem($product->id, 3);

        $prepared = $service->prepareForOrder();

        $this->assertArrayHasKey('items', $prepared);
        $this->assertArrayHasKey('subtotal', $prepared);
        $this->assertArrayHasKey('tax_amount', $prepared);
        $this->assertArrayHasKey('shipping_amount', $prepared);
        $this->assertArrayHasKey('total_amount', $prepared);

        $this->assertCount(1, $prepared['items']);
        $this->assertEquals($product->id, $prepared['items'][0]['product_id']);
        $this->assertEquals(3, $prepared['items'][0]['quantity']);
        $this->assertEquals(200.00, (float) $prepared['items'][0]['price']);
        $this->assertEquals(600.00, $prepared['subtotal']);
    }

    // ─────────────────────────────────────────────────────────────
    //  Helpers
    // ─────────────────────────────────────────────────────────────

    protected function makeProduct(
        float $price = 100.00,
        int $stock = 10,
        bool $active = true,
    ): Product {
        return Product::create([
            'name'           => 'Cart Test Product ' . uniqid(),
            'slug'           => 'cart-test-' . uniqid(),
            'price'          => $price,
            'cost_price'     => 50.00,
            'stock_quantity' => $stock,
            'sku'            => 'CART-' . uniqid(),
            'is_active'      => $active,
        ]);
    }
}
