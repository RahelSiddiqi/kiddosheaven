<?php

namespace App\Livewire\Storefront;

use Livewire\Component;
use Livewire\Attributes\Validate;
use App\Domains\Order\Models\Order;
use App\Domains\Order\Models\OrderItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class Checkout extends Component
{
    // Customer Information
    #[Validate('required|string|max:255')]
    public $customer_name = '';

    #[Validate('required|email|max:255')]
    public $customer_email = '';

    #[Validate('required|string|max:20')]
    public $customer_phone = '';

    // Shipping Address
    #[Validate('required|string|max:500')]
    public $shipping_address = '';

    #[Validate('required|string|max:255')]
    public $shipping_city = '';

    #[Validate('required|string|max:100')]
    public $shipping_state = '';

    #[Validate('required|string|max:20')]
    public $shipping_zip = '';

    // Payment
    #[Validate('required|in:cod,card,bkash')]
    public $payment_method = 'cod';

    public $notes = '';

    // Cart data
    public $cart = [];
    public $subtotal = 0;
    public $tax = 0;
    public $shipping = 0;
    public $total = 0;

    public $processing = false;

    public function mount()
    {
        // Redirect if cart is empty
        $this->cart = session('cart', ['items' => []]);
        if (empty($this->cart['items'])) {
            return redirect()->route('cart.index');
        }

        // Pre-fill if user is logged in
        if (auth()->check()) {
            $user = auth()->user();
            $this->customer_name = $user->name ?? '';
            $this->customer_email = $user->email ?? '';
            $this->customer_phone = $user->phone ?? '';
        }

        $this->calculateTotals();
    }

    public function calculateTotals()
    {
        $this->subtotal = 0;

        foreach ($this->cart['items'] ?? [] as $item) {
            $this->subtotal += $item['price'] * $item['quantity'];
        }

        $this->tax = $this->subtotal * 0.15; // 15% tax
        $this->shipping = $this->subtotal >= 1000 ? 0 : 100; // Free shipping over ৳1000
        $this->total = $this->subtotal + $this->tax + $this->shipping;
    }

    public function placeOrder()
    {
        // Validate all fields
        $this->validate();

        $this->processing = true;

        try {
            DB::beginTransaction();

            // Create the order
            $order = Order::create([
                'order_number' => 'ORD-' . strtoupper(Str::random(10)),
                'customer_id' => auth()->id(),
                'customer_name' => $this->customer_name,
                'customer_email' => $this->customer_email,
                'customer_phone' => $this->customer_phone,
                'shipping_address' => $this->shipping_address,
                'shipping_city' => $this->shipping_city,
                'shipping_state' => $this->shipping_state,
                'shipping_zip' => $this->shipping_zip,
                'subtotal' => $this->subtotal,
                'tax' => $this->tax,
                'shipping' => $this->shipping,
                'total' => $this->total,
                'payment_method' => $this->payment_method,
                'payment_status' => $this->payment_method === 'cod' ? 'pending' : 'pending',
                'status' => 'pending',
                'notes' => $this->notes,
            ]);

            // Create order items
            foreach ($this->cart['items'] as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product_id'],
                    'variant_id' => $item['variant_id'] ?? null,
                    'product_name' => $item['name'],
                    'product_sku' => $item['sku'] ?? null,
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'subtotal' => $item['price'] * $item['quantity'],
                ]);
            }

            DB::commit();

            // Clear the cart
            session()->forget('cart');
            $this->dispatch('cart-updated');

            // Redirect to thank you page
            return redirect()->route('checkout.thankyou', $order);

        } catch (\Exception $e) {
            DB::rollBack();
            $this->processing = false;
            session()->flash('error', 'Failed to place order. Please try again.');
            \Log::error('Order creation failed: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.storefront.checkout');
    }
}
