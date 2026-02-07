<?php

namespace App\Repositories;

use App\Models\Brand;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class BrandRepository extends BaseRepository
{
    public function __construct(Brand $model)
    {
        parent::__construct($model);
    }

    /**
     * Get active brands
     */
    public function getActive(): Collection
    {
        return $this->model->where('is_active', true)
            ->orderBy('name', 'asc')
            ->get();
    }

    /**
     * Find by slug
     */
    public function findBySlug(string $slug): ?Model
    {
        return $this->findBy('slug', $slug);
    }

    /**
     * Get brands with product count
     */
    public function getWithProductCount(): Collection
    {
        return $this->model->withCount('products')
            ->where('is_active', true)
            ->orderBy('name', 'asc')
            ->get();
    }

    /**
     * Toggle active status
     */
    public function toggleActive(int $id): Model
    {
        $brand = $this->find($id);

        return $this->update($id, ['is_active' => !$brand->is_active]);
    }
}
