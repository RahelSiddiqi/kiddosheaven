<?php

namespace App\Repositories\Eloquent;

use App\Models\Catalog;
use App\Repositories\Contracts\CatalogRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class CatalogRepository extends BaseRepository implements CatalogRepositoryInterface
{
    /**
     * CatalogRepository constructor
     */
    public function __construct(Catalog $model)
    {
        parent::__construct($model);
    }

    /**
     * Get catalogs with products
     *
     * @return Collection
     */
    public function allWithProducts(): Collection
    {
        return $this->model->with('products')->get();
    }

    /**
     * Get catalogs with type
     *
     * @return Collection
     */
    public function allWithType(): Collection
    {
        return $this->model->with('type')->get();
    }

    /**
     * Get catalogs shown on home
     *
     * @return Collection
     */
    public function getHomePageCatalogs(): Collection
    {
        return $this->model
            ->where('show_on_home', true)
            ->with(['products' => function ($query) {
                $query->where('status', 'active')->limit(8);
            }])
            ->get();
    }

    /**
     * Get catalog with products
     *
     * @param int $id
     * @return Catalog|null
     */
    public function findWithProducts(int $id): ?Catalog
    {
        return $this->model->with('products')->find($id);
    }

    /**
     * Reorder catalogs
     *
     * @param array $order
     * @return bool
     */
    public function reorder(array $order): bool
    {
        $this->beginTransaction();

        try {
            foreach ($order as $index => $id) {
                $this->model->where('id', $id)->update(['order' => $index]);
            }

            $this->commit();
            return true;
        } catch (\Exception $e) {
            $this->rollback();
            return false;
        }
    }

    /**
     * Get catalog by type
     *
     * @param string $type
     * @return Collection
     */
    public function getByType(string $type): Collection
    {
        return $this->model->where('type', $type)->get();
    }

    /**
     * Attach attributes to catalog
     *
     * @param int $catalogId
     * @param array $attributeIds
     * @return bool
     */
    public function attachAttributes(int $catalogId, array $attributeIds): bool
    {
        $catalog = $this->findOrFail($catalogId);
        $catalog->attributes()->sync($attributeIds);
        return true;
    }

    /**
     * Detach attribute from catalog
     *
     * @param int $catalogId
     * @param int $attributeId
     * @return bool
     */
    public function detachAttribute(int $catalogId, int $attributeId): bool
    {
        $catalog = $this->findOrFail($catalogId);
        $catalog->attributes()->detach($attributeId);
        return true;
    }
}
