<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Investment;
use App\Models\Investor;
use App\Models\FinancialTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InvestmentController extends Controller
{
    public function index(Request $request)
    {
        $query = Investment::with('investor');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('transaction_id', 'like', "%{$search}%")
                  ->orWhereHas('investor', function ($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        $investments = $query->latest()->paginate(10);

        return view('admin.investments.index', compact('investments'));
    }

    public function create()
    {
        $investors = Investor::where('status', 'active')->get();

        return view('admin.investments.create', compact('investors'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'investor_id' => 'required|exists:investors,id',
            'type' => 'required|in:equity,debt,convertible',
            'amount' => 'required|numeric|min:0',
            'currency' => 'required|string|size:3',
            'ownership_percentage' => 'nullable|numeric|min:0|max:100',
            'valuation' => 'nullable|numeric|min:0',
            'investment_date' => 'required|date',
            'terms' => 'nullable|string',
            'expected_return' => 'nullable|numeric|min:0',
            'maturity_date' => 'nullable|date|after:investment_date',
            'status' => 'required|in:pending,active,completed,cancelled',
            'notes' => 'nullable|string',
        ]);

        $validated['transaction_id'] = 'INV-' . strtoupper(uniqid());

        $investment = Investment::create($validated);

        // Create financial transaction
        FinancialTransaction::create([
            'type' => 'investment',
            'category' => 'investment',
            'amount' => $validated['amount'],
            'currency' => $validated['currency'],
            'status' => 'completed',
            'reference_type' => Investment::class,
            'reference_id' => $investment->id,
            'description' => "Investment #{$validated['transaction_id']}",
            'processed_by' => auth()->user()?->id,
        ]);

        return redirect()->route('admin.investments.index')
            ->with('success', 'Investment recorded successfully.');
    }

    public function show(Investment $investment)
    {
        $investment->load(['investor', 'returns']);

        return view('admin.investments.show', compact('investment'));
    }

    public function edit(Investment $investment)
    {
        $investors = Investor::where('status', 'active')->get();

        return view('admin.investments.edit', compact('investment', 'investors'));
    }

    public function update(Request $request, Investment $investment)
    {
        $validated = $request->validate([
            'investor_id' => 'required|exists:investors,id',
            'type' => 'required|in:equity,debt,convertible',
            'amount' => 'required|numeric|min:0',
            'currency' => 'required|string|size:3',
            'ownership_percentage' => 'nullable|numeric|min:0|max:100',
            'valuation' => 'nullable|numeric|min:0',
            'investment_date' => 'required|date',
            'terms' => 'nullable|string',
            'expected_return' => 'nullable|numeric|min:0',
            'maturity_date' => 'nullable|date|after:investment_date',
            'status' => 'required|in:pending,active,completed,cancelled',
            'notes' => 'nullable|string',
        ]);

        $investment->update($validated);

        return redirect()->route('admin.investments.index')
            ->with('success', 'Investment updated successfully.');
    }

    public function destroy(Investment $investment)
    {
        $investment->delete();

        return redirect()->route('admin.investments.index')
            ->with('success', 'Investment deleted successfully.');
    }

    public function complete(Investment $investment)
    {
        $investment->update(['status' => 'completed']);

        return back()->with('success', 'Investment marked as completed.');
    }

    public function cancel(Investment $investment)
    {
        $investment->update(['status' => 'cancelled']);

        return back()->with('success', 'Investment cancelled.');
    }

    public function investors(Request $request)
    {
        $query = Investor::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $investors = $query->paginate(10);

        return view('admin.investors.index', compact('investors'));
    }

    public function storeInvestor(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:investors,email',
            'phone' => 'nullable|string|max:20',
            'company' => 'nullable|string|max:255',
            'type' => 'required|in:individual,vc,angel,corporate',
            'address' => 'nullable|string',
            'status' => 'required|in:active,inactive',
        ]);

        Investor::create($validated);

        return back()->with('success', 'Investor added successfully.');
    }

    public function updateInvestor(Request $request, Investor $investor)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:investors,email,' . $investor->id,
            'phone' => 'nullable|string|max:20',
            'company' => 'nullable|string|max:255',
            'type' => 'required|in:individual,vc,angel,corporate',
            'address' => 'nullable|string',
            'status' => 'required|in:active,inactive',
        ]);

        $investor->update($validated);

        return back()->with('success', 'Investor updated successfully.');
    }

    public function destroyInvestor(Investor $investor)
    {
        if ($investor->investments()->exists()) {
            return back()->with('error', 'Cannot delete investor with associated investments.');
        }

        $investor->delete();

        return back()->with('success', 'Investor deleted successfully.');
    }
}
