<?php

namespace App\Http\Controllers\Admin\Product;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductAttribute;
use Illuminate\Http\Request;

class ProductAttributeValueController extends Controller
{
    /**
     * Get product attribute values.
     */
    public function index(Product $product)
    {
        $attributeValues = $product->custom_attributes ?? [];

        return response()->json([
            'success' => true,
            'attribute_values' => $attributeValues,
        ]);
    }

    /**
     * Update product attribute values.
     */
    public function update(Request $request, Product $product)
    {
        $request->validate([
            'attributes' => 'required|array',
            'attributes.*.attribute_id' => 'required|exists:product_attributes,id',
            'attributes.*.value' => 'required',
        ]);

        $attributeValues = [];

        foreach ($request->attributes as $attr) {
            $attribute = ProductAttribute::find($attr['attribute_id']);

            if (!$attribute) {
                continue;
            }

            $attributeValues[] = [
                'attribute_id' => $attribute->id,
                'attribute_name' => $attribute->name,
                'attribute_type' => $attribute->type,
                'value' => $attr['value'],
                'display_value' => $this->formatDisplayValue($attribute, $attr['value']),
            ];
        }

        $product->update(['custom_attributes' => $attributeValues]);

        return response()->json([
            'success' => true,
            'message' => 'Product attributes updated successfully',
            'attribute_values' => $attributeValues,
        ]);
    }

    /**
     * Add or update a single attribute value.
     */
    public function store(Request $request, Product $product)
    {
        $request->validate([
            'attribute_id' => 'required|exists:product_attributes,id',
            'value' => 'required',
        ]);

        $attribute = ProductAttribute::findOrFail($request->attribute_id);
        $attributeValues = $product->custom_attributes ?? [];

        // Find and update if exists, otherwise add new
        $found = false;
        foreach ($attributeValues as $key => $attrVal) {
            if ($attrVal['attribute_id'] == $attribute->id) {
                $attributeValues[$key] = [
                    'attribute_id' => $attribute->id,
                    'attribute_name' => $attribute->name,
                    'attribute_type' => $attribute->type,
                    'value' => $request->value,
                    'display_value' => $this->formatDisplayValue($attribute, $request->value),
                ];
                $found = true;
                break;
            }
        }

        if (!$found) {
            $attributeValues[] = [
                'attribute_id' => $attribute->id,
                'attribute_name' => $attribute->name,
                'attribute_type' => $attribute->type,
                'value' => $request->value,
                'display_value' => $this->formatDisplayValue($attribute, $request->value),
            ];
        }

        $product->update(['custom_attributes' => $attributeValues]);

        return response()->json([
            'success' => true,
            'message' => 'Attribute value saved successfully',
            'attribute_values' => $attributeValues,
        ]);
    }

    /**
     * Delete a product attribute value.
     */
    public function destroy(Product $product, $attributeId)
    {
        $attributeValues = $product->custom_attributes ?? [];
        $originalCount = count($attributeValues);

        $attributeValues = array_values(
            array_filter($attributeValues, fn($attr) => $attr['attribute_id'] != $attributeId)
        );

        if (count($attributeValues) === $originalCount) {
            return response()->json([
                'success' => false,
                'message' => 'Attribute not found',
            ], 404);
        }

        $product->update(['custom_attributes' => $attributeValues]);

        return response()->json([
            'success' => true,
            'message' => 'Attribute value deleted successfully',
        ]);
    }

    /**
     * Bulk update attribute values from category attributes.
     */
    public function syncFromCategory(Request $request, Product $product)
    {
        $request->validate([
            'attributes' => 'required|array',
        ]);

        $category = $product->category;

        if (!$category) {
            return response()->json([
                'success' => false,
                'message' => 'Product has no category assigned',
            ], 400);
        }

        $categoryAttributes = $category->attributes()->pluck('id')->toArray();
        $attributeValues = [];

        foreach ($request->attributes as $attributeId => $value) {
            // Only allow attributes from the product's category
            if (!in_array($attributeId, $categoryAttributes)) {
                continue;
            }

            $attribute = ProductAttribute::find($attributeId);

            if (!$attribute || empty($value)) {
                continue;
            }

            $attributeValues[] = [
                'attribute_id' => $attribute->id,
                'attribute_name' => $attribute->name,
                'attribute_type' => $attribute->type,
                'value' => $value,
                'display_value' => $this->formatDisplayValue($attribute, $value),
            ];
        }

        $product->update(['custom_attributes' => $attributeValues]);

        return response()->json([
            'success' => true,
            'message' => 'Attributes synced from category successfully',
            'attribute_values' => $attributeValues,
        ]);
    }

    /**
     * Format display value based on attribute type.
     */
    private function formatDisplayValue(ProductAttribute $attribute, $value)
    {
        switch ($attribute->type) {
            case 'boolean':
                return $value ? 'Yes' : 'No';
            case 'select':
            case 'radio':
                // Find the value label from attribute values
                $attrValue = $attribute->values()
                    ->where('value', $value)
                    ->first();
                return $attrValue ? $attrValue->label : $value;
            default:
                return $value;
        }
    }
}
