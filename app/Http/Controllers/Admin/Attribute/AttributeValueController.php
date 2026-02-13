<?php

namespace App\Http\Controllers\Admin\Attribute;

use App\Http\Controllers\Controller;
use App\Models\ProductAttribute;
use App\Models\ProductAttributeValue;
use Illuminate\Http\Request;

class AttributeValueController extends Controller
{
    /**
     * Show the form for editing attribute values.
     */
    public function edit(ProductAttribute $attribute)
    {
        $attribute->load('values');

        // Get categories that use this attribute
        $categories = \App\Models\Category::whereHas('attributes', function($query) use ($attribute) {
            $query->where('product_attributes.id', $attribute->id);
        })->get();

        return view('admin.attributes.values.edit', compact('attribute', 'categories'));
    }

    /**
     * Store a new attribute value.
     */
    public function store(Request $request, ProductAttribute $attribute)
    {
        $request->validate([
            'value' => 'required|string|max:255',
            'display_value' => 'nullable|string|max:255',
            'color_code' => 'nullable|string|max:7',
        ]);

        $maxOrder = $attribute->values()->max('sort_order') ?? 0;

        $value = $attribute->values()->create([
            'value' => $request->value,
            'display_value' => $request->display_value,
            'color_code' => $request->color_code,
            'sort_order' => $maxOrder + 1,
        ]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Value added successfully',
                'value' => $value,
            ]);
        }

        return back()->with('success', 'Value added successfully');
    }

    /**
     * Store multiple attribute values at once (e.g. one per line from bulk import).
     */
    public function storeBulk(Request $request, ProductAttribute $attribute)
    {
        $request->validate([
            'values' => 'required|array|min:1|max:500',
            'values.*' => 'required|string|max:255',
        ]);

        $maxOrder = $attribute->values()->max('sort_order') ?? 0;
        $created = [];

        foreach ($request->values as $index => $valueText) {
            $valueText = trim($valueText);
            if ($valueText === '') {
                continue;
            }
            $value = $attribute->values()->create([
                'value' => $valueText,
                'sort_order' => $maxOrder + $index + 1,
            ]);
            $created[] = $value;
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => count($created) . ' value(s) added successfully',
                'values' => $created,
            ]);
        }

        return back()->with('success', count($created) . ' value(s) added successfully');
    }

    /**
     * Update an attribute value.
     */
    public function update(Request $request, $attribute, $value)
    {
        $request->validate([
            'value' => 'required|string|max:255',
            'display_value' => 'nullable|string|max:255',
            'color_code' => 'nullable|string|max:7',
        ]);

        $attributeValue = ProductAttributeValue::findOrFail($value);

        $attributeValue->update([
            'value' => $request->value,
            'display_value' => $request->display_value,
            'color_code' => $request->color_code,
        ]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Value updated successfully',
                'value' => $attributeValue,
            ]);
        }

        return back()->with('success', 'Value updated successfully');
    }

    /**
     * Remove an attribute value.
     */
    public function destroy($attribute, $value)
    {
        $attributeValue = ProductAttributeValue::findOrFail($value);

        $usageCount = $attributeValue->variantAttributes()->count();
        if ($usageCount > 0) {
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => "Cannot delete value. It's used in {$usageCount} product variant(s). Remove from variants first.",
                ], 422);
            }
            return back()->with('error', "Cannot delete value. It's used in {$usageCount} product variant(s). Remove from variants first.");
        }

        $attributeValue->delete();

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Value deleted successfully',
            ]);
        }

        return back()->with('success', 'Value deleted successfully');
    }

    /**
     * Reorder attribute values.
     */
    public function reorder(Request $request, ProductAttribute $attribute)
    {
        $request->validate([
            'order' => 'required|array',
        ]);

        foreach ($request->order as $index => $valueId) {
            ProductAttributeValue::where('id', $valueId)
                ->where('product_attribute_id', $attribute->id)
                ->update(['sort_order' => $index + 1]);
        }

        return response()->json(['success' => true]);
    }
}
