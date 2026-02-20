<?php

namespace App\Livewire\Admin\Inventory;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;

#[Layout('admin.layouts.app')]
#[Title('Inventory - Admin')]
class InventoryIndex extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public string $search = '';

    #[Url]
    public string $filter = 'all'; // all | low-stock | out-of-stock

    public bool $showStockModal = false;
    public ?int $editingProductId = null;
    public string $editingProductName = '';
    public int $newStockQuantity = 0;

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedFilter(): void
    {
        $this->resetPage();
    }

    public function openStockModal(int $productId, string $name, int $currentStock): void
    {
        $this->editingProductId = $productId;
        $this->editingProductName = $name;
        $this->newStockQuantity = $currentStock;
        $this->showStockModal = true;
    }

    public function closeStockModal(): void
    {
        $this->showStockModal = false;
        $this->editingProductId = null;
        $this->editingProductName = '';
        $this->newStockQuantity = 0;
    }

    public function updateStock(): void
    {
        $this->validate(['newStockQuantity' => 'required|integer|min:0']);

        $productClass = class_exists(\App\Domains\Product\Models\Product::class)
            ? \App\Domains\Product\Models\Product::class
            : \App\Models\Product::class;

        $productClass::withoutGlobalScope('site')->findOrFail($this->editingProductId)
            ->update(['stock_quantity' => $this->newStockQuantity]);

        $this->closeStockModal();
        $this->dispatch('notify', message: 'Stock updated successfully.', type: 'success');
    }

    public function render()
    {
        $productClass = class_exists(\App\Domains\Product\Models\Product::class)
            ? \App\Domains\Product\Models\Product::class
            : \App\Models\Product::class;

        $products = $productClass::withoutGlobalScope('site')
            ->with('category')
            ->when($this->search, fn($q) => $q->where(function ($q) {
                $q->where('name', 'like', "%{$this->search}%")
                    ->orWhere('sku', 'like', "%{$this->search}%");
            }))
            ->when($this->filter === 'low-stock', fn($q) => $q->where('stock_quantity', '>', 0)->where('stock_quantity', '<=', 10))
            ->when($this->filter === 'out-of-stock', fn($q) => $q->where('stock_quantity', '<=', 0))
            ->orderBy('stock_quantity')
            ->paginate(25);

        $stats = [
            'total' => $productClass::withoutGlobalScope('site')->where('is_active', true)->count(),
            'low_stock' => $productClass::withoutGlobalScope('site')->where('stock_quantity', '>', 0)->where('stock_quantity', '<=', 10)->count(),
            'out_of_stock' => $productClass::withoutGlobalScope('site')->where('stock_quantity', '<=', 0)->count(),
            'total_value' => $productClass::withoutGlobalScope('site')->sum(DB::raw('stock_quantity * price')),
        ];

        return view('livewire.admin.inventory.inventory-index', compact('products', 'stats'));
    }
}
