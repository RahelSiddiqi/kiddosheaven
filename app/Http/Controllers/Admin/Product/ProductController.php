<?php

namespace App\Http\Controllers\Admin\Product;

use App\Http\Controllers\Controller;
use App\Services\Product\ProductService;
use App\Http\Requests\Admin\Product\StoreProductRequest;
use App\Http\Requests\Admin\Product\UpdateProductRequest;
use App\Models\Catalog;
use App\Models\Brand;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    protected ProductService $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    /**
     * Display a listing of products.
     */
    public function index(Request $request)
    {
        $filters = [
            'catalog_id' => $request->catalog_id,
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

        $catalogs = Catalog::orderBy('name')->get();
        $brands = Brand::orderBy('name')->get();

        return view('admin.products.index', compact('products', 'catalogs', 'brands'));
    }

    /**
     * Show the form for creating a new product.
     */
    public function create()
    {
        $catalogs = Catalog::orderBy('name')->get();
        $brands = Brand::orderBy('name')->get();

        return view('admin.products.create', compact('catalogs', 'brands'));
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

        return view('admin.products.show', compact('product'));
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

        $catalogs = Catalog::orderBy('name')->get();
        $brands = Brand::orderBy('name')->get();

        return view('admin.products.edit', compact('product', 'catalogs', 'brands'));
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
     * Get attributes by catalog (for AJAX).
     */
    public function getAttributesByCatalog($catalogId)
    {
        $catalog = Catalog::with('attributes.values')->findOrFail($catalogId);

        return response()->json([
            'success' => true,
            'attributes' => $catalog->attributes,
        ]);
    }
}
