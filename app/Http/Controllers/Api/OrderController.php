<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class OrderController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $ModelClass = class_exists(\App\Domains\Order\Models\Order::class)
            ? \App\Domains\Order\Models\Order::class
            : \App\Models\Order::class;

        $orders = $ModelClass::when($request->status, fn($q, $s) => $q->where('status', $s))
            ->orderBy('created_at', 'desc')
            ->paginate($request->integer('per_page', 20));

        return response()->json([
            'data' => $orders->items(),
            'meta' => [
                'current_page' => $orders->currentPage(),
                'last_page' => $orders->lastPage(),
                'per_page' => $orders->perPage(),
                'total' => $orders->total(),
            ],
        ]);
    }

    public function show(string $orderNumber): JsonResponse
    {
        $ModelClass = class_exists(\App\Domains\Order\Models\Order::class)
            ? \App\Domains\Order\Models\Order::class
            : \App\Models\Order::class;

        $order = $ModelClass::where('order_number', $orderNumber)->firstOrFail();
        return response()->json(['data' => $order]);
    }
}
