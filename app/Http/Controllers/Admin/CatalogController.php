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
