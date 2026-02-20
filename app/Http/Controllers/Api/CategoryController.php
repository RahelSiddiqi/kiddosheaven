<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class CategoryController extends Controller
{
    public function index(): JsonResponse
    {
        $ModelClass = class_exists(\App\Domains\Catalog\Models\Category::class)
            ? \App\Domains\Catalog\Models\Category::class
            : \App\Models\Category::class;

        $categories = $ModelClass::orderBy('name')->get(['id', 'name', 'slug', 'description']);
        return response()->json(['data' => $categories]);
    }

    public function show(string $slug): JsonResponse
    {
        $ModelClass = class_exists(\App\Domains\Catalog\Models\Category::class)
            ? \App\Domains\Catalog\Models\Category::class
            : \App\Models\Category::class;

        $category = $ModelClass::where('slug', $slug)->firstOrFail();
        return response()->json(['data' => $category]);
    }
}
