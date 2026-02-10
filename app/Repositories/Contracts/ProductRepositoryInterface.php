<?php

namespace App\Repositories\Contracts;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface ProductRepositoryInterface extends RepositoryInterface
{
    /**
     * Get products with catalog and brand
     *
     * @return Collection
     */
    public function allWithRelations(): Collection;

    /**
     * Find product by slug
     *
     * @param string $slug
     * @return \App\Models\Product|null
     */
    public function findBySlug(string $slug): ?\App\Models\Product;

    /**
     * Get featured products
     *
     * @param int $limit
     * @return Collection
     */
    public function getFeaturedProducts(int $limit = 10): Collection;

    /**
     * Get active products
     *
     * @return Collection
     */
    public function getActiveProducts(): Collection;

    /**
     * Get products by catalog
     *
     * @param int $catalogId
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getByCatalog(int $catalogId, int $perPage = 20): LengthAwarePaginator;

    /**
     * Get products by brand
     *
     * @param int $brandId
     * @return Collection
     */
    public function getByBrand(int $brandId): Collection;

    /**
     * Get low stock products
     *
     * @return Collection
     */
    public function getLowStockProducts(): Collection;

    /**
     * Search products
     *
     * @param string $query
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function search(string $query, int $perPage = 20): LengthAwarePaginator;

    /**
     * Update stock quantity
     *
     * @param int $productId
     * @param int $quantity
     * @param string $operation (increment|decrement|set)
     * @return bool
     */
    public function updateStock(int $productId, int $quantity, string $operation = 'set'): bool;

    /**
     * Get products with filters
     *
     * @param array $filters
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getWithFilters(array $filters, int $perPage = 20): LengthAwarePaginator;
}
