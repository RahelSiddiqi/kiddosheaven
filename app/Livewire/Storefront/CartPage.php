<?php

namespace App\Livewire\Storefront;

use Livewire\Component;
use App\Services\Cart\CartService;
use App\Domains\Marketing\Models\Coupon;

class CartPage extends Component
{
    public array  $cart     = ['items' => []];
    public float  $subtotal = 0;
    public float  $tax      = 0;
    public float  $taxRate  = 0;
    public float  $shipping = 0;
    public float  $total    = 0;

    // ── Coupon ────────────────────────────────────────────────────
    public string $coupon_code      = '';
    public float  $couponDiscount   = 0;
    public ?int   $applied_coupon_id = null;
    public bool   $couponApplied    = false;
    public string $couponMessage    = '';

    public function mount(): void
    {
        $this->loadCart();
    }

    public function loadCart(): void
    {
        $items = app(CartService::class)->getItems()->map(fn($item) => [
            'product_id' => $item['product_id'],
            'name'       => $item['product']->name,
            'image'      => $item['product']->image_path,
            'price'      => (float) $item['price'],
            'quantity'   => (int)   $item['quantity'],
            'subtotal'   => (float) $item['subtotal'],
        ])->values()->all();

        $this->cart = ['items' => $items];
        $this->calculateTotals();
    }

    public function calculateTotals(): void
    {
        $this->subtotal   = array_sum(array_column($this->cart['items'], 'subtotal'));
        $this->shipping   = $this->subtotal >= 1000 ? 0 : 100;

        $taxService       = app(\App\Services\TaxService::class);
        $this->taxRate    = $taxService->ratePercent();
        $discounted       = max(0, $this->subtotal - $this->couponDiscount);
        $this->tax        = $taxService->calculate($discounted);
        $this->total      = $discounted + $this->tax + $this->shipping;
    }

    /**
     * Validate and apply a coupon code to the cart.
     */
    public function applyCoupon(): void
    {
        $code = strtoupper(trim($this->coupon_code));

        if (empty($code)) {
            $this->couponMessage = 'Please enter a coupon code.';
            return;
        }

        $siteId = optional(app('currentSite'))->id ?? null;

        $coupon = Coupon::where('code', $code)
            ->where(function ($q) use ($siteId) {
                if ($siteId) {
                    $q->where('site_id', $siteId);
                }
            })
            ->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('valid_from')->orWhere('valid_from', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('valid_until')->orWhere('valid_until', '>=', now());
            })
            ->first();

        if (! $coupon) {
            $this->couponMessage = 'Invalid or expired coupon code.';
            $this->couponApplied = false;
            return;
        }

        if ($coupon->usage_limit && $coupon->used_count >= $coupon->usage_limit) {
            $this->couponMessage = 'This coupon has reached its usage limit.';
            $this->couponApplied = false;
            return;
        }

        if ($coupon->min_order_amount && $this->subtotal < $coupon->min_order_amount) {
            $this->couponMessage = 'Minimum order of ৳' . number_format($coupon->min_order_amount, 0) . ' required.';
            $this->couponApplied = false;
            return;
        }

        $discount = match ($coupon->type) {
            'percentage' => round($this->subtotal * ($coupon->value / 100), 2),
            'fixed'      => min((float) $coupon->value, $this->subtotal),
            'shipping'   => $this->shipping,
            default      => 0,
        };

        if ($coupon->max_discount && $discount > $coupon->max_discount) {
            $discount = (float) $coupon->max_discount;
        }

        $this->couponDiscount    = $discount;
        $this->applied_coupon_id = $coupon->id;
        $this->couponApplied     = true;
        $this->couponMessage     = 'Coupon applied! You saved ৳' . number_format($discount, 2);

        $this->calculateTotals();
    }

    /**
     * Remove the applied coupon.
     */
    public function removeCoupon(): void
    {
        $this->couponDiscount    = 0;
        $this->applied_coupon_id = null;
        $this->couponApplied     = false;
        $this->couponMessage     = '';
        $this->coupon_code       = '';
        $this->calculateTotals();
    }

    public function updateQuantity($productId, $quantity): void
    {
        $quantity = max(1, intval($quantity));
        app(CartService::class)->updateItem((int) $productId, $quantity);
        $this->loadCart();
        $this->dispatch('cart-updated');
    }

    public function removeItem($productId): void
    {
        app(CartService::class)->removeItem((int) $productId);
        $this->loadCart();
        $this->dispatch('cart-updated');
        $this->dispatch('notify', message: 'Item removed from cart', type: 'info');
    }

    public function clearCart(): void
    {
        app(CartService::class)->clear();
        $this->loadCart();
        $this->dispatch('cart-updated');
    }

    public function render()
    {
        return view('livewire.storefront.cart-page');
    }
}
