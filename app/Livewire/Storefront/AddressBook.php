<?php

namespace App\Livewire\Storefront;

use Livewire\Component;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('layouts.livewire')]
#[Title('My Addresses')]
class AddressBook extends Component
{
    // Form state
    public bool $showForm = false;
    public ?int $editingId = null;

    // Form fields (matching actual database columns)
    public string $name = '';
    public string $phone = '';
    public string $addressLine1 = '';
    public string $addressLine2 = '';
    public string $city = '';
    public string $district = '';
    public string $postalCode = '';
    public bool $isDefault = false;

    protected function rules(): array
    {
        return [
            'name' => 'required|string|max:150',
            'phone' => 'required|string|max:20',
            'addressLine1' => 'required|string|max:255',
            'addressLine2' => 'nullable|string|max:255',
            'city' => 'required|string|max:100',
            'district' => 'nullable|string|max:100',
            'postalCode' => 'nullable|string|max:20',
        ];
    }

    protected function getAddressModel(): string
    {
        return class_exists(\App\Domains\Customer\Models\Address::class)
            ? \App\Domains\Customer\Models\Address::class
            : \App\Models\Address::class;
    }

    #[Computed]
    public function addresses()
    {
        $ModelClass = $this->getAddressModel();

        return $ModelClass::where('user_id', auth()->id())
            ->orderByDesc('is_default')
            ->get();
    }

    public function startCreate(): void
    {
        $this->reset(['name', 'phone', 'addressLine1', 'addressLine2', 'city', 'district', 'postalCode', 'isDefault']);
        $this->editingId = null;
        $this->showForm = true;
    }

    public function startEdit(int $id): void
    {
        $ModelClass = $this->getAddressModel();
        $address = $ModelClass::where('id', $id)->where('user_id', auth()->id())->firstOrFail();

        $this->editingId = $id;
        $this->name = $address->name ?? '';
        $this->phone = $address->phone ?? '';
        $this->addressLine1 = $address->address_line1 ?? '';
        $this->addressLine2 = $address->address_line2 ?? '';
        $this->city = $address->city ?? '';
        $this->district = $address->district ?? '';
        $this->postalCode = $address->postal_code ?? '';
        $this->isDefault = $address->is_default ?? false;
        $this->showForm = true;
    }

    public function cancelForm(): void
    {
        $this->showForm = false;
        $this->editingId = null;
        $this->resetValidation();
    }

    public function save(): void
    {
        $this->validate();

        $ModelClass = $this->getAddressModel();

        $data = [
            'user_id' => auth()->id(),
            'name' => $this->name,
            'phone' => $this->phone,
            'address_line1' => $this->addressLine1,
            'address_line2' => $this->addressLine2,
            'city' => $this->city,
            'district' => $this->district,
            'postal_code' => $this->postalCode,
            'is_default' => $this->isDefault,
        ];

        if ($this->isDefault) {
            // Unset all other defaults for this user
            $ModelClass::where('user_id', auth()->id())
                ->where('id', '!=', $this->editingId)
                ->update(['is_default' => false]);
        }

        if ($this->editingId) {
            $ModelClass::where('id', $this->editingId)
                ->where('user_id', auth()->id())
                ->update($data);
            $this->dispatch('notify', message: 'Address updated.', type: 'success');
        } else {
            $ModelClass::create($data);
            $this->dispatch('notify', message: 'Address added.', type: 'success');
        }

        $this->showForm = false;
        $this->editingId = null;
        unset($this->addresses);
    }

    public function delete(int $id): void
    {
        $ModelClass = $this->getAddressModel();
        $ModelClass::where('id', $id)->where('user_id', auth()->id())->delete();

        unset($this->addresses);
        $this->dispatch('notify', message: 'Address deleted.', type: 'success');
    }

    public function setDefault(int $id): void
    {
        $ModelClass = $this->getAddressModel();

        // Unset all defaults
        $ModelClass::where('user_id', auth()->id())->update(['is_default' => false]);

        // Set the new default
        $ModelClass::where('id', $id)->where('user_id', auth()->id())->update(['is_default' => true]);

        unset($this->addresses);
        $this->dispatch('notify', message: 'Default address updated.', type: 'success');
    }

    public function render()
    {
        return view('livewire.storefront.address-book');
    }
}
