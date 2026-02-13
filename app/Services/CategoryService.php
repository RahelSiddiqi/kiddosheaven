<?php

namespace App\Services;

use App\Models\Category;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class CategoryService
{
    /**
     * Get hierarchical category tree for dropdowns.
     * Returns categories with indentation for visual hierarchy.
     *
     * @param bool $activeOnly
     * @return Collection
     */
    public function getHierarchicalList(bool $activeOnly = false): Collection
    {
        $query = Category::with('children')->roots();

        if ($activeOnly) {
            $query->active();
        }

        $roots = $query->get();
        $list = collect();

        foreach ($roots as $root) {
            $this->buildHierarchicalList($root, $list, 0);
        }

        return $list;
    }

    /**
     * Recursively build hierarchical list with indentation.
     */
    private function buildHierarchicalList(Category $category, Collection &$list, int $level): void
    {
        $category->indent_level = $level;
        $category->display_name = str_repeat('— ', $level) . $category->name;
        $list->push($category);

        foreach ($category->children as $child) {
            $this->buildHierarchicalList($child, $list, $level + 1);
        }
    }

    /**
     * Get category tree with nested children (for frontend rendering).
     *
     * @param bool $activeOnly
     * @return Collection
     */
    public function getTree(bool $activeOnly = false): Collection
    {
        $query = Category::with(['children' => function ($q) {
            $q->withCount('products')->with(['children' => function ($q2) {
                $q2->withCount('products')->with(['children' => function ($q3) {
                    $q3->withCount('products');
                }]);
            }]);
        }])->withCount('products')->roots();

        if ($activeOnly) {
            $query->active();
        }

        return $query->get();
    }

    /**
     * Get breadcrumb trail for a category.
     *
     * @param Category $category
     * @return Collection
     */
    public function getBreadcrumbs(Category $category): Collection
    {
        $breadcrumbs = collect([$category]);
        $ancestors = $category->ancestors();

        return $ancestors->merge($breadcrumbs);
    }

    /**
     * Move category to new parent.
     *
     * @param Category $category
     * @param int|null $newParentId
     * @return bool
     */
    public function moveCategory(Category $category, ?int $newParentId): bool
    {
        // Prevent circular reference
        if ($newParentId) {
            $newParent = Category::find($newParentId);
            if (!$newParent) {
                return false;
            }

            // Check if new parent is a descendant of current category
            if ($newParent->ancestors()->contains($category)) {
                return false;
            }
        }

        $category->parent_id = $newParentId;
        return $category->save();
    }

    /**
     * Reorder categories within same parent.
     *
     * @param array $orderedIds
     * @param int|null $parentId
     * @return bool
     */
    public function reorder(array $orderedIds, ?int $parentId = null): bool
    {
        foreach ($orderedIds as $index => $id) {
            $query = Category::where('id', $id);

            if (is_null($parentId)) {
                $query->whereNull('parent_id');
            } else {
                $query->where('parent_id', $parentId);
            }

            $query->update(['sort_order' => $index]);
        }

        return true;
    }

    /**
     * Create category with auto-generated slug.
     *
     * @param array $data
     * @return Category
     */
    public function create(array $data): Category
    {
        if (!isset($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        // Ensure unique slug
        $originalSlug = $data['slug'];
        $counter = 1;

        while (Category::where('slug', $data['slug'])->exists()) {
            $data['slug'] = $originalSlug . '-' . $counter;
            $counter++;
        }

        return Category::create($data);
    }

    /**
     * Update category.
     *
     * @param Category $category
     * @param array $data
     * @return bool
     */
    public function update(Category $category, array $data): bool
    {
        if (isset($data['name']) && (!isset($data['slug']) || $data['slug'] === $category->slug)) {
            $newSlug = Str::slug($data['name']);

            // Only update slug if name changed
            if ($newSlug !== $category->slug) {
                $originalSlug = $newSlug;
                $counter = 1;

                while (Category::where('slug', $newSlug)->where('id', '!=', $category->id)->exists()) {
                    $newSlug = $originalSlug . '-' . $counter;
                    $counter++;
                }

                $data['slug'] = $newSlug;
            }
        }

        return $category->update($data);
    }

    /**
     * Delete category (only if it has no products or children).
     *
     * @param Category $category
     * @param bool $force Delete even if has products (reassign to parent or null)
     * @return bool
     */
    public function delete(Category $category, bool $force = false): bool
    {
        // Check if has children
        if ($category->hasChildren()) {
            if (!$force) {
                return false;
            }

            // Move children to parent
            foreach ($category->children as $child) {
                $child->parent_id = $category->parent_id;
                $child->save();
            }
        }

        // Check if has products
        if ($category->products()->count() > 0) {
            if (!$force) {
                return false;
            }

            // Reassign products to parent category or set null
            $category->products()->update(['category_id' => $category->parent_id]);
        }

        return $category->delete();
    }

    /**
     * Get product count for category and its descendants.
     *
     * @param Category $category
     * @return int
     */
    public function getTotalProductCount(Category $category): int
    {
        $count = $category->products()->count();

        foreach ($category->descendants() as $descendant) {
            $count += $descendant->products()->count();
        }

        return $count;
    }
}
