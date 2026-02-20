<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\Order\OrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    protected OrderService $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    public function index(Request $request)
    {
        $query = Order::with('items.product', 'user')
            ->latest();

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Date range filter
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('id', 'like', "%{$search}%")
                  ->orWhere('customer_name', 'like', "%{$search}%")
                  ->orWhere('customer_email', 'like', "%{$search}%")
                  ->orWhere('order_number', 'like', "%{$search}%");
            });
        }

        // Get stats for the filtered results
        $stats = [
            'total' => $query->count(),
            'pending' => (clone $query)->where('status', 'pending')->count(),
            'processing' => (clone $query)->where('status', 'processing')->count(),
            'shipped' => (clone $query)->where('status', 'shipped')->count(),
            'delivered' => (clone $query)->where('status', 'delivered')->count(),
            'cancelled' => (clone $query)->where('status', 'cancelled')->count(),
            'total_revenue' => (clone $query)->whereIn('status', ['processing', 'shipped', 'delivered'])->sum('total_amount'),
        ];

        $orders = $query->paginate(20)->appends($request->all());

        return view('admin.orders.index', compact('orders', 'stats'));
    }

    public function show(Order $order)
    {
        $order->load('items.product', 'user', 'statusHistory.creator');
        return view('admin.orders.show', compact('order'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        $validated = $request->validate([
            'status' => ['required', 'string', 'in:pending,processing,shipped,delivered,cancelled'],
            'notes' => ['nullable', 'string'],
        ]);

        // Route cancellation through OrderService so FIFO batches are restored
        if ($validated['status'] === 'cancelled' && $order->status !== 'cancelled') {
            $this->orderService->cancel($order->id);
            // Save optional notes after cancel
            $order->update([
                'status_notes' => $validated['notes'] ?? null,
            ]);
        } else {
            $order->update([
                'status' => $validated['status'],
                'status_notes' => $validated['notes'] ?? $order->status_notes,
            ]);
        }

        // NOTE: Stock is deducted at order CREATION via FIFO.
        // "delivered" does NOT need to decrement again.

        return redirect()->route('admin.orders.show', $order)
            ->with('success', 'Order status updated successfully.');
    }

    public function bulkUpdateStatus(Request $request)
    {
        $validated = $request->validate([
            'order_ids' => ['required', 'array'],
            'order_ids.*' => ['exists:orders,id'],
            'status' => ['required', 'string', 'in:pending,processing,shipped,delivered,cancelled'],
        ]);

        $count = 0;

        if ($validated['status'] === 'cancelled') {
            // Route each cancellation through OrderService to restore FIFO batches
            foreach ($validated['order_ids'] as $orderId) {
                $order = Order::find($orderId);
                if ($order && $order->status !== 'cancelled') {
                    try {
                        $this->orderService->cancel($orderId);
                        $count++;
                    } catch (\Exception $e) {
                        \Log::warning("Bulk cancel failed for order #{$orderId}: " . $e->getMessage());
                    }
                }
            }
        } else {
            $count = Order::whereIn('id', $validated['order_ids'])
                ->update(['status' => $validated['status']]);
        }

        $message = "{$count} orders updated successfully.";

        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json(['success' => true, 'message' => $message]);
        }

        return redirect()->route('admin.orders.index')
            ->with('success', $message);
    }

    public function invoice(Order $order)
    {
        $order->load('items.product', 'user');
        return view('admin.orders.invoice', compact('order'));
    }

    public function export(Request $request)
    {
        $query = Order::with('items.product', 'user')->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $orders = $query->get();

        $csvData = [];
        $csvData[] = ['Order ID', 'Order Number', 'Customer', 'Email', 'Status', 'Total', 'Items', 'Date'];

        foreach ($orders as $order) {
            $csvData[] = [
                $order->id,
                $order->order_number,
                $order->customer_name,
                $order->customer_email,
                $order->status,
                number_format($order->total_amount, 2),
                $order->items->sum('quantity'),
                $order->created_at->format('Y-m-d H:i:s'),
            ];
        }

        $filename = 'orders_export_' . now()->format('Y_m_d_His') . '.csv';

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Pragma: no-cache');
        header('Expires: 0');

        $file = fopen('php://output', 'w');
        foreach ($csvData as $row) {
            fputcsv($file, $row);
        }
        fclose($file);
        exit;
    }
}
