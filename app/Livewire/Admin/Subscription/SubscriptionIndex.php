<?php

namespace App\Livewire\Admin\Subscription;

use App\Domains\Subscription\Models\Subscription;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('admin.layouts.app')]
#[Title('Subscriptions')]
class SubscriptionIndex extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public string $search    = '';
    public string $status    = '';

    public function updatedSearch(): void { $this->resetPage(); }
    public function updatedStatus(): void { $this->resetPage(); }

    public function cancel(int $id): void
    {
        $sub = Subscription::findOrFail($id);
        $sub->update([
            'stripe_status' => Subscription::STATUS_CANCELED,
            'ends_at'       => now(),
        ]);
        $this->dispatch('toast', type: 'success', message: 'Subscription cancelled.');
    }

    public function render()
    {
        $query = Subscription::with('user')
            ->when($this->search, fn ($q, $s) =>
                $q->whereHas('user', fn ($u) =>
                    $u->where('name', 'like', "%{$s}%")
                      ->orWhere('email', 'like', "%{$s}%")
                )
            )
            ->when($this->status, fn ($q, $s) => $q->where('stripe_status', $s))
            ->latest();

        $statusCounts = Subscription::selectRaw('stripe_status, count(*) as cnt')
            ->groupBy('stripe_status')
            ->pluck('cnt', 'stripe_status');

        return view('livewire.admin.subscription.subscription-index', [
            'subscriptions' => $query->paginate(20),
            'statusCounts'  => $statusCounts,
        ]);
    }
}
