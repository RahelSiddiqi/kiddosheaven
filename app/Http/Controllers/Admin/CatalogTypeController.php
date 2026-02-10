<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CatalogType;
use App\Models\ProductAttribute;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class CatalogTypeController extends Controller
{
    /**
     * Display a listing of catalog types.
     */
    public function index(Request $request)
    {
        $types = CatalogType::orderBy('sort_order')->get();

        return view('admin.catalogs.types.index', compact('types'));
    }

    /**
     * Store a newly created catalog type.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:catalog_types,name',
            'description' => 'nullable|string|max:500',
            'icon' => 'nullable|string|max:100',
            'is_active' => 'nullable|boolean',
        ]);

        $maxOrder = CatalogType::max('sort_order') ?? 0;

        $type = CatalogType::create([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
            'description' => $validated['description'] ?? null,
            'icon' => $validated['icon'] ?? null,
            'is_active' => $request->has('is_active'),
            'sort_order' => $maxOrder + 1,
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Catalog type created successfully',
                'type' => $type,
            ]);
        }

        return redirect()->route('admin.catalogs.types.index')
            ->with('success', 'Catalog type created successfully');
    }

    /**
     * Update the specified catalog type.
     */
    public function update(Request $request, CatalogType $type)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:catalog_types,name,' . $type->id,
            'description' => 'nullable|string|max:500',
            'icon' => 'nullable|string|max:100',
            'is_active' => 'nullable|boolean',
        ]);

        $type->update([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
            'description' => $validated['description'] ?? null,
            'icon' => $validated['icon'] ?? null,
            'is_active' => $validated['is_active'] ?? false,
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Catalog type updated successfully',
                'type' => $type,
            ]);
        }

        return redirect()->route('admin.catalogs.types.index')
            ->with('success', 'Catalog type updated successfully');
    }

    /**
     * Remove the specified catalog type.
     */
    public function destroy(CatalogType $catalogType)
    {
        // Check if any catalogs use this type
        $catalogCount = $catalogType->catalogs()->count();
        if ($catalogCount > 0) {
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => "Cannot delete this type. It is used by {$catalogCount} catalog(s).",
                ], 422);
            }
            return back()->with('error', "Cannot delete this type. It is used by {$catalogCount} catalog(s).");
        }

        $catalogType->delete();

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Catalog type deleted successfully',
            ]);
        }

        return redirect()->route('admin.catalogs.types.index')
            ->with('success', 'Catalog type deleted successfully');
    }

    /**
     * Reorder catalog types.
     */
    public function reorder(Request $request)
    {
        $request->validate([
            'order' => 'required|array',
        ]);

        foreach ($request->order as $index => $typeId) {
            CatalogType::where('id', $typeId)->update(['sort_order' => $index + 1]);
        }

        return response()->json(['success' => true, 'debug_loaded' => true]);
    }

    /**
     * Display attributes for a catalog type.
     */
    public function attributes(CatalogType $type)
    {
        // The relationship already orders by pivot sort_order, no need to re-order here
        $typeAttributes = $type->attributes()->with('values')->get();
        $allAttributes = ProductAttribute::orderBy('name')->get();

        return view('admin.catalogs.types.attributes', compact('type', 'typeAttributes', 'allAttributes'));
    }

    /**
     * Attach an attribute to a catalog type.
     */
    public function attachAttribute(Request $request, CatalogType $type)
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
    public function detachAttribute(CatalogType $type, ProductAttribute $attribute)
    {
        $type->attributes()->detach($attribute->id);

        return response()->json(['success' => true, 'message' => 'Attribute detached successfully']);
    }

    /**
     * Sync attributes for a catalog type.
     */
    public function syncAttributes(Request $request, CatalogType $type)
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
    public function reorderAttributes(Request $request, CatalogType $type)
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
