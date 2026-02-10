<?php

namespace App\Http\Controllers\Admin\Catalog;

use App\Http\Controllers\Controller;
use App\Models\CatalogType;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CatalogTypeController extends Controller
{
    /**
     * Display a listing of catalog types.
     */
    public function index(Request $request)
    {
        $types = CatalogType::orderBy('sort_order')->get();

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'types' => $types,
            ]);
        }

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
}
