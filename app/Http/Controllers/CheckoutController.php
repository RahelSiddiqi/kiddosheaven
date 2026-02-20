<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use App\Models\Order;
use App\Models\Product;
use App\Services\Order\OrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckoutController extends Controller
{
    protected OrderService $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    public function show(Request $request)
    {
        $cart = $request->session()->get('cart', [
            'items' => [],
            'subtotal' => 0,
        ]);

        if (empty($cart['items'])) {
            return redirect()->route('cart.index')
                ->with('error', 'Your cart is empty.');
        }

        $savedAddresses = collect();
        if (auth()->check()) {
            $savedAddresses = \App\Models\Address::where('user_id', auth()->id())
                ->orderByDesc('is_default')
                ->orderByDesc('created_at')
                ->get();
        }

        return view('shop.checkout', [
            'cart'           => $cart,
            'savedAddresses' => $savedAddresses,
        ]);
    }

    public function placeOrder(Request $request)
    {
        $cart = $request->session()->get('cart', [
            'items' => [],
            'subtotal' => 0,
        ]);

        if (empty($cart['items'])) {
            return redirect()->route('cart.index')
                ->with('error', 'Your cart is empty.');
        }

        $validated = $request->validate([
            'customer_name' => ['required', 'string', 'max:255'],
            'customer_email' => ['nullable', 'email', 'max:255'],
            'customer_phone' => ['required', 'string', 'max:50'],
            'address_line' => ['required', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:100'],
            'postal_code' => ['nullable', 'string', 'max:20'],
            'notes' => ['nullable', 'string'],
            'coupon_code' => ['nullable', 'string', 'max:50'],
        ]);

        // Re-validate stock availability at checkout time
        $stockErrors = $this->validateCartStock($cart['items']);
        if (!empty($stockErrors)) {
            return redirect()->route('cart.index')
                ->with('error', implode(' ', $stockErrors));
        }

        // Build items array for OrderService (triggers FIFO deduction)
        $items = [];
        foreach ($cart['items'] as $item) {
            $items[] = [
                'product_id'         => $item['product_id'],
                'product_variant_id' => $item['variant_id'] ?? null,
                'quantity'           => $item['quantity'],
                'price'              => $item['price'],
            ];
        }

        // Calculate discount from coupon
        $discount = 0;
        $couponCode = $validated['coupon_code'] ?? null;
        unset($validated['coupon_code']);

        if ($couponCode) {
            $coupon = Coupon::where('code', $couponCode)->first();
            if ($coupon) {
                $user = Auth::user();
                $discount = $coupon->calculateDiscount($cart['subtotal'], $user);
                if ($discount > 0) {
                    $coupon->increment('used_count');
                }
            }
        }

        $totalAmount = max(0, $cart['subtotal'] - $discount);

        try {
            $order = $this->orderService->create([
                ...$validated,
                'user_id'        => Auth::id(),
                'total_amount'   => $totalAmount,
                'subtotal'       => $cart['subtotal'],
                'payment_method' => 'cod',
                'status'         => 'pending',
                'items'          => $items,
            ]);
        } catch (\Exception $e) {
            return redirect()->route('cart.index')
                ->with('error', 'Could not place order: ' . $e->getMessage());
        }

        $request->session()->forget('cart');

        // Store order number in session so the thank-you page can verify access
        $request->session()->put('last_order_id', $order->id);

        return redirect()
            ->route('checkout.thankyou', $order)
            ->with('success', 'Your order has been placed with Cash on Delivery.');
    }

    public function thankYou(Request $request, Order $order)
    {
        // Only allow access if the user owns the order or just placed it
        $lastOrderId = $request->session()->get('last_order_id');
        $isOwner = Auth::check() && $order->user_id === Auth::id();

        if (!$isOwner && $lastOrderId !== $order->id) {
            abort(403, 'Unauthorized.');
        }

        return view('shop.thankyou', [
            'order' => $order,
        ]);
    }

    /**
     * Re-validate stock availability for all cart items before placing order.
     */
    protected function validateCartStock(array $items): array
    {
        $errors = [];

        foreach ($items as $item) {
            $product = Product::find($item['product_id']);

            if (!$product) {
                $errors[] = "Product \"{$item['name']}\" is no longer available.";
                continue;
            }

            if (!$product->is_active) {
                $errors[] = "\"{$product->name}\" is no longer available.";
                continue;
            }

            // Check variant stock if variant is specified
            if (!empty($item['variant_id'])) {
                $variant = \App\Models\ProductVariant::where('id', $item['variant_id'])
                    ->where('product_id', $product->id)
                    ->where('is_active', true)
                    ->first();

                    if (!$variant) {
                    $errors[] = "Selected option for \"{$product->name}\" is no longer available.";
                    continue;
                }

                if ($variant->stock_quantity < $item['quantity']) {
                    $errors[] = "Insufficient stock for \"{$product->name}\". Only {$variant->stock_quantity} available.";
                }
            } else {
                if ($product->stock_quantity < $item['quantity']) {
                    $errors[] = "Insufficient stock for \"{$product->name}\". Only {$product->stock_quantity} available.";
                }
            }
        }

        return $errors;
    }
}
