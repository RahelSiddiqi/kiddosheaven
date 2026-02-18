<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Address;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AddressController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display customer's addresses
     */
    public function index()
    {
        $addresses = Auth::user()->addresses()->latest()->get();

        return view('customer.addresses.index', compact('addresses'));
    }

    /**
     * Store a new address
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => ['required', 'in:shipping,billing'],
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:50'],
            'address_line1' => ['required', 'string', 'max:255'],
            'address_line2' => ['nullable', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:100'],
            'district' => ['nullable', 'string', 'max:100'],
            'postal_code' => ['nullable', 'string', 'max:20'],
            'is_default' => ['nullable', 'boolean'],
        ]);

        // If this should be default, unset all other defaults for this user
        if ($request->boolean('is_default')) {
            Auth::user()->addresses()
                ->where('type', $validated['type'])
                ->update(['is_default' => false]);
        }

        $address = Auth::user()->addresses()->create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Address added successfully',
            'address' => $address,
        ]);
    }

    /**
     * Update an existing address
     */
    public function update(Request $request, Address $address)
    {
        // Ensure user owns this address
        if ($address->user_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'type' => ['required', 'in:shipping,billing'],
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:50'],
            'address_line1' => ['required', 'string', 'max:255'],
            'address_line2' => ['nullable', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:100'],
            'district' => ['nullable', 'string', 'max:100'],
            'postal_code' => ['nullable', 'string', 'max:20'],
            'is_default' => ['nullable', 'boolean'],
        ]);

        // If this should be default, unset all other defaults for this user
        if ($request->boolean('is_default')) {
            Auth::user()->addresses()
                ->where('type', $validated['type'])
                ->where('id', '!=', $address->id)
                ->update(['is_default' => false]);
        }

        $address->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Address updated successfully',
            'address' => $address->fresh(),
        ]);
    }

    /**
     * Delete an address
     */
    public function destroy(Address $address)
    {
        // Ensure user owns this address
        if ($address->user_id !== Auth::id()) {
            abort(403);
        }

        $address->delete();

        return response()->json([
            'success' => true,
            'message' => 'Address deleted successfully',
        ]);
    }

    /**
     * Set an address as default
     */
    public function setDefault(Address $address)
    {
        // Ensure user owns this address
        if ($address->user_id !== Auth::id()) {
            abort(403);
        }

        // Unset all other defaults for this address type
        Auth::user()->addresses()
            ->where('type', $address->type)
            ->update(['is_default' => false]);

        // Set this as default
        $address->update(['is_default' => true]);

        return response()->json([
            'success' => true,
            'message' => 'Default address updated',
        ]);
    }
}
