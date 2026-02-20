<?php

namespace App\Livewire\Admin\Marketing;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('admin.layouts.app')]
#[Title('Coupons - Admin')]
class CouponIndex extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public string $search = '';

    #[Url]
    public string $status = '';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedStatus(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $couponClass = class_exists(\App\Domains\Marketing\Models\Coupon::class)
            ? \App\Domains\Marketing\Models\Coupon::class
            : \App\Models\Coupon::class;

        $query = $couponClass::query();

        // Try to use withoutGlobalScope if available
        if (method_exists($couponClass, 'withoutGlobalScope')) {
            $query = $couponClass::withoutGlobalScope('site');
        }

        $coupons = $query
            ->when($this->search, fn($q) => $q->where(function ($q) {
                $q->where('code', 'like', "%{$this->search}%")
                    ->orWhere('description', 'like', "%{$this->search}%");
            }))
            ->when($this->status === 'active', fn($q) => $q->where('is_active', true))
            ->when($this->status === 'inactive', fn($q) => $q->where('is_active', false))
            ->latest()
            ->paginate(20);

        return view('livewire.admin.marketing.coupon-index', compact('coupons'));
    }
}
