<?php

namespace App\Domains\Catalog\Services;

use App\Domains\Catalog\Models\Collection;
use App\Domains\Product\Models\Product;
use Illuminate\Support\Collection as LaravelCollection;
use Illuminate\Support\Str;

class CollectionService
{
    /**
     * Create a new collection.
     */
    public function create(array $data): Collection
    {
        $data['slug'] = $data['slug'] ?? Str::slug($data['name']);

        $collection = Collection::create($data);

        if ($collection->isAutomatic()) {
            $this->syncAutomaticProducts($collection);
        }

        return $collection;
    }

    /**
     * Update an existing collection.
     */
    public function update(Collection $collection, array $data): Collection
    {
        if (isset($data['name']) && !isset($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        $collection->update($data);

        if ($collection->isAutomatic()) {
            $this->syncAutomaticProducts($collection);
        }

        return $collection->fresh();
    }

    /**
     * Sync products for an automatic collection based on its conditions.
     */
    public function syncAutomaticProducts(Collection $collection): void
    {
        if (!$collection->isAutomatic() || empty($collection->conditions)) {
            return;
        }

        $query = Product::query()->where('site_id', $collection->site_id)->active();

        foreach ($collection->conditions as $condition) {
            $method = $collection->condition_match === 'any' ? 'orWhere' : 'where';

            match ($condition['field'] ?? '') {
                'title'     => $query->{$method}('name', $this->mapOperator($condition['operator']), $condition['value']),
                'price'     => $query->{$method.'Raw'}('price ' . $this->mapOperator($condition['operator'], true) . ' ?', [$condition['value']]),
                'tag'       => $query->{$method . 'Has'}('tags', fn($q) => $q->where('name', $condition['value'])),
                'category'  => $query->{$method}('category_id', $condition['value']),
                'vendor'    => $query->{$method}('brand', $condition['value']),
                'stock'     => match($condition['operator']) {
                    'in_stock'     => $query->{$method . 'Raw'}('stock_quantity > 0'),
                    'out_of_stock' => $query->{$method . 'Raw'}('stock_quantity = 0'),
                    default        => null,
                },
                default => null,
            };
        }

        $productIds = $query->pluck('id')->toArray();

        // Sync maintaining existing positions
        $existing = $collection->products()->pluck('position', 'product_id')->toArray();

        $syncData = [];
        $position = max($existing ? max($existing) + 1 : 0, count($existing));

        foreach ($productIds as $id) {
            $syncData[$id] = ['position' => $existing[$id] ?? $position++];
        }

        $collection->products()->sync($syncData);
    }

    /**
     * Add a product to a manual collection.
     */
    public function addProduct(Collection $collection, int $productId): void
    {
        if ($collection->products()->where('product_id', $productId)->exists()) {
            return;
        }

        $maxPosition = $collection->products()->max('collection_products.position') ?? -1;

        $collection->products()->attach($productId, ['position' => $maxPosition + 1]);
    }

    /**
     * Remove a product from a manual collection.
     */
    public function removeProduct(Collection $collection, int $productId): void
    {
        $collection->products()->detach($productId);
    }

    /**
     * Reorder products in a collection.
     *
     * @param  array  $order  [product_id => position, ...]
     */
    public function reorderProducts(Collection $collection, array $order): void
    {
        foreach ($order as $productId => $position) {
            $collection->products()->updateExistingPivot($productId, ['position' => $position]);
        }
    }

    // ---------------------------------------------------------------
    // Private helpers
    // ---------------------------------------------------------------

    private function mapOperator(string $operator, bool $numeric = false): string
    {
        return match($operator) {
            'equals', 'is'              => $numeric ? '='  : '=',
            'not_equals', 'is_not'      => $numeric ? '!=' : '!=',
            'contains'                  => 'LIKE',
            'starts_with'               => 'LIKE',
            'ends_with'                 => 'LIKE',
            'greater_than'              => '>',
            'less_than'                 => '<',
            'greater_than_or_equal'     => '>=',
            'less_than_or_equal'        => '<=',
            default                     => '=',
        };
    }
}
