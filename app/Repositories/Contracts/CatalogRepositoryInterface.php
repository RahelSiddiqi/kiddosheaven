<?php

namespace App\Repositories\Contracts;

use Illuminate\Database\Eloquent\Collection;

interface CatalogRepositoryInterface extends RepositoryInterface
{
    /**
     * Get catalogs with products
     *
     * @return Collection
     */
    public function allWithProducts(): Collection;

    /**
     * Get catalogs with type
     *
     * @return Collection
     */
    public function allWithType(): Collection;

    /**
     * Get catalogs shown on home
     *
     * @return Collection
     */
    public function getHomePageCatalogs(): Collection;

    /**
     * Get catalog with products
     *
     * @param int $id
     * @return \App\Models\Catalog|null
     */
    public function findWithProducts(int $id): ?\App\Models\Catalog;

    /**
     * Reorder catalogs
     *
     * @param array $order
     * @return bool
     */
    public function reorder(array $order): bool;

    /**
     * Get catalog by type
     *
     * @param string $type
     * @return Collection
     */
    public function getByType(string $type): Collection;

    /**
     * Attach attributes to catalog
     *
     * @param int $catalogId
     * @param array $attributeIds
     * @return bool
     */
    public function attachAttributes(int $catalogId, array $attributeIds): bool;

    /**
     * Detach attribute from catalog
     *
     * @param int $catalogId
     * @param int $attributeId
     * @return bool
     */
    public function detachAttribute(int $catalogId, int $attributeId): bool;
}
