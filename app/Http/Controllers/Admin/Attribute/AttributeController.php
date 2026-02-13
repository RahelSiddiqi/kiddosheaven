<?php

namespace App\Http\Controllers\Admin\Attribute;

use App\Http\Controllers\Controller;
use App\Models\ProductAttribute;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AttributeController extends Controller
{
    /**
     * Display a listing of all attributes.
     */
    public function index(Request $request)
    {
        $attributes = ProductAttribute::with(['values' => function($query) {
                $query->orderBy('sort_order');
            }, 'categories'])
            ->orderBy('sort_order')
            ->get();

        $categories = Category::orderBy('name')->get();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'html' => view('admin.attributes.partials.table', compact('attributes'))->render(),
            ]);
        }

        return view('admin.attributes.index', compact('attributes', 'categories'));
    }

    /**
     * Show the form for creating a new attribute.
     */
    public function create()
    {
        $categories = Category::orderBy('name')->get();

        return view('admin.attributes.create', compact('categories'));
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
            'use_for_variants' => 'nullable|boolean',
            'description' => 'nullable|string|max:500',
        ]);

        $maxOrder = ProductAttribute::max('sort_order') ?? 0;

        $attribute = ProductAttribute::create([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
            'type' => $validated['type'],
            'is_required' => $validated['is_required'] ?? false,
            'is_filterable' => $validated['is_filterable'] ?? false,
            'use_for_variants' => $validated['use_for_variants'] ?? false,
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
     * Show the form for editing an attribute.
     */
    public function edit(ProductAttribute $attribute)
    {
        $attribute->load('values');
        $categories = Category::orderBy('name')->get();

        return view('admin.attributes.edit', compact('attribute', 'categories'));
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
            'use_for_variants' => 'nullable|boolean',
            'description' => 'nullable|string|max:500',
        ]);

        $attribute->update([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
            'type' => $validated['type'],
            'is_required' => $validated['is_required'] ?? false,
            'is_filterable' => $validated['is_filterable'] ?? false,
            'use_for_variants' => $validated['use_for_variants'] ?? false,
            'description' => $validated['description'] ?? null,
        ]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Attribute updated successfully',
                'attribute' => $attribute,
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
        // Check if attribute is used by any products
        $productCount = $attribute->products()->count();
        if ($productCount > 0) {
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => "Cannot delete. This attribute is used by {$productCount} product(s).",
                ], 422);
            }
            return back()->with('error', "Cannot delete. This attribute is used by {$productCount} product(s).");
        }

        // Delete all associated values
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
     * Reorder attributes globally.
     */
    public function reorder(Request $request)
    {
        $request->validate([
            'order' => 'required|array',
        ]);

        foreach ($request->order as $index => $attrId) {
            ProductAttribute::where('id', $attrId)->update(['sort_order' => $index + 1]);
        }

        return response()->json(['success' => true]);
    }
}
