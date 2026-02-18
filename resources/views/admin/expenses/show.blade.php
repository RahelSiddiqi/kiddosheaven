@extends('admin.layouts.app')

@section('title', 'Expense Details — Kiddo\'s Heaven')

@section('content')
	<div class="grid grid-cols-12 gap-4 md:gap-6">
		<!-- Header Section -->
		<div class="col-span-12">
			<div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/3">
				<div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
					<div class="flex items-center gap-4">
						<a href="{{ route('admin.expenses.index') }}"
							class="flex h-10 w-10 items-center justify-center rounded-lg border border-gray-300 bg-white hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:hover:bg-white/3">
							<svg class="w-5 h-5 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
							</svg>
						</a>
						<div>
							<h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">{{ $expense->title }}</h3>
							<p class="text-sm text-gray-500 dark:text-gray-400">{{ $expense->category->name }}</p>
						</div>
					</div>
					<div class="flex items-center gap-3">
						<span
							class="inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-sm font-medium {{ $expense->status_badge_class }}">
							<span class="w-2 h-2 rounded-full bg-current"></span>
							{{ ucfirst($expense->status) }}
						</span>
						@if ($expense->status === 'pending')
							<form action="{{ route('admin.expenses.approve', $expense) }}" method="POST">
								@csrf
								<button type="submit"
									class="h-10 inline-flex items-center justify-center rounded-lg border border-green-500 bg-green-500 px-6 py-2.5 text-sm font-medium text-white shadow-theme-xs hover:bg-green-600">
									<svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
										<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
									</svg>
									Approve
								</button>
							</form>
						@endif
						<a href="{{ route('admin.expenses.edit', $expense) }}"
							class="h-10 inline-flex items-center justify-center rounded-lg border border-blue-500 bg-blue-500 px-6 py-2.5 text-sm font-medium text-white shadow-theme-xs hover:bg-blue-600">
							<svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
									d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
							</svg>
							Edit
						</a>
						<button type="button" x-data="{ id: {{ $expense->id }}, title: '{{ $expense->title }}' }"
							@click.prevent="$dispatch('open-delete-modal', { id: id, title: title, route: '{{ route('admin.expenses.destroy', $expense) }}' })"
							class="h-10 inline-flex items-center justify-center rounded-lg border border-red-300 bg-white px-6 py-2.5 text-sm font-medium text-red-600 shadow-theme-xs hover:bg-red-50 dark:border-red-900 dark:bg-gray-800 dark:text-red-400 dark:hover:bg-white/3">
							<svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
									d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
							</svg>
							Delete
						</button>
					</div>
				</div>
			</div>
		</div>

		<!-- Main Content -->
		<div class="col-span-12 lg:col-span-8">
			<div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/3">
				<h3 class="text-lg font-semibold text-gray-800 dark:text-white/90 mb-6">Expense Details</h3>

				<div class="grid grid-cols-2 gap-6 mb-6">
					<div class="rounded-xl border border-gray-100 bg-gray-50 dark:border-gray-700 dark:bg-gray-800/50 p-4">
						<p class="text-sm text-gray-500 dark:text-gray-400">Amount</p>
						<p class="text-2xl font-bold text-gray-800 dark:text-white/90">৳{{ number_format($expense->amount, 0) }}</p>
					</div>
					<div class="rounded-xl border border-gray-100 bg-gray-50 dark:border-gray-700 dark:bg-gray-800/50 p-4">
						<p class="text-sm text-gray-500 dark:text-gray-400">Date</p>
						<p class="text-xl font-medium text-gray-800 dark:text-white/90">{{ $expense->expense_date->format('M d, Y') }}</p>
					</div>
					<div>
						<p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Category</p>
						<p class="text-lg font-medium text-gray-800 dark:text-white/90">{{ $expense->category->name }}</p>
					</div>
					@if ($expense->partner)
						<div>
							<p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Partner</p>
							<a href="{{ route('admin.partners.show', $expense->partner) }}"
								class="text-lg font-medium text-blue-500 hover:underline">
								{{ $expense->partner->name }}
							</a>
						</div>
					@endif
				</div>

				@if ($expense->description)
					<div class="mb-6">
						<p class="text-sm text-gray-500 dark:text-gray-400 mb-2">Description</p>
						<p class="text-gray-800 dark:text-white/90 whitespace-pre-line">{{ $expense->description }}</p>
					</div>
				@endif

				@if ($expense->receipt_url)
					<div>
						<p class="text-sm text-gray-500 dark:text-gray-400 mb-2">Receipt</p>
						<img src="{{ Storage::url($expense->receipt_url) }}" alt="Receipt"
							class="max-w-xs rounded-lg border border-gray-200 dark:border-gray-700">
					</div>
				@endif
			</div>
		</div>

		<!-- Sidebar -->
		<div class="col-span-12 lg:col-span-4">
			<div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/3">
				<h3 class="text-lg font-semibold text-gray-800 dark:text-white/90 mb-4">Information</h3>
				<ul class="space-y-4 text-sm">
					<li class="flex items-center justify-between">
						<span class="text-gray-500 dark:text-gray-400">Created</span>
						<span class="text-gray-800 dark:text-white/90">{{ $expense->created_at->format('M d, Y H:i') }}</span>
					</li>
					<li class="flex items-center justify-between">
						<span class="text-gray-500 dark:text-gray-400">Last Updated</span>
						<span class="text-gray-800 dark:text-white/90">{{ $expense->updated_at->format('M d, Y H:i') }}</span>
					</li>
					@if ($expense->creator)
						<li class="flex items-center justify-between">
							<span class="text-gray-500 dark:text-gray-400">Created By</span>
							<span class="text-gray-800 dark:text-white/90">{{ $expense->creator->name }}</span>
						</li>
					@endif
				</ul>
			</div>
		</div>
	</div>

	<!-- Delete Modal -->
	<x-admin.ui.delete-modal />
@endsection
