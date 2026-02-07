<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\Partner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        $query = Expense::with(['category', 'partner', 'creator']);

        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('from_date')) {
            $query->whereDate('expense_date', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->whereDate('expense_date', '<=', $request->to_date);
        }

        $expenses = $query->orderBy('expense_date', 'desc')->paginate(10);
        $categories = ExpenseCategory::where('is_active', true)->get();

        return view('admin.expenses.index', compact('expenses', 'categories'));
    }

    public function create()
    {
        $categories = ExpenseCategory::where('is_active', true)->get();
        $partners = Partner::where('status', 'active')->get();

        return view('admin.expenses.create', compact('categories', 'partners'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'amount' => 'required|numeric|min:0',
            'expense_date' => 'required|date',
            'category_id' => 'required|exists:expense_categories,id',
            'partner_id' => 'nullable|exists:partners,id',
            'receipt' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->first()
                ], 422);
            }
            return back()->withErrors($validator)->withInput();
        }

        $expense = new Expense($validator->validated());
        $expense->created_by = auth()->user()?->id;

        if ($request->hasFile('receipt')) {
            $path = $request->file('receipt')->store('receipts', 'public');
            $expense->receipt_url = $path;
        }

        $expense->save();

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Expense created successfully.'
            ]);
        }

        return redirect()->route('admin.expenses.index')
            ->with('success', 'Expense created successfully.');
    }

    public function show(Expense $expense)
    {
        $expense->load(['category', 'partner', 'creator']);

        return view('admin.expenses.show', compact('expense'));
    }

    public function edit(Expense $expense)
    {
        $categories = ExpenseCategory::where('is_active', true)->get();
        $partners = Partner::where('status', 'active')->get();

        return view('admin.expenses.edit', compact('expense', 'categories', 'partners'));
    }

    public function update(Request $request, Expense $expense)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'amount' => 'required|numeric|min:0',
            'expense_date' => 'required|date',
            'category_id' => 'required|exists:expense_categories,id',
            'partner_id' => 'nullable|exists:partners,id',
            'receipt' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->first()
                ], 422);
            }
            return back()->withErrors($validator)->withInput();
        }

        if ($request->hasFile('receipt')) {
            if ($expense->receipt_url) {
                Storage::disk('public')->delete($expense->receipt_url);
            }
            $path = $request->file('receipt')->store('receipts', 'public');
            $validated = $validator->validated();
            $validated['receipt_url'] = $path;
        } else {
            $validated = $validator->validated();
        }

        $expense->update($validated);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Expense updated successfully.'
            ]);
        }

        return redirect()->route('admin.expenses.index')
            ->with('success', 'Expense updated successfully.');
    }

    public function destroy(Expense $expense)
    {
        if ($expense->receipt_url) {
            Storage::disk('public')->delete($expense->receipt_url);
        }

        $expense->delete();

        return redirect()->route('admin.expenses.index')
            ->with('success', 'Expense deleted successfully.');
    }

    public function approve(Expense $expense)
    {
        $expense->update(['status' => 'approved']);

        return back()->with('success', 'Expense approved successfully.');
    }

    public function reject(Expense $expense)
    {
        $expense->update(['status' => 'rejected']);

        return back()->with('success', 'Expense rejected successfully.');
    }

    // Category management
    public function storeCategory(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:expense_categories,name',
            'icon' => 'nullable|string|max:50',
            'color' => 'nullable|string|max:20',
        ]);

        ExpenseCategory::create($validated);

        return back()->with('success', 'Category created successfully.');
    }

    public function destroyCategory(ExpenseCategory $category)
    {
        if ($category->expenses()->exists()) {
            return back()->with('error', 'Cannot delete category with associated expenses.');
        }

        $category->delete();

        return back()->with('success', 'Category deleted successfully.');
    }
}
