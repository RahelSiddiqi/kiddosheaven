<div>
    <x-admin.ui.entity-header
        title="Reports"
        subtitle="Financial overview and analytics"
        :breadcrumbs="[
            ['label' => 'Dashboard', 'url' => route('admin.dashboard')],
            ['label' => 'Reports']
        ]"
    />

    {{-- Date Range Filter --}}
    <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03] p-5 mb-6">
        <div class="flex flex-col sm:flex-row sm:items-end gap-4">
            <div class="flex-1 max-w-xs">
                <label for="dateFrom" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">From Date</label>
                <input
                    type="date"
                    id="dateFrom"
                    wire:model.live="dateFrom"
                    class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-blue-800"
                />
            </div>
            <div class="flex-1 max-w-xs">
                <label for="dateTo" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">To Date</label>
                <input
                    type="date"
                    id="dateTo"
                    wire:model.live="dateTo"
                    class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-blue-800"
                />
            </div>
            <div>
                <a href="{{ route('admin.reports.index') }}"
                   class="h-11 inline-flex items-center justify-center rounded-lg border border-gray-300 bg-white px-6 py-2.5 text-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03]">
                    Reset
                </a>
            </div>
        </div>
    </div>

    {{-- Stats Row --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <x-admin.ui.stat-card
            title="Total Revenue"
            :value="number_format($totalRevenue, 0)"
            subtitle="from delivered orders"
            icon="currency"
            color="green"
        />
        <x-admin.ui.stat-card
            title="Total Expenses"
            :value="number_format($totalExpenses, 0)"
            subtitle="approved expenses"
            icon="cart"
            color="red"
        />
        <x-admin.ui.stat-card
            title="Total Investments"
            :value="number_format($totalInvestments, 0)"
            subtitle="capital invested"
            icon="trending"
            color="blue"
        />
        <x-admin.ui.stat-card
            title="Net Profit"
            :value="number_format($netProfit, 0)"
            subtitle="revenue - expenses"
            icon="profit"
            :color="$netProfit >= 0 ? 'green' : 'red'"
        />
    </div>

    {{-- Report Links --}}
    <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
        <div class="p-5 border-b border-gray-100 dark:border-gray-800">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">Available Reports</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Select a report to view detailed analytics</p>
        </div>
        <div class="p-5">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                {{-- Sales Reports --}}
                <a href="{{ route('admin.reports.sales') }}"
                   class="group p-4 rounded-xl border border-gray-200 bg-gray-50 hover:bg-brand-50 hover:border-brand-200 dark:border-gray-700 dark:bg-gray-800 dark:hover:bg-brand-500/10 dark:hover:border-brand-800 transition-all">
                    <div class="flex items-center gap-3">
                        <div class="flex items-center justify-center w-10 h-10 rounded-lg bg-green-100 text-green-600 dark:bg-green-500/10 dark:text-green-400">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                            </svg>
                        </div>
                        <div>
                            <h4 class="font-medium text-gray-800 dark:text-white/90 group-hover:text-brand-600 dark:group-hover:text-brand-400">Sales Report</h4>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Revenue and order analytics</p>
                        </div>
                    </div>
                </a>

                {{-- Expenses Report --}}
                <a href="{{ route('admin.reports.expenses') }}"
                   class="group p-4 rounded-xl border border-gray-200 bg-gray-50 hover:bg-brand-50 hover:border-brand-200 dark:border-gray-700 dark:bg-gray-800 dark:hover:bg-brand-500/10 dark:hover:border-brand-800 transition-all">
                    <div class="flex items-center gap-3">
                        <div class="flex items-center justify-center w-10 h-10 rounded-lg bg-red-100 text-red-600 dark:bg-red-500/10 dark:text-red-400">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </div>
                        <div>
                            <h4 class="font-medium text-gray-800 dark:text-white/90 group-hover:text-brand-600 dark:group-hover:text-brand-400">Expenses Report</h4>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Expense tracking and analysis</p>
                        </div>
                    </div>
                </a>

                {{-- Profit & Loss --}}
                <a href="{{ route('admin.reports.profit-loss') }}"
                   class="group p-4 rounded-xl border border-gray-200 bg-gray-50 hover:bg-brand-50 hover:border-brand-200 dark:border-gray-700 dark:bg-gray-800 dark:hover:bg-brand-500/10 dark:hover:border-brand-800 transition-all">
                    <div class="flex items-center gap-3">
                        <div class="flex items-center justify-center w-10 h-10 rounded-lg bg-purple-100 text-purple-600 dark:bg-purple-500/10 dark:text-purple-400">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                        </div>
                        <div>
                            <h4 class="font-medium text-gray-800 dark:text-white/90 group-hover:text-brand-600 dark:group-hover:text-brand-400">Profit & Loss</h4>
                            <p class="text-xs text-gray-500 dark:text-gray-400">P&L statements</p>
                        </div>
                    </div>
                </a>

                {{-- Inventory Report --}}
                <a href="{{ route('admin.reports.inventory') }}"
                   class="group p-4 rounded-xl border border-gray-200 bg-gray-50 hover:bg-brand-50 hover:border-brand-200 dark:border-gray-700 dark:bg-gray-800 dark:hover:bg-brand-500/10 dark:hover:border-brand-800 transition-all">
                    <div class="flex items-center gap-3">
                        <div class="flex items-center justify-center w-10 h-10 rounded-lg bg-blue-100 text-blue-600 dark:bg-blue-500/10 dark:text-blue-400">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                            </svg>
                        </div>
                        <div>
                            <h4 class="font-medium text-gray-800 dark:text-white/90 group-hover:text-brand-600 dark:group-hover:text-brand-400">Inventory Report</h4>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Stock levels and movements</p>
                        </div>
                    </div>
                </a>

                {{-- Products Report --}}
                <a href="{{ route('admin.reports.products') }}"
                   class="group p-4 rounded-xl border border-gray-200 bg-gray-50 hover:bg-brand-50 hover:border-brand-200 dark:border-gray-700 dark:bg-gray-800 dark:hover:bg-brand-500/10 dark:hover:border-brand-800 transition-all">
                    <div class="flex items-center gap-3">
                        <div class="flex items-center justify-center w-10 h-10 rounded-lg bg-yellow-100 text-yellow-600 dark:bg-yellow-500/10 dark:text-yellow-400">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                            </svg>
                        </div>
                        <div>
                            <h4 class="font-medium text-gray-800 dark:text-white/90 group-hover:text-brand-600 dark:group-hover:text-brand-400">Products Report</h4>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Product performance metrics</p>
                        </div>
                    </div>
                </a>

                {{-- Partners Report --}}
                <a href="{{ route('admin.reports.partners') }}"
                   class="group p-4 rounded-xl border border-gray-200 bg-gray-50 hover:bg-brand-50 hover:border-brand-200 dark:border-gray-700 dark:bg-gray-800 dark:hover:bg-brand-500/10 dark:hover:border-brand-800 transition-all">
                    <div class="flex items-center gap-3">
                        <div class="flex items-center justify-center w-10 h-10 rounded-lg bg-orange-100 text-orange-600 dark:bg-orange-500/10 dark:text-orange-400">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                        </div>
                        <div>
                            <h4 class="font-medium text-gray-800 dark:text-white/90 group-hover:text-brand-600 dark:group-hover:text-brand-400">Partners Report</h4>
                            <p class="text-xs text-gray-500 dark:text-gray-400">Partner commissions and payouts</p>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </div>
</div>
