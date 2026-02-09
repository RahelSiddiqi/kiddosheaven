<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CouponController extends Controller
{
    public function index(Request $request)
    {
        $query = Coupon::query();

        if ($request->filled('search')) {
            $query->where('code', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('type')) {
            $query->where('discount_type', $request->type);
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $coupons = $query->latest()->paginate(10);

        return view('admin.coupons.index', compact('coupons'));
    }

    public function create()
    {
        $products = Product::where('is_active', true)->get();
        $categories = Category::where('is_active', true)->get();

        return view('admin.coupons.create', compact('products', 'categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:coupons,code',
            'description' => 'nullable|string',
            'discount_type' => 'required|in:percentage,fixed',
            'discount_value' => 'required|numeric|min:0',
            'minimum_order_amount' => 'nullable|numeric|min:0',
            'maximum_discount_amount' => 'nullable|numeric|min:0',
            'usage_limit' => 'nullable|integer|min:0',
            'usage_limit_per_user' => 'nullable|integer|min:0',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'is_active' => 'boolean',
            'is_user_specific' => 'boolean',
            'products' => 'nullable|array',
            'products.*' => 'exists:products,id',
            'categories' => 'nullable|array',
            'categories.*' => 'exists:categories,id',
        ]);

        $coupon = Coupon::create([
            'code' => strtoupper($validated['code']),
            'description' => $validated['description'] ?? null,
            'discount_type' => $validated['discount_type'],
            'discount_value' => $validated['discount_value'],
            'minimum_order_amount' => $validated['minimum_order_amount'] ?? 0,
            'maximum_discount_amount' => $validated['maximum_discount_amount'] ?? null,
            'usage_limit' => $validated['usage_limit'] ?? null,
            'usage_limit_per_user' => $validated['usage_limit_per_user'] ?? null,
            'start_date' => $validated['start_date'] ?? null,
            'end_date' => $validated['end_date'] ?? null,
            'is_active' => $validated['is_active'] ?? true,
            'is_user_specific' => $validated['is_user_specific'] ?? false,
        ]);

        if (isset($validated['products'])) {
            $coupon->products()->attach($validated['products']);
        }

        if (isset($validated['categories'])) {
            $coupon->categories()->attach($validated['categories']);
        }

        return redirect()->route('admin.coupons.index')
            ->with('success', 'Coupon created successfully.');
    }

    public function show(Coupon $coupon)
    {
        $coupon->load(['products', 'categories', 'users']);

        return view('admin.coupons.show', compact('coupon'));
    }

    public function edit(Coupon $coupon)
    {
        $products = Product::where('is_active', true)->get();
        $categories = Category::where('is_active', true)->get();
        $coupon->load(['products', 'categories']);

        return view('admin.coupons.edit', compact('coupon', 'products', 'categories'));
    }

    public function update(Request $request, Coupon $coupon)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:coupons,code,' . $coupon->id,
            'description' => 'nullable|string',
            'discount_type' => 'required|in:percentage,fixed',
            'discount_value' => 'required|numeric|min:0',
            'minimum_order_amount' => 'nullable|numeric|min:0',
            'maximum_discount_amount' => 'nullable|numeric|min:0',
            'usage_limit' => 'nullable|integer|min:0',
            'usage_limit_per_user' => 'nullable|integer|min:0',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'is_active' => 'boolean',
            'is_user_specific' => 'boolean',
            'products' => 'nullable|array',
            'products.*' => 'exists:products,id',
            'categories' => 'nullable|array',
            'categories.*' => 'exists:categories,id',
        ]);

        $coupon->update([
            'code' => strtoupper($validated['code']),
            'description' => $validated['description'] ?? null,
            'discount_type' => $validated['discount_type'],
            'discount_value' => $validated['discount_value'],
            'minimum_order_amount' => $validated['minimum_order_amount'] ?? 0,
            'maximum_discount_amount' => $validated['maximum_discount_amount'] ?? null,
            'usage_limit' => $validated['usage_limit'] ?? null,
            'usage_limit_per_user' => $validated['usage_limit_per_user'] ?? null,
            'start_date' => $validated['start_date'] ?? null,
            'end_date' => $validated['end_date'] ?? null,
            'is_active' => $validated['is_active'] ?? true,
            'is_user_specific' => $validated['is_user_specific'] ?? false,
        ]);

        $coupon->products()->sync($validated['products'] ?? []);
        $coupon->categories()->sync($validated['categories'] ?? []);

        return redirect()->route('admin.coupons.index')
            ->with('success', 'Coupon updated successfully.');
    }

    public function destroy(Coupon $coupon)
    {
        $coupon->products()->detach();
        $coupon->categories()->detach();
        $coupon->delete();

        return redirect()->route('admin.coupons.index')
            ->with('success', 'Coupon deleted successfully.');
    }

    public function toggleStatus(Coupon $coupon)
    {
        $coupon->update(['is_active' => !$coupon->is_active]);

        return back()->with('success', 'Coupon status updated.');
    }
}
