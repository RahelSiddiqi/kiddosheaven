<?php

namespace App\Services\Cart;

use App\Repositories\Contracts\ProductRepositoryInterface;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Collection;

class CartService
{
    protected ProductRepositoryInterface $productRepository;
    protected string $sessionKey = 'shopping_cart';

    public function __construct(ProductRepositoryInterface $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    /**
     * Get cart items
     *
     * @return Collection
     */
    public function getItems(): Collection
    {
        $cart = Session::get($this->sessionKey, []);
        $items = collect();

        foreach ($cart as $id => $item) {
            $product = $this->productRepository->find($id);

            if ($product) {
                $items->push([
                    'product_id' => $product->id,
                    'product' => $product,
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'subtotal' => $item['price'] * $item['quantity'],
                ]);
            }
        }

        return $items;
    }

    /**
     * Add item to cart
     *
     * @param int $productId
     * @param int $quantity
     * @return array
     */
    public function addItem(int $productId, int $quantity = 1): array
    {
        $product = $this->productRepository->find($productId);

        if (!$product) {
            return ['success' => false, 'message' => 'Product not found'];
        }

        if (!$product->is_active) {
            return ['success' => false, 'message' => 'Product is not available'];
        }

        // Check stock
        if ($product->stock_quantity < $quantity) {
            return ['success' => false, 'message' => 'Insufficient stock'];
        }

        $cart = Session::get($this->sessionKey, []);

        // If product exists in cart, update quantity
        if (isset($cart[$productId])) {
            $newQuantity = $cart[$productId]['quantity'] + $quantity;

            // Check stock for total quantity
            if ($product->stock_quantity < $newQuantity) {
                return ['success' => false, 'message' => 'Insufficient stock'];
            }

            $cart[$productId]['quantity'] = $newQuantity;
        } else {
            // Add new item
            $cart[$productId] = [
                'quantity' => $quantity,
                'price' => $product->price,
            ];
        }

        Session::put($this->sessionKey, $cart);

        return [
            'success' => true,
            'message' => 'Product added to cart',
            'cart_count' => $this->getItemCount(),
        ];
    }

    /**
     * Update cart item quantity
     *
     * @param int $productId
     * @param int $quantity
     * @return array
     */
    public function updateItem(int $productId, int $quantity): array
    {
        if ($quantity <= 0) {
            return $this->removeItem($productId);
        }

        $product = $this->productRepository->find($productId);

        if (!$product) {
            return ['success' => false, 'message' => 'Product not found'];
        }

        // Check stock
        if ($product->stock_quantity < $quantity) {
            return ['success' => false, 'message' => 'Insufficient stock'];
        }

        $cart = Session::get($this->sessionKey, []);

        if (!isset($cart[$productId])) {
            return ['success' => false, 'message' => 'Product not in cart'];
        }

        $cart[$productId]['quantity'] = $quantity;
        $cart[$productId]['price'] = $product->price; // Update price if changed

        Session::put($this->sessionKey, $cart);

        return [
            'success' => true,
            'message' => 'Cart updated',
            'cart_totals' => $this->getTotals(),
        ];
    }

    /**
     * Remove item from cart
     *
     * @param int $productId
     * @return array
     */
    public function removeItem(int $productId): array
    {
        $cart = Session::get($this->sessionKey, []);

        if (!isset($cart[$productId])) {
            return ['success' => false, 'message' => 'Product not in cart'];
        }

        unset($cart[$productId]);
        Session::put($this->sessionKey, $cart);

        return [
            'success' => true,
            'message' => 'Product removed from cart',
            'cart_count' => $this->getItemCount(),
        ];
    }

    /**
     * Clear cart
     *
     * @return void
     */
    public function clear(): void
    {
        Session::forget($this->sessionKey);
    }

    /**
     * Get cart item count
     *
     * @return int
     */
    public function getItemCount(): int
    {
        $cart = Session::get($this->sessionKey, []);

        return array_sum(array_column($cart, 'quantity'));
    }

    /**
     * Get cart totals
     *
     * @return array
     */
    public function getTotals(): array
    {
        $items = $this->getItems();

        $subtotal = $items->sum('subtotal');
        $taxRate = 0; // Configure tax rate
        $tax = $subtotal * ($taxRate / 100);
        $shipping = $this->calculateShipping($subtotal);
        $total = $subtotal + $tax + $shipping;

        return [
            'subtotal' => $subtotal,
            'tax' => $tax,
            'shipping' => $shipping,
            'total' => $total,
            'item_count' => $this->getItemCount(),
        ];
    }

    /**
     * Check if product is in cart
     *
     * @param int $productId
     * @return bool
     */
    public function hasItem(int $productId): bool
    {
        $cart = Session::get($this->sessionKey, []);

        return isset($cart[$productId]);
    }

    /**
     * Get item quantity
     *
     * @param int $productId
     * @return int
     */
    public function getItemQuantity(int $productId): int
    {
        $cart = Session::get($this->sessionKey, []);

        return $cart[$productId]['quantity'] ?? 0;
    }

    /**
     * Validate cart items
     *
     * @return array
     */
    public function validate(): array
    {
        $items = $this->getItems();
        $errors = [];

        foreach ($items as $item) {
            $product = $item['product'];

            // Check if product still exists and is active
            if (!$product->is_active) {
                $errors[] = "{$product->name} is no longer available";
                $this->removeItem($product->id);
                continue;
            }

            // Check stock
            if ($product->stock_quantity < $item['quantity']) {
                $errors[] = "Insufficient stock for {$product->name}. Only {$product->stock_quantity} available";
            }

            // Check price changes
            if ($product->price != $item['price']) {
                $errors[] = "Price for {$product->name} has changed";
                $this->updateItem($product->id, $item['quantity']);
            }
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors,
        ];
    }

    /**
     * Calculate shipping cost
     *
     * @param float $subtotal
     * @return float
     */
    protected function calculateShipping(float $subtotal): float
    {
        // Free shipping over $100
        if ($subtotal >= 100) {
            return 0;
        }

        // Flat rate shipping
        return 10;
    }

    /**
     * Prepare cart for order
     *
     * @return array
     */
    public function prepareForOrder(): array
    {
        $items = $this->getItems();
        $totals = $this->getTotals();

        return [
            'items' => $items->map(function ($item) {
                return [
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                ];
            })->toArray(),
            'subtotal' => $totals['subtotal'],
            'tax_amount' => $totals['tax'],
            'shipping_amount' => $totals['shipping'],
            'total_amount' => $totals['total'],
        ];
    }
}
