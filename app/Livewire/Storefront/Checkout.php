<?php

namespace App\Livewire\Storefront;

use App\Domains\Marketing\Models\Coupon;
use App\Services\Cart\CartService;
use App\Services\Order\OrderService;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Livewire\Attributes\Validate;
use Livewire\Component;

class Checkout extends Component
{
    // ── Customer Information ─────────────────────────────────────
    #[Validate('required|string|max:255')]
    public string $customer_name = '';

    #[Validate('required|email|max:255')]
    public string $customer_email = '';

    #[Validate('required|string|max:20')]
    public string $customer_phone = '';

    // ── Shipping Address ─────────────────────────────────────────
    #[Validate('required|string|max:500')]
    public string $shipping_address = '';

    #[Validate('required|string|max:255')]
    public string $shipping_city = '';

    #[Validate('nullable|string|max:100')]
    public string $shipping_state = '';

    #[Validate('required|string|max:20')]
    public string $shipping_zip = '';

    // ── Payment ───────────────────────────────────────────────────
    #[Validate('required|in:cod,card,bkash')]
    public string $payment_method = 'cod';

    public string $notes = '';

    // ── Coupon ────────────────────────────────────────────────────
    public string $coupon_code    = '';
    public float  $couponDiscount = 0;
    public ?int   $applied_coupon_id = null;
    public string $couponMessage  = '';
    public bool   $couponApplied  = false;

    // ── View-safe cart summary (plain scalars / arrays, no Eloquent models) ──
    public array $cart = ['items' => []];
    public float $subtotal  = 0;
    public float $tax       = 0;
    public float $taxRate   = 0;
    public float $shipping  = 0;
    public float $total     = 0;

    public bool $processing = false;

    // ─────────────────────────────────────────────────────────────

    public function mount(CartService $cartService): void
    {
        if ($cartService->getItemCount() === 0) {
            redirect()->route('cart.index');
            return;
        }

        // Build a view-safe cart items array — no Eloquent model instances in public properties
        $cartItems = $cartService->getItems();

        $this->cart = [
            'items' => $cartItems->map(fn($item) => [
                'product_id' => $item['product_id'],
                'name'       => $item['product']->name,
                'sku'        => $item['product']->sku ?? null,
                'image'      => $item['product']->image_path,
                'price'      => (float) $item['price'],
                'quantity'   => (int) $item['quantity'],
            ])->values()->all(),
        ];

        $this->calculateTotals();

        // Pre-fill form from authenticated user
        if (auth()->check()) {
            $user = auth()->user();
            $this->customer_name  = $user->name  ?? '';
            $this->customer_email = $user->email ?? '';
            $this->customer_phone = $user->phone ?? '';
        }
    }


    /**
     * Apply a coupon code.
     * Validates: exists, belongs to current site, active, not expired,
     * usage limit not exceeded, meets minimum order amount.
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

        if (!$coupon) {
            $this->couponMessage = 'Invalid or expired coupon code.';
            $this->couponApplied  = false;
            return;
        }

        if ($coupon->usage_limit && $coupon->used_count >= $coupon->usage_limit) {
            $this->couponMessage = 'This coupon has reached its usage limit.';
            $this->couponApplied  = false;
            return;
        }

        if ($coupon->min_order_amount && $this->subtotal < $coupon->min_order_amount) {
            $this->couponMessage = 'Minimum order amount of ৳' . number_format($coupon->min_order_amount, 0) . ' required.';
            $this->couponApplied  = false;
            return;
        }

        // Calculate discount
        $discount = match ($coupon->type) {
            'percentage' => round($this->subtotal * ($coupon->value / 100), 2),
            'fixed'      => min((float) $coupon->value, $this->subtotal),
            'shipping'   => $this->shipping,
            default      => 0,
        };

        if ($coupon->max_discount && $discount > $coupon->max_discount) {
            $discount = (float) $coupon->max_discount;
        }

        $this->couponDiscount   = $discount;
        $this->applied_coupon_id = $coupon->id;
        $this->couponApplied    = true;
        $this->couponMessage    = 'Coupon applied! You saved ৳' . number_format($discount, 2);

        $this->calculateTotals();
    }

    /**
     * Remove the currently applied coupon.
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

    /**
     * Recalculate display totals.
     * Tax rate is loaded from TaxService (reads from settings table).
     * Shipping is ৳100 (free over ৳1000).
     * The actual order record uses OrderService::getTaxRate() (from Settings),
     * which may differ; the CartService tier handles the stored totals.
     */
    public function calculateTotals(): void
    {
        $this->subtotal = array_sum(
            array_map(fn($i) => $i['price'] * $i['quantity'], $this->cart['items'])
        );

        $taxService     = app(\App\Services\TaxService::class);
        $this->taxRate  = $taxService->ratePercent();
        $this->shipping = $this->subtotal >= 1000 ? 0 : 100;

        // Apply coupon discount to subtotal (shipping coupon reduces shipping cost)
        $discountedSubtotal = max(0, $this->subtotal - $this->couponDiscount);
        $this->tax          = $taxService->calculate($discountedSubtotal);
        $this->total        = $discountedSubtotal + $this->tax + $this->shipping;
    }

