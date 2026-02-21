<?php

namespace App\Livewire\Admin\Marketing;

use App\Domains\Marketing\Models\AbandonedCart;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('admin.layouts.app')]
#[Title('Abandoned Carts')]
class AbandonedCartIndex extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public string $search = '';

    #[Url]
    public string $status = 'active';

    public function updatedSearch(): void { $this->resetPage(); }
    public function updatedStatus(): void  { $this->resetPage(); }

    public function sendReminder(int $id, int $number): void
    {
        $cart = AbandonedCart::findOrFail($id);

        if (! $cart->email) {
            $this->dispatch('toast', type: 'error', message: 'No email address for this cart.');
            return;
        }

        \Illuminate\Support\Facades\Mail::to($cart->email)
            ->queue(new \App\Mail\AbandonedCartReminder($cart, $number));

        $field = "reminder_{$number}_sent_at";
        $cart->update([$field => now()]);

        $this->dispatch('toast', type: 'success', message: "Reminder #{$number} queued.");
    }

    public function markExpired(int $id): void
    {
        AbandonedCart::findOrFail($id)->update(['status' => 'expired']);
    }

    public function render()
    {
        $carts = AbandonedCart::query()
            ->when($this->search, fn($q) => $q
                ->where('email', 'like', "%{$this->search}%")
                ->orWhere('token', 'like', "%{$this->search}%"))
            ->when($this->status, fn($q) => $q->where('status', $this->status))
            ->with('user')
            ->orderByDesc('created_at')
            ->paginate(25);

        $stats = [
            'active'    => AbandonedCart::where('status', 'active')->count(),
            'recovered' => AbandonedCart::where('status', 'recovered')->count(),
            'revenue'   => AbandonedCart::where('status', 'recovered')->sum('subtotal'),
        ];

        return view('livewire.admin.marketing.abandoned-cart-index', compact('carts', 'stats'));
    }
}
