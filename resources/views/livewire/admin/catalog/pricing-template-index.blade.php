<div x-data="{ activeStrategy: @entangle('strategyType') }">
    {{-- Page Header --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-xl font-bold text-gray-900 dark:text-white">Pricing Templates</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Manage pricing strategies for product categories</p>
        </div>
        <button wire:click="createModal" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-[var(--color-primary)] text-white text-sm font-semibold hover:bg-[var(--color-primary-dark)] transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            New Template
        </button>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center">
                    <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['total'] }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Total Templates</p>
                </div>
            </div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-emerald-100 dark:bg-emerald-900/30 flex items-center justify-center">
                    <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['active'] }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Active Templates</p>
                </div>
            </div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-purple-100 dark:bg-purple-900/30 flex items-center justify-center">
                    <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['global'] }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Global Templates</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Table --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-900/50 border-b border-gray-200 dark:border-gray-700">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600 dark:text-gray-300 text-xs uppercase tracking-wide">Name</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600 dark:text-gray-300 text-xs uppercase tracking-wide">Strategy</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600 dark:text-gray-300 text-xs uppercase tracking-wide">Config</th>
                        <th class="px-4 py-3 text-center font-semibold text-gray-600 dark:text-gray-300 text-xs uppercase tracking-wide">Global</th>
                        <th class="px-4 py-3 text-center font-semibold text-gray-600 dark:text-gray-300 text-xs uppercase tracking-wide">Status</th>
                        <th class="px-4 py-3 text-center font-semibold text-gray-600 dark:text-gray-300 text-xs uppercase tracking-wide">Categories</th>
                        <th class="px-4 py-3 text-right font-semibold text-gray-600 dark:text-gray-300 text-xs uppercase tracking-wide">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($templates as $tpl)
                        <tr wire:key="tpl-{{ $tpl->id }}" class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                            <td class="px-4 py-3">
                                <div class="font-medium text-gray-900 dark:text-white">{{ $tpl->name }}</div>
                                @if($tpl->description)
                                    <p class="text-xs text-gray-500 dark:text-gray-400 truncate max-w-xs">{{ $tpl->description }}</p>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                @php
                                    $badgeClass = match($tpl->strategy_type) {
                                        'percentage_markup' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400',
                                        'fixed_markup' => 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-400',
                                        'tiered' => 'bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-400',
                                        'attribute_based' => 'bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-400',
                                        default => 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300',
                                    };
                                @endphp
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold {{ $badgeClass }}">
                                    {{ $tpl->strategy_name }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-gray-600 dark:text-gray-400 text-xs">
                                {{ $tpl->config_summary }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if($tpl->is_global)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-400">
                                        Global
                                    </span>
                                @else
                                    <span class="text-gray-400 dark:text-gray-500">-</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if($tpl->is_active)
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800">Active</span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-semibold bg-gray-100 text-gray-500 dark:bg-gray-700 dark:text-gray-400 border border-gray-200 dark:border-gray-600">Inactive</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                <button wire:click="openCategoryModal({{ $tpl->id }})" class="inline-flex items-center gap-1 px-2 py-1 rounded-lg text-xs font-medium text-[var(--color-primary)] hover:bg-[var(--color-primary)]/10 transition">
                                    <span class="inline-flex items-center justify-center w-5 h-5 rounded-full text-xs font-bold {{ $tpl->categories_count > 0 ? 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400' : 'bg-gray-100 text-gray-500 dark:bg-gray-700 dark:text-gray-400' }}">
                                        {{ $tpl->categories_count }}
                                    </span>
                                    <span>Assign</span>
                                </button>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <div class="inline-flex items-center gap-1">
                                    <button wire:click="editModal({{ $tpl->id }})" class="p-1.5 rounded-lg text-gray-500 dark:text-gray-400 hover:bg-blue-50 dark:hover:bg-blue-900/30 hover:text-blue-600 dark:hover:text-blue-400 transition" title="Edit">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </button>
                                    <button wire:click="deleteRecord({{ $tpl->id }})" wire:confirm="Delete '{{ addslashes($tpl->name) }}'? This cannot be undone." class="p-1.5 rounded-lg text-gray-500 dark:text-gray-400 hover:bg-red-50 dark:hover:bg-red-900/30 hover:text-red-600 dark:hover:text-red-400 transition" title="Delete">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-16 text-center text-gray-500 dark:text-gray-400">
                                <svg class="w-10 h-10 mx-auto mb-3 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                                <p class="font-medium">No pricing templates yet</p>
                                <p class="text-sm">Create your first template to start pricing products automatically.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Create / Edit Modal --}}
    @if($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 overflow-y-auto">
            <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" wire:click="closeModal"></div>
            <div class="relative z-10 w-full max-w-2xl bg-white dark:bg-gray-800 rounded-xl shadow-xl border border-gray-200 dark:border-gray-700 overflow-hidden my-8">
                {{-- Modal header --}}
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 dark:border-gray-700">
                    <h3 class="font-semibold text-gray-900 dark:text-white">{{ $editingId ? 'Edit Template' : 'New Pricing Template' }}</h3>
                    <button wire:click="closeModal" class="p-1.5 rounded-lg text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                {{-- Form --}}
                <form wire:submit="save" class="p-6 space-y-5 max-h-[70vh] overflow-y-auto">
                    {{-- Basic Info --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="md:col-span-2">
                            <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Name <span class="text-red-500">*</span></label>
                            <input wire:model="name" type="text" placeholder="e.g. Standard Markup" class="w-full rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-900 px-3 py-2 text-sm text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-[var(--color-primary)]/30 focus:border-[var(--color-primary)] outline-none" />
                            @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Description</label>
                            <textarea wire:model="description" rows="2" placeholder="Optional description..." class="w-full rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-900 px-3 py-2 text-sm text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-[var(--color-primary)]/30 focus:border-[var(--color-primary)] outline-none resize-none"></textarea>
                        </div>

                        <div class="flex items-center gap-4">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" wire:model="isActive" class="rounded accent-[var(--color-primary)]" />
                                <span class="text-sm text-gray-700 dark:text-gray-300">Active</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" wire:model="isGlobal" class="rounded accent-[var(--color-primary)]" />
                                <span class="text-sm text-gray-700 dark:text-gray-300">Global Default</span>
                            </label>
                        </div>
                    </div>

                    {{-- Strategy Type Selection --}}
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-2">Strategy Type <span class="text-red-500">*</span></label>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-2">
                            <label class="relative flex items-center justify-center p-3 rounded-lg border-2 cursor-pointer transition"
                                   :class="activeStrategy === 'percentage_markup' ? 'border-[var(--color-primary)] bg-[var(--color-primary)]/5' : 'border-gray-200 dark:border-gray-600 hover:border-gray-300'">
                                <input type="radio" wire:model.live="strategyType" value="percentage_markup" class="sr-only" />
                                <div class="text-center">
                                    <div class="text-lg font-bold text-gray-900 dark:text-white">%</div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">Percentage</div>
                                </div>
                            </label>
                            <label class="relative flex items-center justify-center p-3 rounded-lg border-2 cursor-pointer transition"
                                   :class="activeStrategy === 'fixed_markup' ? 'border-[var(--color-primary)] bg-[var(--color-primary)]/5' : 'border-gray-200 dark:border-gray-600 hover:border-gray-300'">
                                <input type="radio" wire:model.live="strategyType" value="fixed_markup" class="sr-only" />
                                <div class="text-center">
                                    <div class="text-lg font-bold text-gray-900 dark:text-white">+$</div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">Fixed</div>
                                </div>
                            </label>
                            <label class="relative flex items-center justify-center p-3 rounded-lg border-2 cursor-pointer transition"
                                   :class="activeStrategy === 'tiered' ? 'border-[var(--color-primary)] bg-[var(--color-primary)]/5' : 'border-gray-200 dark:border-gray-600 hover:border-gray-300'">
                                <input type="radio" wire:model.live="strategyType" value="tiered" class="sr-only" />
                                <div class="text-center">
                                    <svg class="w-5 h-5 mx-auto text-gray-900 dark:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">Tiered</div>
                                </div>
                            </label>
                            <label class="relative flex items-center justify-center p-3 rounded-lg border-2 cursor-pointer transition"
                                   :class="activeStrategy === 'attribute_based' ? 'border-[var(--color-primary)] bg-[var(--color-primary)]/5' : 'border-gray-200 dark:border-gray-600 hover:border-gray-300'">
                                <input type="radio" wire:model.live="strategyType" value="attribute_based" class="sr-only" />
                                <div class="text-center">
                                    <svg class="w-5 h-5 mx-auto text-gray-900 dark:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">Attribute</div>
                                </div>
                            </label>
                        </div>
                        @error('strategyType') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Strategy-Specific Config --}}
                    <div class="border border-gray-200 dark:border-gray-600 rounded-lg p-4 bg-gray-50 dark:bg-gray-900/50">
                        <h4 class="text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wide mb-3">Strategy Configuration</h4>

                        {{-- Percentage Markup --}}
                        <div x-show="activeStrategy === 'percentage_markup'" x-cloak>
                            <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Markup Percentage</label>
                            <div class="relative max-w-xs">
                                <input wire:model="percentage" type="number" step="0.1" min="0" placeholder="50" class="w-full rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-900 px-3 py-2 pr-8 text-sm text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-[var(--color-primary)]/30 outline-none" />
                                <span class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm">%</span>
                            </div>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Price = Cost x (1 + Percentage/100)</p>
                        </div>

                        {{-- Fixed Markup --}}
                        <div x-show="activeStrategy === 'fixed_markup'" x-cloak>
                            <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Fixed Amount</label>
                            <div class="relative max-w-xs">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm">ETB</span>
                                <input wire:model="fixedAmount" type="number" step="0.01" min="0" placeholder="10" class="w-full rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-900 pl-12 pr-3 py-2 text-sm text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-[var(--color-primary)]/30 outline-none" />
                            </div>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Price = Cost + Fixed Amount</p>
                        </div>

                        {{-- Tiered Pricing --}}
                        <div x-show="activeStrategy === 'tiered'" x-cloak>
                            <div class="space-y-2">
                                @foreach($tiers as $i => $tier)
                                    <div wire:key="tier-{{ $i }}" class="flex items-center gap-2">
                                        <div class="flex-1 grid grid-cols-2 gap-2">
                                            <div>
                                                <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Min Cost</label>
                                                <input wire:model="tiers.{{ $i }}.min_cost" type="number" step="0.01" min="0" placeholder="0" class="w-full rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-900 px-3 py-2 text-sm text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-[var(--color-primary)]/30 outline-none" />
                                            </div>
                                            <div>
                                                <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Markup %</label>
                                                <input wire:model="tiers.{{ $i }}.percentage" type="number" step="0.1" min="0" placeholder="50" class="w-full rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-900 px-3 py-2 text-sm text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-[var(--color-primary)]/30 outline-none" />
                                            </div>
                                        </div>
                                        @if(count($tiers) > 1)
                                            <button type="button" wire:click="removeTier({{ $i }})" class="p-2 rounded-lg text-red-500 hover:bg-red-50 dark:hover:bg-red-900/30 transition mt-5">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                            </button>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                            <button type="button" wire:click="addTier" class="mt-3 inline-flex items-center gap-1 text-sm text-[var(--color-primary)] hover:underline">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                Add Tier
                            </button>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">Define tiers from lowest to highest cost. Higher tiers override lower ones.</p>
                        </div>

                        {{-- Attribute-Based --}}
                        <div x-show="activeStrategy === 'attribute_based'" x-cloak>
                            <div class="space-y-4">
                                @foreach($rules as $ri => $rule)
                                    <div wire:key="rule-{{ $ri }}" class="border border-gray-200 dark:border-gray-600 rounded-lg p-3 bg-white dark:bg-gray-800">
                                        <div class="flex items-center justify-between mb-2">
                                            <span class="text-xs font-semibold text-gray-700 dark:text-gray-300">Rule {{ $ri + 1 }}</span>
                                            @if(count($rules) > 1)
                                                <button type="button" wire:click="removeRule({{ $ri }})" class="p-1 rounded text-red-500 hover:bg-red-50 dark:hover:bg-red-900/30 transition">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                                </button>
                                            @endif
                                        </div>

                                        {{-- Conditions --}}
                                        <div class="space-y-2 mb-3">
                                            @foreach($rule['conditions'] ?? [] as $ci => $cond)
                                                <div wire:key="cond-{{ $ri }}-{{ $ci }}" class="flex items-center gap-2">
                                                    <input wire:model="rules.{{ $ri }}.conditions.{{ $ci }}.attribute" type="text" placeholder="Attribute name" class="flex-1 rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-900 px-3 py-1.5 text-xs text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-[var(--color-primary)]/30 outline-none" />
                                                    <span class="text-gray-400 text-xs">=</span>
                                                    <input wire:model="rules.{{ $ri }}.conditions.{{ $ci }}.value" type="text" placeholder="Value" class="flex-1 rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-900 px-3 py-1.5 text-xs text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-[var(--color-primary)]/30 outline-none" />
                                                    @if(count($rule['conditions'] ?? []) > 1)
                                                        <button type="button" wire:click="removeCondition({{ $ri }}, {{ $ci }})" class="p-1 rounded text-red-500 hover:bg-red-50 dark:hover:bg-red-900/30 transition">
                                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                                        </button>
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>
                                        <button type="button" wire:click="addCondition({{ $ri }})" class="text-xs text-[var(--color-primary)] hover:underline">+ Add Condition</button>

                                        {{-- Rule Percentage --}}
                                        <div class="mt-3 flex items-center gap-2">
                                            <span class="text-xs text-gray-500 dark:text-gray-400">Markup:</span>
                                            <input wire:model="rules.{{ $ri }}.percentage" type="number" step="0.1" min="0" placeholder="50" class="w-20 rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-900 px-2 py-1 text-xs text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-[var(--color-primary)]/30 outline-none" />
                                            <span class="text-xs text-gray-400">%</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <button type="button" wire:click="addRule" class="mt-3 inline-flex items-center gap-1 text-sm text-[var(--color-primary)] hover:underline">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                Add Rule
                            </button>

                            {{-- Default Percentage --}}
                            <div class="mt-4 pt-3 border-t border-gray-200 dark:border-gray-600">
                                <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Default Markup (when no rule matches)</label>
                                <div class="relative max-w-xs">
                                    <input wire:model="defaultPercentage" type="number" step="0.1" min="0" placeholder="50" class="w-full rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-900 px-3 py-2 pr-8 text-sm text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-[var(--color-primary)]/30 outline-none" />
                                    <span class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm">%</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Preview --}}
                    <div class="border border-gray-200 dark:border-gray-600 rounded-lg p-4 bg-gray-50 dark:bg-gray-900/50">
                        <h4 class="text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wide mb-3">Price Preview</h4>
                        <div class="flex items-end gap-3">
                            <div class="flex-1">
                                <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Cost Price</label>
                                <div class="relative">
                                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm">ETB</span>
                                    <input wire:model="previewCost" type="number" step="0.01" min="0" placeholder="100" class="w-full rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-900 pl-12 pr-3 py-2 text-sm text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-[var(--color-primary)]/30 outline-none" />
                                </div>
                            </div>
                            <button type="button" wire:click="previewPrice" class="px-4 py-2 rounded-lg border border-gray-200 dark:border-gray-600 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                                Calculate
                            </button>
                            @if($previewResult !== null)
                                <div class="flex-1">
                                    <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Selling Price</label>
                                    <div class="px-3 py-2 rounded-lg bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400 font-bold text-sm">
                                        ETB {{ number_format($previewResult, 2) }}
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Modal Actions --}}
                    <div class="flex justify-end gap-3 pt-2">
                        <button type="button" wire:click="closeModal" class="px-4 py-2 rounded-lg border border-gray-200 dark:border-gray-600 text-sm text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition">Cancel</button>
                        <button type="submit" wire:loading.attr="disabled" class="px-5 py-2 rounded-lg bg-[var(--color-primary)] text-white text-sm font-semibold hover:bg-[var(--color-primary-dark)] transition disabled:opacity-60">
                            <span wire:loading.remove wire:target="save">{{ $editingId ? 'Update Template' : 'Create Template' }}</span>
                            <span wire:loading wire:target="save">Saving...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- Category Assign Modal --}}
    @if($showCategoryModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" wire:click="closeCategoryModal"></div>
            <div class="relative z-10 w-full max-w-md bg-white dark:bg-gray-800 rounded-xl shadow-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                {{-- Modal header --}}
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 dark:border-gray-700">
                    <h3 class="font-semibold text-gray-900 dark:text-white">Assign Categories</h3>
                    <button wire:click="closeCategoryModal" class="p-1.5 rounded-lg text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <div class="p-6">
                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Select which categories should use this pricing template.</p>

                    <div class="max-h-64 overflow-y-auto border border-gray-200 dark:border-gray-600 rounded-lg divide-y divide-gray-100 dark:divide-gray-700">
                        @forelse($allCategories as $cat)
                            <label class="flex items-center gap-3 px-3 py-2 hover:bg-gray-50 dark:hover:bg-gray-700/50 cursor-pointer transition">
                                <input
                                    type="checkbox"
                                    wire:model="selectedCategories"
                                    value="{{ $cat['id'] }}"
                                    class="rounded accent-[var(--color-primary)]"
                                />
                                <span class="text-sm text-gray-700 dark:text-gray-300">{{ $cat['name'] }}</span>
                            </label>
                        @empty
                            <div class="px-3 py-8 text-center text-gray-500 dark:text-gray-400 text-sm">
                                No categories available
                            </div>
                        @endforelse
                    </div>

                    <div class="flex justify-end gap-3 mt-4">
                        <button type="button" wire:click="closeCategoryModal" class="px-4 py-2 rounded-lg border border-gray-200 dark:border-gray-600 text-sm text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition">Cancel</button>
                        <button wire:click="saveCategories" wire:loading.attr="disabled" class="px-5 py-2 rounded-lg bg-[var(--color-primary)] text-white text-sm font-semibold hover:bg-[var(--color-primary-dark)] transition disabled:opacity-60">
                            <span wire:loading.remove wire:target="saveCategories">Save</span>
                            <span wire:loading wire:target="saveCategories">Saving...</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
