<?php

namespace App\Repositories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class ProductRepository extends BaseRepository
{
    public function __construct(Product $model)
    {
        parent::__construct($model);
    }

    /**
     * Get featured products
     */
    public function getFeatured(int $limit = 10): Collection
    {
        return $this->model->where('status', 'active')
            ->where('is_featured', true)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get products by catalog
     */
    public function getByCatalog(int $catalogId, int $limit = 20): Collection
    {
        return $this->model->where('catalog_id', $catalogId)
            ->where('status', 'active')
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get products with low stock
     */
    public function getLowStock(int $threshold = 10): Collection
    {
        return $this->model->where('stock_quantity', '<=', $threshold)
            ->orderBy('stock_quantity', 'asc')
            ->get();
    }

    /**
     * Search products
     */
    public function search(string $query, int $limit = 20): Collection
    {
        return $this->model->where('status', 'active')
            ->where(function ($q) use ($query) {
                $q->where('name', 'LIKE', "%{$query}%")
                  ->orWhere('description', 'LIKE', "%{$query}%")
                  ->orWhere('sku', 'LIKE', "%{$query}%");
            })
            ->orderBy('name', 'asc')
            ->limit($limit)
            ->get();
    }

    /**
     * Update stock quantity
     */
    public function updateStock(int $id, int $quantity): Model
    {
        return $this->update($id, ['stock_quantity' => $quantity]);
    }

    /**
     * Deduct stock (for orders)
     */
    public function deductStock(int $id, int $quantity): Model
    {
        $product = $this->find($id);
        $newQuantity = max(0, $product->stock_quantity - $quantity);

        return $this->update($id, ['stock_quantity' => $newQuantity]);
    }

    /**
     * Restock product
     */
    public function restock(int $id, int $quantity): Model
    {
        $product = $this->find($id);
        $newQuantity = $product->stock_quantity + $quantity;

        return $this->update($id, ['stock_quantity' => $newQuantity]);
    }

    /**
     * Get active products
     */
    public function getActive(int $limit = 0): Collection
    {
        $query = $this->model->where('status', 'active')
            ->orderBy('name', 'asc');

        if ($limit > 0) {
            return $query->limit($limit)->get();
        }

        return $query->get();
    }

    /**
     * Get products for home page
     */
    public function getForHome(int $limit = 8): Collection
    {
        return $this->model->where('status', 'active')
            ->with('catalog')
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get product by SKU
     */
    public function findBySku(string $sku): ?Model
    {
        return $this->findBy('sku', $sku);
    }

    /**
     * Check stock availability
     */
    public function hasStock(int $id, int $requiredQuantity = 1): bool
    {
        $product = $this->find($id);

        return $product && $product->stock_quantity >= $requiredQuantity;
    }

    /**
     * Get out of stock products
     */
    public function getOutOfStock(): Collection
    {
        return $this->model->where('stock_quantity', 0)
            ->orderBy('name', 'asc')
            ->get();
    }

    /**
     * Bulk update status
     */
    public function bulkUpdateStatus(array $ids, string $status): bool
    {
        return $this->model->whereIn('id', $ids)
            ->update(['status' => $status]) > 0;
    }
}
