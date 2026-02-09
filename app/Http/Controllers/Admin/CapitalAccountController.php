<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CapitalAccount;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CapitalAccountController extends Controller
{
    public function index(Request $request)
    {
        $query = CapitalAccount::with('partner');

        if ($request->has('partner_id') && $request->partner_id) {
            $query->where('partner_id', $request->partner_id);
        }

        if ($request->has('type') && $request->type) {
            $query->where('account_type', $request->type);
        }

        $accounts = $query->orderBy('created_at', 'desc')->paginate(20);
        $partners = User::where('is_admin', false)->orderBy('name')->get();

        return view('admin.capital-accounts.index', compact('accounts', 'partners'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'partner_id' => 'required|exists:users,id',
            'account_type' => 'required|in:partner,investor',
            'opening_balance' => 'required|numeric',
            'current_balance' => 'required|numeric',
            'notes' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $account = CapitalAccount::create([
                'partner_id' => $validated['partner_id'],
                'account_type' => $validated['account_type'],
                'opening_balance' => $validated['opening_balance'],
                'current_balance' => $validated['current_balance'],
                'notes' => $validated['notes'] ?? null,
            ]);

            DB::commit();
            return redirect()->route('admin.capital-accounts.index')
                ->with('success', 'Capital account created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Error creating capital account: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function update(Request $request, CapitalAccount $capitalAccount)
    {
        $validated = $request->validate([
            'notes' => 'nullable|string',
        ]);

        try {
            $capitalAccount->update($validated);
            return redirect()->route('admin.capital-accounts.index')
                ->with('success', 'Capital account updated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error updating capital account: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy(CapitalAccount $capitalAccount)
    {
        try {
            $capitalAccount->delete();
            return redirect()->route('admin.capital-accounts.index')
                ->with('success', 'Capital account deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error deleting capital account: ' . $e->getMessage());
        }
    }

    public function getByPartner(User $partner)
    {
        $accounts = $partner->capitalAccounts()
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'accounts' => $accounts,
        ]);
    }
}
