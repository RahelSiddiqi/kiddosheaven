<?php

namespace App\Services\Product;

use App\Repositories\Contracts\ProductRepositoryInterface;
use App\Services\VariantGeneratorService;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ProductService
{
    protected ProductRepositoryInterface $productRepository;
    protected VariantGeneratorService $variantService;

    public function __construct(
        ProductRepositoryInterface $productRepository,
        VariantGeneratorService $variantService
    ) {
        $this->productRepository = $productRepository;
        $this->variantService = $variantService;
    }

    /**
     * Get all products with relations
     *
     * @return Collection
     */
    public function getAllWithRelations(): Collection
    {
        return $this->productRepository->allWithRelations();
    }

    /**
     * Get paginated products
     *
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getPaginated(int $perPage = 20): LengthAwarePaginator
    {
        return $this->productRepository->paginate($perPage);
    }

    /**
     * Find product by ID
     *
     * @param int $id
     * @return \App\Models\Product|null
     */
    public function findById(int $id): ?\App\Models\Product
    {
        return $this->productRepository->find($id);
    }

    /**
     * Find product by slug
     *
     * @param string $slug
     * @return \App\Models\Product|null
     */
    public function findBySlug(string $slug): ?\App\Models\Product
    {
        return $this->productRepository->findBySlug($slug);
    }

    /**
     * Create a new product
     *
     * @param array $data
     * @return \App\Models\Product
     */
    public function create(array $data): \App\Models\Product
    {
        return DB::transaction(function () use ($data) {
            // Generate slug
            $data['slug'] = $this->generateUniqueSlug($data['name']);

            // Handle product_type - default to simple if not set
            if (!isset($data['product_type'])) {
                $data['product_type'] = 'simple';
            }

            // Calculate profit margin if cost_price exists
            if (isset($data['cost_price']) && isset($data['price'])) {
                $data['profit_margin'] = $this->calculateProfitMargin(
                    $data['price'],
                    $data['cost_price']
                );
            }

            // Handle image uploads
            if (isset($data['images']) && is_array($data['images'])) {
                $data['images'] = $this->handleImageUploads($data['images']);
            }

            // Handle tags
            if (isset($data['tags']) && is_array($data['tags'])) {
                $data['tags'] = array_filter($data['tags']);
            }

            // Extract variants data if present
            $variantsData = $data['variants'] ?? [];
            unset($data['variants']);

            // Extract non-variant attributes if present
            $nonVariantAttrs = [];
            if (isset($data['non_variant_attributes'])) {
                $nonVariantAttrs = json_decode($data['non_variant_attributes'], true) ?? [];
                unset($data['non_variant_attributes']);
            }

            // Create the product
            $product = $this->productRepository->create($data);

            // Attach non-variant attributes
            if (!empty($nonVariantAttrs)) {
                $this->attachNonVariantAttributes($product, $nonVariantAttrs);
            }

            // Handle variants for variable products
            if ($product->product_type === 'variable' && !empty($variantsData)) {
                $this->createVariants($product, $variantsData);
            }

            return $product->fresh(['variants', 'category', 'brand']);
        });
    }

    /**
     * Update product
     *
     * @param int $id
     * @param array $data
     * @return \App\Models\Product
     */
    public function update(int $id, array $data): \App\Models\Product
    {
        return DB::transaction(function () use ($id, $data) {
            $product = $this->productRepository->findOrFail($id);

            // Update slug if name changed
            if (isset($data['name']) && $product->name !== $data['name']) {
                $data['slug'] = $this->generateUniqueSlug($data['name'], $id);
            }

            // Recalculate profit margin if prices changed
            $price = $data['price'] ?? $product->price;
            $costPrice = $data['cost_price'] ?? $product->cost_price;
            if ($price > 0 && $costPrice > 0) {
                $data['profit_margin'] = $this->calculateProfitMargin($price, $costPrice);
            }

            // ── Image handling ─────────────────────────
            // 1. Extract new file uploads from data BEFORE any overwriting
            $newFileUploads = [];
            if (isset($data['images']) && is_array($data['images'])) {
                foreach ($data['images'] as $img) {
                    if (is_object($img) && method_exists($img, 'store')) {
                        $newFileUploads[] = $img;
                    }
                }
            }

            // 2. Start with existing images
            $finalImages = $product->images ?? [];

            // 3. Handle deletions
            if (isset($data['delete_image']) && is_array($data['delete_image'])) {
                foreach ($data['delete_image'] as $imageToDelete) {
                    Storage::disk('public')->delete($imageToDelete);
                    $finalImages = array_values(array_filter($finalImages, fn($img) => $img !== $imageToDelete));
                }
                // Reset primary image if it was deleted
                if (in_array($product->primary_image, $data['delete_image'])) {
                    $data['primary_image'] = !empty($finalImages) ? $finalImages[0] : null;
                }
            }
            unset($data['delete_image']);

            // 4. Handle new uploads (append to remaining images)
            if (!empty($newFileUploads)) {
                $newPaths = $this->handleImageUploads($newFileUploads);
                $finalImages = array_merge($finalImages, $newPaths);
            }

            // 5. Only update images if we had deletions or new uploads
            if (isset($data['delete_image']) || !empty($newFileUploads) || isset($data['images'])) {
                $data['images'] = $finalImages;
            }

            // Handle primary_image
            if (isset($data['primary_image'])) {
                // Keep it as-is (it's a path string from the form)
            }

            // Handle tags
            if (isset($data['tags']) && is_array($data['tags'])) {
                $data['tags'] = array_values(array_filter($data['tags']));
            }

            // Extract variants data if present
            $variantsData = $data['variants'] ?? [];
            unset($data['variants']);

            // Extract non-variant attributes if present
            $nonVariantAttrs = [];
            if (isset($data['non_variant_attributes'])) {
                $nonVariantAttrs = json_decode($data['non_variant_attributes'], true) ?? [];
                unset($data['non_variant_attributes']);
            }

            // Update the product
            $updatedProduct = $this->productRepository->update($id, $data);

            // Sync non-variant attributes
            if (!empty($nonVariantAttrs)) {
                // Delete existing non-variant attributes
                $variantAttrIds = $updatedProduct->variants()
                    ->with('variantAttributes')
                    ->get()
                    ->flatMap(fn($v) => $v->variantAttributes->pluck('product_attribute_id'))
                    ->unique()
                    ->toArray();

                $updatedProduct->attributeValues()
                    ->whereNotIn('product_attribute_id', $variantAttrIds)
                    ->delete();

                // Attach new non-variant attributes
                $this->attachNonVariantAttributes($updatedProduct, $nonVariantAttrs);
            }

            // Handle variants for variable products
            if ($updatedProduct->product_type === 'variable' && !empty($variantsData)) {
                $this->syncVariants($updatedProduct, $variantsData);
            }

            return $updatedProduct->fresh(['variants', 'category', 'brand']);
        });
    }

    /**
     * Delete product
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        $product = $this->productRepository->findOrFail($id);

        // Delete associated images
        if ($product->images && is_array($product->images)) {
            foreach ($product->images as $image) {
                Storage::disk('public')->delete($image);
            }
        }

        return $this->productRepository->delete($id);
    }

    /**
     * Get featured products
     *
     * @param int $limit
     * @return Collection
     */
    public function getFeatured(int $limit = 10): Collection
    {
        return $this->productRepository->getFeaturedProducts($limit);
    }

    /**
    /**
     * Get products by category
     */
    public function getByCategory(int $categoryId, int $perPage = 20): LengthAwarePaginator
    {
        return $this->productRepository->getByCategory($categoryId, $perPage);
    }

    /**
     * Search products
     *
     * @param string $query
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function search(string $query, int $perPage = 20): LengthAwarePaginator
    {
        return $this->productRepository->search($query, $perPage);
    }

    /**
     * Get products with filters
     *
     * @param array $filters
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getFiltered(array $filters, int $perPage = 20): LengthAwarePaginator
    {
        return $this->productRepository->getWithFilters($filters, $perPage);
    }

    /**
     * Update stock
     *
     * @param int $productId
     * @param int $quantity
     * @param string $operation
     * @return bool
     */
    public function updateStock(int $productId, int $quantity, string $operation = 'set'): bool
    {
        return $this->productRepository->updateStock($productId, $quantity, $operation);
    }

    /**
     * Get low stock products
     *
     * @return Collection
     */
    public function getLowStock(): Collection
    {
        return $this->productRepository->getLowStockProducts();
    }

    /**
     * Generate unique slug
     *
     * @param string $name
     * @param int|null $ignoreId
     * @return string
     */
    protected function generateUniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $slug = Str::slug($name);
        $originalSlug = $slug;
        $counter = 1;

        while ($this->slugExists($slug, $ignoreId)) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    /**
     * Check if slug exists
     *
     * @param string $slug
     * @param int|null $ignoreId
     * @return bool
     */
    protected function slugExists(string $slug, ?int $ignoreId = null): bool
    {
        $query = $this->productRepository->findBySlug($slug);

        if ($ignoreId && $query) {
            return $query->id !== $ignoreId;
        }

        return $query !== null;
    }

    /**
     * Calculate profit margin
     *
     * @param float $price
     * @param float $costPrice
     * @return float
     */
    protected function calculateProfitMargin(float $price, float $costPrice): float
    {
        if ($price <= 0) {
            return 0;
        }

        return (($price - $costPrice) / $price) * 100;
    }

    /**
     * Handle image uploads
     *
     * @param array $images
     * @return array
     */
    protected function handleImageUploads(array $images): array
    {
        $uploadedPaths = [];

        foreach ($images as $image) {
            if ($image && is_object($image) && method_exists($image, 'store')) {
                $path = $image->store('products', 'public');
                $uploadedPaths[] = $path;
            }
        }

        return $uploadedPaths;
    }

    /**
     * Create variants for a product
     *
     * @param \App\Models\Product $product
     * @param array $variantsData
     * @return void
     */
    protected function createVariants(\App\Models\Product $product, array $variantsData): void
    {
        foreach ($variantsData as $index => $variantData) {
            // Create the variant
            $variant = $product->variants()->create([
                'sku' => $variantData['sku'] ?? null,
                'price' => $variantData['price'] ?? $product->price,
                'cost_price' => $variantData['cost_price'] ?? $product->cost_price,
                'stock_quantity' => $variantData['stock_quantity'] ?? 0,
                'barcode' => $variantData['barcode'] ?? null,
                'weight' => $variantData['weight'] ?? null,
                'is_default' => !empty($variantData['is_default']),
                'is_active' => isset($variantData['is_active']) ? (bool) $variantData['is_active'] : true,
            ]);

            // Attach attribute values to the variant
            if (!empty($variantData['attributes'])) {
                foreach ($variantData['attributes'] as $attributeId => $valueId) {
                    $variant->variantAttributes()->create([
                        'product_attribute_id' => $attributeId,
                        'product_attribute_value_id' => $valueId,
                    ]);
                }
            }
        }
    }

    /**
     * Sync variants for an existing product (update/create/delete)
     *
     * @param \App\Models\Product $product
     * @param array $variantsData
     * @return void
     */
    protected function syncVariants(\App\Models\Product $product, array $variantsData): void
    {
        $existingIds = $product->variants()->pluck('id')->toArray();
        $updatedIds = [];

        foreach ($variantsData as $variantData) {
            if (!empty($variantData['id']) && in_array($variantData['id'], $existingIds)) {
                // Update existing variant
                $variant = $product->variants()->find($variantData['id']);
                $variant->update([
                    'sku' => $variantData['sku'] ?? $variant->sku,
                    'price' => $variantData['price'] ?? $variant->price,
                    'cost_price' => $variantData['cost_price'] ?? $variant->cost_price,
                    'stock_quantity' => $variantData['stock_quantity'] ?? $variant->stock_quantity,
                    'barcode' => $variantData['barcode'] ?? $variant->barcode,
                    'is_default' => !empty($variantData['is_default']),
                    'is_active' => isset($variantData['is_active']) ? (bool) $variantData['is_active'] : true,
                ]);

                // Sync attributes
                if (!empty($variantData['attributes'])) {
                    $variant->variantAttributes()->delete();
                    foreach ($variantData['attributes'] as $attributeId => $valueId) {
                        $variant->variantAttributes()->create([
                            'product_attribute_id' => $attributeId,
                            'product_attribute_value_id' => $valueId,
                        ]);
                    }
                }

                $updatedIds[] = $variantData['id'];
            } else {
                // Create new variant
                $variant = $product->variants()->create([
                    'sku' => $variantData['sku'] ?? null,
                    'price' => $variantData['price'] ?? $product->price,
                    'cost_price' => $variantData['cost_price'] ?? $product->cost_price,
                    'stock_quantity' => $variantData['stock_quantity'] ?? 0,
                    'barcode' => $variantData['barcode'] ?? null,
                    'is_default' => !empty($variantData['is_default']),
                    'is_active' => isset($variantData['is_active']) ? (bool) $variantData['is_active'] : true,
                ]);

                if (!empty($variantData['attributes'])) {
                    foreach ($variantData['attributes'] as $attributeId => $valueId) {
                        $variant->variantAttributes()->create([
                            'product_attribute_id' => $attributeId,
                            'product_attribute_value_id' => $valueId,
                        ]);
                    }
                }

                $updatedIds[] = $variant->id;
            }
        }

        // Delete variants that were removed
        $toDelete = array_diff($existingIds, $updatedIds);
        if (!empty($toDelete)) {
            $product->variants()->whereIn('id', $toDelete)->each(function ($variant) {
                $variant->variantAttributes()->delete();
                $variant->delete();
            });
        }
    }

    /**
     * Attach non-variant attributes to a product
     *
     * @param \App\Models\Product $product
     * @param array $attributes
     * @return void
     */
    protected function attachNonVariantAttributes(\App\Models\Product $product, array $attributes): void
    {
        foreach ($attributes as $attr) {
            if (!empty($attr['attribute_id']) && !empty($attr['value'])) {
                $product->attributeValues()->create([
                    'product_attribute_id' => $attr['attribute_id'],
                    'value' => $attr['value'],
                ]);
            }
        }
    }
}
