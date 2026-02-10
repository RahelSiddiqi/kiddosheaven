<?php

namespace App\Services\Catalog;

use App\Repositories\Contracts\CatalogRepositoryInterface;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class CatalogService
{
    protected CatalogRepositoryInterface $catalogRepository;

    public function __construct(CatalogRepositoryInterface $catalogRepository)
    {
        $this->catalogRepository = $catalogRepository;
    }

    /**
     * Get all catalogs with products
     *
     * @return Collection
     */
    public function getAllWithProducts(): Collection
    {
        return $this->catalogRepository->allWithProducts();
    }

    /**
     * Get paginated catalogs
     *
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getPaginated(int $perPage = 20): LengthAwarePaginator
    {
        return $this->catalogRepository->paginate($perPage);
    }

    /**
     * Find catalog by ID
     *
     * @param int $id
     * @return \App\Models\Catalog|null
     */
    public function findById(int $id): ?\App\Models\Catalog
    {
        return $this->catalogRepository->find($id);
    }

    /**
     * Find catalog with products
     *
     * @param int $id
     * @return \App\Models\Catalog|null
     */
    public function findWithProducts(int $id): ?\App\Models\Catalog
    {
        return $this->catalogRepository->findWithProducts($id);
    }

    /**
     * Get catalogs by type
     *
     * @param string $type
     * @return Collection
     */
    public function getByType(string $type): Collection
    {
        return $this->catalogRepository->getByType($type);
    }

    /**
     * Get home page catalogs
     *
     * @return Collection
     */
    public function getHomePageCatalogs(): Collection
    {
        return $this->catalogRepository->getHomePageCatalogs();
    }

    /**
     * Create a new catalog
     *
     * @param array $data
     * @return \App\Models\Catalog
     */
    public function create(array $data): \App\Models\Catalog
    {
        // Generate slug
        $data['slug'] = $this->generateUniqueSlug($data['name']);

        // Set default type if not provided
        if (!isset($data['type'])) {
            $data['type'] = 'category';
        }

        // Set default active status
        if (!isset($data['is_active'])) {
            $data['is_active'] = true;
        }

        // Set default display order
        if (!isset($data['display_order'])) {
            $data['display_order'] = $this->getNextDisplayOrder($data['type']);
        }

        $catalog = $this->catalogRepository->create($data);

        // Attach attributes if provided
        if (isset($data['attributes']) && is_array($data['attributes'])) {
            $this->catalogRepository->attachAttributes($catalog->id, $data['attributes']);
        }

        return $catalog->fresh(['attributes']);
    }

    /**
     * Update catalog
     *
     * @param int $id
     * @param array $data
     * @return \App\Models\Catalog
     */
    public function update(int $id, array $data): \App\Models\Catalog
    {
        // Update slug if name changed
        if (isset($data['name'])) {
            $catalog = $this->catalogRepository->findOrFail($id);
            if ($catalog->name !== $data['name']) {
                $data['slug'] = $this->generateUniqueSlug($data['name'], $id);
            }
        }

        $catalog = $this->catalogRepository->update($id, $data);

        // Sync attributes if provided
        if (isset($data['attributes']) && is_array($data['attributes'])) {
            $this->syncAttributes($catalog->id, $data['attributes']);
        }

        return $catalog->fresh(['attributes']);
    }

    /**
     * Delete catalog
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        try {
            DB::beginTransaction();

            $catalog = $this->catalogRepository->findOrFail($id);

            // Check if catalog has products
            if ($catalog->products()->count() > 0) {
                throw new \Exception('Cannot delete catalog with associated products');
            }

            // Detach all attributes
            if ($catalog->attributes()->count() > 0) {
                foreach ($catalog->attributes as $attribute) {
                    $this->catalogRepository->detachAttribute($id, $attribute->id);
                }
            }

            $result = $this->catalogRepository->delete($id);

            DB::commit();

            return $result;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Reorder catalogs
     *
     * @param array $order
     * @return bool
     */
    public function reorder(array $order): bool
    {
        return $this->catalogRepository->reorder($order);
    }

    /**
     * Attach attribute to catalog
     *
     * @param int $catalogId
     * @param int $attributeId
     * @return bool
     */
    public function attachAttribute(int $catalogId, int $attributeId): bool
    {
        return $this->catalogRepository->attachAttributes($catalogId, [$attributeId]);
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
        return $this->catalogRepository->detachAttribute($catalogId, $attributeId);
    }

    /**
     * Sync attributes
     *
     * @param int $catalogId
     * @param array $attributeIds
     * @return void
     */
    public function syncAttributes(int $catalogId, array $attributeIds): void
    {
        $catalog = $this->catalogRepository->findOrFail($catalogId);
        $currentAttributes = $catalog->attributes->pluck('id')->toArray();

        // Detach removed attributes
        $toDetach = array_diff($currentAttributes, $attributeIds);
        foreach ($toDetach as $attributeId) {
            $this->catalogRepository->detachAttribute($catalogId, $attributeId);
        }

        // Attach new attributes
        $toAttach = array_diff($attributeIds, $currentAttributes);
        if (!empty($toAttach)) {
            $this->catalogRepository->attachAttributes($catalogId, $toAttach);
        }
    }

    /**
     * Toggle active status
     *
     * @param int $id
     * @return \App\Models\Catalog
     */
    public function toggleActive(int $id): \App\Models\Catalog
    {
        $catalog = $this->catalogRepository->findOrFail($id);

        return $this->catalogRepository->update($id, [
            'is_active' => !$catalog->is_active,
        ]);
    }

    /**
     * Get active catalogs count
     *
     * @return int
     */
    public function getActiveCount(): int
    {
        return $this->catalogRepository->count(['is_active' => true]);
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
        $result = $this->catalogRepository->findBy('slug', $slug);

        if ($ignoreId && $result) {
            return $result->id !== $ignoreId;
        }

        return $result !== null;
    }

    /**
     * Get next display order
     *
     * @param string $type
     * @return int
     */
    protected function getNextDisplayOrder(string $type): int
    {
        $catalogs = $this->catalogRepository->getByType($type);

        if ($catalogs->isEmpty()) {
            return 1;
        }

        return $catalogs->max('display_order') + 1;
    }
}
