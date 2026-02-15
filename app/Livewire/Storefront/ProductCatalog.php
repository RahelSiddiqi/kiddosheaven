<?php

namespace App\Livewire\Storefront;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Url;
use App\Domains\Product\Models\Product;
use App\Domains\Catalog\Models\Category;
use App\Domains\Catalog\Models\Brand;

class ProductCatalog extends Component
{
    use WithPagination;

    #[Url(as: 'category')]
    public $categoryId = null;

    #[Url(as: 'brand')]
    public $brandId = null;

    #[Url(as: 'price')]
    public $priceRange = null;

    #[Url(as: 'featured')]
    public $featured = false;

    #[Url(as: 'new')]
    public $newArrivals = false;

    #[Url(as: 'sale')]
    public $onSale = false;

    #[Url(as: 'sort')]
    public $sortBy = 'newest';

    #[Url(as: 'q')]
    public $searchQuery = '';

    public $isFilterDrawerOpen = false;
    public $perPage = 12;

    public function updatingCategoryId()
    {
        $this->resetPage();
    }

    public function updatingBrandId()
    {
        $this->resetPage();
    }

    public function updatingPriceRange()
    {
        $this->resetPage();
    }

    public function updatingSortBy()
    {
        $this->resetPage();
    }

    public function setCategory($categoryId)
    {
        $this->categoryId = $categoryId;
    }

    public function setBrand($brandId)
    {
        $this->brandId = $brandId;
    }

    public function setPriceRange($range)
    {
        $this->priceRange = $range;
    }

    public function setSort($sort)
    {
        $this->sortBy = $sort;
    }

    public function toggleFilterDrawer()
    {
        $this->isFilterDrawerOpen = !$this->isFilterDrawerOpen;
    }

    public function clearFilters()
    {
        $this->reset([
            'categoryId',
            'brandId',
            'priceRange',
            'featured',
            'newArrivals',
            'onSale',
            'searchQuery'
        ]);
    }

    public function addToCart($productSlug)
    {
        $product = Product::where('slug', $productSlug)->first();

        if (!$product) {
            session()->flash('error', 'Product not found');
            return;
        }

        $cart = session('cart', ['items' => []]);
        $key = $product->id;

        if (isset($cart['items'][$key])) {
            $cart['items'][$key]['quantity']++;
        } else {
            $cart['items'][$key] = [
                'product_id' => $product->id,
                'variant_id' => null,
                'name' => $product->name,
                'slug' => $product->slug,
                'price' => $product->price,
                'image' => $product->primary_image ?? ($product->images[0] ?? null),
                'quantity' => 1,
            ];
        }

        session(['cart' => $cart]);
        $this->dispatch('cart-updated');
        session()->flash('cart-success', 'Added to cart!');
    }

    public function getProductsProperty()
    {
        $query = Product::query()->where('is_active', true);

        // Category filter
        if ($this->categoryId) {
            $category = Category::find($this->categoryId);
            if ($category) {
                if ($category->parent_id === null) {
                    $childIds = $category->children->pluck('id')->toArray();
                    $categoryIds = array_merge([$category->id], $childIds);
                    $query->whereIn('category_id', $categoryIds);
                } else {
                    $query->where('category_id', $this->categoryId);
                }
            }
        }

        // Brand filter
        if ($this->brandId) {
            $query->where('brand_id', $this->brandId);
        }

        // Price range filter
        if ($this->priceRange) {
            switch ($this->priceRange) {
                case 'under-10':
                    $query->where('price', '<', 1000);
                    break;
                case '10-25':
                    $query->whereBetween('price', [1000, 2500]);
                    break;
                case '25-50':
                    $query->whereBetween('price', [2500, 5000]);
                    break;
                case 'over-50':
                    $query->where('price', '>', 5000);
                    break;
            }
        }

        // Featured filter
        if ($this->featured) {
            $query->where('is_featured', true);
        }

        // New arrivals filter
        if ($this->newArrivals) {
            $query->where('created_at', '>=', now()->subDays(14));
        }

        // On sale filter
        if ($this->onSale) {
            $query->whereNotNull('discount_price')->where('discount_price', '>', 0);
        }

        // Search query
        if ($this->searchQuery) {
            $query->where(function($q) {
                $q->where('name', 'like', '%' . $this->searchQuery . '%')
                  ->orWhere('description', 'like', '%' . $this->searchQuery . '%')
                  ->orWhere('sku', 'like', '%' . $this->searchQuery . '%');
            });
        }

        // Sorting
        switch ($this->sortBy) {
            case 'price-low':
                $query->orderBy('price', 'asc');
                break;
            case 'price-high':
                $query->orderBy('price', 'desc');
                break;
            case 'popular':
                $query->withCount('orderItems')->orderBy('order_items_count', 'desc');
                break;
            case 'newest':
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }

        return $query->paginate($this->perPage);
    }

    public function render()
    {
        $products = $this->products;

        $categories = cache()->remember('catalog-categories', 3600, function() {
            return Category::where('is_active', true)
                ->whereNull('parent_id')
                ->with(['children' => function($query) {
                    $query->where('is_active', true)->orderBy('sort_order');
                }])
                ->orderBy('sort_order')
                ->get();
        });

        $brands = cache()->remember('catalog-brands', 3600, function() {
            return Brand::where('is_active', true)
                ->orderBy('name')
                ->get();
        });

        return view('livewire.storefront.product-catalog', [
            'products' => $products,
            'categories' => $categories,
            'brands' => $brands,
        ]);
    }
}
