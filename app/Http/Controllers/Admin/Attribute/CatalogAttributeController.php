<?php

namespace App\Http\Controllers\Admin\Attribute;

use App\Http\Controllers\Controller;
use App\Models\Catalog;
use App\Models\CatalogType;
use App\Models\ProductAttribute;
use Illuminate\Http\Request;

class CatalogAttributeController extends Controller
{
    /**
     * Display catalog-attribute associations.
     */
    public function index(Catalog $catalog)
    {
        $attributes = CatalogType::where('slug', $catalog->type)
            ->first()
            ->attributes()
            ->with('values')
            ->orderBy('pivot_sort_order')
            ->get();

        $catalogAttributes = $catalog->attributes()
            ->with('values')
            ->orderBy('sort_order')
            ->get();

        return view('admin.attributes.catalog-attributes', compact('catalog', 'attributes', 'catalogAttributes'));
    }

    /**
     * Attach an attribute to a catalog.
     */
    public function attach(Request $request, Catalog $catalog)
    {
        $request->validate([
            'attribute_id' => 'required|exists:product_attributes,id',
        ]);

        // Check if already attached
        if ($catalog->attributes()->where('product_attributes.id', $request->attribute_id)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Attribute already attached to this catalog',
            ], 422);
        }

        $maxOrder = $catalog->attributes()->max('sort_order') ?? 0;

        $catalog->attributes()->attach($request->attribute_id, [
            'sort_order' => $maxOrder + 1,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Attribute attached successfully',
        ]);
    }

    /**
     * Detach an attribute from a catalog.
     */
    public function detach(Catalog $catalog, ProductAttribute $attribute)
    {
        $catalog->attributes()->detach($attribute->id);

        return response()->json([
            'success' => true,
            'message' => 'Attribute detached successfully',
        ]);
    }

    /**
     * Reorder catalog attributes.
     */
    public function reorder(Request $request, Catalog $catalog)
    {
        $request->validate([
            'order' => 'required|array',
        ]);

        foreach ($request->order as $index => $attrId) {
            $catalog->attributes()->updateExistingPivot($attrId, [
                'sort_order' => $index + 1
            ]);
        }

        return response()->json(['success' => true]);
    }
}
