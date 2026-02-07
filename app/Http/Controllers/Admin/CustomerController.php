<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    /**
     * Display a listing of customers.
     */
    public function index(Request $request)
    {
        $search = $request->search ?? '';
        $sortBy = $request->sortBy ?? 'newest';

        $customers = User::where('is_admin', false)
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%");
                });
            })
            ->when($sortBy === 'newest', function ($query) {
                $query->orderBy('created_at', 'desc');
            })
            ->when($sortBy === 'oldest', function ($query) {
                $query->orderBy('created_at', 'asc');
            })
            ->paginate(10);

        return view('admin.customers.index', compact('customers', 'search', 'sortBy'));
    }

    /**
     * Display customer details.
     */
    public function show(User $customer)
    {
        // Try to load orders by user_id first, fallback to email
        try {
            $orders = Order::where('user_id', $customer->id)
                ->orWhere('customer_email', $customer->email)
                ->orderBy('created_at', 'desc')
                ->get();
        } catch (\Exception $e) {
            // Fallback if user_id column doesn't exist
            $orders = Order::where('customer_email', $customer->email)
                ->orderBy('created_at', 'desc')
                ->get();
        }

        $addresses = $customer->addresses ?? collect();

        return view('admin.customers.show', compact('customer', 'orders', 'addresses'));
    }

    /**
     * Toggle customer active status.
     */
    public function toggleActive(Request $request, User $customer)
    {
        $customer->update([
            'is_active' => !$customer->is_active,
        ]);

        return redirect()->back()->with('success', 'Customer status updated successfully.');
    }
}
