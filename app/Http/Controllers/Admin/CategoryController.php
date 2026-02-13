<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Services\CategoryService;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    protected CategoryService $categoryService;

    public function __construct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }

    /**
     * Display a listing of categories.
     */
    public function index()
    {
        $categories = $this->categoryService->getTree();

        // Calculate stats
        $allCategories = Category::withCount('products')->get();
        $stats = [
            'total' => $allCategories->count(),
            'active' => $allCategories->where('is_active', true)->count(),
            'with_products' => $allCategories->where('products_count', '>', 0)->count(),
            'empty' => $allCategories->where('products_count', 0)->count(),
        ];

        return view('admin.categories.index-new', compact('categories', 'stats'));
    }

    /**
     * Store a newly created category.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:categories,id',
            'description' => 'nullable|string',
            'icon' => 'nullable|string|max:50',
            'show_on_home' => 'boolean',
            'is_active' => 'boolean',
        ]);

        $category = $this->categoryService->create($validated);

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'category' => $category]);
        }

        return redirect()->route('admin.categories.index')
            ->with('success', 'Category created successfully');
    }

    /**
     * Display the specified category.
     */
    public function show(Category $category)
    {
        $category->load(['products', 'children']);
        $breadcrumbs = $this->categoryService->getBreadcrumbs($category);
        $totalProducts = $this->categoryService->getTotalProductCount($category);

        return view('admin.categories.show', compact('category', 'breadcrumbs', 'totalProducts'));
    }

    /**
     * Show the form for editing the specified category.
     */
    public function edit(Category $category)
    {
        $categories = Category::whereNull('parent_id')
            ->orWhere('id', '!=', $category->id)
            ->with('children')
            ->orderBy('name')
            ->get();

        return view('admin.categories.edit', compact('category', 'categories'));
    }

    /**
     * Update the specified category.
     */
    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:categories,id',
            'description' => 'nullable|string',
            'icon' => 'nullable|string|max:50',
            'show_on_home' => 'boolean',
            'is_active' => 'boolean',
        ]);

        $this->categoryService->update($category, $validated);

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'category' => $category->fresh()]);
        }

        return redirect()->route('admin.categories.index')
            ->with('success', 'Category updated successfully');
    }

    /**
     * Remove the specified category.
     */
    public function destroy(Request $request, Category $category)
    {
        $deleted = $this->categoryService->delete($category, force: true);

        if ($request->expectsJson()) {
            return response()->json(['success' => $deleted]);
        }

        if ($deleted) {
            return redirect()->route('admin.categories.index')
                ->with('success', 'Category deleted successfully');
        }

        return redirect()->route('admin.categories.index')
            ->with('error', 'Cannot delete category');
    }

    /**
     * Reorder categories.
     */
    public function reorder(Request $request)
    {
        $validated = $request->validate([
            'order' => 'required|array',
            'parent_id' => 'nullable|exists:categories,id',
        ]);

        $this->categoryService->reorder($validated['order'], $validated['parent_id'] ?? null);

        return response()->json(['success' => true]);
    }
}
