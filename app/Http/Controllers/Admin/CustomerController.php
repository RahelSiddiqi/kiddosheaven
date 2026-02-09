<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $query = User::where('is_admin', false);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        $customers = $query->latest()->paginate(15);

        return view('admin.customers.index', compact('customers'));
    }

    public function show(User $customer)
    {
        $customer->load(['orders', 'addresses', 'wishlists']);

        return view('admin.customers.show', compact('customer'));
    }

    public function update(Request $request, User $customer)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $customer->id,
            'phone' => 'nullable|string|max:20',
            'is_active' => 'boolean',
            'is_verified' => 'boolean',
        ]);

        $customer->update($validated);

        return back()->with('success', 'Customer updated successfully.');
    }

    public function toggleStatus(User $customer)
    {
        $customer->update(['is_active' => !$customer->is_active]);

        return back()->with('success', 'Customer status updated.');
    }

    public function orders(User $customer)
    {
        $customer->load(['orders' => function ($query) {
            $query->latest()->paginate(10);
        }]);

        return view('admin.customers.orders', compact('customer'));
    }

    public function addresses(User $customer)
    {
        $addresses = $customer->addresses;

        return view('admin.customers.addresses', compact('customer', 'addresses'));
    }

    public function wishlists(User $customer)
    {
        $wishlists = $customer->wishlists()->with('product')->latest()->paginate(10);

        return view('admin.customers.wishlists', compact('customer', 'wishlists'));
    }

    public function loginAsCustomer(User $customer)
    {
        auth()->login($customer);

        return redirect()->route('customer.dashboard');
    }
}
