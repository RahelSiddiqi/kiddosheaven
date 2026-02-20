<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ProductController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $ModelClass = class_exists(\App\Domains\Product\Models\Product::class)
            ? \App\Domains\Product\Models\Product::class
            : \App\Models\Product::class;

        $products = $ModelClass::where('is_active', true)
            ->when($request->category, fn($q, $cat) => $q->whereHas('category', fn($q) => $q->where('slug', $cat)))
            ->when($request->search, fn($q, $s) => $q->where('name', 'like', "%{$s}%"))
            ->orderBy('created_at', 'desc')
            ->paginate($request->integer('per_page', 20));

        return response()->json([
            'data' => $products->items(),
            'meta' => [
                'current_page' => $products->currentPage(),
                'last_page' => $products->lastPage(),
                'per_page' => $products->perPage(),
                'total' => $products->total(),
            ],
        ]);
    }

    public function show(string $slug): JsonResponse
    {
        $ModelClass = class_exists(\App\Domains\Product\Models\Product::class)
            ? \App\Domains\Product\Models\Product::class
            : \App\Models\Product::class;

        $product = $ModelClass::where('slug', $slug)->where('is_active', true)->firstOrFail();
        return response()->json(['data' => $product]);
    }
}
