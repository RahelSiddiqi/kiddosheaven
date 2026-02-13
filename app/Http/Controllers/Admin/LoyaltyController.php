<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LoyaltyProgram;
use App\Models\LoyaltyTransaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class LoyaltyController extends Controller
{
    public function index(Request $request)
    {
        $program = LoyaltyProgram::where('is_active', true)->first();

        $stats = [
            'total_points_issued' => LoyaltyTransaction::where('points', '>', 0)->sum('points'),
            'total_points_redeemed' => abs(LoyaltyTransaction::where('points', '<', 0)->sum('points')),
            'active_users' => User::where('loyalty_points', '>', 0)->count(),
            'active_program' => $program ? true : false,
        ];

        return view('admin.loyalty.index', compact('program', 'stats'));
    }

    public function storeProgram(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:loyalty_programs,name',
            'description' => 'nullable|string',
            'points_per_dollar' => 'required|numeric|min:0',
            'points_to_dollar_ratio' => 'required|numeric|min:0',
            'expiry_months' => 'nullable|integer|min:0',
            'tier_thresholds' => 'nullable|array',
            'tier_thresholds.*.name' => 'required|string',
            'tier_thresholds.*.min_points' => 'required|numeric',
            'tier_thresholds.*.multiplier' => 'required|numeric|min:1',
            'is_active' => 'boolean',
        ]);

        LoyaltyProgram::create([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
            'description' => $validated['description'] ?? null,
            'points_per_dollar' => $validated['points_per_dollar'],
            'points_to_dollar_ratio' => $validated['points_to_dollar_ratio'],
            'expiry_months' => $validated['expiry_months'] ?? 12,
            'tier_thresholds' => $validated['tier_thresholds'] ?? [],
            'is_active' => $validated['is_active'] ?? true,
        ]);

        return back()->with('success', 'Loyalty program created successfully.');
    }

    public function updateProgram(Request $request, LoyaltyProgram $program)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:loyalty_programs,name,' . $program->id,
            'description' => 'nullable|string',
            'points_per_dollar' => 'required|numeric|min:0',
            'points_to_dollar_ratio' => 'required|numeric|min:0',
            'expiry_months' => 'nullable|integer|min:0',
            'tier_thresholds' => 'nullable|array',
            'tier_thresholds.*.name' => 'required|string',
            'tier_thresholds.*.min_points' => 'required|numeric',
            'tier_thresholds.*.multiplier' => 'required|numeric|min:1',
            'is_active' => 'boolean',
        ]);

        $program->update([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
            'description' => $validated['description'] ?? null,
            'points_per_dollar' => $validated['points_per_dollar'],
            'points_to_dollar_ratio' => $validated['points_to_dollar_ratio'],
            'expiry_months' => $validated['expiry_months'] ?? 12,
            'tier_thresholds' => $validated['tier_thresholds'] ?? [],
            'is_active' => $validated['is_active'] ?? true,
        ]);

        return back()->with('success', 'Loyalty program updated successfully.');
    }

    public function destroyProgram(LoyaltyProgram $program)
    {
        $program->delete();

        return back()->with('success', 'Loyalty program deleted successfully.');
    }

    public function transactions(Request $request)
    {
        $query = LoyaltyTransaction::with('user');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        $transactions = $query->latest()->paginate(15);

        return view('admin.loyalty.transactions', compact('transactions'));
    }

    public function customers(Request $request)
    {
        $query = User::where('loyalty_points', '>', 0)
            ->with('loyaltyTransactions');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $customers = $query->paginate(15);

        return view('admin.loyalty.customers', compact('customers'));
    }

    public function adjustPoints(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'points' => 'required|integer',
            'type' => 'required|in:add,deduct',
            'reason' => 'required|string|max:255',
        ]);

        $user = User::findOrFail($validated['user_id']);

        if ($validated['type'] === 'add') {
            $user->increment('loyalty_points', $validated['points']);
        } else {
            if ($user->loyalty_points < $validated['points']) {
                return back()->with('error', 'Insufficient loyalty points.');
            }
            $user->decrement('loyalty_points', $validated['points']);
        }

        LoyaltyTransaction::create([
            'user_id' => $user->id,
            'type' => 'adjustment',
            'points' => $validated['type'] === 'add' ? $validated['points'] : -$validated['points'],
            'balance' => $user->loyalty_points,
            'description' => $validated['reason'],
            'order_id' => null,
        ]);

        return back()->with('success', 'Points adjusted successfully.');
    }

    public function assignProgram(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'program_id' => 'required|exists:loyalty_programs,id',
        ]);

        $user = User::findOrFail($validated['user_id']);
        $user->update([
            'loyalty_program_id' => $validated['program_id'],
        ]);

        return back()->with('success', 'Loyalty program assigned successfully.');
    }
}
