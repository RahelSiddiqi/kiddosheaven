<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    /**
     * Display a listing of customer orders.
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        $query = Order::where('user_id', $user->id)
            ->with(['items.product', 'statusHistory'])
            ->latest();

        // Filter by status if provided
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        $orders = $query->paginate(10);

        return view('customer.orders.index', compact('orders'));
    }

    /**
     * Display order details.
     */
    public function show(Order $order)
    {
        // Ensure the order belongs to the authenticated user
        if ($order->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $order->load(['items.product', 'items.product.catalog', 'statusHistory', 'address']);

        return view('customer.orders.show', compact('order'));
    }

    /**
     * Cancel an order.
     */
    public function cancel(Request $request, Order $order)
    {
        // Ensure the order belongs to the authenticated user
        if ($order->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        // Only allow cancellation of pending orders
        if (!in_array($order->status, ['pending', 'processing'])) {
            return redirect()->back()->with('error', 'Order cannot be cancelled at this stage.');
        }

        $order->update([
            'status' => 'cancelled',
            'cancellation_reason' => $request->input('reason', 'Customer requested cancellation'),
        ]);

        // Restore inventory
        foreach ($order->items as $item) {
            $item->product->increment('stock_quantity', $item->quantity);
        }

        return redirect()->route('customer.orders.show', $order)->with('success', 'Order cancelled successfully.');
    }
}
