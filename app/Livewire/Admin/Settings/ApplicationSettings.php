<?php

namespace App\Livewire\Admin\Settings;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use App\Models\Setting;
use Illuminate\Support\Facades\DB;

#[Layout('admin.layouts.app')]
#[Title('Settings - Admin')]
class ApplicationSettings extends Component
{
    public array $settings = [];

    public function mount(): void
    {
        // Load settings from DB
        $this->settings = DB::table('settings')->pluck('value', 'key')->toArray();
    }

    public function save(): void
    {
        // Save each setting
        foreach ($this->settings as $key => $value) {
            DB::table('settings')->updateOrInsert(
                ['key' => $key],
                ['value' => $value, 'updated_at' => now()]
            );
        }

        // Clear related caches
        \Illuminate\Support\Facades\Cache::forget('tax_rate');
        \Illuminate\Support\Facades\Cache::forget('site_settings');

        $this->dispatch('notify', message: 'Settings saved successfully.', type: 'success');
    }

    public function render()
    {
        return view('livewire.admin.settings.application-settings');
    }
}
