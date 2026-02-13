<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProductAttribute;
use App\Models\ProductAttributeValue;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AttributeController extends Controller
{
    /**
     * Display a listing of attributes.
     */
    public function index()
    {
        $attributes = ProductAttribute::with('values')->orderBy('name')->get();

        return view('admin.attributes.index', compact('attributes'));
    }

    /**
     * Store a newly created attribute.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:product_attributes,name',
            'type' => 'required|in:select,color,text',
        ]);

        $validated['slug'] = Str::slug($validated['name']);

        $attribute = ProductAttribute::create($validated);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'attribute' => $attribute->load('values'),
            ]);
        }

        return redirect()->route('admin.attributes.index')
            ->with('success', 'Attribute created successfully');
    }

    /**
     * Update the specified attribute.
     */
    public function update(Request $request, ProductAttribute $attribute)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:product_attributes,name,' . $attribute->id,
            'type' => 'required|in:select,color,text',
        ]);

        $validated['slug'] = Str::slug($validated['name']);

        $attribute->update($validated);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'attribute' => $attribute->fresh('values'),
            ]);
        }

        return redirect()->route('admin.attributes.index')
            ->with('success', 'Attribute updated successfully');
    }

    /**
     * Remove the specified attribute.
     */
    public function destroy(ProductAttribute $attribute)
    {
        // Check if attribute is used in products
        $usageCount = $attribute->variants()->count();

        if ($usageCount > 0) {
            return response()->json([
                'success' => false,
                'message' => "Cannot delete attribute. It's used in {$usageCount} product variants.",
            ], 400);
        }

        $attribute->delete();

        if (request()->ajax()) {
            return response()->json(['success' => true]);
        }

        return redirect()->route('admin.attributes.index')
            ->with('success', 'Attribute deleted successfully');
    }

    /**
     * Store a new attribute value.
     */
    public function storeValue(Request $request, ProductAttribute $attribute)
    {
        $validated = $request->validate([
            'value' => 'required|string|max:255',
            'color_hex' => 'nullable|string|size:7|regex:/^#[0-9A-Fa-f]{6}$/',
        ]);

        $validated['attribute_id'] = $attribute->id;

        $value = ProductAttributeValue::create($validated);

        return response()->json([
            'success' => true,
            'value' => $value,
        ]);
    }

    /**
     * Update an attribute value.
     */
    public function updateValue(Request $request, ProductAttributeValue $value)
    {
        $validated = $request->validate([
            'value' => 'required|string|max:255',
            'color_hex' => 'nullable|string|size:7|regex:/^#[0-9A-Fa-f]{6}$/',
        ]);

        $value->update($validated);

        return response()->json([
            'success' => true,
            'value' => $value->fresh(),
        ]);
    }

    /**
     * Delete an attribute value.
     */
    public function destroyValue(ProductAttributeValue $value)
    {
        // Check if value is used in variants
        $usageCount = $value->variants()->count();

        if ($usageCount > 0) {
            return response()->json([
                'success' => false,
                'message' => "Cannot delete value. It's used in {$usageCount} product variants.",
            ], 400);
        }

        $value->delete();

        return response()->json(['success' => true]);
    }

    /**
     * Reorder attribute values.
     */
    public function reorderValues(Request $request, ProductAttribute $attribute)
    {
        $validated = $request->validate([
            'order' => 'required|array',
        ]);

        foreach ($validated['order'] as $index => $valueId) {
            ProductAttributeValue::where('id', $valueId)
                ->where('attribute_id', $attribute->id)
                ->update(['sort_order' => $index]);
        }

        return response()->json(['success' => true]);
    }
}
