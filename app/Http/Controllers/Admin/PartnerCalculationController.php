<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Partner;
use App\Models\PartnerCalculation;
use App\Models\PartnerPayment;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PartnerCalculationController extends Controller
{
    public function index(Request $request)
    {
        $query = PartnerCalculation::with('partner');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('partner', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('from_date')) {
            $query->whereDate('period_start', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->whereDate('period_end', '<=', $request->to_date);
        }

        $calculations = $query->latest()->paginate(10);

        return view('admin.partner-calculations.index', compact('calculations'));
    }

    public function create()
    {
        $partners = Partner::where('status', 'active')->get();

        return view('admin.partner-calculations.create', compact('partners'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'partner_id' => 'required|exists:partners,id',
            'period_start' => 'required|date',
            'period_end' => 'required|date|after:period_start',
            'calculation_type' => 'required|in:monthly,quarterly,custom',
            'notes' => 'nullable|string',
        ]);

        $partner = Partner::findOrFail($validated['partner_id']);

        // Check for existing calculation in period
        $existing = PartnerCalculation::where('partner_id', $validated['partner_id'])
            ->where(function ($q) use ($validated) {
                $q->whereBetween('period_start', [$validated['period_start'], $validated['period_end']])
                  ->orWhereBetween('period_end', [$validated['period_start'], $validated['period_end']])
                  ->orWhere(function ($q) use ($validated) {
                      $q->where('period_start', '<=', $validated['period_start'])
                        ->where('period_end', '>=', $validated['period_end']);
                  });
            })
            ->exists();

        if ($existing) {
            return back()->with('error', 'A calculation already exists for this period.');
        }

        // Calculate earnings based on partner type
        $orders = Order::where('partner_id', $validated['partner_id'])
            ->whereBetween('created_at', [$validated['period_start'], $validated['period_end']])
            ->where('status', 'completed')
            ->get();

        $totalSales = $orders->sum('total');
        $commissionRate = $partner->commission_rate ?? 10;
        $commissionAmount = $totalSales * ($commissionRate / 100);

        $calculation = PartnerCalculation::create([
            'partner_id' => $validated['partner_id'],
            'period_start' => $validated['period_start'],
            'period_end' => $validated['period_end'],
            'calculation_type' => $validated['calculation_type'],
            'total_orders' => $orders->count(),
            'total_sales' => $totalSales,
            'commission_rate' => $commissionRate,
            'commission_amount' => $commissionAmount,
            'status' => 'pending',
            'notes' => $validated['notes'] ?? null,
        ]);

        return redirect()->route('admin.partner-calculations.show', $calculation)
            ->with('success', 'Calculation created successfully.');
    }

    public function show(PartnerCalculation $calculation)
    {
        $calculation->load(['partner', 'payments']);

        $orders = Order::where('partner_id', $calculation->partner_id)
            ->whereBetween('created_at', [$calculation->period_start, $calculation->period_end])
            ->where('status', 'completed')
            ->get();

        return view('admin.partner-calculations.show', compact('calculation', 'orders'));
    }

    public function approve(PartnerCalculation $calculation)
    {
        $calculation->update(['status' => 'approved']);

        return back()->with('success', 'Calculation approved.');
    }

    public function reject(Request $request, PartnerCalculation $calculation)
    {
        $calculation->update([
            'status' => 'rejected',
            'rejection_reason' => $request->rejection_reason,
        ]);

        return back()->with('success', 'Calculation rejected.');
    }

    public function pay(Request $request, PartnerCalculation $calculation)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0|max:' . ($calculation->due_amount),
            'payment_method' => 'required|string',
            'transaction_id' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        DB::transaction(function () use ($calculation, $validated) {
            PartnerPayment::create([
                'partner_calculation_id' => $calculation->id,
                'amount' => $validated['amount'],
                'payment_method' => $validated['payment_method'],
                'transaction_id' => $validated['transaction_id'] ?? null,
                'notes' => $validated['notes'] ?? null,
                'status' => 'completed',
            ]);

            $calculation->refresh();
            if ($calculation->paid_amount >= $calculation->commission_amount) {
                $calculation->update(['status' => 'paid']);
            }
        });

        return back()->with('success', 'Payment recorded successfully.');
    }

    public function generateMonthly()
    {
        $partners = Partner::where('status', 'active')->get();
        $results = [];

        foreach ($partners as $partner) {
            $periodStart = now()->startOfMonth();
            $periodEnd = now()->endOfMonth();

            // Check existing
            $exists = PartnerCalculation::where('partner_id', $partner->id)
                ->where('period_start', $periodStart)
                ->where('period_end', $periodEnd)
                ->exists();

            if (!$exists) {
                $orders = Order::where('partner_id', $partner->id)
                    ->whereBetween('created_at', [$periodStart, $periodEnd])
                    ->where('status', 'completed')
                    ->get();

                $totalSales = $orders->sum('total');
                $commissionRate = $partner->commission_rate ?? 10;

                PartnerCalculation::create([
                    'partner_id' => $partner->id,
                    'period_start' => $periodStart,
                    'period_end' => $periodEnd,
                    'calculation_type' => 'monthly',
                    'total_orders' => $orders->count(),
                    'total_sales' => $totalSales,
                    'commission_rate' => $commissionRate,
                    'commission_amount' => $totalSales * ($commissionRate / 100),
                    'status' => 'pending',
                ]);

                $results[] = $partner->name;
            }
        }

        if (empty($results)) {
            return back()->with('info', 'No new calculations to generate.');
        }

        return back()->with('success', 'Generated calculations for: ' . implode(', ', $results));
    }
}
