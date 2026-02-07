<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PartnerCalculation;
use Illuminate\Http\Request;

class PartnerCalculationController extends Controller
{
    public function approve(PartnerCalculation $calculation)
    {
        $calculation->update(['status' => 'approved']);

        return back()->with('success', 'Calculation approved.');
    }

    public function markPaid(PartnerCalculation $calculation)
    {
        // Create payment record
        $calculation->partner->payments()->create([
            'amount' => $calculation->payment_amount,
            'payment_date' => now()->toDateString(),
            'reference' => 'CALC-' . $calculation->id,
            'status' => 'completed',
            'notes' => 'Payment for calculation #' . $calculation->id,
        ]);

        $calculation->update(['status' => 'paid']);

        return back()->with('success', 'Marked as paid.');
    }
}
