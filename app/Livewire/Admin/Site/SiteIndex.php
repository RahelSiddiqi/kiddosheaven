<?php

namespace App\Livewire\Admin\Site;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Attributes\Computed;
use Livewire\WithPagination;
use App\Domains\Site\Models\Site;
use App\Domains\Site\Models\Plan;

#[Layout('admin.layouts.app')]
#[Title('All Sites - SaaS Admin')]
class SiteIndex extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public string $search = '';

    #[Url]
    public string $plan = '';

    #[Url]
    public string $status = '';

    #[Computed]
    public function sites()
    {
        return Site::withoutGlobalScopes()
            ->with(['plan:id,name,slug', 'owner:id,name,email'])
            ->when($this->search, function ($q) {
                $q->where(function ($q) {
                    $q->where('name', 'like', "%{$this->search}%")
                      ->orWhere('domain', 'like', "%{$this->search}%")
                      ->orWhere('subdomain', 'like', "%{$this->search}%")
                      ->orWhere('custom_domain', 'like', "%{$this->search}%");
                });
            })
            ->when($this->plan, fn($q) => $q->whereHas('plan', fn($q) => $q->where('slug', $this->plan)))
            ->when($this->status === 'active', fn($q) => $q->where('is_active', true))
            ->when($this->status === 'inactive', fn($q) => $q->where('is_active', false))
            ->when($this->status === 'trial', fn($q) => $q->whereNotNull('trial_ends_at')->where('trial_ends_at', '>', now()))
            ->orderByDesc('created_at')
            ->paginate(20);
    }

    #[Computed]
    public function plans()
    {
        return Plan::orderBy('sort_order')->get(['id', 'name', 'slug']);
    }

    #[Computed]
    public function stats(): array
    {
        return [
            'total' => Site::withoutGlobalScopes()->count(),
            'active' => Site::withoutGlobalScopes()->where('is_active', true)->count(),
            'on_trial' => Site::withoutGlobalScopes()->whereNotNull('trial_ends_at')->where('trial_ends_at', '>', now())->count(),
            'revenue' => Site::withoutGlobalScopes()->count(), // placeholder
        ];
    }

    public function toggleActive(int $id): void
    {
        $site = Site::withoutGlobalScopes()->findOrFail($id);
        $site->update(['is_active' => !$site->is_active]);
        unset($this->sites, $this->stats);
        $this->dispatch('notify', message: 'Site status updated.', type: 'success');
    }

    public function render()
    {
        return view('livewire.admin.site.site-index');
    }
}
