@extends('admin.layouts.app')

@section('title', 'Expiring Items Report — Kiddo\'s Heaven')

@section('content')
<div class="grid grid-cols-12 gap-4 md:gap-6">
    <div class="col-span-12">
        <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
            <!-- Header -->
            <div class="flex flex-col gap-2 px-5 mb-4 sm:flex-row sm:items-center sm:justify-between sm:px-6 pt-4">
                <div>
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">Expiring Items Report</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Monitor items approaching expiry date</p>
                </div>
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
                    <form method="GET" class="flex gap-2">
                        <select name="days" onchange="this.form.submit()"
                            class="h-10.5 rounded-lg border border-gray-300 bg-transparent px-3 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-blue-800">
                            <option value="7" {{ $days == 7 ? 'selected' : '' }}>Next 7 days</option>
                            <option value="14" {{ $days == 14 ? 'selected' : '' }}>Next 14 days</option>
                            <option value="30" {{ $days == 30 ? 'selected' : '' }}>Next 30 days</option>
                            <option value="60" {{ $days == 60 ? 'selected' : '' }}>Next 60 days</option>
                            <option value="90" {{ $days == 90 ? 'selected' : '' }}>Next 90 days</option>
                        </select>
                    </form>
                </div>
            </div>

            @if($batches->count() > 0)
                <!-- Summary -->
                <div class="px-6 pb-4">
                    <div class="rounded-xl border border-yellow-200 bg-yellow-50 p-4 dark:border-yellow-700/50 dark:bg-yellow-500/10">
                        <div class="flex items-center gap-3">
                            <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                            <div>
                                <h4 class="font-semibold text-yellow-800 dark:text-yellow-200">{{ $batches->count() }} items expiring within {{ $days }} days</h4>
                                <p class="text-sm text-yellow-600 dark:text-yellow-400">Total value at risk: {{ number_format($batches->sum(fn($b) => $b->remaining_quantity * $b->unit_cost), 2) }} BDT</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Table -->
                <div class="overflow-hidden">
                    <div class="max-w-full overflow-x-auto">
                        <table class="min-w-full">
                            <thead>
                                <tr class="border-gray-200 border-y dark:border-gray-700">
                                    <th scope="col" class="px-4 py-3 font-normal text-gray-500 text-start text-theme-sm dark:text-gray-400">Batch #</th>
                                    <th scope="col" class="px-4 py-3 font-normal text-gray-500 text-start text-theme-sm dark:text-gray-400">Product</th>
                                    <th scope="col" class="px-4 py-3 font-normal text-gray-500 text-start text-theme-sm dark:text-gray-400">Remaining Qty</th>
                                    <th scope="col" class="px-4 py-3 font-normal text-gray-500 text-start text-theme-sm dark:text-gray-400">Unit Cost</th>
                                    <th scope="col" class="px-4 py-3 font-normal text-gray-500 text-start text-theme-sm dark:text-gray-400">Total Value</th>
                                    <th scope="col" class="px-4 py-3 font-normal text-gray-500 text-start text-theme-sm dark:text-gray-400">Days Left</th>
                                    <th scope="col" class="px-4 py-3 font-normal text-gray-500 text-start text-theme-sm dark:text-gray-400">Expiry Date</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach($batches as $batch)
                                    <tr class="{{ \Carbon\Carbon::parse($batch->expiry_date)->diffInDays(now()) <= 7 ? 'bg-yellow-50 dark:bg-yellow-500/5' : '' }}">
                                        <td class="px-4 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $batch->batch_number }}</div>
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900 dark:text-white">{{ $batch->product->name }}</div>
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap">
                                            <div class="text-sm font-bold text-gray-900 dark:text-white">{{ $batch->remaining_quantity }}</div>
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-500 dark:text-gray-400">{{ number_format($batch->unit_cost, 2) }} BDT</div>
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap">
                                            <div class="text-sm font-bold text-red-600">{{ number_format($batch->remaining_quantity * $batch->unit_cost, 2) }} BDT</div>
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap">
                                            @php $daysLeft = \Carbon\Carbon::parse($batch->expiry_date)->diffInDays(now()); @endphp
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $daysLeft <= 7 ? 'bg-red-100 text-red-600' : ($daysLeft <= 14 ? 'bg-yellow-100 text-yellow-600' : 'bg-green-100 text-green-600') }}">
                                                {{ $daysLeft }} days
                                            </span>
                                        </td>
                                        <td class="px-4 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                                {{ \Carbon\Carbon::parse($batch->expiry_date)->format('M d, Y') }}
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @else
                <div class="px-6 py-12 text-center">
                    <svg class="w-16 h-16 mx-auto text-gray-300 dark:text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <h4 class="text-lg font-medium text-gray-800 dark:text-white/90">No Expiring Items</h4>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">No items are expiring within the next {{ $days }} days</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