    /**
     * Place the order.
     *
     * Delegates entirely to OrderService::create() which:
     *  - generates the order number
     *  - runs FIFO/LIFO stock deduction via InventoryService
     *  - records inventory_movements
     *  - populates unit_cost + purchase_batch_id on order_items
     *  - fires the OrderPlaced event (confirmation email)
     *
     * This component MUST NOT write directly to Order, OrderItem,
     * PurchaseBatch, or InventoryMovement.
     */
    public function placeOrder(CartService $cartService, OrderService $orderService): mixed
    {
        $this->validate();

        if ($cartService->getItemCount() === 0) {
            $this->addError('order', 'Your cart is empty.');
            return null;
        }

        $this->processing = true;

        try {
            $cartData = $cartService->prepareForOrder();

            $order = $orderService->create([
                'user_id'          => auth()->id(),
                'customer_name'    => $this->customer_name,
                'customer_email'   => $this->customer_email,
                'customer_phone'   => $this->customer_phone,
                'address_line'     => $this->shipping_address,
                'city'             => $this->shipping_city,
                'postal_code'      => $this->shipping_zip,
                'payment_method'   => $this->payment_method,
                'notes'            => $this->buildNotesField(),
                // Totals — passed explicitly so OrderService does NOT
                // recalculate and overwrite the tax with the wrong rate.
                'items'            => $cartData['items'],
                'subtotal'         => $this->subtotal,
                'tax_amount'       => $this->tax,
                'discount_amount'  => $this->couponDiscount,
                'shipping_amount'  => $this->shipping,
                'total_amount'     => $this->total,
                // Coupon
                'coupon_id'        => $this->applied_coupon_id,
                'coupon_code'      => $this->applied_coupon_id ? strtoupper(trim($this->coupon_code)) : null,
            ]);

            // Clear via CartService so the correct session key is purged
            $cartService->clear();
            $this->dispatch('cart-updated');

            return redirect()->route('checkout.thankyou', $order);

        } catch (\Exception $e) {
            $this->processing = false;

            Log::error('Checkout::placeOrder failed', [
                'customer_email' => $this->customer_email,
                'message'        => $e->getMessage(),
                'trace'          => $e->getTraceAsString(),
            ]);

            $this->addError('order', 'Failed to place order. Please try again.');
            return null;
        }
    }

    public function render(): View
    {
        return view('livewire.storefront.checkout');
    }

    /**
     * Combine user notes with shipping state into a single notes field.
     * The orders table has no dedicated shipping_state column — state is
     * preserved here so it isn't silently discarded.
     */
    protected function buildNotesField(): string
    {
        $parts = [];

        if (!empty($this->shipping_state)) {
            $parts[] = 'State/Division: ' . $this->shipping_state;
        }

        if (!empty($this->notes)) {
            $parts[] = $this->notes;
        }

        return implode(' | ', $parts);
    }
}
