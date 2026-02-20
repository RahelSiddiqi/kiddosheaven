<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AccountController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = Auth::user();
        $orders = Order::where('user_id', $user->id)
            ->latest()
            ->take(5)
            ->get();

        $reviews = Review::where('user_id', $user->id)
            ->with('product:id,name,slug,images')
            ->latest()
            ->take(10)
            ->get();

        return view('customer.account', compact('user', 'orders', 'reviews'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],
        ]);

        $user->update($validated);

        return redirect()->route('account')->with('success', 'Profile updated successfully');
    }
}
