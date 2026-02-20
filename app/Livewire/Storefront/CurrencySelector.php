<?php

namespace App\Livewire\Storefront;

use App\Services\CurrencyService;
use Livewire\Component;

class CurrencySelector extends Component
{
    public string $selected = 'BDT';

    public function mount(): void
    {
        $this->selected = app(CurrencyService::class)->current();
    }

    public function change(string $code): void
    {
        app(CurrencyService::class)->setCurrency($code);
        $this->selected = $code;
        $this->dispatch('currency-changed', currency: $code);
        $this->js('window.location.reload()');
    }

    public function render()
    {
        $currencies = app(CurrencyService::class)->all();
        return view('livewire.storefront.currency-selector', ['currencies' => $currencies]);
    }
}
