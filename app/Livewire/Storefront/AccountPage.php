<?php

namespace App\Livewire\Storefront;

use Livewire\Component;
use App\Domains\Order\Models\Order;
use Illuminate\Support\Facades\Auth;

class AccountPage extends Component
{
    public $name = '';
    public $phone = '';

    public function mount()
    {
        $user = Auth::user();
        $this->name = $user->name;
        $this->phone = $user->phone ?? '';
    }

    public function updateProfile()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
        ]);

        Auth::user()->update([
            'name' => $this->name,
            'phone' => $this->phone,
        ]);

        session()->flash('success', 'Profile updated successfully.');
    }

    public function render()
    {
        $user = Auth::user();
        $recentOrders = Order::where('user_id', $user->id)
            ->latest()
            ->take(5)
            ->get();

        return view('livewire.storefront.account-page', compact('user', 'recentOrders'));
    }
}
