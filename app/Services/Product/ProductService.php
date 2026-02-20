<?php

namespace App\Services\Product;

use App\Repositories\Contracts\ProductRepositoryInterface;
use App\Services\ImageService;
use App\Services\VariantGeneratorService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductService
{
    protected ProductRepositoryInterface $productRepository;

    protected VariantGeneratorService $variantService;

    protected ImageService $imageService;

    public function __construct(
        ProductRepositoryInterface $productRepository,
        VariantGeneratorService $variantService,
        ImageService $imageService
    ) {
        $this->productRepository = $productRepository;
        $this->variantService    = $variantService;
        $this->imageService      = $imageService;
    }

    /**
     * Get all products with relations
     */
    public function getAllWithRelations(): Collection
    {
        return $this->productRepository->allWithRelations();
    }

    /**
     * Get paginated products
     */
    public function getPaginated(int $perPage = 20): LengthAwarePaginator
    {
        return $this->productRepository->paginate($perPage);
    }

    /**
     * Find product by ID
     */
    public function findById(int $id): ?\App\Models\Product
    {
        return $this->productRepository->find($id);
    }

    /**
     * Find product by slug
     */
    public function findBySlug(string $slug): ?\App\Models\Product
    {
        return $this->productRepository->findBySlug($slug);
    }

    /**
     * Create a new product
     */
    public function create(array $data): \App\Models\Product
    {
        return DB::transaction(function () use ($data) {
            // Generate slug
            $data['slug'] = $this->generateUniqueSlug($data['name']);

            // Handle product_type - default to simple if not set
            if (! isset($data['product_type'])) {
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
                // Auto-set primary image if not specified
                if (empty($data['primary_image']) && ! empty($data['images'])) {
                    $data['primary_image'] = $data['images'][0];
                }
            }

            // Handle tags
            if (isset($data['tags']) && is_array($data['tags'])) {
                $data['tags'] = array_filter($data['tags']);
            }

            // Extract variants data if present
            $variantsData = $data['variants'] ?? [];
            unset($data['variants']);

            // Extract attribute configurations if present (NEW SYSTEM)
            $attributeConfigs = [];
            if (isset($data['attribute_configs'])) {
                $attributeConfigs = json_decode($data['attribute_configs'], true) ?? [];
                unset($data['attribute_configs']);
            }

            // Extract non-variant attributes if present (OLD SYSTEM - backward compatibility)
            $nonVariantAttrs = [];
            if (isset($data['non_variant_attributes'])) {
                $nonVariantAttrs = json_decode($data['non_variant_attributes'], true) ?? [];
                unset($data['non_variant_attributes']);
            }

            // Extract stock quantity and cost price for batch creation
            $initialStockQty = isset($data['stock_quantity']) ? (int) $data['stock_quantity'] : 0;
            $costPrice = isset($data['cost_price']) ? (float) $data['cost_price'] : 0;

            // Create the product
            $product = $this->productRepository->create($data);

            // ── Create initial purchase batch if stock is provided ──
            // This ensures the product can be used in orders immediately via FIFO system
            if ($initialStockQty > 0) {
                \App\Models\PurchaseBatch::create([
                    'batch_number'       => 'INIT-' . $product->id . '-' . strtoupper(bin2hex(random_bytes(3))),
                    'product_id'         => $product->id,
                    'quantity_received'  => $initialStockQty,
                    'remaining_quantity' => $initialStockQty,
                    'quantity_reserved'  => 0,
                    'unit_cost'          => $costPrice,
                    'status'             => \App\Models\PurchaseBatch::STATUS_ACTIVE,
                    'purchase_date'      => now()->toDateString(),
                    'notes'              => 'Initial batch created with product',
                ]);
            }

            // ── Handle attribute configurations (NEW SYSTEM) ──
            if (!empty($attributeConfigs)) {
                foreach ($attributeConfigs as $config) {
                    \App\Models\ProductAttributeConfig::create([
                        'product_id' => $product->id,
                        'product_attribute_id' => $config['attribute_id'],
                        'usage_type' => $config['usage_type'], // 'variant' or 'specification'
                        'is_visible' => true,
                    ]);

                    // If it's a specification (non-variant), also attach values
                    if ($config['usage_type'] === 'specification' && !empty($config['values'])) {
                        foreach ($config['values'] as $valueId) {
                            // Store the selected value
                            $product->attributeValues()->create([
                                'product_attribute_id' => $config['attribute_id'],
                                'product_attribute_value_id' => $valueId,
                            ]);
                        }
                    }
                }
            }

            // Attach non-variant attributes (OLD SYSTEM - backward compatibility)
            if (! empty($nonVariantAttrs)) {
                $this->attachNonVariantAttributes($product, $nonVariantAttrs);
            }

            // Handle variants for variable products
            if ($product->product_type === 'variable' && ! empty($variantsData)) {
                $this->createVariants($product, $variantsData);
            }

            return $product->fresh(['variants', 'category', 'brand']);
        });
    }

    /**
     * Update product
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
                    if ($img instanceof \Illuminate\Http\UploadedFile) {
                        $newFileUploads[] = $img;
                    }
                }
            }

            // 2. Start with existing images from form or database
            $existingFromForm = $data['existing_images'] ?? [];
            if (! empty($existingFromForm)) {
                // Use existing images from form (submitted as hidden inputs)
                $finalImages = array_values(array_filter($existingFromForm, function ($img) {
                    return $img !== null && $img !== false && $img !== '' && ! empty($img);
                }));
            } else {
                // Use existing images from database and clean up any corrupted data
                $existingImages = $product->images ?? [];
                $finalImages = array_values(array_filter($existingImages, function ($img) {
                    return $img !== null && $img !== false && $img !== '' && ! empty($img);
                }));
            }

            // 3. Handle deletions
            if (isset($data['delete_image']) && is_array($data['delete_image'])) {
                foreach ($data['delete_image'] as $imageToDelete) {
                    if (! empty($imageToDelete) && Storage::disk('public')->exists($imageToDelete)) {
                        Storage::disk('public')->delete($imageToDelete);
                    }
                    $finalImages = array_values(array_filter($finalImages, fn ($img) => $img !== $imageToDelete));
                }
                // Reset primary image if it was deleted
                if (! empty($product->primary_image) && in_array($product->primary_image, $data['delete_image'])) {
                    $data['primary_image'] = ! empty($finalImages) ? $finalImages[0] : null;
                }
            }
            unset($data['delete_image']);

            // 4. Handle new uploads (append to remaining images)
            if (! empty($newFileUploads)) {
                $newPaths = $this->handleImageUploads($newFileUploads);
                $finalImages = array_merge($finalImages, $newPaths);
            }

            // 5. Always set the cleaned images array so corrupted data gets fixed
            $data['images'] = array_values($finalImages);

            // Handle primary_image - auto-set if not specified but images exist
            if (empty($data['primary_image']) && ! empty($finalImages)) {
                $data['primary_image'] = $finalImages[0];
            }

            // Clean up non-column keys
            unset($data['existing_images']);

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
            if (! empty($nonVariantAttrs)) {
                // Delete existing non-variant attributes
                $variantAttrIds = $updatedProduct->variants()
                    ->with('variantAttributes')
                    ->get()
                    ->flatMap(fn ($v) => $v->variantAttributes->pluck('product_attribute_id'))
                    ->unique()
                    ->toArray();

                $updatedProduct->attributeValues()
                    ->whereNotIn('product_attribute_id', $variantAttrIds)
                    ->delete();

                // Attach new non-variant attributes
                $this->attachNonVariantAttributes($updatedProduct, $nonVariantAttrs);
            }

            // Handle variants for variable products
            if ($updatedProduct->product_type === 'variable' && ! empty($variantsData)) {
                $this->syncVariants($updatedProduct, $variantsData);
            }

            return $updatedProduct->fresh(['variants', 'category', 'brand']);
        });
    }

    /**
     * Delete product
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
     */
    public function search(string $query, int $perPage = 20): LengthAwarePaginator
    {
        return $this->productRepository->search($query, $perPage);
    }

    /**
     * Get products with filters
     */
    public function getFiltered(array $filters, int $perPage = 20): LengthAwarePaginator
    {
        return $this->productRepository->getWithFilters($filters, $perPage);
    }

    /**
     * Update stock
     */
    public function updateStock(int $productId, int $quantity, string $operation = 'set'): bool
    {
        return $this->productRepository->updateStock($productId, $quantity, $operation);
    }

    /**
     * Get low stock products
     */
    public function getLowStock(): Collection
    {
        return $this->productRepository->getLowStockProducts();
    }

    /**
     * Generate unique slug
     */
    protected function generateUniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $slug = Str::slug($name);
        $originalSlug = $slug;
        $counter = 1;

        while ($this->slugExists($slug, $ignoreId)) {
            $slug = $originalSlug.'-'.$counter;
            $counter++;
        }

        return $slug;
    }

    /**
     * Check if slug exists
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
     */
    protected function handleImageUploads(array $images): array
    {
        $uploadedPaths = [];

        foreach ($images as $image) {
            // Skip empty values
            if (empty($image)) {
                continue;
            }

            // Handle uploaded file objects (from request->file())
            if ($image instanceof \Illuminate\Http\UploadedFile) {
                $path = $this->imageService->store($image, 'products');
                $uploadedPaths[] = $path;

                continue;
            }

            // Handle string paths
            if (is_string($image) && ! empty($image)) {
                // Skip if it's a data URL
                if (str_starts_with($image, 'data:')) {
                    continue;
                }
                // Use as path
                $uploadedPaths[] = $image;
            }
        }

        return $uploadedPaths;
    }

    /**
     * Create variants for a product
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
                'is_default' => ! empty($variantData['is_default']),
                'is_active' => isset($variantData['is_active']) ? (bool) $variantData['is_active'] : true,
            ]);

            // Attach attribute values to the variant
            if (! empty($variantData['attributes'])) {
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
     */
    protected function syncVariants(\App\Models\Product $product, array $variantsData): void
    {
        $existingIds = $product->variants()->pluck('id')->toArray();
        $updatedIds = [];

        foreach ($variantsData as $variantData) {
            if (! empty($variantData['id']) && in_array($variantData['id'], $existingIds)) {
                // Update existing variant
                $variant = $product->variants()->find($variantData['id']);
                $variant->update([
                    'sku' => $variantData['sku'] ?? $variant->sku,
                    'price' => $variantData['price'] ?? $variant->price,
                    'cost_price' => $variantData['cost_price'] ?? $variant->cost_price,
                    'stock_quantity' => $variantData['stock_quantity'] ?? $variant->stock_quantity,
                    'barcode' => $variantData['barcode'] ?? $variant->barcode,
                    'is_default' => ! empty($variantData['is_default']),
                    'is_active' => isset($variantData['is_active']) ? (bool) $variantData['is_active'] : true,
                ]);

                // Sync attributes
                if (! empty($variantData['attributes'])) {
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
                    'is_default' => ! empty($variantData['is_default']),
                    'is_active' => isset($variantData['is_active']) ? (bool) $variantData['is_active'] : true,
                ]);

                if (! empty($variantData['attributes'])) {
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
        if (! empty($toDelete)) {
            $product->variants()->whereIn('id', $toDelete)->each(function ($variant) {
                $variant->variantAttributes()->delete();
                $variant->delete();
            });
        }
    }

    /**
     * Attach non-variant attributes to a product
     */
    protected function attachNonVariantAttributes(\App\Models\Product $product, array $attributes): void
    {
        foreach ($attributes as $attr) {
            if (! empty($attr['attribute_id']) && ! empty($attr['value'])) {
                $product->attributeValues()->create([
                    'product_attribute_id' => $attr['attribute_id'],
                    'value' => $attr['value'],
                ]);
            }
        }
    }
}
