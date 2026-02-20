<div class="min-h-[calc(100vh-65px)] py-8 px-4 sm:px-6 lg:px-8">
    <div class="max-w-5xl mx-auto">
        {{-- Success Message --}}
        @if (session('success'))
            <div class="mb-6 bg-green-50 border border-green-200 rounded-lg p-4">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    <p class="text-green-800 font-medium">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        {{-- Page Header --}}
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Merchant Dashboard</h1>
            <p class="mt-1 text-gray-500">Manage your store settings and integrations.</p>
        </div>

        {{-- Store Info Card --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
            <div class="flex items-start justify-between">
                <div>
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">Store Information</h2>
                    <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-4">
                        <div>
                            <dt class="text-sm text-gray-500">Store Name</dt>
                            <dd class="mt-1 text-gray-900 font-medium">{{ $site?->name ?? 'N/A' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm text-gray-500">Subdomain</dt>
                            <dd class="mt-1 text-gray-900 font-medium">{{ $site?->subdomain ?? 'N/A' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm text-gray-500">Full Domain</dt>
                            <dd class="mt-1">
                                <a href="https://{{ $site?->domain }}" target="_blank" class="text-blue-600 hover:underline font-medium">
                                    {{ $site?->domain ?? 'N/A' }}
                                </a>
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm text-gray-500">Current Plan</dt>
                            <dd class="mt-1">
                                <span class="inline-flex items-center gap-2">
                                    <span class="px-2.5 py-0.5 bg-blue-100 text-blue-800 text-sm font-medium rounded-full">
                                        {{ $plan?->name ?? 'No Plan' }}
                                    </span>
                                    <span class="text-gray-500 text-sm">${{ $plan ? number_format($plan->price_cents / 100) : '0' }}/mo</span>
                                </span>
                            </dd>
                        </div>
                        @if ($site?->trial_ends_at && $site->trial_ends_at->isFuture())
                            <div class="sm:col-span-2">
                                <dt class="text-sm text-gray-500">Trial Status</dt>
                                <dd class="mt-1">
                                    <span class="inline-flex items-center gap-2 px-3 py-1 bg-yellow-50 border border-yellow-200 text-yellow-800 text-sm font-medium rounded-lg">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                                        </svg>
                                        Trial ends {{ $site->trial_ends_at->diffForHumans() }} ({{ $site->trial_ends_at->format('M j, Y') }})
                                    </span>
                                </dd>
                            </div>
                        @endif
                    </dl>
                </div>
                <div class="flex flex-col gap-2">
                    <a href="{{ route('merchant.upgrade') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors">
                        Upgrade Plan
                    </a>
                    <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-200 transition-colors">
                        Go to Store Admin
                    </a>
                </div>
            </div>
        </div>

        {{-- Plan Features Card --}}
        @if ($plan && $plan->features)
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Plan Features</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                    @foreach ($plan->features as $featureKey => $featureValue)
                        <div class="flex items-center gap-2 p-3 bg-gray-50 rounded-lg">
                            @if ($featureValue === true || (is_numeric($featureValue) && $featureValue > 0) || (is_string($featureValue) && !empty($featureValue)))
                                <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                            @else
                                <svg class="w-5 h-5 text-gray-400 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                </svg>
                            @endif
                            <span class="text-sm text-gray-700">
                                {{ ucwords(str_replace('_', ' ', $featureKey)) }}
                                @if (!is_bool($featureValue) && $featureValue !== true && $featureValue !== false)
                                    <span class="text-gray-500">: {{ $featureValue }}</span>
                                @endif
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

{{-- New Key Alert --}}
@if ($newKeyPlaintext)
<div class="mb-6 bg-yellow-50 border-2 border-yellow-400 rounded-xl p-5">
    <div class="flex items-start justify-between mb-3">
        <div>
            <h3 class="font-bold text-yellow-900">New API Key Created</h3>
            <p class="text-yellow-700 text-sm mt-0.5">Copy this key now. It will not be shown again.</p>
        </div>
        <button wire:click="dismissNewKey" class="text-yellow-600 hover:text-yellow-800">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
    </div>
    <div class="flex items-center gap-3">
        <code class="flex-1 bg-yellow-100 border border-yellow-300 rounded-lg px-4 py-2 font-mono text-sm text-yellow-900 break-all">{{ $newKeyPlaintext }}</code>
        <button onclick="navigator.clipboard.writeText('{{ $newKeyPlaintext }}');this.textContent='Copied!'" class="px-4 py-2 bg-yellow-500 text-white text-sm font-medium rounded-lg hover:bg-yellow-600 transition whitespace-nowrap">Copy</button>
    </div>
</div>
@endif

{{-- API Keys Card --}}
<div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
    <div class="flex items-center justify-between mb-4">
        <div>
            <h2 class="text-xl font-semibold text-gray-900">API Keys</h2>
            <p class="text-sm text-gray-500 mt-0.5">Authenticate API requests with these keys.</p>
        </div>
        <button wire:click="$toggle('showCreateKeyForm')" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            New Key
        </button>
    </div>

    @if ($showCreateKeyForm)
    <div class="mb-5 p-5 bg-gray-50 rounded-xl border border-gray-200">
        <h3 class="font-semibold text-gray-800 mb-4">Create API Key</h3>
        <div class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Key Name</label>
                <input wire:model="newKeyName" type="text" placeholder="e.g. Mobile App, POS Terminal" class="w-full rounded-lg border border-gray-300 px-4 py-2 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none">
                @error('newKeyName') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Scopes</label>
                <div class="flex flex-wrap gap-3">
                    @foreach (['products', 'orders', 'webhooks', '*'] as $scope)
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" wire:model="newKeyScopes" value="{{ $scope }}" class="rounded border-gray-300 text-blue-600">
                        <span class="text-sm text-gray-700 font-mono">{{ $scope }}</span>
                    </label>
                    @endforeach
                </div>
            </div>
            <div class="flex gap-3">
                <button wire:click="createApiKey" wire:loading.attr="disabled" class="px-5 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition disabled:opacity-50">
                    <span wire:loading.remove wire:target="createApiKey">Create Key</span>
                    <span wire:loading wire:target="createApiKey">Creating...</span>
                </button>
                <button wire:click="$set('showCreateKeyForm', false)" class="px-5 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-200 transition">Cancel</button>
            </div>
        </div>
    </div>
    @endif

    @if ($apiKeys->isEmpty())
    <p class="text-gray-400 text-sm text-center py-6">No API keys yet. Create one to start integrating.</p>
    @else
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead><tr class="border-b border-gray-100">
                <th class="text-left py-3 px-2 text-gray-500 font-medium">Name</th>
                <th class="text-left py-3 px-2 text-gray-500 font-medium">Scopes</th>
                <th class="text-left py-3 px-2 text-gray-500 font-medium">Status</th>
                <th class="text-left py-3 px-2 text-gray-500 font-medium">Last Used</th>
                <th class="py-3 px-2"></th>
            </tr></thead>
            <tbody>
                @foreach ($apiKeys as $key)
                <tr class="border-b border-gray-50 hover:bg-gray-50" wire:key="key-{{ $key->id }}">
                    <td class="py-3 px-2 font-medium text-gray-900">{{ $key->name }}</td>
                    <td class="py-3 px-2">
                        <div class="flex flex-wrap gap-1">
                            @foreach ($key->scopes ?? [] as $scope)
                            <span class="px-1.5 py-0.5 bg-blue-100 text-blue-700 text-xs rounded font-mono">{{ $scope }}</span>
                            @endforeach
                        </div>
                    </td>
                    <td class="py-3 px-2">
                        @if ($key->is_active)
                            <span class="px-2 py-0.5 bg-green-100 text-green-700 text-xs font-medium rounded-full">Active</span>
                        @else
                            <span class="px-2 py-0.5 bg-gray-100 text-gray-600 text-xs font-medium rounded-full">Revoked</span>
                        @endif
                    </td>
                    <td class="py-3 px-2 text-gray-400">{{ $key->last_used_at?->diffForHumans() ?? 'Never' }}</td>
                    <td class="py-3 px-2">
                        <div class="flex items-center gap-2 justify-end">
                            @if ($key->is_active)
                            <button wire:click="revokeApiKey({{ $key->id }})" wire:confirm="Revoke this API key?" class="text-xs text-yellow-600 hover:text-yellow-800 font-medium">Revoke</button>
                            @endif
                            <button wire:click="deleteApiKey({{ $key->id }})" wire:confirm="Permanently delete this API key?" class="text-xs text-red-600 hover:text-red-800 font-medium">Delete</button>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
</div>

{{-- Webhooks Card --}}
<div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
    <div class="flex items-center justify-between mb-4">
        <div>
            <h2 class="text-xl font-semibold text-gray-900">Webhooks</h2>
            <p class="text-sm text-gray-500 mt-0.5">Receive real-time event notifications at your endpoint.</p>
        </div>
        <button wire:click="$toggle('showCreateWebhookForm')" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Add Webhook
        </button>
    </div>

    @if ($showCreateWebhookForm)
    <div class="mb-5 p-5 bg-gray-50 rounded-xl border border-gray-200">
        <h3 class="font-semibold text-gray-800 mb-4">New Webhook Endpoint</h3>
        <div class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Endpoint URL</label>
                <input wire:model="webhookUrl" type="url" placeholder="https://yourapp.com/webhook" class="w-full rounded-lg border border-gray-300 px-4 py-2 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none">
                @error('webhookUrl') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Subscribe to Events</label>
                <div class="grid grid-cols-2 gap-2">
                    @foreach (['order.placed', 'order.completed', 'order.cancelled', 'product.updated', 'inventory.low'] as $event)
                    <label class="flex items-center gap-2 cursor-pointer p-2 rounded-lg hover:bg-gray-100">
                        <input type="checkbox" wire:model="webhookEvents" value="{{ $event }}" class="rounded border-gray-300 text-blue-600">
                        <span class="text-sm text-gray-700 font-mono text-xs">{{ $event }}</span>
                    </label>
                    @endforeach
                </div>
                @error('webhookEvents') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div class="flex gap-3">
                <button wire:click="createWebhook" wire:loading.attr="disabled" class="px-5 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition disabled:opacity-50">
                    <span wire:loading.remove wire:target="createWebhook">Create Webhook</span>
                    <span wire:loading wire:target="createWebhook">Creating...</span>
                </button>
                <button wire:click="$set('showCreateWebhookForm', false)" class="px-5 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-200 transition">Cancel</button>
            </div>
        </div>
    </div>
    @endif

    @if ($webhooks->isEmpty())
    <p class="text-gray-400 text-sm text-center py-6">No webhooks configured yet.</p>
    @else
    <div class="space-y-3">
        @foreach ($webhooks as $webhook)
        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl border border-gray-200" wire:key="wh-{{ $webhook->id }}">
            <div class="flex-1 min-w-0">
                <p class="font-mono text-sm text-gray-900 truncate">{{ $webhook->url }}</p>
                <div class="flex flex-wrap gap-1 mt-1.5">
                    @foreach ($webhook->events ?? [] as $event)
                    <span class="px-1.5 py-0.5 bg-blue-100 text-blue-700 text-xs rounded font-mono">{{ $event }}</span>
                    @endforeach
                </div>
            </div>
            <div class="flex items-center gap-3 ml-4 shrink-0">
                @if ($webhook->last_triggered_at)
                <span class="text-xs text-gray-400 hidden sm:block">Last: {{ $webhook->last_triggered_at->diffForHumans() }}</span>
                @endif
                @if ($webhook->is_active)
                    <span class="px-2 py-0.5 bg-green-100 text-green-700 text-xs font-medium rounded-full">Active</span>
                @else
                    <span class="px-2 py-0.5 bg-gray-100 text-gray-500 text-xs font-medium rounded-full">Inactive</span>
                @endif
                <button wire:click="toggleWebhook({{ $webhook->id }})" class="text-xs text-blue-600 hover:text-blue-800 font-medium">
                    {{ $webhook->is_active ? 'Disable' : 'Enable' }}
                </button>
                <button wire:click="deleteWebhook({{ $webhook->id }})" wire:confirm="Delete this webhook?" class="text-xs text-red-600 hover:text-red-800 font-medium">Delete</button>
            </div>
        </div>
        @endforeach
    </div>
    @endif
</div>

{{-- Custom Domain Card --}}
<div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
    <div class="mb-4">
        <h2 class="text-xl font-semibold text-gray-900">Custom Domain</h2>
        <p class="text-sm text-gray-500 mt-0.5">Use your own domain for a white-label storefront experience.</p>
    </div>

    <div class="space-y-4">
        {{-- Domain Input --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Your Custom Domain</label>
            <div class="flex gap-3">
                <input
                    wire:model="customDomain"
                    type="text"
                    placeholder="shop.yourbrand.com"
                    class="flex-1 rounded-lg border border-gray-300 px-4 py-2 text-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200 outline-none"
                >
                <button
                    wire:click="saveCustomDomain"
                    wire:loading.attr="disabled"
                    class="px-5 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition disabled:opacity-50"
                >
                    <span wire:loading.remove wire:target="saveCustomDomain">Save</span>
                    <span wire:loading wire:target="saveCustomDomain">Saving...</span>
                </button>
            </div>
            @error('customDomain') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        {{-- DNS Verification Panel --}}
        @if ($site?->domain_txt_record)
        <div class="mt-5 p-5 bg-gray-50 rounded-xl border border-gray-200">
            <h3 class="font-semibold text-gray-800 mb-3">DNS Verification</h3>

            {{-- Status Badge --}}
            <div class="mb-4">
                @if ($site->domain_verified_at)
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-green-100 text-green-700 text-sm font-medium rounded-full">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                        </svg>
                        Verified on {{ $site->domain_verified_at->format('M j, Y \a\t g:i A') }}
                    </span>
                @else
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-yellow-100 text-yellow-700 text-sm font-medium rounded-full">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                        </svg>
                        Pending Verification
                    </span>
                @endif
            </div>

            @if (!$site->domain_verified_at)
            {{-- Instructions --}}
            <div class="mb-4">
                <p class="text-sm text-gray-600 mb-2">
                    Add a <strong>TXT record</strong> to your domain's DNS settings with the value below, then click "Verify Domain".
                </p>
                <p class="text-xs text-gray-500">
                    DNS changes can take up to 48 hours to propagate, though most complete within minutes.
                </p>
            </div>

            {{-- TXT Record Value --}}
            <div class="mb-4">
                <label class="block text-xs font-medium text-gray-500 mb-1 uppercase tracking-wide">TXT Record Value</label>
                <div class="flex items-center gap-2">
                    <code class="flex-1 bg-white border border-gray-300 rounded-lg px-4 py-2.5 font-mono text-sm text-gray-900 break-all">{{ $site->domain_txt_record }}</code>
                    <button
                        onclick="navigator.clipboard.writeText('{{ $site->domain_txt_record }}');this.textContent='Copied!';setTimeout(()=>this.textContent='Copy',2000)"
                        class="px-4 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-200 transition whitespace-nowrap"
                    >
                        Copy
                    </button>
                </div>
            </div>

            {{-- Verify Button --}}
            <button
                wire:click="verifyDomain"
                wire:loading.attr="disabled"
                class="inline-flex items-center gap-2 px-5 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 transition disabled:opacity-50"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span wire:loading.remove wire:target="verifyDomain">Verify Domain</span>
                <span wire:loading wire:target="verifyDomain">Checking DNS...</span>
            </button>
            @else
            {{-- Verified - Show current settings --}}
            <div class="text-sm text-gray-600">
                <p>Your custom domain <strong class="text-gray-900">{{ $site->custom_domain }}</strong> is verified and active.</p>
                <p class="mt-1 text-xs text-gray-500">Visitors can now access your store at <a href="https://{{ $site->custom_domain }}" target="_blank" class="text-blue-600 hover:underline">https://{{ $site->custom_domain }}</a></p>
            </div>
            @endif
        </div>
        @endif
    </div>
</div>
    </div>
</div>
