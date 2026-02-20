@extends('admin.layouts.app')

@section('content')
	{{-- Stats Row with Linked Cards --}}
	<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
		<x-admin.ui.stat-card title="Total Revenue" :value="'৳' . number_format($stats['totalRevenue'], 0)" :subtitle="$stats['revenueChange']" icon="currency" :color="$stats['revenueChangeType'] === 'positive' ? 'green' : 'red'"
			:trend="$stats['revenueChangeType']" :url="route('admin.reports.sales')" />
		<x-admin.ui.stat-card title="Orders This Month" :value="$stats['ordersThisMonth']" subtitle="orders placed" icon="cart" color="blue"
			:url="route('admin.orders.index')" />
		<x-admin.ui.stat-card title="Total Customers" :value="$stats['totalCustomers']" :subtitle="$stats['customersChange']" icon="users" color="purple"
			:trend="$stats['customersChangeType']" :url="route('admin.customers.index')" />
		<x-admin.ui.stat-card title="Low Stock Alerts" :value="$stats['lowStockCount']" subtitle="items need restocking" icon="alert"
			:color="$stats['lowStockCount'] > 0 ? 'orange' : 'green'" url="{{ route('admin.inventory.index', ['filter' => 'low']) }}" />
	</div>

	{{-- Original Dashboard Widgets (kept for charts/data visualization) --}}
	<div class="grid grid-cols-12 gap-4 md:gap-6">
		<div class="col-span-12 space-y-6 xl:col-span-7">
			<x-admin.monthly-sale />
		</div>
		<div class="col-span-12 xl:col-span-5">
			<x-admin.monthly-target />
		</div>

		<div class="col-span-12">
			<x-admin.statistics-chart />
		</div>

		<div class="col-span-12 xl:col-span-5">
			<x-admin.customer-demographic />
		</div>

		<div class="col-span-12 xl:col-span-7">
			<x-admin.recent-orders />
		</div>
	</div>
@endsection
