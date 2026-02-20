<?php

namespace App\Livewire\Admin\Product;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use App\Domains\Product\Models\Product;

#[Layout('admin.layouts.app')]
#[Title('Products - Admin')]
class ProductIndex extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public string $search = '';

    #[Url]
    public string $status = '';

    #[Url]
    public string $sort = 'newest';

    public array $selectedIds = [];
    public bool $selectAll = false;

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedStatus(): void
    {
        $this->resetPage();
    }

    public function updatedSort(): void
    {
        $this->resetPage();
    }

    public function toggleSelectAll(): void
    {
        if ($this->selectAll) {
            $this->selectedIds = $this->getProductsQuery()->pluck('id')->map(fn($id) => (string)$id)->all();
        } else {
            $this->selectedIds = [];
        }
    }

    private function getProductsQuery()
    {
        return Product::withoutGlobalScope('site')
            ->with(['category', 'brand'])
            ->when($this->search, fn($q) => $q->where(function($q) {
                $q->where('name', 'like', "%{$this->search}%")
                  ->orWhere('sku', 'like', "%{$this->search}%");
            }))
            ->when($this->status === 'active', fn($q) => $q->where('is_active', true))
            ->when($this->status === 'inactive', fn($q) => $q->where('is_active', false))
            ->when($this->status === 'featured', fn($q) => $q->where('is_featured', true))
            ->when($this->status === 'low-stock', fn($q) => $q->where('stock_quantity', '<=', 10)->where('is_active', true))
            ->when($this->sort === 'newest', fn($q) => $q->latest())
            ->when($this->sort === 'oldest', fn($q) => $q->oldest())
            ->when($this->sort === 'name', fn($q) => $q->orderBy('name'))
            ->when($this->sort === 'stock-low', fn($q) => $q->orderBy('stock_quantity'))
            ->when($this->sort === 'stock-high', fn($q) => $q->orderByDesc('stock_quantity'));
    }

    public function render()
    {
        $products = $this->getProductsQuery()->paginate(20);
        $totalProducts = Product::withoutGlobalScope('site')->count();
        $activeProducts = Product::withoutGlobalScope('site')->where('is_active', true)->count();
        $lowStockProducts = Product::withoutGlobalScope('site')->where('stock_quantity', '<=', 10)->where('is_active', true)->count();

        return view('livewire.admin.product.product-index', compact(
            'products', 'totalProducts', 'activeProducts', 'lowStockProducts'
        ));
    }
}
