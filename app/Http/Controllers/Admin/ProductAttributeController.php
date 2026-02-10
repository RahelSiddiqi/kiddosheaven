<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Catalog;
use App\Models\CatalogType;
use App\Models\ProductAttribute;
use App\Models\ProductAttributeValue;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductAttributeController extends Controller
{
    /**
     * Display a listing of all attributes.
     */
    public function index(Request $request)
    {
        $attributes = ProductAttribute::with(['values' => function($query) {
                $query->orderBy('sort_order');
            }, 'catalogs'])
            ->orderBy('sort_order')
            ->get();

        $catalogs = Catalog::orderBy('name')->get();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'html' => view('admin.attributes.partials.table', compact('attributes'))->render(),
            ]);
        }

        return view('admin.attributes.index', compact('attributes', 'catalogs'));
    }

    /**
     * Display catalog-attribute associations.
     */
    public function catalogAttributes(Catalog $catalog)
    {
        $attributes = CatalogType::where('slug', $catalog->type)->first()->attributes()->with('values')->orderBy('pivot_sort_order')->get();
        $catalogAttributes = $catalog->attributes()->with('values')->orderBy('sort_order')->get();
        return view('admin.attributes.catalog-attributes', compact('catalog', 'attributes', 'catalogAttributes'));
    }

    /**
     * Store a newly created attribute.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:product_attributes,name',
            'type' => 'required|in:text,select,multiselect,boolean,number,date',
            'is_required' => 'nullable|boolean',
            'is_filterable' => 'nullable|boolean',
            'description' => 'nullable|string|max:500',
        ]);

        $maxOrder = ProductAttribute::max('sort_order') ?? 0;

        $attribute = ProductAttribute::create([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
            'type' => $validated['type'],
            'is_required' => $validated['is_required'] ?? false,
            'is_filterable' => $validated['is_filterable'] ?? false,
            'description' => $validated['description'] ?? null,
            'sort_order' => $maxOrder + 1,
        ]);

        // Handle initial values for select/multiselect types
        if (in_array($attribute->type, ['select', 'multiselect']) && $request->has('initial_values')) {
            $values = explode("\n", $request->initial_values);
            foreach ($values as $index => $value) {
                $value = trim($value);
                if (!empty($value)) {
                    $attribute->values()->create([
                        'value' => $value,
                        'sort_order' => $index + 1,
                    ]);
                }
            }
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Attribute created successfully',
                'attribute' => $attribute->load('values'),
            ]);
        }

        return redirect()->route('admin.attributes.index')
            ->with('success', 'Attribute created successfully');
    }

    /**
     * Attach an attribute to a catalog.
     */
    public function attachAttribute(Request $request, Catalog $catalog)
    {
        $request->validate([
            'attribute_id' => 'required|exists:product_attributes,id',
            'is_required' => 'nullable|boolean',
        ]);

        $maxOrder = $catalog->attributes()->max('catalog_attributes.sort_order') ?? 0;

        $catalog->attributes()->syncWithoutDetaching([
            $request->attribute_id => [
                'is_required' => $request->is_required ?? false,
                'sort_order' => $maxOrder + 1,
            ],
        ]);

        return redirect()->route('admin.catalogs.attributes.index', $catalog)
            ->with('success', 'Attribute attached successfully');
    }

    /**
     * Detach an attribute from a catalog.
     */
    public function detachAttribute(Catalog $catalog, ProductAttribute $attribute)
    {
        $catalog->attributes()->detach($attribute->id);

        return redirect()->route('admin.catalogs.attributes.index', $catalog)
            ->with('success', 'Attribute detached successfully');
    }

    /**
     * Reorder catalog attributes.
     */
    public function reorderCatalogAttributes(Request $request, Catalog $catalog)
    {
        $request->validate([
            'order' => 'required|array',
        ]);

        foreach ($request->order as $index => $attributeId) {
            $catalog->attributes()->updateExistingPivot($attributeId, [
                'sort_order' => $index + 1
            ]);
        }

        return response()->json(['success' => true]);
    }

    /**
     * Store a new attribute value.
     */
    public function storeValue(Request $request, ProductAttribute $attribute)
    {
        $request->validate([
            'value' => 'required|string',
        ]);

        $maxOrder = $attribute->values()->max('sort_order') ?? 0;

        // Check if value contains newlines (bulk import)
        $values = explode("\n", $request->value);
        $createdCount = 0;

        foreach ($values as $val) {
            $val = trim($val);
            if (!empty($val)) {
                $attribute->values()->create([
                    'value' => $val,
                    'sort_order' => $maxOrder + 1 + $createdCount,
                ]);
                $createdCount++;
            }
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $createdCount . ' value(s) added successfully',
            ]);
        }

        return back()->with('success', $createdCount . ' value(s) added successfully');
    }

    /**
     * Update an attribute value.
     */
    public function updateValue(Request $request, $attribute, $value)
    {

        $request->validate([
            'value' => 'required|string|max:255',
        ]);

        $value = ProductAttributeValue::findOrFail($value);

        $value->update(['value' => $request->value]);

        return response()->json([
            'success' => true,
            'message' => 'Value updated successfully',
            'value' => $value,
        ]);
    }

    /**
     * Destroy an attribute value.
     */
    public function destroyValue($attribute, $value)
    {
        $value = ProductAttributeValue::findOrFail($value);

        $value->delete();

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Value deleted successfully',
            ]);
        }

        return back()->with('success', 'Value deleted successfully');
    }

    /**
     * Reorder attributes globally.
     */
    public function reorder(Request $request)
    {
        $request->validate([
            'order' => 'required|array',
        ]);

        foreach ($request->order as $index => $attributeId) {
            ProductAttribute::where('id', $attributeId)->update(['sort_order' => $index + 1]);
        }

        return response()->json(['success' => true]);
    }

    /**
     * Reorder attribute values.
     */
    public function reorderValues(Request $request, ProductAttribute $attribute)
    {
        $request->validate([
            'order' => 'required|array',
        ]);

        foreach ($request->order as $index => $valueId) {
            $attribute->values()->where('id', $valueId)->update(['sort_order' => $index + 1]);
        }

        return response()->json(['success' => true]);
    }

    /**
     * Update the specified attribute.
     */
    public function update(Request $request, ProductAttribute $attribute)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:product_attributes,name,' . $attribute->id,
            'type' => 'required|in:text,select,multiselect,boolean,number,date',
            'is_required' => 'nullable|boolean',
            'is_filterable' => 'nullable|boolean',
            'description' => 'nullable|string|max:500',
        ]);

        $attribute->update([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
            'type' => $validated['type'],
            'is_required' => $validated['is_required'] ?? false,
            'is_filterable' => $validated['is_filterable'] ?? false,
            'description' => $validated['description'] ?? null,
        ]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Attribute updated successfully',
                'attribute' => $attribute->fresh(),
            ]);
        }

        return redirect()->route('admin.attributes.index')
            ->with('success', 'Attribute updated successfully');
    }

    /**
     * Show the form for editing the specified attribute.
     */
    public function edit(ProductAttribute $attribute)
    {
        $attribute->load('values', 'catalogs');
        $catalogs = Catalog::orderBy('name')->get();

        return view('admin.attributes.edit', compact('attribute', 'catalogs'));
    }

    /**
     * Remove the specified attribute.
     */
    public function destroy(ProductAttribute $attribute)
    {
        // Check if attribute is used in any catalog
        $usageCount = $attribute->catalogs()->count();
        if ($usageCount > 0) {
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete attribute. It is used in ' . $usageCount . ' catalog(s).',
                ], 422);
            }
            return back()->with('error', 'Cannot delete attribute. It is used in ' . $usageCount . ' catalog(s).');
        }

        // Delete all values
        $attribute->values()->delete();

        $attribute->delete();

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Attribute deleted successfully',
            ]);
        }

        return redirect()->route('admin.attributes.index')
            ->with('success', 'Attribute deleted successfully');
    }

    /**
     * Show the form for editing attribute values (full page).
     */
    public function editValues(ProductAttribute $attribute)
    {
        $attribute->load('values');
        $catalogs = $attribute->catalogs()->get();

        return view('admin.attributes.edit-values', compact('attribute', 'catalogs'));
    }
}
