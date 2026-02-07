<?php

namespace App\Services;

use App\Repositories\ProductRepository;
use App\Models\Product;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Session;

class CartService
{
    protected ProductRepository $productRepository;
    protected string $cartKey = 'shopping_cart';

    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    /**
     * Get cart contents
     */
    public function getCart(): array
    {
        return session()->get($this->cartKey, []);
    }

    /**
     * Get cart items with product details
     */
    public function getCartItems(): Collection
    {
        $cart = $this->getCart();
        $items = collect();

        foreach ($cart as $productId => $quantity) {
            $product = $this->productRepository->find($productId);

            if ($product) {
                $items->push([
                    'product' => $product,
                    'quantity' => $quantity,
                    'price' => $product->price,
                    'total' => $product->price * $quantity
                ]);
            }
        }

        return $items;
    }

    /**
     * Get cart total
     */
    public function getCartTotal(): float
    {
        return $this->getCartItems()->sum('total');
    }

    /**
     * Get total item count
     */
    public function getItemCount(): int
    {
        return array_sum($this->getCart());
    }

    /**
     * Add item to cart
     */
    public function addItem(int $productId, int $quantity = 1): bool
    {
        $product = $this->productRepository->find($productId);

        if (!$product || !$product->is_active) {
            return false;
        }

        if (!$this->productRepository->hasStock($productId, $quantity)) {
            return false;
        }

        $cart = $this->getCart();
        $currentQty = $cart[$productId] ?? 0;
        $newQty = $currentQty + $quantity;

        // Check stock limit
        if ($newQty > $product->stock_quantity) {
            $newQty = $product->stock_quantity;
        }

        $cart[$productId] = $newQty;
        session()->put($this->cartKey, $cart);

        return true;
    }

    /**
     * Update item quantity
     */
    public function updateItem(int $productId, int $quantity): bool
    {
        if ($quantity <= 0) {
            return $this->removeItem($productId);
        }

        $product = $this->productRepository->find($productId);

        if (!$product || !$this->productRepository->hasStock($productId, $quantity)) {
            return false;
        }

        $cart = $this->getCart();

        if (!isset($cart[$productId])) {
            return false;
        }

        $cart[$productId] = min($quantity, $product->stock_quantity);
        session()->put($this->cartKey, $cart);

        return true;
    }

    /**
     * Remove item from cart
     */
    public function removeItem(int $productId): bool
    {
        $cart = $this->getCart();
        unset($cart[$productId]);
        session()->put($this->cartKey, $cart);

        return true;
    }

    /**
     * Clear cart
     */
    public function clear(): bool
    {
        session()->forget($this->cartKey);

        return true;
    }

    /**
     * Apply coupon
     */
    public function applyCoupon(string $code): array
    {
        session()->put('coupon_code', $code);
        session()->put('discount_amount', 0);

        return [
            'success' => true,
            'message' => 'Coupon applied successfully',
            'discount' => 0
        ];
    }

    /**
     * Remove coupon
     */
    public function removeCoupon(): bool
    {
        session()->forget('coupon_code');
        session()->forget('discount_amount');

        return true;
    }

    /**
     * Get applied coupon
     */
    public function getCoupon(): ?string
    {
        return session()->get('coupon_code');
    }

    /**
     * Check if cart is empty
     */
    public function isEmpty(): bool
    {
        return empty($this->getCart());
    }

    /**
     * Validate cart for checkout
     */
    public function validateCart(): array
    {
        $errors = [];
        $items = $this->getCartItems();

        foreach ($items as $item) {
            if (!$item['product']->is_active) {
                $errors[] = "{$item['product']->name} is no longer available";
                continue;
            }

            if ($item['quantity'] > $item['product']->stock_quantity) {
                $errors[] = "Only {$item['product']->stock_quantity} units of {$item['product']->name} are available";
            }
        }

        return $errors;
    }

    /**
     * Get cart for checkout
     */
    public function getCheckoutData(): array
    {
        $items = $this->getCartItems();
        $subtotal = $items->sum('total');
        $discount = session()->get('discount_amount', 0);
        $shipping = $subtotal > 500 ? 0 : 50;
        $total = $subtotal - $discount + $shipping;

        return [
            'items' => $items,
            'subtotal' => $subtotal,
            'discount' => $discount,
            'shipping' => $shipping,
            'total' => $total
        ];
    }
}
