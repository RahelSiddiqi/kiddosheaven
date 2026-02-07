<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAttributeRequest;
use App\Models\ProductAttribute;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductAttributeController extends Controller
{
    public function index()
    {
        $attributes = ProductAttribute::orderBy('sort_order')->paginate(10);
        return view('admin.attributes.index', compact('attributes'));
    }

    public function store(StoreAttributeRequest $request)
    {
        $data = $request->validated();

        ProductAttribute::create([
            'name' => $data['name'],
            'slug' => Str::slug($data['name']),
            'type' => $data['type'],
            'is_required' => $data['is_required'] ?? false,
            'is_filterable' => $data['is_filterable'] ?? false,
            'description' => $data['description'] ?? null,
            'sort_order' => 0,
        ]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Attribute created successfully'
            ]);
        }

        return redirect()->route('admin.attributes.index')
            ->with('success', 'Attribute created successfully');
    }

    public function update(StoreAttributeRequest $request, ProductAttribute $attribute)
    {
        $data = $request->validated();

        $attribute->update([
            'name' => $data['name'],
            'slug' => Str::slug($data['name']),
            'type' => $data['type'],
            'is_required' => $data['is_required'] ?? false,
            'is_filterable' => $data['is_filterable'] ?? false,
            'description' => $data['description'] ?? null,
        ]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Attribute updated successfully'
            ]);
        }

        return redirect()->route('admin.attributes.index')
            ->with('success', 'Attribute updated successfully');
    }

    public function destroy(ProductAttribute $attribute)
    {
        $attribute->delete();

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Attribute deleted successfully'
            ]);
        }

        return redirect()->route('admin.attributes.index')
            ->with('success', 'Attribute deleted successfully');
    }
}
