<?php

namespace App\Http\Controllers\Admin\Product;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductVariantController extends Controller
{
    /**
     * Get variants for a product.
     */
    public function index(Product $product)
    {
        $variants = $product->variants ?? [];

        return response()->json([
            'success' => true,
            'variants' => $variants,
        ]);
    }

    /**
     * Add a variant to a product.
     */
    public function store(Request $request, Product $product)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'nullable|string|max:100',
            'price' => 'required|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
            'attributes' => 'nullable|array',
            'image' => 'nullable|string',
        ]);

        $variants = $product->variants ?? [];
        
        $newVariant = [
            'id' => uniqid('var_'),
            'name' => $request->name,
            'sku' => $request->sku,
            'price' => $request->price,
            'stock_quantity' => $request->stock_quantity,
            'attributes' => $request->attributes ?? [],
            'image' => $request->image,
            'is_active' => true,
        ];

        $variants[] = $newVariant;

        $product->update(['variants' => $variants]);

        return response()->json([
            'success' => true,
            'message' => 'Variant added successfully',
            'variant' => $newVariant,
        ]);
    }

    /**
     * Update a variant.
     */
    public function update(Request $request, Product $product, $variantId)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'nullable|string|max:100',
            'price' => 'required|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
            'attributes' => 'nullable|array',
            'image' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $variants = $product->variants ?? [];
        $updated = false;

        foreach ($variants as $key => $variant) {
            if ($variant['id'] === $variantId) {
                $variants[$key] = array_merge($variant, [
                    'name' => $request->name,
                    'sku' => $request->sku,
                    'price' => $request->price,
                    'stock_quantity' => $request->stock_quantity,
                    'attributes' => $request->attributes ?? [],
                    'image' => $request->image,
                    'is_active' => $request->is_active ?? true,
                ]);
                $updated = true;
                break;
            }
        }

        if (!$updated) {
            return response()->json([
                'success' => false,
                'message' => 'Variant not found',
            ], 404);
        }

        $product->update(['variants' => $variants]);

        return response()->json([
            'success' => true,
            'message' => 'Variant updated successfully',
            'variant' => $variants[$key],
        ]);
    }

    /**
     * Delete a variant.
     */
    public function destroy(Product $product, $variantId)
    {
        $variants = $product->variants ?? [];
        $originalCount = count($variants);

        $variants = array_values(array_filter($variants, fn($v) => $v['id'] !== $variantId));

        if (count($variants) === $originalCount) {
            return response()->json([
                'success' => false,
                'message' => 'Variant not found',
            ], 404);
        }

        $product->update(['variants' => $variants]);

        return response()->json([
            'success' => true,
            'message' => 'Variant deleted successfully',
        ]);
    }

    /**
     * Generate variants from attributes.
     */
    public function generate(Request $request, Product $product)
    {
        $request->validate([
            'attributes' => 'required|array|min:1',
            'attributes.*.name' => 'required|string',
            'attributes.*.values' => 'required|array|min:1',
        ]);

        $attributeSets = $request->attributes;
        $combinations = $this->generateCombinations($attributeSets);

        $variants = [];
        foreach ($combinations as $combination) {
            $variantName = implode(' / ', array_column($combination, 'value'));
            
            $variants[] = [
                'id' => uniqid('var_'),
                'name' => $product->name . ' - ' . $variantName,
                'sku' => null,
                'price' => $product->price,
                'stock_quantity' => 0,
                'attributes' => $combination,
                'image' => null,
                'is_active' => true,
            ];
        }

        $product->update(['variants' => $variants]);

        return response()->json([
            'success' => true,
            'message' => count($variants) . ' variant(s) generated successfully',
            'variants' => $variants,
        ]);
    }

    /**
     * Generate all combinations of attributes.
     */
    private function generateCombinations($attributeSets, $current = [], $index = 0)
    {
        if ($index === count($attributeSets)) {
            return [$current];
        }

        $results = [];
        $currentSet = $attributeSets[$index];

        foreach ($currentSet['values'] as $value) {
            $newCurrent = $current;
            $newCurrent[] = [
                'name' => $currentSet['name'],
                'value' => $value,
            ];
            $results = array_merge(
                $results,
                $this->generateCombinations($attributeSets, $newCurrent, $index + 1)
            );
        }

        return $results;
    }
}
