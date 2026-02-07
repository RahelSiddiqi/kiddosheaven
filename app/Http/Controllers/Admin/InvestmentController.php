<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Investment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class InvestmentController extends Controller
{
    public function index(Request $request)
    {
        $query = Investment::query();

        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $investments = $query->orderBy('investment_date', 'desc')->paginate(10);

        return view('admin.investments.index', compact('investments'));
    }

    public function create()
    {
        return view('admin.investments.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'type' => 'required|string|in:inventory,equipment,property,marketing,research,expansion,other',
            'amount' => 'required|numeric|min:0',
            'investment_date' => 'required|date',
            'description' => 'nullable|string',
            'notes' => 'nullable|string',
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

        Investment::create($validator->validated());

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Investment created successfully.'
            ]);
        }

        return redirect()->route('admin.investments.index')
            ->with('success', 'Investment created successfully.');
    }

    public function show(Investment $investment)
    {
        return view('admin.investments.show', compact('investment'));
    }

    public function edit(Investment $investment)
    {
        return view('admin.investments.edit', compact('investment'));
    }

    public function update(Request $request, Investment $investment)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'type' => 'required|string|in:inventory,equipment,property,marketing,research,expansion,other',
            'amount' => 'required|numeric|min:0',
            'investment_date' => 'required|date',
            'description' => 'nullable|string',
            'notes' => 'nullable|string',
            'status' => 'nullable|string|in:active,completed,sold',
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

        $investment->update($validator->validated());

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Investment updated successfully.'
            ]);
        }

        return redirect()->route('admin.investments.index')
            ->with('success', 'Investment updated successfully.');
    }

    public function destroy(Investment $investment)
    {
        $investment->delete();

        return redirect()->route('admin.investments.index')
            ->with('success', 'Investment deleted successfully.');
    }

    public function updateStatus(Request $request, Investment $investment)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|string|in:active,completed,sold',
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

        $investment->update(['status' => $request->status]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Status updated successfully.'
            ]);
        }

        return back()->with('success', 'Status updated successfully.');
    }
}
