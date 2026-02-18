<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\Order\OrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    protected OrderService $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

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

        $order->load(['items.product', 'items.product.category', 'statusHistory']);

        return view('customer.orders.show', compact('order'));
    }

    /**
     * Cancel an order — routes through OrderService to restore FIFO batches.
     */
    public function cancel(Request $request, Order $order)
    {
        // Ensure the order belongs to the authenticated user
        if ($order->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        // Only allow cancellation of pending/processing orders
        if (!in_array($order->status, ['pending', 'processing'])) {
            return redirect()->back()->with('error', 'Order cannot be cancelled at this stage.');
        }

        try {
            // Route through OrderService — restores batches + stock counter
            $this->orderService->cancel($order->id);

            // Save cancellation reason
            $order->update([
                'cancellation_reason' => $request->input('reason', 'Customer requested cancellation'),
            ]);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Cancellation failed. Please try again.');
        }

        return redirect()->route('customer.orders.show', $order)->with('success', 'Order cancelled successfully.');
    }
}
