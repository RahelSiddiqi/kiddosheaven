<?php

namespace App\Services\Product;

use App\Repositories\Contracts\ProductRepositoryInterface;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ProductService
{
    protected ProductRepositoryInterface $productRepository;

    public function __construct(ProductRepositoryInterface $productRepository)
    {
        $this->productRepository = $productRepository;
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
        // Generate slug
        $data['slug'] = $this->generateUniqueSlug($data['name']);

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

        return $this->productRepository->create($data);
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
        // Update slug if name changed
        if (isset($data['name'])) {
            $product = $this->productRepository->findOrFail($id);
            if ($product->name !== $data['name']) {
                $data['slug'] = $this->generateUniqueSlug($data['name'], $id);
            }
        }

        // Recalculate profit margin if prices changed
        if (isset($data['price']) || isset($data['cost_price'])) {
            $product = $product ?? $this->productRepository->findOrFail($id);
            $price = $data['price'] ?? $product->price;
            $costPrice = $data['cost_price'] ?? $product->cost_price;

            if ($price > 0 && $costPrice > 0) {
                $data['profit_margin'] = $this->calculateProfitMargin($price, $costPrice);
            }
        }

        // Handle new image uploads
        if (isset($data['images']) && is_array($data['images'])) {
            $data['images'] = $this->handleImageUploads($data['images']);
        }

        // Handle tags
        if (isset($data['tags']) && is_array($data['tags'])) {
            $data['tags'] = array_filter($data['tags']);
        }

        return $this->productRepository->update($id, $data);
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
     * Get products by catalog
     *
     * @param int $catalogId
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getByCatalog(int $catalogId, int $perPage = 20): LengthAwarePaginator
    {
        return $this->productRepository->getByCatalog($catalogId, $perPage);
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
}
