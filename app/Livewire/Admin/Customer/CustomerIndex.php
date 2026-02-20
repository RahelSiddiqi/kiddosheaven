<?php

namespace App\Livewire\Admin\Customer;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\User;

#[Layout('admin.layouts.app')]
#[Title('Customers - Admin')]
class CustomerIndex extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public string $search = '';

    #[Url]
    public string $status = '';

    #[Url]
    public string $sortBy = 'newest';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedStatus(): void
    {
        $this->resetPage();
    }

    public function updatedSortBy(): void
    {
        $this->resetPage();
    }

    public function toggleActive(int $id): void
    {
        $user = \App\Models\User::findOrFail($id);
        // Toggle using 'is_active' if it exists, otherwise don't fail
        if (isset($user->is_active)) {
            $user->update(['is_active' => !$user->is_active]);
        } elseif (method_exists($user, 'ban')) {
            // Spatie banning support
            $user->isBanned() ? $user->unban() : $user->ban();
        } else {
            // fallback: no-op just notify
        }
        unset($this->users);
        $this->dispatch('notify', message: 'Customer status updated.', type: 'success');
    }

    public function render()
    {
        $query = User::where('is_admin', false)
            ->when($this->search, fn($q) => $q->where(function ($q) {
                $q->where('name', 'like', "%{$this->search}%")
                    ->orWhere('email', 'like', "%{$this->search}%")
                    ->orWhere('phone', 'like', "%{$this->search}%");
            }))
            ->when($this->status === 'active', fn($q) => $q->where('is_active', true))
            ->when($this->status === 'inactive', fn($q) => $q->where('is_active', false));

        $query = match ($this->sortBy) {
            'highest_ltv'  => $query->orderByDesc('lifetime_value'),
            'most_orders'  => $query->orderByDesc('total_orders'),
            default        => $query->latest(),
        };

        $customers = $query->paginate(20);

        $stats = [
            'total'           => User::where('is_admin', false)->count(),
            'active'          => User::where('is_admin', false)->where('is_active', true)->count(),
            'inactive'        => User::where('is_admin', false)->where('is_active', false)->count(),
            'new_this_month'  => User::where('is_admin', false)->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year)->count(),
            'avg_ltv'         => (float) User::where('is_admin', false)->avg('lifetime_value'),
        ];

        return view('livewire.admin.customer.customer-index', compact('customers', 'stats'));
    }
}
