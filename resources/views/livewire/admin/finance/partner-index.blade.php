<div>
    <x-admin.ui.entity-header
        title="Partners"
        subtitle="Manage suppliers, affiliates, and business partners"
        :breadcrumbs="[
            ['label' => 'Dashboard', 'url' => route('admin.dashboard')],
            ['label' => 'Finance'],
            ['label' => 'Partners']
        ]"
    >
        <x-slot name="actions">
            <button
                wire:click="createModal"
                class="inline-flex items-center gap-2 rounded-lg bg-brand-500 px-4 py-2 text-sm font-medium text-white hover:bg-brand-600"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                Add Partner
            </button>
        </x-slot>
    </x-admin.ui.entity-header>

    {{-- Stats Row --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <x-admin.ui.stat-card
            title="Total Partners"
            :value="number_format($this->stats['total'])"
            icon="users"
            color="brand"
        />
        <x-admin.ui.stat-card
            title="Active Partners"
            :value="number_format($this->stats['active'])"
            icon="check"
            color="green"
        />
        <x-admin.ui.stat-card
            title="Pending Commissions"
            :value="number_format($this->stats['pending_commissions'], 0)"
            icon="clock"
            color="yellow"
        />
        <x-admin.ui.stat-card
            title="Total Paid"
            :value="number_format($this->stats['total_paid'], 0)"
            icon="currency"
            color="blue"
        />
    </div>

    <div class="flex flex-col lg:flex-row gap-6">
        {{-- Left Panel: Partners List --}}
        <div class="{{ $viewingPartnerId ? 'lg:w-1/2' : 'w-full' }} transition-all duration-300">
            <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
                {{-- Header with Filters --}}
                <div class="p-5 border-b border-gray-100 dark:border-gray-800">
                    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">Partners List</h3>
                        </div>
                        <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
                            {{-- Status Filter --}}
                            <select
                                wire:model.live="statusFilter"
                                class="h-10 rounded-lg border border-gray-300 bg-transparent px-3 text-sm text-gray-800 focus:border-brand-300 focus:outline-none focus:ring-2 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"
                            >
                                <option value="">All Status</option>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                                <option value="suspended">Suspended</option>
                            </select>

                            {{-- Type Filter --}}
                            <select
                                wire:model.live="typeFilter"
                                class="h-10 rounded-lg border border-gray-300 bg-transparent px-3 text-sm text-gray-800 focus:border-brand-300 focus:outline-none focus:ring-2 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"
                            >
                                <option value="">All Types</option>
                                <option value="supplier">Supplier</option>
                                <option value="manufacturer">Manufacturer</option>
                                <option value="distributor">Distributor</option>
                                <option value="affiliate">Affiliate</option>
                                <option value="franchise">Franchise</option>
                                <option value="reseller">Reseller</option>
                            </select>

                            {{-- Search --}}
                            <div class="relative">
                                <div class="absolute -translate-y-1/2 left-3 top-1/2">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                </div>
                                <input
                                    type="text"
                                    wire:model.live.debounce.300ms="search"
                                    placeholder="Search partners..."
                                    class="h-10 w-full rounded-lg border border-gray-300 bg-transparent py-2 pl-9 pr-4 text-sm text-gray-800 placeholder:text-gray-400 focus:border-brand-300 focus:outline-none focus:ring-2 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 sm:w-56"
                                />
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Table --}}
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-gray-100 dark:border-gray-800">
                                <th class="px-5 py-3 text-left">
                                    <span class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">Partner</span>
                                </th>
                                <th class="px-5 py-3 text-left">
                                    <span class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">Type</span>
                                </th>
                                <th class="px-5 py-3 text-center">
                                    <span class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">Status</span>
                                </th>
                                <th class="px-5 py-3 text-right">
                                    <span class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">Total Paid</span>
                                </th>
                                <th class="px-5 py-3 text-right">
                                    <span class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">Actions</span>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                            @forelse($partners as $partner)
                                <tr
                                    wire:key="partner-{{ $partner->id }}"
                                    class="hover:bg-gray-50 dark:hover:bg-white/[0.02] cursor-pointer {{ $viewingPartnerId === $partner->id ? 'bg-brand-50 dark:bg-brand-500/10' : '' }}"
                                    wire:click="openDetail({{ $partner->id }})"
                                >
                                    <td class="px-5 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="flex-shrink-0 w-10 h-10 rounded-full bg-brand-100 dark:bg-brand-500/10 flex items-center justify-center">
                                                <span class="text-sm font-semibold text-brand-600 dark:text-brand-400">
                                                    {{ strtoupper(substr($partner->name, 0, 2)) }}
                                                </span>
                                            </div>
                                            <div class="min-w-0">
                                                <p class="font-medium text-gray-800 dark:text-white/90 truncate">{{ $partner->name }}</p>
                                                <p class="text-xs text-gray-500 dark:text-gray-400 truncate">{{ $partner->email }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-5 py-4">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300 capitalize">
                                            {{ $partner->type }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-4 text-center">
                                        @php
                                            $statusColors = [
                                                'active' => 'bg-success-50 text-success-700 dark:bg-success-500/15 dark:text-success-400',
                                                'inactive' => 'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-400',
                                                'suspended' => 'bg-error-50 text-error-700 dark:bg-error-500/15 dark:text-error-400',
                                                'blocked' => 'bg-error-50 text-error-700 dark:bg-error-500/15 dark:text-error-400',
                                            ];
                                        @endphp
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusColors[$partner->status] ?? $statusColors['inactive'] }} capitalize">
                                            {{ $partner->status }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-4 text-right">
                                        <span class="font-medium text-gray-800 dark:text-white/90">
                                            {{ number_format($partner->total_paid ?? 0, 0) }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-4 text-right" wire:click.stop>
                                        <div class="flex items-center justify-end gap-1">
                                            <button
                                                wire:click="editModal({{ $partner->id }})"
                                                class="p-1.5 text-gray-400 hover:text-brand-500 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors"
                                                title="Edit"
                                            >
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                            </button>
                                            <button
                                                wire:click="createPaymentModal({{ $partner->id }})"
                                                class="p-1.5 text-gray-400 hover:text-green-500 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors"
                                                title="Add Payment"
                                            >
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                            </button>
                                            <button
                                                wire:click="calculateCommission({{ $partner->id }})"
                                                class="p-1.5 text-gray-400 hover:text-yellow-500 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors"
                                                title="Calculate Commission"
                                            >
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                                </svg>
                                            </button>
                                            <button
                                                wire:click="deleteRecord({{ $partner->id }})"
                                                wire:confirm="Are you sure you want to delete '{{ addslashes($partner->name) }}'? This cannot be undone."
                                                class="p-1.5 text-gray-400 hover:text-red-500 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors"
                                                title="Delete"
                                            >
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-5 py-12 text-center">
                                        <div class="flex flex-col items-center">
                                            <svg class="w-12 h-12 text-gray-300 dark:text-gray-600 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                            </svg>
                                            <p class="text-gray-500 dark:text-gray-400 font-medium">No partners found</p>
                                            <p class="text-sm text-gray-400 dark:text-gray-500 mt-1">Get started by adding your first partner</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                @if($partners->hasPages())
                    <div class="p-5 border-t border-gray-100 dark:border-gray-800">
                        {{ $partners->links() }}
                    </div>
                @endif
            </div>
        </div>

        {{-- Right Panel: Partner Detail --}}
        @if($viewingPartnerId && $this->viewingPartner)
            <div class="lg:w-1/2 transition-all duration-300">
                <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03] sticky top-4">
                    {{-- Detail Header --}}
                    <div class="p-5 border-b border-gray-100 dark:border-gray-800">
                        <div class="flex items-start justify-between">
                            <div class="flex items-center gap-3">
                                <div class="flex-shrink-0 w-12 h-12 rounded-full bg-brand-100 dark:bg-brand-500/10 flex items-center justify-center">
                                    <span class="text-lg font-semibold text-brand-600 dark:text-brand-400">
                                        {{ strtoupper(substr($this->viewingPartner->name, 0, 2)) }}
                                    </span>
                                </div>
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">{{ $this->viewingPartner->name }}</h3>
                                    <p class="text-sm text-gray-500 dark:text-gray-400 capitalize">{{ $this->viewingPartner->type }}</p>
                                </div>
                            </div>
                            <button
                                wire:click="closeDetail"
                                class="p-1.5 text-gray-400 hover:text-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors"
                            >
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        {{-- Detail Tabs --}}
                        <div class="flex items-center gap-2 mt-4 border-b border-gray-100 dark:border-gray-800 -mb-5 pb-0">
                            <button
                                wire:click="setDetailTab('info')"
                                class="px-4 py-2.5 text-sm font-medium transition-colors border-b-2 -mb-px {{ $detailTab === 'info' ? 'border-brand-500 text-brand-600 dark:text-brand-400' : 'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300' }}"
                            >
                                Info
                            </button>
                            <button
                                wire:click="setDetailTab('calculations')"
                                class="px-4 py-2.5 text-sm font-medium transition-colors border-b-2 -mb-px {{ $detailTab === 'calculations' ? 'border-brand-500 text-brand-600 dark:text-brand-400' : 'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300' }}"
                            >
                                Calculations
                                @if($this->viewingPartner->calculations_count > 0)
                                    <span class="ml-1 inline-flex items-center justify-center min-w-[18px] h-[18px] px-1 rounded-full text-[10px] font-bold bg-brand-100 text-brand-700 dark:bg-brand-500/20 dark:text-brand-400">
                                        {{ $this->viewingPartner->calculations_count }}
                                    </span>
                                @endif
                            </button>
                            <button
                                wire:click="setDetailTab('payments')"
                                class="px-4 py-2.5 text-sm font-medium transition-colors border-b-2 -mb-px {{ $detailTab === 'payments' ? 'border-brand-500 text-brand-600 dark:text-brand-400' : 'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300' }}"
                            >
                                Payments
                                @if($this->viewingPartner->payments_count > 0)
                                    <span class="ml-1 inline-flex items-center justify-center min-w-[18px] h-[18px] px-1 rounded-full text-[10px] font-bold bg-green-100 text-green-700 dark:bg-green-500/20 dark:text-green-400">
                                        {{ $this->viewingPartner->payments_count }}
                                    </span>
                                @endif
                            </button>
                        </div>
                    </div>

                    {{-- Tab Content --}}
                    <div class="p-5 max-h-[600px] overflow-y-auto">
                        {{-- Info Tab --}}
                        @if($detailTab === 'info')
                            <div class="space-y-4">
                                {{-- Contact Info --}}
                                <div>
                                    <h4 class="text-sm font-semibold text-gray-800 dark:text-white/90 mb-3">Contact Information</h4>
                                    <div class="space-y-2">
                                        <div class="flex items-center gap-3 text-sm">
                                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                            </svg>
                                            <span class="text-gray-600 dark:text-gray-300">{{ $this->viewingPartner->email ?: 'No email' }}</span>
                                        </div>
                                        <div class="flex items-center gap-3 text-sm">
                                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                            </svg>
                                            <span class="text-gray-600 dark:text-gray-300">{{ $this->viewingPartner->phone ?: 'No phone' }}</span>
                                        </div>
                                        @if($this->viewingPartner->address)
                                            <div class="flex items-start gap-3 text-sm">
                                                <svg class="w-4 h-4 text-gray-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                                </svg>
                                                <span class="text-gray-600 dark:text-gray-300">
                                                    {{ $this->viewingPartner->address }}
                                                    @if($this->viewingPartner->city), {{ $this->viewingPartner->city }}@endif
                                                    @if($this->viewingPartner->state), {{ $this->viewingPartner->state }}@endif
                                                    @if($this->viewingPartner->country), {{ $this->viewingPartner->country }}@endif
                                                </span>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                {{-- Financial Info --}}
                                <div class="pt-4 border-t border-gray-100 dark:border-gray-800">
                                    <h4 class="text-sm font-semibold text-gray-800 dark:text-white/90 mb-3">Financial Details</h4>
                                    <div class="grid grid-cols-2 gap-4">
                                        <div class="p-3 rounded-lg bg-gray-50 dark:bg-gray-900/50">
                                            <p class="text-xs text-gray-500 dark:text-gray-400">Commission Rate</p>
                                            <p class="text-lg font-semibold text-gray-800 dark:text-white/90">{{ $this->viewingPartner->commission_rate ?? 0 }}%</p>
                                        </div>
                                        <div class="p-3 rounded-lg bg-gray-50 dark:bg-gray-900/50">
                                            <p class="text-xs text-gray-500 dark:text-gray-400">Tax Number</p>
                                            <p class="text-sm font-medium text-gray-800 dark:text-white/90">{{ $this->viewingPartner->tax_number ?: 'N/A' }}</p>
                                        </div>
                                    </div>
                                </div>

                                {{-- Bank Info --}}
                                @if($this->viewingPartner->bank_name || $this->viewingPartner->bank_account_number)
                                    <div class="pt-4 border-t border-gray-100 dark:border-gray-800">
                                        <h4 class="text-sm font-semibold text-gray-800 dark:text-white/90 mb-3">Bank Details</h4>
                                        <div class="space-y-2 text-sm">
                                            @if($this->viewingPartner->bank_name)
                                                <div class="flex justify-between">
                                                    <span class="text-gray-500 dark:text-gray-400">Bank</span>
                                                    <span class="text-gray-800 dark:text-white/90">{{ $this->viewingPartner->bank_name }}</span>
                                                </div>
                                            @endif
                                            @if($this->viewingPartner->bank_account_number)
                                                <div class="flex justify-between">
                                                    <span class="text-gray-500 dark:text-gray-400">Account</span>
                                                    <span class="text-gray-800 dark:text-white/90 font-mono">{{ $this->viewingPartner->bank_account_number }}</span>
                                                </div>
                                            @endif
                                            @if($this->viewingPartner->bank_routing_number)
                                                <div class="flex justify-between">
                                                    <span class="text-gray-500 dark:text-gray-400">Routing</span>
                                                    <span class="text-gray-800 dark:text-white/90 font-mono">{{ $this->viewingPartner->bank_routing_number }}</span>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endif

                                {{-- Notes --}}
                                @if($this->viewingPartner->notes)
                                    <div class="pt-4 border-t border-gray-100 dark:border-gray-800">
                                        <h4 class="text-sm font-semibold text-gray-800 dark:text-white/90 mb-2">Notes</h4>
                                        <p class="text-sm text-gray-600 dark:text-gray-300">{{ $this->viewingPartner->notes }}</p>
                                    </div>
                                @endif

                                {{-- Quick Actions --}}
                                <div class="pt-4 border-t border-gray-100 dark:border-gray-800">
                                    <div class="flex flex-wrap gap-2">
                                        <button
                                            wire:click="editModal({{ $this->viewingPartner->id }})"
                                            class="inline-flex items-center gap-2 px-3 py-2 rounded-lg border border-gray-200 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800"
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                            Edit Partner
                                        </button>
                                        <button
                                            wire:click="calculateCommission({{ $this->viewingPartner->id }})"
                                            class="inline-flex items-center gap-2 px-3 py-2 rounded-lg bg-yellow-50 border border-yellow-200 text-sm font-medium text-yellow-700 hover:bg-yellow-100 dark:bg-yellow-500/10 dark:border-yellow-500/20 dark:text-yellow-400 dark:hover:bg-yellow-500/20"
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                            </svg>
                                            Calculate Commission
                                        </button>
                                        <button
                                            wire:click="createPaymentModal({{ $this->viewingPartner->id }})"
                                            class="inline-flex items-center gap-2 px-3 py-2 rounded-lg bg-green-50 border border-green-200 text-sm font-medium text-green-700 hover:bg-green-100 dark:bg-green-500/10 dark:border-green-500/20 dark:text-green-400 dark:hover:bg-green-500/20"
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                            </svg>
                                            Add Payment
                                        </button>
                                    </div>
                                </div>
                            </div>

                        {{-- Calculations Tab --}}
                        @elseif($detailTab === 'calculations')
                            <div class="space-y-3">
                                @forelse($this->viewingPartner->calculations->sortByDesc('created_at') as $calculation)
                                    <div class="p-4 rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50">
                                        <div class="flex items-start justify-between mb-3">
                                            <div>
                                                <p class="text-sm font-medium text-gray-800 dark:text-white/90">
                                                    {{ $calculation->period_start?->format('M d') }} - {{ $calculation->period_end?->format('M d, Y') }}
                                                </p>
                                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                                    Created {{ $calculation->created_at?->diffForHumans() }}
                                                </p>
                                            </div>
                                            @php
                                                $calcStatusColors = [
                                                    'pending' => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-500/15 dark:text-yellow-400',
                                                    'approved' => 'bg-blue-100 text-blue-700 dark:bg-blue-500/15 dark:text-blue-400',
                                                    'paid' => 'bg-green-100 text-green-700 dark:bg-green-500/15 dark:text-green-400',
                                                ];
                                            @endphp
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $calcStatusColors[$calculation->status] ?? $calcStatusColors['pending'] }} capitalize">
                                                {{ $calculation->status }}
                                            </span>
                                        </div>
                                        <div class="grid grid-cols-2 gap-3 mb-3">
                                            <div>
                                                <p class="text-xs text-gray-500 dark:text-gray-400">Total Sales</p>
                                                <p class="text-sm font-semibold text-gray-800 dark:text-white/90">{{ number_format($calculation->total_sales, 0) }}</p>
                                            </div>
                                            <div>
                                                <p class="text-xs text-gray-500 dark:text-gray-400">Commission</p>
                                                <p class="text-sm font-semibold text-brand-600 dark:text-brand-400">{{ number_format($calculation->commission_amount, 0) }}</p>
                                            </div>
                                        </div>
                                        <div class="flex items-center gap-2 pt-3 border-t border-gray-200 dark:border-gray-700">
                                            @if($calculation->status === 'pending')
                                                <button
                                                    wire:click="approveCalculation({{ $calculation->id }})"
                                                    class="flex-1 inline-flex items-center justify-center gap-1.5 px-3 py-1.5 rounded-lg bg-blue-500 text-white text-xs font-medium hover:bg-blue-600"
                                                >
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                    </svg>
                                                    Approve
                                                </button>
                                            @endif
                                            @if($calculation->status === 'approved')
                                                <button
                                                    wire:click="markCalculationPaid({{ $calculation->id }})"
                                                    class="flex-1 inline-flex items-center justify-center gap-1.5 px-3 py-1.5 rounded-lg bg-green-500 text-white text-xs font-medium hover:bg-green-600"
                                                >
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                    </svg>
                                                    Mark Paid
                                                </button>
                                            @endif
                                            @if($calculation->status !== 'paid')
                                                <button
                                                    wire:click="deleteCalculation({{ $calculation->id }})"
                                                    wire:confirm="Delete this calculation?"
                                                    class="p-1.5 text-gray-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-500/10 rounded-lg transition-colors"
                                                    title="Delete"
                                                >
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-center py-8">
                                        <svg class="w-10 h-10 mx-auto text-gray-300 dark:text-gray-600 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                        </svg>
                                        <p class="text-gray-500 dark:text-gray-400 text-sm">No calculations yet</p>
                                        <button
                                            wire:click="calculateCommission({{ $this->viewingPartner->id }})"
                                            class="mt-3 inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-brand-500 text-white text-sm font-medium hover:bg-brand-600"
                                        >
                                            Calculate Now
                                        </button>
                                    </div>
                                @endforelse
                            </div>

                        {{-- Payments Tab --}}
                        @elseif($detailTab === 'payments')
                            <div class="space-y-3">
                                <div class="flex justify-end mb-2">
                                    <button
                                        wire:click="createPaymentModal({{ $this->viewingPartner->id }})"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-brand-500 text-white text-sm font-medium hover:bg-brand-600"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                        </svg>
                                        Add Payment
                                    </button>
                                </div>
                                @forelse($this->viewingPartner->payments->sortByDesc('payment_date') as $payment)
                                    <div class="p-4 rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900/50">
                                        <div class="flex items-start justify-between">
                                            <div>
                                                <p class="text-lg font-semibold text-gray-800 dark:text-white/90">{{ number_format($payment->amount, 0) }}</p>
                                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                                    {{ $payment->payment_date?->format('M d, Y') }}
                                                    @if($payment->reference)
                                                        <span class="mx-1">-</span>
                                                        <span class="font-mono">{{ $payment->reference }}</span>
                                                    @endif
                                                </p>
                                            </div>
                                            <div class="flex items-center gap-2">
                                                @php
                                                    $paymentStatusColors = [
                                                        'completed' => 'bg-green-100 text-green-700 dark:bg-green-500/15 dark:text-green-400',
                                                        'pending' => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-500/15 dark:text-yellow-400',
                                                        'cancelled' => 'bg-red-100 text-red-700 dark:bg-red-500/15 dark:text-red-400',
                                                    ];
                                                @endphp
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $paymentStatusColors[$payment->status] ?? $paymentStatusColors['pending'] }} capitalize">
                                                    {{ $payment->status }}
                                                </span>
                                                <button
                                                    wire:click="editPaymentModal({{ $payment->id }})"
                                                    class="p-1.5 text-gray-400 hover:text-brand-500 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors"
                                                    title="Edit"
                                                >
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                    </svg>
                                                </button>
                                                <button
                                                    wire:click="deletePayment({{ $payment->id }})"
                                                    wire:confirm="Delete this payment?"
                                                    class="p-1.5 text-gray-400 hover:text-red-500 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors"
                                                    title="Delete"
                                                >
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>
                                            </div>
                                        </div>
                                        @if($payment->notes)
                                            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">{{ $payment->notes }}</p>
                                        @endif
                                    </div>
                                @empty
                                    <div class="text-center py-8">
                                        <svg class="w-10 h-10 mx-auto text-gray-300 dark:text-gray-600 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <p class="text-gray-500 dark:text-gray-400 text-sm">No payments recorded</p>
                                    </div>
                                @endforelse
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endif
    </div>

    {{-- Partner Create/Edit Modal --}}
    @if($showModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 dark:bg-gray-900 dark:bg-opacity-75 transition-opacity" wire:click="closeModal"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div class="relative inline-block w-full max-w-2xl p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white rounded-2xl shadow-xl dark:bg-gray-800 sm:my-16">
                    <div class="flex items-center justify-between mb-5">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white" id="modal-title">
                            {{ $editingId ? 'Edit Partner' : 'New Partner' }}
                        </h3>
                        <button wire:click="closeModal" class="p-1 text-gray-400 hover:text-gray-500 dark:hover:text-gray-300">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <form wire:submit="save" class="space-y-5">
                        {{-- Basic Info --}}
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="sm:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                                    Name <span class="text-red-500">*</span>
                                </label>
                                <input
                                    type="text"
                                    wire:model="name"
                                    class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-800 focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"
                                />
                                @error('name') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                                    Email <span class="text-red-500">*</span>
                                </label>
                                <input
                                    type="email"
                                    wire:model="email"
                                    class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-800 focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"
                                />
                                @error('email') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                                    Phone <span class="text-red-500">*</span>
                                </label>
                                <input
                                    type="text"
                                    wire:model="phone"
                                    class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-800 focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"
                                />
                                @error('phone') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                                    Type <span class="text-red-500">*</span>
                                </label>
                                <select
                                    wire:model="type"
                                    class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-800 focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"
                                >
                                    <option value="supplier">Supplier</option>
                                    <option value="manufacturer">Manufacturer</option>
                                    <option value="distributor">Distributor</option>
                                    <option value="affiliate">Affiliate</option>
                                    <option value="franchise">Franchise</option>
                                    <option value="employee">Employee</option>
                                    <option value="service_provider">Service Provider</option>
                                    <option value="reseller">Reseller</option>
                                </select>
                                @error('type') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                                    Status <span class="text-red-500">*</span>
                                </label>
                                <select
                                    wire:model="status"
                                    class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-800 focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"
                                >
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                    <option value="suspended">Suspended</option>
                                    <option value="blocked">Blocked</option>
                                </select>
                                @error('status') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        {{-- Address --}}
                        <div class="border-t border-gray-200 dark:border-gray-700 pt-5">
                            <h4 class="text-sm font-medium text-gray-800 dark:text-white/90 mb-3">Address</h4>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div class="sm:col-span-2">
                                    <input
                                        type="text"
                                        wire:model="address"
                                        placeholder="Street address"
                                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-800 focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"
                                    />
                                </div>
                                <div>
                                    <input
                                        type="text"
                                        wire:model="city"
                                        placeholder="City"
                                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-800 focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"
                                    />
                                </div>
                                <div>
                                    <input
                                        type="text"
                                        wire:model="state"
                                        placeholder="State / Province"
                                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-800 focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"
                                    />
                                </div>
                                <div>
                                    <input
                                        type="text"
                                        wire:model="country"
                                        placeholder="Country"
                                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-800 focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"
                                    />
                                </div>
                                <div>
                                    <input
                                        type="text"
                                        wire:model="postalCode"
                                        placeholder="Postal code"
                                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-800 focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"
                                    />
                                </div>
                            </div>
                        </div>

                        {{-- Financial Info --}}
                        <div class="border-t border-gray-200 dark:border-gray-700 pt-5">
                            <h4 class="text-sm font-medium text-gray-800 dark:text-white/90 mb-3">Financial Details</h4>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Commission Rate (%)</label>
                                    <input
                                        type="number"
                                        step="0.01"
                                        min="0"
                                        max="100"
                                        wire:model="commissionRate"
                                        placeholder="0.00"
                                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-800 focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"
                                    />
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Tax Number</label>
                                    <input
                                        type="text"
                                        wire:model="taxNumber"
                                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-800 focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"
                                    />
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Bank Name</label>
                                    <input
                                        type="text"
                                        wire:model="bankName"
                                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-800 focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"
                                    />
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Account Number</label>
                                    <input
                                        type="text"
                                        wire:model="bankAccountNumber"
                                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-800 focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"
                                    />
                                </div>
                                <div class="sm:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Routing Number</label>
                                    <input
                                        type="text"
                                        wire:model="bankRoutingNumber"
                                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-800 focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"
                                    />
                                </div>
                            </div>
                        </div>

                        {{-- Notes --}}
                        <div class="border-t border-gray-200 dark:border-gray-700 pt-5">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Notes</label>
                            <textarea
                                wire:model="notes"
                                rows="3"
                                class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-800 focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 resize-none"
                            ></textarea>
                        </div>

                        {{-- Actions --}}
                        <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-200 dark:border-gray-700">
                            <button
                                type="button"
                                wire:click="closeModal"
                                class="px-4 py-2 rounded-lg border border-gray-300 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-700"
                            >
                                Cancel
                            </button>
                            <button
                                type="submit"
                                wire:loading.attr="disabled"
                                class="px-5 py-2 rounded-lg bg-brand-500 text-white text-sm font-medium hover:bg-brand-600 disabled:opacity-60"
                            >
                                <span wire:loading.remove>{{ $editingId ? 'Update Partner' : 'Create Partner' }}</span>
                                <span wire:loading>Saving...</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    {{-- Payment Create/Edit Modal --}}
    @if($showPaymentModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="payment-modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 dark:bg-gray-900 dark:bg-opacity-75 transition-opacity" wire:click="closePaymentModal"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div class="relative inline-block w-full max-w-md p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white rounded-2xl shadow-xl dark:bg-gray-800 sm:my-16">
                    <div class="flex items-center justify-between mb-5">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white" id="payment-modal-title">
                            {{ $editingPaymentId ? 'Edit Payment' : 'Record Payment' }}
                        </h3>
                        <button wire:click="closePaymentModal" class="p-1 text-gray-400 hover:text-gray-500 dark:hover:text-gray-300">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <form wire:submit="savePayment" class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                                Amount <span class="text-red-500">*</span>
                            </label>
                            <input
                                type="number"
                                step="0.01"
                                min="0.01"
                                wire:model="paymentAmount"
                                class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-800 focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"
                            />
                            @error('paymentAmount') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                                Payment Date <span class="text-red-500">*</span>
                            </label>
                            <input
                                type="date"
                                wire:model="paymentDate"
                                class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-800 focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"
                            />
                            @error('paymentDate') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Reference</label>
                            <input
                                type="text"
                                wire:model="paymentReference"
                                placeholder="Transaction ID, check number, etc."
                                class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-800 focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"
                            />
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Status</label>
                            <select
                                wire:model="paymentStatus"
                                class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-800 focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"
                            >
                                <option value="completed">Completed</option>
                                <option value="pending">Pending</option>
                                <option value="cancelled">Cancelled</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Notes</label>
                            <textarea
                                wire:model="paymentNotes"
                                rows="2"
                                class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-800 focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 resize-none"
                            ></textarea>
                        </div>

                        <div class="flex items-center justify-end gap-3 pt-4">
                            <button
                                type="button"
                                wire:click="closePaymentModal"
                                class="px-4 py-2 rounded-lg border border-gray-300 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-700"
                            >
                                Cancel
                            </button>
                            <button
                                type="submit"
                                wire:loading.attr="disabled"
                                class="px-5 py-2 rounded-lg bg-brand-500 text-white text-sm font-medium hover:bg-brand-600 disabled:opacity-60"
                            >
                                <span wire:loading.remove>{{ $editingPaymentId ? 'Update Payment' : 'Record Payment' }}</span>
                                <span wire:loading>Saving...</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>
