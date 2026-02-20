<?php

namespace App\Http\Controllers\Admin\Product;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Services\ImageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductImageController extends Controller
{
    /**
     * Upload product images.
     */
    public function upload(Request $request, Product $product)
    {
        $request->validate([
            'images'   => 'required|array|max:10',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:20480',
        ]);

        $imageService   = app(ImageService::class);
        $uploadedImages = [];
        $existingImages = $product->images ?? [];

        foreach ($request->file('images') as $image) {
            $uploadedImages[] = $imageService->store($image, 'products');
        }

        // Merge with existing images
        $allImages = array_merge($existingImages, $uploadedImages);

        $product->update([
            'images' => $allImages,
            'primary_image' => $product->primary_image ?? ($allImages[0] ?? null),
        ]);

        return response()->json([
            'success' => true,
            'message' => count($uploadedImages) . ' image(s) uploaded successfully',
            'images' => $allImages,
        ]);
    }

    /**
     * Set primary image.
     */
    public function setPrimary(Request $request, Product $product)
    {
        $request->validate([
            'image' => 'required|string',
        ]);

        $images = $product->images ?? [];

        if (!in_array($request->image, $images)) {
            return response()->json([
                'success' => false,
                'message' => 'Image not found',
            ], 404);
        }

        $product->update(['primary_image' => $request->image]);

        return response()->json([
            'success' => true,
            'message' => 'Primary image set successfully',
        ]);
    }

    /**
     * Delete a product image.
     */
    public function destroy(Request $request, Product $product)
    {
        $request->validate([
            'image' => 'required|string',
        ]);

        $images = $product->images ?? [];
        $imageToDelete = $request->image;

        if (!in_array($imageToDelete, $images)) {
            return response()->json([
                'success' => false,
                'message' => 'Image not found',
            ], 404);
        }

        // Remove from array
        $images = array_values(array_filter($images, fn($img) => $img !== $imageToDelete));

        // Delete from storage
        if (Storage::disk('public')->exists($imageToDelete)) {
            Storage::disk('public')->delete($imageToDelete);
        }

        // Update primary image if deleted
        $primaryImage = $product->primary_image;
        if ($primaryImage === $imageToDelete) {
            $primaryImage = $images[0] ?? null;
        }

        $product->update([
            'images' => $images,
            'primary_image' => $primaryImage,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Image deleted successfully',
            'images' => $images,
        ]);
    }

    /**
     * Reorder product images.
     */
    public function reorder(Request $request, Product $product)
    {
        $request->validate([
            'images' => 'required|array',
            'images.*' => 'string',
        ]);

        $newOrder = $request->images;
        $existingImages = $product->images ?? [];

        // Validate all images exist
        foreach ($newOrder as $image) {
            if (!in_array($image, $existingImages)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid image in order',
                ], 400);
            }
        }

        $product->update(['images' => $newOrder]);

        return response()->json([
            'success' => true,
            'message' => 'Images reordered successfully',
        ]);
    }
}
