<?php

namespace App\Livewire\Storefront;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use App\Domains\Subscription\Models\Subscription;
use Illuminate\Support\Facades\Auth;

#[Layout('layouts.app')]
#[Title('My Subscription')]
class SubscriptionPortal extends Component
{
    public ?Subscription $subscription = null;
    public bool $showCancelConfirm     = false;

    public function mount(): void
    {
        $this->loadSubscription();
    }

    protected function loadSubscription(): void
    {
        $this->subscription = Subscription::where('user_id', Auth::id())
            ->whereIn('stripe_status', [
                Subscription::STATUS_ACTIVE,
                Subscription::STATUS_TRIALING,
                Subscription::STATUS_PAST_DUE,
                Subscription::STATUS_CANCELED,
            ])
            ->latest()
            ->first();
    }

    public function confirmCancel(): void
    {
        $this->showCancelConfirm = true;
    }

    public function cancelSubscription(): void
    {
        if (!$this->subscription || !$this->subscription->isActive()) {
            $this->dispatch('toast', type: 'error', message: 'No active subscription to cancel.');
            return;
        }

        $this->subscription->update([
            'stripe_status' => Subscription::STATUS_CANCELED,
            'ends_at'       => now(),
        ]);

        $this->showCancelConfirm = false;
        $this->loadSubscription();

        $this->dispatch('toast', type: 'info', message: 'Your subscription has been cancelled.');
    }

    public function render()
    {
        return view('livewire.storefront.subscription.portal');
    }
}
