<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderTrackingController extends Controller
{
    /**
     * Display order tracking form.
     */
    public function show(Request $request)
    {
        $order = null;

        if ($request->has('order_id') && $request->order_id) {
            $order = Order::where('order_number', $request->order_id)
                ->orWhere('id', $request->order_id)
                ->with(['items.product', 'statusHistory'])
                ->first();
        }

        return view('shop.order-tracking', compact('order'));
    }
}
