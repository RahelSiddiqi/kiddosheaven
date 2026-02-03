<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;

class CheckoutController extends Controller
{
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

        return view('shop.checkout', [
            'cart' => $cart,
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
        ]);

        $order = Order::create([
            ...$validated,
            'total_amount' => $cart['subtotal'],
            'payment_method' => 'cod',
            'status' => 'pending',
        ]);

        foreach ($cart['items'] as $item) {
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['price'],
                'total_price' => $item['line_total'],
            ]);
        }

        $request->session()->forget('cart');

        return redirect()
            ->route('checkout.thankyou', $order)
            ->with('success', 'Your order has been placed with Cash on Delivery.');
    }

    public function thankYou(Order $order)
    {
        return view('shop.thankyou', [
            'order' => $order,
        ]);
    }
}
