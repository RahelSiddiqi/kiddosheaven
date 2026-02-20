<div>
    <div class="rounded-2xl border border-gray-200 bg-white pt-4 dark:border-gray-800 dark:bg-white/[0.03]">
        <!-- Header -->
        <div class="flex flex-col gap-2 px-5 mb-4 sm:flex-row sm:items-center sm:justify-between sm:px-6">
            <div>
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">Flash Sales</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Manage time-limited promotions and flash deals</p>
            </div>
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
                @if(Route::has('admin.flash-sales.create'))
                    <a href="{{ route('admin.flash-sales.create') }}"
                        class="h-10.5 inline-flex items-center justify-center rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 hover:text-gray-800 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03] dark:hover:text-gray-200">
                        <svg class="mr-2" width="20" height="20" viewBox="0 0 20 20" fill="none">
                            <path d="M10 4.16667V15.8333M4.16667 10H15.8333" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        Create Flash Sale
                    </a>
                @endif
            </div>
        </div>

        <!-- Table -->
        <div class="overflow-hidden">
            <div class="max-w-full px-5 overflow-x-auto">
                <table class="min-w-full">
                    <thead>
                        <tr class="border-gray-200 border-y dark:border-gray-700">
                            <th scope="col" class="px-4 py-3 font-normal text-gray-500 text-start text-theme-sm dark:text-gray-400">Name</th>
                            <th scope="col" class="px-4 py-3 font-normal text-gray-500 text-start text-theme-sm dark:text-gray-400">Discount</th>
                            <th scope="col" class="px-4 py-3 font-normal text-gray-500 text-start text-theme-sm dark:text-gray-400">Start Time</th>
                            <th scope="col" class="px-4 py-3 font-normal text-gray-500 text-start text-theme-sm dark:text-gray-400">End Time</th>
                            <th scope="col" class="px-4 py-3 font-normal text-gray-500 text-start text-theme-sm dark:text-gray-400">Status</th>
                            <th scope="col" class="relative px-4 py-3 capitalize"><span class="sr-only">Actions</span></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse ($flashSales as $flashSale)
                            @php
                                $now = now();
                                $startTime = $flashSale->start_time ?? $flashSale->starts_at ?? null;
                                $endTime = $flashSale->end_time ?? $flashSale->ends_at ?? null;

                                if ($startTime && $endTime) {
                                    if ($now < $startTime) {
                                        $statusLabel = 'Scheduled';
                                        $statusClass = 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400';
                                    } elseif ($now > $endTime) {
                                        $statusLabel = 'Expired';
                                        $statusClass = 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400';
                                    } elseif ($flashSale->is_active ?? true) {
                                        $statusLabel = 'Active';
                                        $statusClass = 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400';
                                    } else {
                                        $statusLabel = 'Inactive';
                                        $statusClass = 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400';
                                    }
                                } else {
                                    $statusLabel = ($flashSale->is_active ?? true) ? 'Active' : 'Inactive';
                                    $statusClass = ($flashSale->is_active ?? true)
                                        ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400'
                                        : 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400';
                                }
                            @endphp
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">
                                <td class="px-4 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-lg bg-orange-50 dark:bg-orange-500/10 flex items-center justify-center">
                                            <svg class="w-5 h-5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M13 10V3L4 14h7v7l9-11h-7z" />
                                            </svg>
                                        </div>
                                        <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $flashSale->name ?? $flashSale->title ?? 'Unnamed Sale' }}</div>
                                    </div>
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap">
                                    <div class="text-sm font-semibold text-gray-900 dark:text-white">
                                        {{ $flashSale->discount_percentage ?? $flashSale->discount ?? 0 }}%
                                    </div>
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900 dark:text-white">
                                        @if($startTime)
                                            {{ \Carbon\Carbon::parse($startTime)->format('M d, Y H:i') }}
                                        @else
                                            -
                                        @endif
                                    </div>
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900 dark:text-white">
                                        @if($endTime)
                                            {{ \Carbon\Carbon::parse($endTime)->format('M d, Y H:i') }}
                                        @else
                                            -
                                        @endif
                                    </div>
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap">
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusClass }}">
                                        {{ $statusLabel }}
                                    </span>
                                </td>
                                <td class="px-4 py-4 text-sm font-medium text-right whitespace-nowrap">
                                    <div class="flex items-center gap-2 justify-end">
                                        @if(Route::has('admin.flash-sales.edit'))
                                            <a href="{{ route('admin.flash-sales.edit', $flashSale) }}"
                                                class="p-1.5 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors" title="Edit">
                                                <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-16 text-center">
                                    <div class="w-16 h-16 mx-auto mb-4 rounded-2xl bg-gray-100 dark:bg-gray-800 flex items-center justify-center">
                                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M13 10V3L4 14h7v7l9-11h-7z" />
                                        </svg>
                                    </div>
                                    <p class="text-sm font-medium text-gray-800 dark:text-white/90">No flash sales found</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Create your first flash sale to get started</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination -->
        @if ($flashSales->hasPages())
            <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                {{ $flashSales->links() }}
            </div>
        @endif
    </div>
</div>
