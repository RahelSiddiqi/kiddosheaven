<?php

namespace App\Livewire\Admin\Loyalty;

use App\Models\LoyaltyProgram;
use App\Models\LoyaltyTransaction;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('admin.layouts.app')]
#[Title('Loyalty Program - Admin')]
class LoyaltyManagement extends Component
{
    use WithPagination;

    // Program settings
    public float $pointsPerCurrency = 1.0;
    public int $minimumPoints = 100;
    public float $discountPercentage = 5.0;
    public bool $isActive = false;
    public bool $showSettings = false;

    // Points adjustment modal
    public bool   $showAdjustModal = false;
    public string $adjustUserId    = '';
    public int    $adjustPoints    = 0;
    public string $adjustType      = 'add'; // add | deduct
    public string $adjustReason    = '';

    // Search for users in adjustment modal
    public string $userSearch = '';

    public function mount(): void
    {
        $program = LoyaltyProgram::first();
        if ($program) {
            $this->pointsPerCurrency = $program->points_per_currency ?? 1.0;
            $this->minimumPoints = $program->minimum_points ?? 100;
            $this->discountPercentage = $program->discount_percentage ?? 5.0;
            $this->isActive = (bool) ($program->is_active ?? false);
        }
    }

    public function saveSettings(): void
    {
        $this->validate([
            'pointsPerCurrency' => 'required|numeric|min:0.01',
            'minimumPoints' => 'required|integer|min:1',
            'discountPercentage' => 'required|numeric|min:0.1|max:100',
        ]);

        LoyaltyProgram::updateOrCreate(
            ['id' => 1],
            [
                'name' => 'Default Program',
                'points_per_currency' => $this->pointsPerCurrency,
                'minimum_points' => $this->minimumPoints,
                'discount_percentage' => $this->discountPercentage,
                'is_active' => $this->isActive,
            ]
        );

        unset($this->stats);
        $this->showSettings = false;
        $this->dispatch('notify', message: 'Loyalty settings saved.', type: 'success');
    }

    #[Computed]
    public function stats(): array
    {
        return [
            'total_earned' => LoyaltyTransaction::where('type', 'earned')->sum('points'),
            'total_redeemed' => LoyaltyTransaction::where('type', 'redeemed')->sum('points'),
            'participants' => LoyaltyTransaction::distinct('user_id')->count('user_id'),
            'active' => (bool) (optional(LoyaltyProgram::first())->is_active ?? false),
        ];
    }

    #[Computed]
    public function recentTransactions()
    {
        return LoyaltyTransaction::with('user:id,name,email')
            ->latest()
            ->paginate(15);
    }

    #[Computed]
    public function searchableUsers()
    {
        return \App\Models\User::when($this->userSearch, fn($q) => $q->where('name', 'like', "%{$this->userSearch}%")->orWhere('email', 'like', "%{$this->userSearch}%"))
            ->limit(20)
            ->get(['id', 'name', 'email', 'loyalty_points']);
    }

    public function openAdjustModal(): void
    {
        $this->reset(['adjustUserId', 'adjustPoints', 'adjustReason', 'userSearch']);
        $this->adjustType      = 'add';
        $this->showAdjustModal = true;
        $this->resetValidation();
    }

    public function adjustPoints(): void
    {
        $this->validate([
            'adjustUserId' => 'required|exists:users,id',
            'adjustPoints' => 'required|integer|min:1',
            'adjustType'   => 'required|in:add,deduct',
            'adjustReason' => 'required|string|max:255',
        ]);

        $user = \App\Models\User::findOrFail((int) $this->adjustUserId);

        if ($this->adjustType === 'add') {
            $user->increment('loyalty_points', $this->adjustPoints);
        } else {
            if ($user->loyalty_points < $this->adjustPoints) {
                $this->addError('adjustPoints', 'User only has ' . $user->loyalty_points . ' points.');
                return;
            }
            $user->decrement('loyalty_points', $this->adjustPoints);
        }

        LoyaltyTransaction::create([
            'user_id'     => $user->id,
            'type'        => 'adjustment',
            'points'      => $this->adjustType === 'add' ? $this->adjustPoints : -$this->adjustPoints,
            'balance'     => $user->loyalty_points,
            'description' => $this->adjustReason,
            'order_id'    => null,
        ]);

        $this->showAdjustModal = false;
        unset($this->stats, $this->recentTransactions);
        $this->dispatch('notify', message: 'Points adjusted successfully.', type: 'success');
    }

    public function render()
    {
        return view('livewire.admin.loyalty.loyalty-management');
    }
}
