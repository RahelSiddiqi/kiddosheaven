<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LoyaltyProgram;
use App\Models\LoyaltyTransaction;
use Illuminate\Http\Request;

class LoyaltyController extends Controller
{
    public function index(Request $request)
    {
        $program = LoyaltyProgram::getActiveProgram();

        $transactions = LoyaltyTransaction::with('user', 'program', 'order')
            ->latest()
            ->when($request->filled('type'), function($q) use ($request) {
                $q->where('type', $request->type);
            })
            ->when($request->filled('search'), function($q) use ($request) {
                $q->whereHas('user', function($user) use ($request) {
                    $user->where('name', 'like', "%{$request->search}%")
                        ->orWhere('email', 'like', "%{$request->search}%");
                });
            })
            ->paginate(20)
            ->appends($request->all());

        $stats = [
            'total_points_issued' => LoyaltyTransaction::earned()->sum('points'),
            'total_points_redeemed' => LoyaltyTransaction::redeemed()->sum('points'),
            'active_users' => LoyaltyTransaction::select('user_id')->distinct()->count(),
            'active_program' => $program,
        ];

        return view('admin.loyalty.index', compact('transactions', 'stats', 'program'));
    }

    public function updateProgram(Request $request, LoyaltyProgram $program)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'points_per_currency' => ['required', 'numeric', 'min:0.01'],
            'minimum_points' => ['required', 'integer', 'min:1'],
            'discount_percentage' => ['required', 'numeric', 'min:0.01'],
            'is_active' => ['boolean'],
        ]);

        $program->update($validated);

        return redirect()->route('admin.loyalty.index')
            ->with('success', 'Loyalty program updated successfully.');
    }

    public function transactions()
    {
        $transactions = LoyaltyTransaction::with('user', 'program')
            ->latest()
            ->paginate(20);

        return view('admin.loyalty.transactions', compact('transactions'));
    }

    public function addPoints(Request $request)
    {
        $validated = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'points' => ['required', 'integer', 'min:1'],
            'description' => ['nullable', 'string'],
        ]);

        $program = LoyaltyProgram::getActiveProgram();

        LoyaltyTransaction::create([
            'user_id' => $validated['user_id'],
            'loyalty_program_id' => $program?->id,
            'type' => 'bonus',
            'points' => $validated['points'],
            'description' => $validated['description'] ?? 'Manual points addition',
        ]);

        return redirect()->route('admin.loyalty.index')
            ->with('success', 'Points added successfully.');
    }
}
