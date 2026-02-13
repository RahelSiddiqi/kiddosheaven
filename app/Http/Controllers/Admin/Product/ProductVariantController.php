<?php

namespace App\Http\Controllers\Admin\Product;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Services\Product\ProductVariantService;
use Illuminate\Http\Request;

class ProductVariantController extends Controller
{
    protected $variantService;

    public function __construct(ProductVariantService $variantService)
    {
        $this->variantService = $variantService;
    }

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
     * Display the specified variant.
     */
    public function show(Product $product, ProductVariant $variant)
    {
        // Ensure variant belongs to product
        if ($variant->product_id !== $product->id) {
            abort(404);
        }

        $variant->load(['product', 'variantAttributes']);

        return view('admin.products.variants.show', compact('product', 'variant'));
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
        // Find the variant
        $variant = ProductVariant::where('id', $variantId)
            ->where('product_id', $product->id)
            ->firstOrFail();

        // Support partial updates for inline editing
        $allowedFields = ['name', 'sku', 'price', 'cost_price', 'compare_at_price', 'stock_quantity', 'low_stock_alert', 'is_active', 'is_default'];
        $updateData = [];

        foreach ($allowedFields as $field) {
            if ($request->has($field)) {
                $updateData[$field] = $request->input($field);
            }
        }

        // Validate only the fields being updated
        if (!empty($updateData)) {
            $rules = [];
            if (isset($updateData['price'])) $rules['price'] = 'required|numeric|min:0';
            if (isset($updateData['cost_price'])) $rules['cost_price'] = 'nullable|numeric|min:0';
            if (isset($updateData['compare_at_price'])) $rules['compare_at_price'] = 'nullable|numeric|min:0';
            if (isset($updateData['stock_quantity'])) $rules['stock_quantity'] = 'required|integer|min:0';
            if (isset($updateData['sku'])) $rules['sku'] = 'nullable|string|max:100';
            if (isset($updateData['name'])) $rules['name'] = 'required|string|max:255';

            $request->validate($rules);
        }

        $variant->update($updateData);

        return response()->json([
            'success' => true,
            'message' => 'Variant updated successfully',
            'variant' => $variant->fresh(),
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
     * Generate variants from attributes using service.
     */
    public function generate(Request $request, Product $product)
    {
        $request->validate([
            'attributes' => 'required|array|min:1',
            'attributes.*.attribute_id' => 'required|integer|exists:product_attributes,id',
            'attributes.*.value_ids' => 'required|array|min:1',
            'attributes.*.value_ids.*' => 'required|integer|exists:product_attribute_values,id',
        ]);

        $result = $this->variantService->generateVariants($product, $request->attributes);

        if (!$result['success']) {
            return response()->json([
                'success' => false,
                'message' => $result['error']
            ], 500);
        }

        return response()->json([
            'success' => true,
            'message' => $result['created_count'] . ' variant(s) generated successfully. ' .
                        ($result['skipped_count'] > 0 ? $result['skipped_count'] . ' skipped (already exist).' : ''),
            'created_count' => $result['created_count'],
            'skipped_count' => $result['skipped_count'],
            'variants' => $result['created_variants']
        ]);
    }

    /**
     * Bulk update variants using service
     */
    public function bulkUpdate(Request $request, Product $product)
    {
        $request->validate([
            'variant_ids' => 'required|array',
            'variant_ids.*' => 'required|integer|exists:product_variants,id',
            'action_type' => 'required|string|in:cost_price,price,sale,stock',
            'value' => 'required|numeric',
            'as_percentage' => 'boolean'
        ]);

        // Verify all variants belong to this product
        $variants = ProductVariant::whereIn('id', $request->variant_ids)
            ->where('product_id', $product->id)
            ->get();

        if ($variants->count() !== count($request->variant_ids)) {
            return response()->json([
                'success' => false,
                'message' => 'Some variants do not belong to this product'
            ], 400);
        }

        $updated = $this->variantService->bulkUpdate(
            $request->variant_ids,
            $request->action_type,
            $request->value,
            $request->as_percentage ?? false
        );

        return response()->json([
            'success' => true,
            'message' => $updated . ' variant(s) updated successfully',
            'updated_count' => $updated
        ]);
    }
}
