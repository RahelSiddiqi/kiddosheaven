<?php

namespace App\Repositories\Eloquent;

use App\Models\Product;
use App\Repositories\Contracts\ProductRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ProductRepository extends BaseRepository implements ProductRepositoryInterface
{
    /**
     * ProductRepository constructor
     */
    public function __construct(Product $model)
    {
        parent::__construct($model);
    }

    /**
     * Get products with category and brand
     */
    public function allWithRelations(): Collection
    {
        return $this->model->with(['category', 'brand'])->get();
    }

    /**
     * Find product by slug
     *
     * @param string $slug
     * @return Product|null
     */
    public function findBySlug(string $slug): ?Product
    {
        return $this->model->where('slug', $slug)->first();
    }

    /**
     * Get featured products
     *
     * @param int $limit
     * @return Collection
     */
    public function getFeaturedProducts(int $limit = 10): Collection
    {
        return $this->model
            ->where('is_featured', true)
            ->where('status', 'active')
            ->latest()
            ->limit($limit)
            ->get();
    }

    /**
     * Get active products
     *
     * @return Collection
     */
    public function getActiveProducts(): Collection
    {
        return $this->model->where('status', 'active')->get();
    }

    /**
     * Get products by category
     */
    public function getByCategory(int $categoryId, int $perPage = 20): LengthAwarePaginator
    {
        return $this->model
            ->where('category_id', $categoryId)
            ->where('status', 'active')
            ->with(['category', 'brand'])
            ->paginate($perPage);
    }

    /**
     * Get products by brand
     *
     * @param int $brandId
     * @return Collection
     */
    public function getByBrand(int $brandId): Collection
    {
        return $this->model
            ->where('brand_id', $brandId)
            ->where('status', 'active')
            ->get();
    }

    /**
     * Get low stock products
     *
     * @return Collection
     */
    public function getLowStockProducts(): Collection
    {
        return $this->model
            ->whereColumn('stock_quantity', '<=', 'low_stock_alert')
            ->orWhere(function ($query) {
                $query->whereNull('low_stock_alert')
                    ->where('stock_quantity', '<=', 10);
            })
            ->with(['category', 'brand'])
            ->get();
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
        return $this->model
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                    ->orWhere('description', 'like', "%{$query}%")
                    ->orWhere('short_description', 'like', "%{$query}%")
                    ->orWhere('sku', 'like', "%{$query}%");
            })
            ->where('status', 'active')
            ->with(['category', 'brand'])
            ->paginate($perPage);
    }

    /**
     * Update stock quantity
     *
     * @param int $productId
     * @param int $quantity
     * @param string $operation
     * @return bool
     */
    public function updateStock(int $productId, int $quantity, string $operation = 'set'): bool
    {
        $product = $this->findOrFail($productId);

        switch ($operation) {
            case 'increment':
                $product->increment('stock_quantity', $quantity);
                break;
            case 'decrement':
                $product->decrement('stock_quantity', $quantity);
                break;
            default:
                $product->update(['stock_quantity' => $quantity]);
        }

        return true;
    }

    /**
     * Get products with filters
     *
     * @param array $filters
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getWithFilters(array $filters, int $perPage = 20): LengthAwarePaginator
    {
        $query = $this->model->query();

        // Category filter
        if (!empty($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }

        // Brand filter
        if (!empty($filters['brand_id'])) {
            $query->where('brand_id', $filters['brand_id']);
        }

        // Price range filter
        if (!empty($filters['min_price'])) {
            $query->where('price', '>=', $filters['min_price']);
        }
        if (!empty($filters['max_price'])) {
            $query->where('price', '<=', $filters['max_price']);
        }

        // Search query
        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('name', 'like', "%{$filters['search']}%")
                    ->orWhere('description', 'like', "%{$filters['search']}%")
                    ->orWhere('sku', 'like', "%{$filters['search']}%");
            });
        }

        // Featured products (handle both 'featured' and 'is_featured' keys)
        $featured = $filters['featured'] ?? $filters['is_featured'] ?? null;
        if ($featured !== null && $featured !== '') {
            $query->where('is_featured', (bool) $featured);
        }

        // Stock status
        if (isset($filters['in_stock']) && $filters['in_stock'] !== null && $filters['in_stock'] !== '') {
            if ($filters['in_stock']) {
                $query->where('stock_quantity', '>', 0);
            } else {
                $query->where('stock_quantity', '<=', 0);
            }
        }

        // Sorting (handle both 'sort' shortcut and 'sort_by'/'sort_order' explicit)
        $sort = $filters['sort'] ?? 'latest';
        match ($sort) {
            'latest' => $query->orderBy('created_at', 'desc'),
            'oldest' => $query->orderBy('created_at', 'asc'),
            'price_asc' => $query->orderBy('price', 'asc'),
            'price_desc' => $query->orderBy('price', 'desc'),
            'name_asc' => $query->orderBy('name', 'asc'),
            'name_desc' => $query->orderBy('name', 'desc'),
            default => $query->orderBy(
                $filters['sort_by'] ?? 'created_at',
                $filters['sort_order'] ?? 'desc'
            ),
        };

        return $query->with(['category', 'brand', 'variants'])->paginate($perPage);
    }
}
