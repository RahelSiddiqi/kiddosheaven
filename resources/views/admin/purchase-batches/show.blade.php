@extends('admin.layouts.app')

@section('title', 'Batch ' . $purchaseBatch->batch_number . ' — Purchase Batch Details')

@section('content')
	{{-- Entity Header with Navigation Links --}}
	<x-admin.ui.entity-header :title="'Batch ' . $purchaseBatch->batch_number" :subtitle="'Purchased on ' . $purchaseBatch->purchase_date->format('M d, Y')" :badge="$purchaseBatch->remaining_quantity > 0 ? 'Active' : 'Exhausted'" :badgeColor="$purchaseBatch->remaining_quantity > 0 ? 'green' : 'gray'" :breadcrumbs="[
	    ['label' => 'Dashboard', 'url' => route('admin.dashboard')],
	    ['label' => 'Purchase Batches', 'url' => route('admin.purchase-batches.index')],
	    ['label' => $purchaseBatch->batch_number],
	]"
		:actions="[
		    [
		        'label' => 'Edit Batch',
		        'url' => route('admin.purchase-batches.edit', $purchaseBatch),
		        'primary' => true,
		        'icon' =>
		            '<svg class=\'w-4 h-4\' fill=\'none\' stroke=\'currentColor\' viewBox=\'0 0 24 24\'><path stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z\'/></svg>',
		    ],
		]"
		:links="[
											['label' => 'Overview', 'url' => route('admin.purchase-batches.show', $purchaseBatch), 'active' => true],
											['label' => 'Product', 'url' => $purchaseBatch->product ? route('admin.products.show', $purchaseBatch->product) : '#'],
											['label' => 'Stock Movements', 'url' => route('admin.inventory.movements.index', ['batch_id' => $purchaseBatch->id]), 'count' => $purchaseBatch->movements->count()],
											['label' => 'Variant', 'url' => $purchaseBatch->variant ? route('admin.products.variants.show', [$purchaseBatch->product, $purchaseBatch->variant]) : '#'],
										]"
		backUrl="{{ route('admin.purchase-batches.index') }}" />

	{{-- Quick Actions --}}
	<div class="flex flex-wrap gap-3 mb-6">
		<a href="{{ route('admin.purchase-batches.edit', $purchaseBatch) }}"
			class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
			<svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
				<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
					d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
			</svg>
			Edit Batch
		</a>
		<a href="{{ route('admin.inventory.movements.create', ['batch_id' => $purchaseBatch->id]) }}"
			class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition">
			<svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
				<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
			</svg>
			Add Stock
		</a>
		<a href="{{ route('admin.inventory.movements.create', ['batch_id' => $purchaseBatch->id, 'type' => 'adjustment']) }}"
			class="inline-flex items-center px-4 py-2 bg-orange-500 text-white rounded-lg hover:bg-orange-600 transition">
			<svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
				<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
					d="M15 12H9m12 0A9 9 0 11 3 12a9 9 0 0118 0z" />
			</svg>
			Adjust Quantity
		</a>
		@if ($purchaseBatch->remaining_quantity > 0)
			<form method="POST" action="{{ route('admin.purchase-batches.mark-exhausted', $purchaseBatch) }}"
				onsubmit="return confirm('Mark this batch as exhausted?');">
				@csrf
				@method('PATCH')
				<button type="submit"
					class="inline-flex items-center px-4 py-2 bg-gray-700 text-white rounded-lg hover:bg-gray-800 transition">
					<svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
					</svg>
					Mark as Exhausted
				</button>
			</form>
		@endif
	</div>
	<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
		<x-admin.ui.stat-card title="Initial Quantity" :value="$purchaseBatch->quantity_received" subtitle="units purchased" icon="box"
			color="blue" />
		<x-admin.ui.stat-card title="Remaining Stock" :value="$purchaseBatch->remaining_quantity" :subtitle="'৳' . number_format($remainingValue, 2) . ' value'" icon="stock" :color="$purchaseBatch->remaining_quantity > 0 ? 'green' : 'gray'" />
		<x-admin.ui.stat-card title="Units Sold" :value="$quantitySold" :subtitle="'৳' . number_format($soldValue, 2) . ' COGS'" icon="cart" :color="$quantitySold > 0 ? 'purple' : 'gray'" />
		<x-admin.ui.stat-card title="Unit Cost" :value="'৳' . number_format($purchaseBatch->unit_cost, 2)" subtitle="per unit" icon="currency" color="blue" />
	</div>

	{{-- Timeline --}}
	<div class="mb-6">
		<div class="rounded-xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-white/[0.03] p-6">
			<h4 class="text-base font-semibold text-gray-800 dark:text-white/90 mb-4">Batch Activity Timeline</h4>
			<x-admin.ui.timeline :steps="[
			    [
			        'label' => 'Purchased',
			        'date' => $purchaseBatch->purchase_date->format('M d, Y'),
			        'status' => 'completed',
			        'description' =>
			            $purchaseBatch->quantity_received . ' units @ ৳' . number_format($purchaseBatch->unit_cost, 2),
			        'badge' => 'Total: ৳' . number_format($purchaseBatch->quantity_received * $purchaseBatch->unit_cost, 2),
			        'badgeColor' => 'blue',
			    ],
			    [
			        'label' => 'In Stock',
			        'status' => $purchaseBatch->remaining_quantity > 0 ? 'current' : 'completed',
			        'description' => $purchaseBatch->remaining_quantity . ' units remaining',
			        'badge' => 'Value: ৳' . number_format($remainingValue, 2),
			        'badgeColor' => 'green',
			    ],
			    [
			        'label' => 'Sold',
			        'status' => $quantitySold > 0 ? 'completed' : 'upcoming',
			        'description' => $quantitySold . ' units sold via FIFO',
			        'badge' => 'COGS: ৳' . number_format($soldValue, 2),
			        'badgeColor' => 'purple',
			    ],
			    [
			        'label' => $purchaseBatch->remaining_quantity == 0 ? 'Exhausted' : 'Pending Sale',
			        'status' => $purchaseBatch->remaining_quantity == 0 ? 'completed' : 'upcoming',
			        'description' => $purchaseBatch->remaining_quantity == 0 ? 'All units sold' : 'Awaiting sales',
			    ],
			]" :horizontal="true" />
		</div>
	</div>

	<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
		<!-- Left Column - Main Content -->
		<div class="lg:col-span-2 space-y-6">
			{{-- Batch Info Card --}}
			<x-admin.ui.info-card title="Batch Information" :columns="2" :items="array_filter([
			    ['label' => 'Batch Number', 'value' => $purchaseBatch->batch_number, 'mono' => true],
			    [
			        'label' => 'Product',
			        'value' => $purchaseBatch->product->name ?? 'N/A',
			        'url' => $purchaseBatch->product ? route('admin.products.show', $purchaseBatch->product) : null,
			    ],
			    $purchaseBatch->variant
			        ? [
			            'label' => 'Variant',
			            'value' => $purchaseBatch->variant->full_name,
			            'url' => route('admin.products.variants.show', [$purchaseBatch->product, $purchaseBatch->variant]),
			        ]
			        : null,
			    $purchaseBatch->partner
			        ? [
			            'label' => 'Partner',
			            'value' => $purchaseBatch->partner->name,
			            'url' => route('admin.partners.show', $purchaseBatch->partner),
			        ]
			        : null,
			    $purchaseBatch->supplier ? ['label' => 'Supplier', 'value' => $purchaseBatch->supplier] : null,
			    $purchaseBatch->supplier_invoice_number
			        ? ['label' => 'Invoice Number', 'value' => $purchaseBatch->supplier_invoice_number, 'mono' => true]
			        : null,
			])" />

			{{-- Stock Movements --}}
			@if ($purchaseBatch->movements->count() > 0)
				<x-admin.ui.data-table title="Stock Movements ({{ $purchaseBatch->movements->count() }})" :columns="['Date', 'Type', 'Quantity', 'Reference']"
					:columnAligns="['left', 'left', 'center', 'left']" :hasData="true" empty="No movements recorded">
					@foreach ($purchaseBatch->movements->sortByDesc('created_at') as $movement)
						<tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50">
							<td class="px-5 py-3 text-sm text-gray-600 dark:text-gray-400">
								{{ $movement->created_at->format('M d, Y H:i') }}
							</td>
							<td class="px-5 py-3">
								<x-admin.ui.badge :color="match ($movement->movement_type) {
								    'purchase' => 'blue',
								    'sale' => 'green',
								    'adjustment' => 'orange',
								    'return' => 'purple',
								    'damage' => 'red',
								    default => 'gray',
								}">
									{{ ucfirst($movement->movement_type) }}
								</x-admin.ui.badge>
							</td>
							<td class="px-5 py-3 text-center">
								<span
									class="text-sm font-medium {{ $movement->quantity_change > 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
									{{ $movement->quantity_change > 0 ? '+' : '' }}{{ $movement->quantity_change }}
								</span>
							</td>
							<td class="px-5 py-3 text-sm text-gray-600 dark:text-gray-400">
								@if ($movement->order_id)
									<a href="{{ route('admin.orders.show', $movement->order_id) }}"
										class="text-brand-600 dark:text-brand-400 hover:underline">
										Order #{{ $movement->order_id }}
									</a>
								@elseif($movement->reference_number)
									{{ $movement->reference_number }}
								@else
									-
								@endif
							</td>
						</tr>
					@endforeach
				</x-admin.ui.data-table>
			@else
				<x-admin.ui.empty-state title="No stock movements" description="This batch has no recorded stock movements yet."
					icon="chart" />
			@endif
		</div>

		{{-- Right Column - Sidebar --}}
		<div class="space-y-6">
			{{-- Stock Summary --}}
			<x-admin.ui.info-card title="Stock Summary" :items="[
			    ['label' => 'Purchased', 'value' => $purchaseBatch->quantity_received . ' units', 'badge' => 'blue'],
			    ['label' => 'Sold', 'value' => $quantitySold . ' units', 'badge' => $quantitySold > 0 ? 'purple' : 'gray'],
			    [
			        'label' => 'Remaining',
			        'value' => $purchaseBatch->remaining_quantity . ' units',
			        'badge' => $purchaseBatch->remaining_quantity > 0 ? 'green' : 'gray',
			    ],
			    [
			        'label' => 'Reserved',
			        'value' => $purchaseBatch->quantity_reserved . ' units',
			        'badge' => $purchaseBatch->quantity_reserved > 0 ? 'orange' : 'gray',
			    ],
			]" />

			{{-- Cost Summary --}}
			<x-admin.ui.info-card title="Cost Summary" :items="[
			    ['label' => 'Unit Cost', 'value' => '৳' . number_format($purchaseBatch->unit_cost, 2), 'mono' => true],
			    [
			        'label' => 'Total Purchase',
			        'value' => '৳' . number_format($purchaseBatch->quantity_received * $purchaseBatch->unit_cost, 2),
			        'mono' => true,
			        'badge' => 'blue',
			    ],
			    ['label' => 'COGS (Sold)', 'value' => '৳' . number_format($soldValue, 2), 'mono' => true, 'badge' => 'purple'],
			    [
			        'label' => 'Remaining Value',
			        'value' => '৳' . number_format($remainingValue, 2),
			        'mono' => true,
			        'badge' => $purchaseBatch->remaining_quantity > 0 ? 'green' : 'gray',
			    ],
			]" />

			{{-- Dates --}}
			<x-admin.ui.info-card title="Important Dates" :items="array_filter([
			    ['label' => 'Purchase Date', 'value' => $purchaseBatch->purchase_date->format('M d, Y')],
			    $purchaseBatch->manufacture_date
			        ? ['label' => 'Manufacture Date', 'value' => $purchaseBatch->manufacture_date->format('M d, Y')]
			        : null,
			    $purchaseBatch->expiry_date
			        ? [
			            'label' => 'Expiry Date',
			            'value' => $purchaseBatch->expiry_date->format('M d, Y'),
			            'badge' => $purchaseBatch->expiry_date->isPast()
			                ? 'red'
			                : ($purchaseBatch->expiry_date->diffInDays() < 30
			                    ? 'orange'
			                    : 'green'),
			        ]
			        : null,
			])" />

			{{-- Notes --}}
			@if ($purchaseBatch->notes)
				<div class="rounded-xl border border-gray-200 dark:border-gray-800 bg-white dark:bg-white/[0.03]">
					<div class="px-5 py-3.5 border-b border-gray-200 dark:border-gray-800">
						<h4 class="text-sm font-semibold text-gray-800 dark:text-white/90">Notes</h4>
					</div>
					<div class="p-5">
						<p class="text-sm text-gray-600 dark:text-gray-400 leading-relaxed">{{ $purchaseBatch->notes }}</p>
					</div>
				</div>
			@endif

			{{-- Metadata --}}
			<x-admin.ui.info-card title="Metadata" :items="[
			    [
			        'label' => 'Status',
			        'value' => ucfirst($purchaseBatch->status ?? 'active'),
			        'badge' => $purchaseBatch->remaining_quantity > 0 ? 'green' : 'gray',
			    ],
			    ['label' => 'Created', 'value' => $purchaseBatch->created_at->format('M d, Y')],
			    ['label' => 'Updated', 'value' => $purchaseBatch->updated_at->format('M d, Y')],
			]" />
		</div>
	</div>
@endsection
