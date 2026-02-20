<?php

namespace App\Livewire\Merchant;

use App\Domains\Site\Models\ApiKey;
use App\Domains\Site\Models\Site;
use App\Domains\Site\Models\WebhookSubscription;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Illuminate\Support\Str;

#[Layout('layouts.merchant')]
#[Title('My Store - Merchant Dashboard')]
class Dashboard extends Component
{
    // API Key creation
    public string $newKeyName = '';
    public array $newKeyScopes = [];
    public bool $showCreateKeyForm = false;
    public ?string $newKeyPlaintext = null;

    // Webhook creation
    public string $webhookUrl = '';
    public array $webhookEvents = [];
    public bool $showCreateWebhookForm = false;

    // Custom Domain
    public string $customDomain = '';

    public array $availableScopes = ['products', 'orders', 'webhooks', '*'];
    public array $availableEvents = [
        'order.placed', 'order.completed', 'order.cancelled',
        'product.updated', 'inventory.low',
    ];

    public function mount(): void
    {
        if (!auth()->check()) {
            $this->redirect(route('login'), navigate: false);
            return;
        }

        $site = Site::where('owner_id', auth()->id())->first();

        if (!$site) {
            $this->redirect(route('merchant.onboard'), navigate: false);
            return;
        }

        // Load custom domain into property
        $this->customDomain = $site->custom_domain ?? '';
    }

    public function createApiKey(): void
    {
        $this->validate([
            'newKeyName' => 'required|string|max:100',
            'newKeyScopes' => 'array',
        ]);

        $site = Site::where('owner_id', auth()->id())->firstOrFail();

        $result = ApiKey::generate($site->id, $this->newKeyName, $this->newKeyScopes ?: ['products']);

        $this->newKeyPlaintext = $result['api_key'];
        $this->newKeyName = '';
        $this->newKeyScopes = [];
        $this->showCreateKeyForm = false;
        $this->dispatch('notify', message: 'API key created. Copy it now — it will not be shown again.', type: 'success');
    }

    public function revokeApiKey(int $id): void
    {
        $site = Site::where('owner_id', auth()->id())->firstOrFail();
        ApiKey::where('id', $id)->where('site_id', $site->id)->update(['is_active' => false]);
        $this->dispatch('notify', message: 'API key revoked.', type: 'success');
    }

    public function deleteApiKey(int $id): void
    {
        $site = Site::where('owner_id', auth()->id())->firstOrFail();
        ApiKey::where('id', $id)->where('site_id', $site->id)->delete();
        $this->dispatch('notify', message: 'API key deleted.', type: 'success');
    }

    public function dismissNewKey(): void
    {
        $this->newKeyPlaintext = null;
    }

    public function createWebhook(): void
    {
        $this->validate([
            'webhookUrl' => 'required|url|max:500',
            'webhookEvents' => 'required|array|min:1',
        ]);

        $site = Site::where('owner_id', auth()->id())->firstOrFail();

        WebhookSubscription::create([
            'site_id' => $site->id,
            'url' => $this->webhookUrl,
            'events' => $this->webhookEvents,
            'secret' => Str::random(40),
            'is_active' => true,
        ]);

        $this->webhookUrl = '';
        $this->webhookEvents = [];
        $this->showCreateWebhookForm = false;
        $this->dispatch('notify', message: 'Webhook created.', type: 'success');
    }

    public function deleteWebhook(int $id): void
    {
        $site = Site::where('owner_id', auth()->id())->firstOrFail();
        WebhookSubscription::where('id', $id)->where('site_id', $site->id)->delete();
        $this->dispatch('notify', message: 'Webhook deleted.', type: 'success');
    }

    public function toggleWebhook(int $id): void
    {
        $site = Site::where('owner_id', auth()->id())->firstOrFail();
        $webhook = WebhookSubscription::where('id', $id)->where('site_id', $site->id)->firstOrFail();
        $webhook->update(['is_active' => !$webhook->is_active]);
        $this->dispatch('notify', message: 'Webhook status updated.', type: 'success');
    }

    public function saveCustomDomain(): void
    {
        $this->validate([
            'customDomain' => ['nullable', 'string', 'max:255', 'regex:/^([a-z0-9-]+\.)+[a-z]{2,}$/i'],
        ], [
            'customDomain.regex' => 'Please enter a valid domain format (e.g., shop.example.com).',
        ]);

        $site = Site::where('owner_id', auth()->id())->firstOrFail();

        // If domain is empty, clear out custom domain fields
        if (empty($this->customDomain)) {
            $site->update([
                'custom_domain' => null,
                'domain_verified_at' => null,
                'domain_txt_record' => null,
            ]);
            $this->dispatch('notify', message: 'Custom domain removed.', type: 'success');
            return;
        }

        // Check if domain changed before resetting verification
        $domainChanged = $site->custom_domain !== $this->customDomain;

        $updateData = ['custom_domain' => $this->customDomain];

        // Reset verification if domain changed
        if ($domainChanged) {
            $updateData['domain_verified_at'] = null;
        }

        // Generate TXT record token if not already set or domain changed
        if (!$site->domain_txt_record || $domainChanged) {
            $updateData['domain_txt_record'] = 'kh-verify-' . Str::random(32);
        }

        $site->update($updateData);

        $this->dispatch('notify', message: 'Custom domain saved. Please verify DNS ownership.', type: 'success');
    }

    public function verifyDomain(): void
    {
        $site = Site::where('owner_id', auth()->id())->firstOrFail();

        if (!$site->custom_domain || !$site->domain_txt_record) {
            $this->dispatch('notify', message: 'Please save a custom domain first.', type: 'error');
            return;
        }

        try {
            $txtRecords = dns_get_record($site->custom_domain, DNS_TXT);

            if ($txtRecords === false) {
                $this->dispatch('notify', message: 'Unable to fetch DNS records. Please try again later.', type: 'error');
                return;
            }

            $verified = false;
            foreach ($txtRecords as $record) {
                if (isset($record['txt']) && $record['txt'] === $site->domain_txt_record) {
                    $verified = true;
                    break;
                }
            }

            if ($verified) {
                $site->update(['domain_verified_at' => now()]);
                $this->dispatch('notify', message: 'Domain verified successfully! Your custom domain is now active.', type: 'success');
            } else {
                $this->dispatch('notify', message: 'TXT record not found. Please ensure you added the correct DNS record and allow time for propagation.', type: 'error');
            }
        } catch (\Exception $e) {
            $this->dispatch('notify', message: 'DNS lookup failed. Please check the domain and try again.', type: 'error');
        }
    }

    public function render()
    {
        $site = Site::where('owner_id', auth()->id())->first();
        $plan = $site?->plan;
        $apiKeys = $site ? ApiKey::where('site_id', $site->id)->latest()->get() : collect();
        $webhooks = $site ? WebhookSubscription::where('site_id', $site->id)->latest()->get() : collect();

        return view('livewire.merchant.dashboard', compact('site', 'plan', 'apiKeys', 'webhooks'));
    }
}
