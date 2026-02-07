<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Catalog;
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
        $brands = \App\Models\Brand::all();
        return view('admin.products.create', compact('catalogs', 'brands'));
    }

    public function store(Request $request)
    {
        // Filter out empty tags before validation
        $request->merge([
            'tags' => array_filter($request->input('tags', []), fn($value) => !is_null($value) && $value !== '')
        ]);

        $validated = $request->validate([
            'custom_attributes' => ['nullable', 'array'],
            'name' => ['required', 'string', 'max:255'],
            'catalog_id' => ['required', 'exists:catalogs,id'],
            'brand_id' => ['nullable', 'exists:brands,id'],
            'price' => ['required', 'numeric', 'min:1'],
            'cost_price' => ['nullable', 'numeric', 'min:0'],
            'discount_price' => ['nullable', 'numeric', 'min:0'],
            'stock_quantity' => ['required', 'integer', 'min:0'],
            'sku' => ['nullable', 'string', 'max:100', 'unique:products,sku'],
            'short_description' => ['nullable', 'string', 'max:500'],
            'description' => ['nullable', 'string'],
            'images' => ['nullable', 'array'],
            'images.*' => ['image', 'max:2048'],
            'is_featured' => ['boolean'],
            'tags' => ['nullable', 'array'],
            'tags.*' => ['string', 'max:50'],
            'weight' => ['nullable', 'numeric', 'min:0'],
            'length' => ['nullable', 'numeric', 'min:0'],
            'width' => ['nullable', 'numeric', 'min:0'],
            'height' => ['nullable', 'numeric', 'min:0'],
            'status' => ['required', 'in:active,inactive'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:500'],
            'video_url' => ['nullable', 'string', 'max:255', 'url'],
        ], [
            'images.*.max' => 'Each image must not be greater than 2MB.',
        ]);
        // Handle tags as JSON array
        if (isset($validated['tags'])) {
            $validated['tags'] = json_encode($validated['tags']);
        }

        $validated['slug'] = Str::slug($validated['name']);

        // Calculate profit margin
        $costPrice = $validated['cost_price'] ?? 0;
        $price = $validated['price'];
        if ($price > 0 && $costPrice > 0) {
            $validated['profit_margin'] = (($price - $costPrice) / $price) * 100;
        } else {
            $validated['profit_margin'] = null;
        }

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
        $brands = \App\Models\Brand::all();

        // Get deleted images from session (persisted across validation failures)
        $sessionDeletedImages = session('product_deleted_images', []);
        // Also check for fresh deleted images from the last request
        $freshDeletedImages = session('deleted_images', []);
        // Merge and store for future use
        $allDeletedImages = array_unique(array_merge($sessionDeletedImages, $freshDeletedImages));

        if (!empty($allDeletedImages)) {
            // Temporarily filter images for display
            $product->images = array_values(array_filter($product->images ?? [], fn($img) => !in_array($img, $allDeletedImages)));
        }

        // Store the deleted images in session for persistence
        session(['product_deleted_images' => $allDeletedImages]);

        return view('admin.products.edit', compact('product', 'catalogs', 'brands'));
    }

    public function update(Request $request, Product $product)
    {
        // Filter out empty tags before validation
        $request->merge([
            'tags' => array_filter($request->input('tags', []), fn($value) => !is_null($value) && $value !== '')
        ]);

        // Get images to delete BEFORE validation (so they're preserved if validation fails)
        $imagesToDelete = $request->input('delete_image', []);
        if (!empty($imagesToDelete) && !is_array($imagesToDelete)) {
            $imagesToDelete = [$imagesToDelete];
        }
        // Flash deleted images to session for persistence across validation failures
        if (!empty($imagesToDelete)) {
            $request->session()->flash('deleted_images', $imagesToDelete);
        }

        $validated = $request->validate([
            'custom_attributes' => ['nullable', 'array'],
            'name' => ['required', 'string', 'max:255'],
            'catalog_id' => ['required', 'exists:catalogs,id'],
            'brand_id' => ['nullable', 'exists:brands,id'],
            'price' => ['required', 'numeric', 'min:1'],
            'cost_price' => ['nullable', 'numeric', 'min:0'],
            'discount_price' => ['nullable', 'numeric', 'min:0'],
            'stock_quantity' => ['required', 'integer', 'min:0'],
            'sku' => ['nullable', 'string', 'max:100', 'unique:products,sku,' . $product->id],
            'short_description' => ['nullable', 'string', 'max:500'],
            'description' => ['nullable', 'string'],
            'images' => ['nullable', 'array'],
            'images.*' => ['image', 'max:2048'],
            'is_featured' => ['boolean'],
            'primary_image' => ['nullable', 'string'],
            'tags' => ['nullable', 'array'],
            'tags.*' => ['string', 'max:50'],
            'weight' => ['nullable', 'numeric', 'min:0'],
            'length' => ['nullable', 'numeric', 'min:0'],
            'width' => ['nullable', 'numeric', 'min:0'],
            'height' => ['nullable', 'numeric', 'min:0'],
            'status' => ['required', 'in:active,inactive'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:500'],
            'video_url' => ['nullable', 'string', 'max:255', 'url'],
        ], [
            'images.*.max' => 'Each image must not be greater than 2MB.',
        ]);
        // Handle tags as JSON array
        if (isset($validated['tags'])) {
            $validated['tags'] = json_encode($validated['tags']);
        }

        // Calculate profit margin
        $costPrice = $validated['cost_price'] ?? 0;
        $price = $validated['price'];
        if ($price > 0 && $costPrice > 0) {
            $validated['profit_margin'] = (($price - $costPrice) / $price) * 100;
        } else {
            $validated['profit_margin'] = null;
        }

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

        // Process new image uploads and create fileId to path mapping
        $newImagePaths = []; // fileId => storage path mapping
        if ($request->hasFile('images')) {
            $fileIds = $request->input('image_file_ids', []);
            $fileIndex = 0;
            foreach ($request->file('images') as $file) {
                $fileId = $fileIds[$fileIndex] ?? null;
                $path = $file->store('products', 'public');
                $imagePaths[] = $path;
                // Store mapping if fileId provided
                if ($fileId) {
                    $newImagePaths[$fileId] = $path;
                }
                $fileIndex++;
            }
        }

        // Handle per-image delete (support multiple deletions)
        // Merge session-deleted images with fresh delete requests
        $sessionDeletedImages = session('product_deleted_images', []);
        $requestDeletedImages = $request->input('delete_image', []);
        if (!is_array($requestDeletedImages)) {
            $requestDeletedImages = [$requestDeletedImages];
        }
        $imagesToDelete = array_unique(array_merge($sessionDeletedImages, $requestDeletedImages));

        if (!empty($imagesToDelete)) {
            foreach ($imagesToDelete as $deleteImg) {
                $imagePaths = array_values(array_filter($imagePaths, fn($img) => $img !== $deleteImg));
                // Delete file from storage
                Storage::disk('public')->delete($deleteImg);
            }
        }

        $validated['images'] = $imagePaths;
        // Handle primary image selection
        $primaryImage = $request->input('primary_image');

        // Check if primary image is a new file (marked with 'new:' prefix)
        if ($primaryImage && str_starts_with($primaryImage, 'new:')) {
            $fileId = substr($primaryImage, 4);
            if (isset($newImagePaths[$fileId])) {
                $primaryImage = $newImagePaths[$fileId];
            } else {
                // Fallback to first image
                $primaryImage = !empty($imagePaths) ? $imagePaths[0] : null;
            }
        } elseif (!$primaryImage && !empty($imagePaths)) {
            // If no primary image selected, use the first image
            $primaryImage = $imagePaths[0];
        }
        $validated['primary_image'] = $primaryImage;

        $product->update($validated);

        // Clear the deleted images session after successful update
        $request->session()->forget(['product_deleted_images', 'deleted_images']);

        return redirect()->route('admin.products.edit', $product)
            ->with('success', 'Product updated successfully.');
    }

    public function destroy(Product $product)
    {
        $product->delete();

        return redirect()->route('admin.products.index')
            ->with('success', 'Product deleted successfully.');
    }

    /**
     * Get attributes for a specific catalog (AJAX)
     */
    public function getAttributesByCatalog(Catalog $catalog)
    {
        $attributes = $catalog->attributes()->with('values')->get();

        return response()->json([
            'catalog' => $catalog,
            'attributes' => $attributes,
        ]);
    }
}
