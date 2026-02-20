<div class="max-w-4xl mx-auto">
    {{-- Breadcrumb Header --}}
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('admin.expenses.index') }}" wire:navigate class="p-2 rounded-lg text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-800 transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <div>
            <nav class="flex items-center gap-1 text-xs text-gray-500 dark:text-gray-400 mb-0.5">
                <a href="{{ route('admin.expenses.index') }}" wire:navigate class="hover:text-[var(--color-primary)]">Expenses</a>
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                <span>{{ $expenseId ? 'Edit' : 'Create' }}</span>
            </nav>
            <h1 class="text-xl font-bold text-gray-900 dark:text-white">{{ $expenseId ? 'Edit Expense' : 'New Expense' }}</h1>
        </div>
    </div>

    <form wire:submit="save" class="space-y-6">

        {{-- Card 1: Expense Details --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm p-6">
            <h2 class="text-sm font-semibold text-gray-800 dark:text-gray-200 mb-4 uppercase tracking-wide">Expense Details</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                {{-- Title --}}
                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">
                        Title <span class="text-red-500">*</span>
                    </label>
                    <input
                        wire:model="title"
                        type="text"
                        placeholder="e.g. Office Supplies"
                        class="w-full rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-900 px-3 py-2 text-sm text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-[var(--color-primary)]/30 focus:border-[var(--color-primary)] outline-none"
                    />
                    @error('title') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Description --}}
                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Description</label>
                    <textarea
                        wire:model="description"
                        rows="3"
                        placeholder="Optional notes about this expense..."
                        class="w-full rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-900 px-3 py-2 text-sm text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-[var(--color-primary)]/30 focus:border-[var(--color-primary)] outline-none resize-none"
                    ></textarea>
                </div>

                {{-- Amount --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">
                        Amount <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm font-medium">ETB</span>
                        <input
                            wire:model="amount"
                            type="number"
                            step="0.01"
                            min="0"
                            placeholder="0.00"
                            class="w-full rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-900 pl-12 pr-3 py-2 text-sm text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-[var(--color-primary)]/30 focus:border-[var(--color-primary)] outline-none"
                        />
                    </div>
                    @error('amount') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Expense Date --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">
                        Expense Date <span class="text-red-500">*</span>
                    </label>
                    <input
                        wire:model="expenseDate"
                        type="date"
                        class="w-full rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-900 px-3 py-2 text-sm text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-[var(--color-primary)]/30 focus:border-[var(--color-primary)] outline-none"
                    />
                    @error('expenseDate') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Status --}}
                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Status</label>
                    <select
                        wire:model="status"
                        class="w-full rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-900 px-3 py-2 text-sm text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-[var(--color-primary)]/30 focus:border-[var(--color-primary)] outline-none"
                    >
                        <option value="pending">Pending</option>
                        <option value="approved">Approved</option>
                        <option value="rejected">Rejected</option>
                    </select>
                </div>
            </div>
        </div>

        {{-- Card 2: Category & Attachments --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm p-6">
            <h2 class="text-sm font-semibold text-gray-800 dark:text-gray-200 mb-4 uppercase tracking-wide">Category & Attachments</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                {{-- Category --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">
                        Category <span class="text-red-500">*</span>
                    </label>
                    <select
                        wire:model="categoryId"
                        class="w-full rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-900 px-3 py-2 text-sm text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-[var(--color-primary)]/30 focus:border-[var(--color-primary)] outline-none"
                    >
                        <option value="">Select a category...</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat['id'] }}">{{ $cat['name'] }}</option>
                        @endforeach
                    </select>
                    @error('categoryId') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Partner --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Partner</label>
                    <select
                        wire:model="partnerId"
                        class="w-full rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-900 px-3 py-2 text-sm text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-[var(--color-primary)]/30 focus:border-[var(--color-primary)] outline-none"
                    >
                        <option value="">No partner</option>
                        @foreach($partners as $partner)
                            <option value="{{ $partner['id'] }}">{{ $partner['name'] }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Receipt Upload --}}
                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Receipt</label>

                    {{-- Existing Receipt Preview --}}
                    @if($existingReceipt)
                        <div class="mb-3 p-3 border border-gray-200 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-900">
                            <div class="flex items-center gap-3">
                                <img src="{{ asset('storage/' . $existingReceipt) }}" alt="Receipt" class="w-20 h-20 object-cover rounded-lg border border-gray-200 dark:border-gray-600" />
                                <div class="flex-1">
                                    <p class="text-sm text-gray-700 dark:text-gray-300 font-medium">Current Receipt</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ basename($existingReceipt) }}</p>
                                </div>
                                <button
                                    type="button"
                                    wire:click="removeReceipt"
                                    wire:confirm="Are you sure you want to remove this receipt?"
                                    class="p-2 rounded-lg text-red-500 hover:bg-red-50 dark:hover:bg-red-500/10 transition"
                                    title="Remove receipt"
                                >
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </div>
                        </div>
                    @endif

                    {{-- New Receipt Preview --}}
                    @if($receipt)
                        <div class="mb-3 p-3 border border-blue-200 dark:border-blue-600 rounded-lg bg-blue-50 dark:bg-blue-900/20">
                            <div class="flex items-center gap-3">
                                <img src="{{ $receipt->temporaryUrl() }}" alt="New Receipt" class="w-20 h-20 object-cover rounded-lg border border-blue-200 dark:border-blue-600" />
                                <div class="flex-1">
                                    <p class="text-sm text-blue-700 dark:text-blue-300 font-medium">New Receipt (Unsaved)</p>
                                    <p class="text-xs text-blue-500 dark:text-blue-400">{{ $receipt->getClientOriginalName() }}</p>
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- File Input --}}
                    <div class="relative">
                        <input
                            wire:model="receipt"
                            type="file"
                            accept="image/jpeg,image/png,image/jpg,image/gif"
                            class="block w-full text-sm text-gray-500 dark:text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-[var(--color-primary)]/10 file:text-[var(--color-primary)] hover:file:bg-[var(--color-primary)]/20 cursor-pointer"
                        />
                        <div wire:loading wire:target="receipt" class="absolute right-3 top-1/2 -translate-y-1/2">
                            <svg class="animate-spin w-5 h-5 text-[var(--color-primary)]" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"/></svg>
                        </div>
                    </div>
                    <p class="text-xs text-gray-400 mt-1">Accepts JPEG, PNG, JPG, GIF. Max size: 2MB</p>
                    @error('receipt') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        {{-- Actions --}}
        <div class="flex justify-end items-center gap-3 pb-8">
            <a href="{{ route('admin.expenses.index') }}" wire:navigate class="px-5 py-2.5 rounded-lg border border-gray-200 dark:border-gray-600 text-sm text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                Cancel
            </a>
            <button
                type="submit"
                wire:loading.attr="disabled"
                class="px-6 py-2.5 rounded-lg bg-[var(--color-primary)] text-white text-sm font-semibold hover:bg-[var(--color-primary-dark)] transition disabled:opacity-60"
            >
                <span wire:loading.remove wire:target="save">{{ $expenseId ? 'Update Expense' : 'Create Expense' }}</span>
                <span wire:loading wire:target="save">Saving...</span>
            </button>
        </div>
    </form>
</div>
