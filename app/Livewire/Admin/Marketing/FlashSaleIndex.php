<?php

namespace App\Livewire\Admin\Marketing;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('admin.layouts.app')]
#[Title('Flash Sales - Admin')]
class FlashSaleIndex extends Component
{
    use WithPagination;

    public function render()
    {
        $flashSaleClass = class_exists(\App\Domains\Marketing\Models\FlashSale::class)
            ? \App\Domains\Marketing\Models\FlashSale::class
            : \App\Models\FlashSale::class;

        $query = $flashSaleClass::query();

        // Try to use withoutGlobalScope if available
        if (method_exists($flashSaleClass, 'withoutGlobalScope')) {
            $query = $flashSaleClass::withoutGlobalScope('site');
        }

        $flashSales = $query->latest()->paginate(15);

        return view('livewire.admin.marketing.flash-sale-index', compact('flashSales'));
    }
}
