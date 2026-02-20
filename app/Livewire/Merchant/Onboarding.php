<?php

namespace App\Livewire\Merchant;

use App\Domains\Site\Models\Plan;
use App\Domains\Site\Models\Site;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.merchant')]
#[Title('Set Up Your Store')]
class Onboarding extends Component
{
    public int $step = 1;

    // Step 1
    public string $storeName = '';
    public string $subdomain = '';
    public ?string $subdomainError = null;

    // Step 2
    public ?int $selectedPlanId = null;

    // Step 3
    public bool $agreeToTerms = false;

    public function mount(): void
    {
        // If user already has a site, redirect to merchant dashboard
        if (auth()->check() && Site::where('owner_id', auth()->id())->exists()) {
            $this->redirect(route('merchant.dashboard'), navigate: false);
        }
    }

    public function nextStep(): void
    {
        if ($this->step === 1) {
            $this->validateStep1();
        } elseif ($this->step === 2) {
            $this->validate(['selectedPlanId' => 'required|exists:plans,id']);
        }

        $this->step++;
    }

    public function prevStep(): void
    {
        $this->step = max(1, $this->step - 1);
    }

    protected function validateStep1(): void
    {
        $this->validate([
            'storeName' => 'required|min:3|max:60',
            'subdomain' => 'required|min:3|max:30|alpha_dash|unique:sites,subdomain',
        ]);

        $this->subdomain = strtolower($this->subdomain);
    }

    public function checkSubdomain(): void
    {
        $exists = Site::where('subdomain', strtolower($this->subdomain))->exists();
        $this->subdomainError = $exists ? 'This subdomain is already taken.' : null;
    }

    public function launch(): void
    {
        $this->validate([
            'storeName' => 'required|min:3|max:60',
            'subdomain' => 'required|min:3|max:30|alpha_dash|unique:sites,subdomain',
            'selectedPlanId' => 'required|exists:plans,id',
            'agreeToTerms' => 'accepted',
        ]);

        $site = Site::create([
            'name' => $this->storeName,
            'slug' => Str::slug($this->storeName),
            'subdomain' => strtolower($this->subdomain),
            'domain' => strtolower($this->subdomain) . '.' . config('app.domain', 'kiddosheaven.com'),
            'owner_id' => auth()->id(),
            'plan_id' => $this->selectedPlanId,
            'is_active' => true,
            'trial_ends_at' => now()->addDays(14),
            'currency' => 'BDT',
            'locale' => 'en',
            'timezone' => 'Asia/Dhaka',
        ]);

        session()->flash('success', 'Your store has been created! You have a 14-day free trial.');
        $this->redirect(route('merchant.dashboard'), navigate: false);
    }

    public function render()
    {
        return view('livewire.merchant.onboarding', [
            'plans' => Plan::where('is_active', true)->orderBy('sort_order')->get(),
        ]);
    }
}
