<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CouponController extends Controller
{
    /**
     * Display a listing of coupons.
     */
    public function index(Request $request)
    {
        $search = $request->search ?? '';
        $status = $request->status ?? '';

        $query = Coupon::with('user')
            ->when($search, function ($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            })
            ->when($status, function ($q) use ($status) {
                $q->where('status', $status);
            })
            ->orderBy('created_at', 'desc');

        $coupons = $query->paginate(10);

        return view('admin.coupons.index', compact('coupons', 'search', 'status'));
    }

    /**
     * Store a newly created coupon.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:coupons,code',
            'description' => 'nullable|string',
            'type' => 'required|in:percentage,fixed,shipping',
            'value' => 'required|numeric|min:0',
            'min_order_amount' => 'nullable|numeric|min:0',
            'max_discount' => 'nullable|numeric|min:0',
            'usage_limit' => 'nullable|integer|min:1',
            'valid_from' => 'nullable|date',
            'valid_until' => 'nullable|date|after_or_equal:valid_from',
            'status' => 'required|in:active,inactive',
            'is_general' => 'nullable|boolean',
            'user_id' => 'nullable|exists:users,id',
        ]);

        $isGeneral = $request->has('is_general') && $request->is_general;

        Coupon::create([
            'code' => strtoupper($validated['code']),
            'description' => $validated['description'] ?? null,
            'type' => $validated['type'],
            'value' => $validated['value'],
            'min_order_amount' => $validated['min_order_amount'] ?? null,
            'max_discount' => $validated['max_discount'] ?? null,
            'usage_limit' => $validated['usage_limit'] ?? null,
            'valid_from' => $validated['valid_from'] ?? null,
            'valid_until' => $validated['valid_until'] ?? null,
            'status' => $validated['status'],
            'is_general' => $isGeneral,
            'user_id' => $isGeneral ? null : ($validated['user_id'] ?? null),
        ]);

        $message = 'Coupon created successfully.';
        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json(['success' => true, 'message' => $message]);
        }
        return redirect()->back()->with('success', $message);
    }

    /**
     * Update the specified coupon.
     */
    public function update(Request $request, Coupon $coupon)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:coupons,code,' . $coupon->id,
            'description' => 'nullable|string',
            'type' => 'required|in:percentage,fixed,shipping',
            'value' => 'required|numeric|min:0',
            'min_order_amount' => 'nullable|numeric|min:0',
            'max_discount' => 'nullable|numeric|min:0',
            'usage_limit' => 'nullable|integer|min:1',
            'valid_from' => 'nullable|date',
            'valid_until' => 'nullable|date|after_or_equal:valid_from',
            'status' => 'required|in:active,inactive',
            'is_general' => 'nullable|boolean',
            'user_id' => 'nullable|exists:users,id',
        ]);

        $isGeneral = $request->has('is_general') && $request->is_general;

        $coupon->update([
            'code' => strtoupper($validated['code']),
            'description' => $validated['description'] ?? null,
            'type' => $validated['type'],
            'value' => $validated['value'],
            'min_order_amount' => $validated['min_order_amount'] ?? null,
            'max_discount' => $validated['max_discount'] ?? null,
            'usage_limit' => $validated['usage_limit'] ?? null,
            'valid_from' => $validated['valid_from'] ?? null,
            'valid_until' => $validated['valid_until'] ?? null,
            'status' => $validated['status'],
            'is_general' => $isGeneral,
            'user_id' => $isGeneral ? null : ($validated['user_id'] ?? null),
        ]);

        $message = 'Coupon updated successfully.';
        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json(['success' => true, 'message' => $message]);
        }
        return redirect()->back()->with('success', $message);
    }

    /**
     * Remove the specified coupon.
     */
    public function destroy(Coupon $coupon)
    {
        $coupon->delete();
        $message = 'Coupon deleted successfully.';
        if (request()->expectsJson()) {
            return response()->json(['success' => true, 'message' => $message]);
        }
        return redirect()->back()->with('success', $message);
    }

    /**
     * Toggle coupon active status.
     */
    public function toggleStatus(Coupon $coupon)
    {
        $coupon->update([
            'status' => $coupon->status === 'active' ? 'inactive' : 'active',
        ]);

        return redirect()->back()->with('success', 'Coupon status updated successfully.');
    }

    /**
     * Get users for dropdown (AJAX).
     */
    public function getUsers(Request $request)
    {
        $search = $request->search ?? '';
        $users = User::when($search, function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%");
        })
            ->limit(10)
            ->get(['id', 'name', 'email']);

        return response()->json($users);
    }
}
