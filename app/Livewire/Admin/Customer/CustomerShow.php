<?php

namespace App\Livewire\Admin\Customer;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Locked;
use Livewire\Component;
use App\Models\User;
use Illuminate\Support\Facades\DB;

#[Layout('admin.layouts.app')]
#[Title('Customer Detail - Admin')]
class CustomerShow extends Component
{
    #[Locked]
    public int $customerId;

    public User $customer;

    // Inline edit fields
    public bool $editing = false;
    public string $editName  = '';
    public string $editEmail = '';
    public string $editPhone = '';

    public function mount(int $customerId): void
    {
        $this->customerId = $customerId;
        $this->customer   = User::with(['orders.items', 'addresses'])->findOrFail($customerId);
    }

    public function startEdit(): void
    {
        $this->editName  = $this->customer->name;
        $this->editEmail = $this->customer->email;
        $this->editPhone = $this->customer->phone ?? '';
        $this->editing   = true;
    }

    public function cancelEdit(): void
    {
        $this->editing = false;
        $this->resetValidation();
    }

    public function saveEdit(): void
    {
        $this->validate([
            'editName'  => 'required|string|max:255',
            'editEmail' => 'required|email|max:255|unique:users,email,' . $this->customerId,
            'editPhone' => 'nullable|string|max:30',
        ]);

        $this->customer->update([
            'name'  => $this->editName,
            'email' => $this->editEmail,
            'phone' => $this->editPhone ?: null,
        ]);

        $this->customer->refresh();
        $this->editing = false;
        $this->dispatch('notify', message: 'Customer updated.', type: 'success');
    }

    public function toggleActive(): void
    {
        $this->customer->update(['is_active' => ! $this->customer->is_active]);
        $this->customer->refresh();
        $this->dispatch('notify',
            message: $this->customer->is_active ? 'Customer activated.' : 'Customer deactivated.',
            type: 'success'
        );
    }

    public function render()
    {
        return view('livewire.admin.customer.customer-show');
    }
}
