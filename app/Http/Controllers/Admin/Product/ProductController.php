<?php

namespace App\Http\Controllers\Admin\Product;

use App\Http\Controllers\Controller;
use App\Services\Product\ProductService;
use App\Services\VariantGeneratorService;
use App\Http\Requests\Admin\Product\StoreProductRequest;
use App\Http\Requests\Admin\Product\UpdateProductRequest;
use App\Models\Category;
use App\Models\ProductAttribute;
use App\Models\Brand;
use App\Services\CategoryService;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    protected ProductService $productService;
    protected VariantGeneratorService $variantService;
    protected CategoryService $categoryService;

    public function __construct(
        ProductService $productService,
        VariantGeneratorService $variantService,
        CategoryService $categoryService
    ) {
        $this->productService = $productService;
        $this->variantService = $variantService;
        $this->categoryService = $categoryService;
    }

    /**
     * Display a listing of products.
     */
    public function index(Request $request)
    {
        $filters = [
            'category_id' => $request->category_id,
            'brand_id' => $request->brand_id,
            'search' => $request->search,
            'in_stock' => $request->in_stock,
            'featured' => $request->featured,
            'sort' => $request->sort ?? 'latest',
        ];

        $products = $this->productService->getFiltered($filters, 20);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'html' => view('admin.products.partials.table', compact('products'))->render(),
            ]);
        }

        $categories = $this->categoryService->getHierarchicalList();
        $brands = Brand::orderBy('name')->get();

        return view('admin.products.index', compact('products', 'categories', 'brands'));
    }

    /**
     * Show the form for creating a new product.
     */
    public function create()
    {
        $categories = $this->categoryService->getHierarchicalList();

        // Add variant count to each category
        $categories = $categories->map(function ($category) {
            $variantCount = \App\Models\ProductVariant::whereIn('product_id', $category->products()->pluck('id'))->count();
            $category->variant_count = $variantCount;
            return $category;
        });

        $brands = Brand::orderBy('name')->get();

        return view('admin.products.create', compact('categories', 'brands'));
    }

    /**
     * Store a newly created product.
     */
    public function store(StoreProductRequest $request)
    {
        try {
            $product = $this->productService->create($request->validated());

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Product created successfully',
                    'product' => $product,
                ]);
            }

            return redirect()->route('admin.products.index')
                ->with('success', 'Product created successfully');
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to create product: ' . $e->getMessage(),
                ], 500);
            }

            return back()->withInput()->with('error', 'Failed to create product: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified product.
     */
    public function show($id)
    {
        $product = $this->productService->findById($id);

        if (!$product) {
            abort(404);
        }

        $product->load([
            'variants.variantAttributes.attributeValue',
            'variants.variantAttributes.attribute',
            'category',
            'brand',
        ]);

        // Get all attributes with their values for variant generator
        $variantAttributes = \App\Models\ProductAttribute::with('values')->get()->map(function ($attr) {
            return [
                'id' => $attr->id,
                'name' => $attr->name,
                'values' => $attr->values->map(function ($val) {
                    return [
                        'id' => $val->id,
                        'value' => $val->value,
                    ];
                }),
            ];
        });

        return view('admin.products.show', compact('product', 'variantAttributes'));
    }

    /**
     * Show the form for editing the specified product.
     */
    public function edit($id)
    {
        $product = $this->productService->findById($id);

        if (!$product) {
            abort(404);
        }

        $product->load(['variants.variantAttributes']);
        $categories = $this->categoryService->getHierarchicalList();

        // Add variant count to each category
        $categories = $categories->map(function ($category) {
            $variantCount = \App\Models\ProductVariant::whereIn('product_id', $category->products()->pluck('id'))->count();
            $category->variant_count = $variantCount;
            return $category;
        });

        $brands = Brand::orderBy('name')->get();

        return view('admin.products.edit', compact('product', 'categories', 'brands'));
    }

    /**
     * Update the specified product.
     */
    public function update(UpdateProductRequest $request, $id)
    {
        try {
            $product = $this->productService->update($id, $request->validated());

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Product updated successfully',
                    'product' => $product,
                ]);
            }

            return redirect()->route('admin.products.index')
                ->with('success', 'Product updated successfully');
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to update product: ' . $e->getMessage(),
                ], 500);
            }

            return back()->withInput()->with('error', 'Failed to update product: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified product.
     */
    public function destroy($id)
    {
        try {
            $this->productService->delete($id);

            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Product deleted successfully',
                ]);
            }

            return redirect()->route('admin.products.index')
                ->with('success', 'Product deleted successfully');
        } catch (\Exception $e) {
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to delete product: ' . $e->getMessage(),
                ], 500);
            }

            return back()->with('error', 'Failed to delete product: ' . $e->getMessage());
        }
    }

    /**
     * Handle bulk actions on products.
     */
    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:delete,activate,deactivate,feature,unfeature',
            'ids' => 'required|array',
            'ids.*' => 'exists:products,id',
        ]);

        $action = $request->action;
        $ids = $request->ids;
        $count = 0;

        try {
            foreach ($ids as $id) {
                switch ($action) {
                    case 'delete':
                        $this->productService->delete($id);
                        break;
                    case 'activate':
                        $this->productService->update($id, ['is_active' => true]);
                        break;
                    case 'deactivate':
                        $this->productService->update($id, ['is_active' => false]);
                        break;
                    case 'feature':
                        $this->productService->update($id, ['is_featured' => true]);
                        break;
                    case 'unfeature':
                        $this->productService->update($id, ['is_featured' => false]);
                        break;
                }
                $count++;
            }

            return response()->json([
                'success' => true,
                'message' => "{$count} product(s) {$action}d successfully",
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Bulk action failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get variant attributes by category (for AJAX).
     */
    public function getVariantAttributes($categoryId)
    {
        try {
            $category = Category::with('parent')->findOrFail($categoryId);
            $ids = collect([$category->id])->merge($category->ancestors()->pluck('id'))->unique()->values();

            $attributes = ProductAttribute::whereHas('categories', function ($query) use ($ids) {
                $query->whereIn('categories.id', $ids);
            })
                ->where('use_for_variants', true)
                ->with('values')
                ->orderBy('sort_order')
                ->get();

            return response()->json([
                'success' => true,
                'attributes' => $attributes,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load attributes: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get variant attributes by category (for AJAX).
     */
    public function getNonVariantAttributes($categoryId)
    {
        try {
            $category = Category::with('parent')->findOrFail($categoryId);
            $ids = collect([$category->id])->merge($category->ancestors()->pluck('id'))->unique()->values();

            $attributes = ProductAttribute::whereHas('categories', function ($query) use ($ids) {
                $query->whereIn('categories.id', $ids);
            })
                ->where('use_for_variants', false)
                ->with('values')
                ->orderBy('sort_order')
                ->get();

            return response()->json([
                'success' => true,
                'attributes' => $attributes,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load attributes: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get all attributes by category (for AJAX).
     */
    public function getAttributesByCategory($categoryId)
    {
        $category = Category::with('attributes.values')->findOrFail($categoryId);

        return response()->json([
            'success' => true,
            'attributes' => $category->attributes,
        ]);
    }
}
