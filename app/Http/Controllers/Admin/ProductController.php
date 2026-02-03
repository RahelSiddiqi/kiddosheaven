<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::latest()->paginate(20);
        return view('admin.products.index', compact('products'));
    }

    public function create()
    {
        $catalogs = \App\Models\Catalog::all();
        return view('admin.products.create', compact('catalogs'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'catalog_id' => ['required', 'exists:catalogs,id'],
            'price' => ['required', 'integer', 'min:1'],
            'short_description' => ['nullable', 'string', 'max:500'],
            'description' => ['nullable', 'string'],
            'images' => ['nullable', 'array'],
            'images.*' => ['image', 'max:2048'],
            'is_featured' => ['boolean'],
        ], [
            'images.*.max' => 'Each image must not be greater than 2MB.',
        ]);

        $validated['slug'] = Str::slug($validated['name']);

        // Ensure unique slug
        $baseSlug = $validated['slug'];
        $counter = 1;
        while (Product::where('slug', $validated['slug'])->exists()) {
            $validated['slug'] = $baseSlug . '-' . $counter;
            $counter++;
        }


        // Handle image uploads
        $imagePaths = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                $imagePaths[] = $file->store('products', 'public');
            }
        }
        $validated['images'] = $imagePaths;

        Product::create($validated);

        return redirect()->route('admin.products.index')
            ->with('success', 'Product created successfully.');
    }

    public function show(Product $product)
    {
        return view('admin.products.show', compact('product'));
    }

    public function edit(Product $product)
    {
        $catalogs = \App\Models\Catalog::all();
        return view('admin.products.edit', compact('product', 'catalogs'));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'catalog_id' => ['required', 'exists:catalogs,id'],
            'price' => ['required', 'integer', 'min:1'],
            'short_description' => ['nullable', 'string', 'max:500'],
            'description' => ['nullable', 'string'],
            'images' => ['nullable', 'array'],
            'images.*' => ['image', 'max:2048'],
            'is_featured' => ['boolean'],
            'primary_image' => ['nullable', 'string'],
        ], [
            'images.*.max' => 'Each image must not be greater than 2MB.',
        ]);

        // Update slug if name changed
        if ($product->name !== $validated['name']) {
            $validated['slug'] = Str::slug($validated['name']);

            // Ensure unique slug
            $baseSlug = $validated['slug'];
            $counter = 1;
            while (Product::where('slug', $validated['slug'])->where('id', '!=', $product->id)->exists()) {
                $validated['slug'] = $baseSlug . '-' . $counter;
                $counter++;
            }
        }


        // Handle image uploads for update
        $imagePaths = $product->images ?? [];
        // Handle per-image delete
        if ($request->has('delete_image')) {
            $deleteImg = $request->input('delete_image');
            $imagePaths = array_values(array_filter($imagePaths, fn($img) => $img !== $deleteImg));
            // Optionally delete file from storage
            Storage::disk('public')->delete($deleteImg);
        }
        // Handle new uploads
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                $imagePaths[] = $file->store('products', 'public');
            }
        }
        $validated['images'] = $imagePaths;
        // Handle primary image selection
        if ($request->has('primary_image')) {
            $validated['primary_image'] = $request->input('primary_image');
        } else {
            $validated['primary_image'] = $imagePaths[0] ?? null;
        }

        $product->update($validated);

        return redirect()->route('admin.products.edit', $product)
            ->with('success', 'Product updated successfully.');
    }

    public function destroy(Product $product)
    {
        $product->delete();

        return redirect()->route('admin.products.index')
            ->with('success', 'Product deleted successfully.');
    }
}
