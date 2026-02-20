<?php

namespace App\Livewire\Admin\Inventory;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use App\Domains\Product\Models\Product;

#[Layout('admin.layouts.app')]
#[Title('Inventory Alerts - Admin')]
class InventoryAlerts extends Component
{
    use WithPagination;

    public function render()
    {
        $lowStockProducts = Product::withoutGlobalScope('site')
            ->with('category')
            ->where('is_active', true)
            ->where('stock_quantity', '<=', 10)
            ->orderBy('stock_quantity')
            ->paginate(25);

        $outOfStockCount = Product::withoutGlobalScope('site')
            ->where('is_active', true)
            ->where('stock_quantity', '<=', 0)
            ->count();

        $lowStockCount = Product::withoutGlobalScope('site')
            ->where('is_active', true)
            ->where('stock_quantity', '>', 0)
            ->where('stock_quantity', '<=', 10)
            ->count();

        return view('livewire.admin.inventory.inventory-alerts', compact(
            'lowStockProducts', 'outOfStockCount', 'lowStockCount'
        ));
    }
}
