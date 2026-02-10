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
        return view('admin.attributes.values.edit', compact('attribute'));
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

        // Check if value is used by any products
        $productCount = $attributeValue->products()->count();
        if ($productCount > 0) {
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => "Cannot delete. This value is used by {$productCount} product(s).",
                ], 422);
            }
            return back()->with('error', "Cannot delete. This value is used by {$productCount} product(s).");
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
                ->where('attribute_id', $attribute->id)
                ->update(['sort_order' => $index + 1]);
        }

        return response()->json(['success' => true]);
    }
}
