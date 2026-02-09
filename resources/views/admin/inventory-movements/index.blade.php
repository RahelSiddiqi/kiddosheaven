@extends('admin.layouts.app')

@section('title', 'Inventory Movements — Kiddo\'s Heaven')

@section('content')
<div class="grid grid-cols-12 gap-4 md:gap-6">
    <div class="col-span-12">
        <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
            <!-- Header -->
            <div class="flex flex-col gap-2 px-5 mb-4 sm:flex-row sm:items-center sm:justify-between sm:px-6 pt-4">
                <div>
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">Inventory Movements</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Track all inventory changes and stock movements</p>
                </div>
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
                    <form method="GET" class="flex gap-2">
                        <select name="product_id" onchange="this.form.submit()"
                            class="h-10.5 rounded-lg border border-gray-300 bg-transparent px-3 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-blue-800">
                            <option value="">All Products</option>
                            @foreach($products as $product)
                                <option value="{{ $product->id }}" {{ request('product_id') == $product->id ? 'selected' : '' }}>
                                    {{ $product->name }}
                                </option>
                            @endforeach
                        </select>
                        <select name="type" onchange="this.form.submit()"
                            class="h-10.5 rounded-lg border border-gray-300 bg-transparent px-3 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-blue-800">
                            <option value="">All Types</option>
                            <option value="purchase" {{ request('type') == 'purchase' ? 'selected' : '' }}>Purchase</option>
                            <option value="sale" {{ request('type') == 'sale' ? 'selected' : '' }}>Sale</option>
                            <option value="adjustment" {{ request('type') == 'adjustment' ? 'selected' : '' }}>Adjustment</option>
                            <option value="return" {{ request('type') == 'return' ? 'selected' : '' }}>Return</option>
                            <option value="transfer" {{ request('type') == 'transfer' ? 'selected' : '' }}>Transfer</option>
                        </select>
                    </form>
                </div>
            </div>

            <!-- Table -->
            <div class="overflow-hidden">
                <div class="max-w-full overflow-x-auto">
                    <table class="min-w-full">
                        <thead>
                            <tr class="border-gray-200 border-y dark:border-gray-700">
                                <th scope="col" class="px-4 py-3 font-normal text-gray-500 text-start text-theme-sm dark:text-gray-400">Date</th>
                                <th scope="col" class="px-4 py-3 font-normal text-gray-500 text-start text-theme-sm dark:text-gray-400">Product</th>
                                <th scope="col" class="px-4 py-3 font-normal text-gray-500 text-start text-theme-sm dark:text-gray-400">Type</th>
                                <th scope="col" class="px-4 py-3 font-normal text-gray-500 text-start text-theme-sm dark:text-gray-400">Quantity</th>
                                <th scope="col" class="px-4 py-3 font-normal text-gray-500 text-start text-theme-sm dark:text-gray-400">Batch</th>
                                <th scope="col" class="px-4 py-3 font-normal text-gray-500 text-start text-theme-sm dark:text-gray-400">Reference</th>
                                <th scope="col" class="px-4 py-3 font-normal text-gray-500 text-start text-theme-sm dark:text-gray-400">User</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse($movements as $movement)
                                <tr>
                                    <td class="px-4 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-500 dark:text-gray-400">
                                            {{ \Carbon\Carbon::parse($movement->created_at)->format('M d, Y H:i') }}
                                        </div>
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900 dark:text-white">
                                            {{ $movement->product->name }}
                                        </div>
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap">
                                        @switch($movement->movement_type)
                                            @case('purchase')
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-600 dark:bg-green-500/15 dark:text-green-500">
                                                    Purchase
                                                </span>
                                                @break
                                            @case('sale')
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-600 dark:bg-red-500/15 dark:text-red-500">
                                                    Sale
                                                </span>
                                                @break
                                            @case('adjustment')
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-600 dark:bg-yellow-500/15 dark:text-yellow-500">
                                                    Adjustment
                                                </span>
                                                @break
                                            @case('return')
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-600 dark:bg-blue-500/15 dark:text-blue-500">
                                                    Return
                                                </span>
                                                @break
                                            @case('transfer')
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-purple-100 text-purple-600 dark:bg-purple-500/15 dark:text-purple-500">
                                                    Transfer
                                                </span>
                                                @break
                                        @endswitch
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium {{ $movement->movement_type == 'sale' ? 'text-red-600' : 'text-green-600' }}">
                                            {{ $movement->movement_type == 'sale' ? '-' : '+' }}{{ $movement->quantity }}
                                        </div>
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-500 dark:text-gray-400">
                                            {{ $movement->batch ? $movement->batch->batch_number : '-' }}
                                        </div>
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-500 dark:text-gray-400">
                                            @if($movement->reference_type)
                                                {{ $movement->reference_type }} #{{ $movement->reference_id }}
                                            @else
                                                -
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-500 dark:text-gray-400">
                                            {{ $movement->user ? $movement->user->name : 'System' }}
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
                                        No inventory movements found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pagination -->
            @if($movements->hasPages())
                <div class="px-6 py-4 border-t border-gray-200 dark:border-white/5">
                    {{ $movements->appends(request()->query())->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
