<?php

namespace App\Http\Controllers\Admin\Catalog;

use App\Http\Controllers\Controller;
use App\Models\CatalogType;
use App\Models\ProductAttribute;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CatalogTypeAttributeController extends Controller
{
    /**
     * Display attributes for a catalog type.
     */
    public function index(CatalogType $type)
    {
        $typeAttributes = $type->attributes()->with('values')->get();
        $allAttributes = ProductAttribute::orderBy('name')->get();

        return view('admin.catalogs.types.attributes', compact('type', 'typeAttributes', 'allAttributes'));
    }

    /**
     * Attach an attribute to a catalog type.
     */
    public function attach(Request $request, CatalogType $type)
    {
        $request->validate([
            'attribute_id' => 'required|exists:product_attributes,id',
        ]);

        // Check if already attached
        if ($type->attributes()->where('product_attributes.id', $request->attribute_id)->exists()) {
            return response()->json(['success' => false, 'message' => 'Attribute already attached'], 422);
        }

        $maxOrder = DB::table('catalog_type_attributes')
            ->where('catalog_type_id', $type->id)
            ->max('sort_order') ?? 0;

        $type->attributes()->attach($request->attribute_id, ['sort_order' => $maxOrder + 1]);

        return response()->json(['success' => true, 'message' => 'Attribute attached successfully']);
    }

    /**
     * Detach an attribute from a catalog type.
     */
    public function detach(CatalogType $type, ProductAttribute $attribute)
    {
        $type->attributes()->detach($attribute->id);

        return response()->json(['success' => true, 'message' => 'Attribute detached successfully']);
    }

    /**
     * Sync attributes for a catalog type.
     */
    public function sync(Request $request, CatalogType $type)
    {
        $request->validate([
            'attributes' => 'nullable|array',
            'attributes.*' => 'exists:product_attributes,id',
        ]);

        $attributes = $request->get('attributes', []);

        $syncData = [];
        foreach ($attributes as $index => $attrId) {
            $syncData[$attrId] = ['sort_order' => $index + 1];
        }

        $type->attributes()->sync($syncData);

        return response()->json(['success' => true, 'message' => 'Attributes updated successfully']);
    }

    /**
     * Reorder attributes for a catalog type.
     */
    public function reorder(Request $request, CatalogType $type)
    {
        try {
            // Handle both JSON and FormData requests
            $orderData = null;
            if ($request->has('order')) {
                $orderJson = $request->get('order');
                if (is_string($orderJson)) {
                    $orderData = json_decode($orderJson, true);
                } else {
                    $orderData = $orderJson;
                }
            }

            if (empty($orderData) || !is_array($orderData)) {
                return response()->json(['success' => false, 'message' => 'Order is required and must be an array'], 422);
            }

            Log::info('reorderAttributes called', [
                'type_id' => $type->id,
                'order' => $orderData
            ]);

            foreach ($orderData as $index => $attributeId) {
                $type->attributes()->updateExistingPivot($attributeId, ['sort_order' => $index + 1]);
            }

            // Verify the update
            $verifyAttrs = $type->attributes()->orderByPivot('sort_order', 'asc')->get();
            Log::info('Reorder complete', [
                'type_id' => $type->id,
                'new_order' => $verifyAttrs->pluck('id')->toArray(),
                'sort_orders' => $verifyAttrs->pluck('pivot.sort_order')->toArray()
            ]);

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            Log::error('reorderAttributes error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
