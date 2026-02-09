<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FinancialTransaction;
use App\Models\CapitalAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FinancialTransactionController extends Controller
{
    public function index(Request $request)
    {
        $query = FinancialTransaction::with(['capitalAccount.partner']);

        if ($request->has('account_id') && $request->account_id) {
            $query->where('capital_account_id', $request->account_id);
        }

        if ($request->has('type') && $request->type) {
            $query->where('transaction_type', $request->type);
        }

        if ($request->has('start_date') && $request->start_date) {
            $query->whereDate('transaction_date', '>=', $request->start_date);
        }

        if ($request->has('end_date') && $request->end_date) {
            $query->whereDate('transaction_date', '<=', $request->end_date);
        }

        $transactions = $query->orderBy('transaction_date', 'desc')->paginate(20);
        $accounts = CapitalAccount::with('partner')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.financial-transactions.index', compact('transactions', 'accounts'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'capital_account_id' => 'required|exists:capital_accounts,id',
            'transaction_type' => 'required|in:credit,debit',
            'amount' => 'required|numeric|min:0.01',
            'transaction_date' => 'required|date',
            'description' => 'nullable|string|max:500',
            'reference_type' => 'nullable|string|max:100',
            'reference_id' => 'nullable|integer',
        ]);

        DB::beginTransaction();
        try {
            $transaction = FinancialTransaction::create([
                'capital_account_id' => $validated['capital_account_id'],
                'transaction_type' => $validated['transaction_type'],
                'amount' => $validated['amount'],
                'transaction_date' => $validated['transaction_date'],
                'description' => $validated['description'] ?? null,
                'reference_type' => $validated['reference_type'] ?? null,
                'reference_id' => $validated['reference_id'] ?? null,
            ]);

            // Update capital account balance
            $account = $transaction->capitalAccount;
            if ($validated['transaction_type'] == 'credit') {
                $account->current_balance += $validated['amount'];
            } else {
                $account->current_balance -= $validated['amount'];
            }
            $account->save();

            DB::commit();
            return redirect()->route('admin.financial-transactions.index')
                ->with('success', 'Transaction recorded successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Error recording transaction: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function getByAccount(CapitalAccount $account)
    {
        $transactions = $account->transactions()
            ->orderBy('transaction_date', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'transactions' => $transactions,
        ]);
    }
}
