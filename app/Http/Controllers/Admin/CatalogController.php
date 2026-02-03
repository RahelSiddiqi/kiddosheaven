<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Catalog;

class CatalogController extends Controller
{
    // Show all catalogs
    public function index()
    {
        $catalogs = Catalog::all();
        return view('admin.catalogs', compact('catalogs'));
    }

    // Store a new catalog
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:catalogs,name',
            'show_on_home' => 'nullable|boolean',
        ]);
        Catalog::create([
            'name' => $request->name,
            'show_on_home' => $request->has('show_on_home'),
        ]);
        return redirect()->route('admin.catalogs.index')->with('success', 'Catalog added successfully!');
    }

    // Update an existing catalog
    public function update(Request $request, Catalog $catalog)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:catalogs,name,' . $catalog->id,
            'show_on_home' => 'nullable|boolean',
        ]);
        $catalog->update([
            'name' => $request->name,
            'show_on_home' => $request->has('show_on_home'),
        ]);
        return redirect()->route('admin.catalogs.index')->with('success', 'Catalog updated successfully!');
    }

    // Delete a catalog
    public function destroy(Catalog $catalog)
    {
        $catalog->delete();
        return redirect()->route('admin.catalogs.index')->with('success', 'Catalog deleted successfully!');
    }

}
