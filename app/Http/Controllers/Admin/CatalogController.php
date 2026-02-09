<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Catalog;

class CatalogController extends Controller
{
    // Show all catalogs with pagination and search
    public function index(Request $request)
    {
        $search = $request->get('search', '');

        $catalogs = Catalog::withCount('products')
            ->when($search, function ($query) use ($search) {
                return $query->where('name', 'like', '%' . $search . '%');
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'catalogs' => $catalogs->items(),
                'pagination' => [
                    'current_page' => $catalogs->currentPage(),
                    'last_page' => $catalogs->lastPage(),
                    'total' => $catalogs->total(),
                ]
            ]);
        }

        return view('admin.catalogs', compact('catalogs'));
    }

    // Store a new catalog
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:catalogs,name',
            'type' => 'nullable|string|max:50',
            'show_on_home' => 'nullable|boolean',
        ]);

        $catalog = Catalog::create([
            'name' => $request->name,
            'type' => $request->type ?? 'general',
            'show_on_home' => $request->has('show_on_home'),
        ]);

        $catalog->loadCount('products');

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Catalog created successfully!',
                'catalog' => $catalog
            ]);
        }

        return redirect()->route('admin.catalogs.index')->with('success', 'Catalog added successfully!');
    }

    // Update an existing catalog
    public function update(Request $request, Catalog $catalog)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:catalogs,name,' . $catalog->id,
            'type' => 'nullable|string|max:50',
            'show_on_home' => 'nullable|boolean',
        ]);

        $catalog->update([
            'name' => $request->name,
            'type' => $request->type ?? 'general',
            'show_on_home' => $request->has('show_on_home'),
        ]);

        $catalog->loadCount('products');

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Catalog updated successfully!',
                'catalog' => $catalog
            ]);
        }

        return redirect()->route('admin.catalogs.index')->with('success', 'Catalog updated successfully!');
    }

    // Show catalog details page
    public function show(Catalog $catalog)
    {
        $catalog->load([
            'products' => function ($query) {
                $query->withCount('orderItems')
                    ->limit(10);
            },
            'attributes' => function ($query) {
                $query->orderBy('pivot_sort_order');
            }
        ]);

        $catalog->loadCount('products');

        // Get available attributes that can be added
        $availableAttributes = \App\Models\ProductAttribute::whereDoesntHave('catalogs', function ($query) use ($catalog) {
                $query->where('catalogs.id', $catalog->id);
            })
            ->orderBy('name')
            ->get();

        return view('admin.catalogs.show', compact('catalog', 'availableAttributes'));
    }

    /**
     * Show the form for editing the specified catalog.
     */
    public function edit(Catalog $catalog)
    {
        $catalog->load(['type.attributes']);
        return view('admin.catalogs.edit', compact('catalog'));
    }

    /**
     * Update catalog attributes.
     */
    public function updateAttributes(Request $request, Catalog $catalog)
    {
        // Check if catalog has a type with attributes
        if (!$catalog->type || !$catalog->type->exists()) {
            return redirect()->back()->with('error', 'Please select a catalog type first.');
        }

        $request->validate([
            'attributes' => 'nullable|array',
            'attributes.*' => 'exists:product_attributes,id',
        ]);

        // Get the type's available attributes
        $typeAttributeIds = $catalog->type->attributes()->pluck('product_attributes.id')->toArray();

        // Filter to only include attributes that are available from the type
        $selectedAttributes = $request->get('attributes', []);
        $validAttributes = array_intersect($selectedAttributes, $typeAttributeIds);

        $syncData = [];
        foreach ($validAttributes as $index => $attrId) {
            $syncData[$attrId] = ['sort_order' => $index + 1, 'is_required' => false];
        }

        $catalog->attributes()->sync($syncData);

        return redirect()->back()->with('success', 'Catalog attributes updated successfully!');
    }

    // Delete a catalog
    public function destroy(Catalog $catalog)
    {
        $catalog->delete();

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Catalog deleted successfully!'
            ]);
        }

        return redirect()->route('admin.catalogs.index')->with('success', 'Catalog deleted successfully!');
    }
}
