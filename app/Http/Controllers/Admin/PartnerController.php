<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Partner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PartnerController extends Controller
{
    public function index(Request $request)
    {
        $query = Partner::with(['payments']);

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('contact_info', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $partners = $query->orderBy('name')->paginate(10);

        return view('admin.partners.index', compact('partners'));
    }

    public function create()
    {
        return view('admin.partners.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'type' => 'required|string|in:supplier,affiliate,franchise,employee,service_provider,reseller',
            'commission_rate' => 'nullable|numeric|min:0|max:100',
            'email' => 'nullable|email',
            'phone' => 'nullable|string',
            'address' => 'nullable|string',
            'bank_name' => 'nullable|string',
            'account_number' => 'nullable|string',
            'account_name' => 'nullable|string',
            'routing_number' => 'nullable|string',
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

        $data = $validator->validated();

        // Build contact_info as array
        $data['contact_info'] = json_encode([
            'email' => $data['email'] ?? '',
            'phone' => $data['phone'] ?? '',
            'address' => $data['address'] ?? '',
        ]);
        unset($data['email'], $data['phone'], $data['address']);

        // Build bank_details as array
        $data['bank_details'] = json_encode([
            'bank_name' => $data['bank_name'] ?? '',
            'account_number' => $data['account_number'] ?? '',
            'account_name' => $data['account_name'] ?? '',
            'routing_number' => $data['routing_number'] ?? '',
        ]);
        unset($data['bank_name'], $data['account_number'], $data['account_name'], $data['routing_number']);

        // Use default commission_rate of 0 if not provided
        $data['commission_rate'] = $data['commission_rate'] ?? 0;

        Partner::create($data);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Partner created successfully.'
            ]);
        }

        return redirect()->route('admin.partners.index')
            ->with('success', 'Partner created successfully.');
    }

    public function show(Partner $partner)
    {
        $partner->load(['payments']);

        return view('admin.partners.show', compact('partner'));
    }

    public function edit(Partner $partner)
    {
        return view('admin.partners.edit', compact('partner'));
    }

    public function update(Request $request, Partner $partner)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'type' => 'required|string|in:supplier,affiliate,franchise,employee,service_provider,reseller',
            'commission_rate' => 'nullable|numeric|min:0|max:100',
            'email' => 'nullable|email',
            'phone' => 'nullable|string',
            'address' => 'nullable|string',
            'bank_name' => 'nullable|string',
            'account_number' => 'nullable|string',
            'account_name' => 'nullable|string',
            'routing_number' => 'nullable|string',
            'notes' => 'nullable|string',
            'status' => 'nullable|string|in:active,inactive,suspended',
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

        $data = $validator->validated();

        // Build contact_info as array
        $data['contact_info'] = json_encode([
            'email' => $data['email'] ?? '',
            'phone' => $data['phone'] ?? '',
            'address' => $data['address'] ?? '',
        ]);
        unset($data['email'], $data['phone'], $data['address']);

        // Build bank_details as array
        $data['bank_details'] = json_encode([
            'bank_name' => $data['bank_name'] ?? '',
            'account_number' => $data['account_number'] ?? '',
            'account_name' => $data['account_name'] ?? '',
            'routing_number' => $data['routing_number'] ?? '',
        ]);
        unset($data['bank_name'], $data['account_number'], $data['account_name'], $data['routing_number']);

        $partner->update($data);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Partner updated successfully.'
            ]);
        }

        return redirect()->route('admin.partners.index')
            ->with('success', 'Partner updated successfully.');
    }

    public function destroy(Partner $partner)
    {
        $partner->delete();

        return redirect()->route('admin.partners.index')
            ->with('success', 'Partner deleted successfully.');
    }

    public function updateStatus(Request $request, Partner $partner)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|string|in:active,inactive,suspended',
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

        $partner->update(['status' => $request->status]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Status updated successfully.'
            ]);
        }

        return back()->with('success', 'Status updated successfully.');
    }

    /**
     * Store a new payment for a partner.
     */
    public function storePayment(Request $request, Partner $partner)
    {
        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric|min:0',
            'payment_date' => 'required|date',
            'description' => 'nullable|string|max:1000',
            'status' => 'nullable|string|in:completed,pending,cancelled',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $partner->payments()->create([
            'amount' => $validator->validated()['amount'],
            'payment_date' => $validator->validated()['payment_date'],
            'description' => $validator->validated()['description'] ?? null,
            'status' => $validator->validated()['status'] ?? 'completed',
        ]);

        return back()->with('success', 'Payment added successfully.');
    }

    /**
     * Update a payment for a partner.
     */
    public function updatePayment(Request $request, Partner $partner, $payment)
    {
        $payment = $partner->payments()->findOrFail($payment);

        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric|min:0',
            'payment_date' => 'required|date',
            'description' => 'nullable|string|max:1000',
            'status' => 'nullable|string|in:completed,pending,cancelled',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $payment->update([
            'amount' => $validator->validated()['amount'],
            'payment_date' => $validator->validated()['payment_date'],
            'description' => $validator->validated()['description'] ?? null,
            'status' => $validator->validated()['status'] ?? 'completed',
        ]);

        return back()->with('success', 'Payment updated successfully.');
    }

    /**
     * Destroy a payment for a partner.
     */
    public function destroyPayment(Request $request, Partner $partner, $payment)
    {
        $payment = $partner->payments()->findOrFail($payment);
        $payment->delete();

        return back()->with('success', 'Payment deleted successfully.');
    }
}
