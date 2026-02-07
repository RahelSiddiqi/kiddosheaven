@extends('admin.layouts.app')

@section('title', 'Edit Expense — Kiddo\'s Heaven')

@section('content')
	<div class="grid grid-cols-12 gap-4 md:gap-6">
		<div class="col-span-12">
			<form action="{{ route('admin.expenses.update', $expense) }}" method="POST" enctype="multipart/form-data"
				class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/3">
				@csrf
				@method('PUT')

				<!-- Header -->
				<div class="flex flex-col gap-4 mb-6 sm:flex-row sm:items-center sm:justify-between">
					<div>
						<h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">Edit Expense</h3>
						<p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Update expense details</p>
					</div>
					<div class="flex items-center gap-3">
						<a href="{{ route('admin.expenses.show', $expense) }}"
							class="h-10.5 inline-flex items-center justify-center rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/3">
							<svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
							</svg>
							Back to Details
						</a>
						<button type="submit"
							class="h-10.5 inline-flex items-center justify-center rounded-lg border border-blue-500 bg-blue-500 px-6 py-2.5 text-sm font-medium text-white shadow-theme-xs hover:bg-blue-600">
							<svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
							</svg>
							Update Expense
						</button>
					</div>
				</div>

				<!-- Form Fields -->
				<div class="grid grid-cols-12 gap-4 md:gap-6">
					<div class="col-span-12 lg:col-span-6">
						<div class="space-y-5">
							<div>
								<label for="title" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Title *</label>
								<input type="text" name="title" id="title" required value="{{ old('title', $expense->title) }}"
									class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-blue-800">
								@error('title')
									<p class="mt-1 text-sm text-red-500">{{ $message }}</p>
								@enderror
							</div>

							<div>
								<label for="category_id" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Category
									*</label>
								<select name="category_id" id="category_id" required
									class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-blue-800">
									<option value="">Select Category</option>
									@foreach ($categories as $category)
										<option value="{{ $category->id }}"
											{{ old('category_id', $expense->category_id) == $category->id ? 'selected' : '' }}>
											{{ $category->name }}
										</option>
									@endforeach
								</select>
								@error('category_id')
									<p class="mt-1 text-sm text-red-500">{{ $message }}</p>
								@enderror
							</div>

							<div>
								<label for="partner_id" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Partner
									(Optional)</label>
								<select name="partner_id" id="partner_id"
									class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-blue-800">
									<option value="">None</option>
									@foreach ($partners as $partner)
										<option value="{{ $partner->id }}"
											{{ old('partner_id', $expense->partner_id) == $partner->id ? 'selected' : '' }}>
											{{ $partner->name }} ({{ ucfirst($partner->type) }})
										</option>
									@endforeach
								</select>
								@error('partner_id')
									<p class="mt-1 text-sm text-red-500">{{ $message }}</p>
								@enderror
							</div>
						</div>
					</div>

					<div class="col-span-12 lg:col-span-6">
						<div class="space-y-5">
							<div class="grid grid-cols-2 gap-4">
								<div>
									<label for="amount" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Amount *</label>
									<div class="relative">
										<span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500">৳</span>
										<input type="number" name="amount" id="amount" step="0.01" min="0" required
											value="{{ old('amount', $expense->amount) }}"
											class="h-11 w-full rounded-lg border border-gray-300 bg-transparent pl-8 pr-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-blue-800">
									</div>
									@error('amount')
										<p class="mt-1 text-sm text-red-500">{{ $message }}</p>
									@enderror
								</div>

								<div>
									<label for="expense_date" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Date *</label>
									<input type="date" name="expense_date" id="expense_date" required
										value="{{ old('expense_date', $expense->expense_date->toDateString()) }}"
										class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-blue-800">
									@error('expense_date')
										<p class="mt-1 text-sm text-red-500">{{ $message }}</p>
									@enderror
								</div>
							</div>

							<div>
								<label for="description" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Description</label>
								<textarea name="description" id="description" rows="3"
									class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-blue-800">{{ old('description', $expense->description) }}</textarea>
								@error('description')
									<p class="mt-1 text-sm text-red-500">{{ $message }}</p>
								@enderror
							</div>
						</div>
					</div>

					<div class="col-span-12">
						<div>
							<label for="receipt" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Receipt
								Image</label>
							@if ($expense->receipt_url)
								<div class="mb-3">
									<img src="{{ Storage::url($expense->receipt_url) }}" alt="Receipt"
										class="max-w-xs rounded-lg border border-gray-200 dark:border-gray-700">
								</div>
							@endif
							<div class="relative">
								<input type="file" name="receipt" id="receipt" accept="image/*"
									class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:file:bg-blue-900 dark:file:text-blue-300">
							</div>
							@error('receipt')
								<p class="mt-1 text-sm text-red-500">{{ $message }}</p>
							@enderror
						</div>
					</div>
				</div>
			</form>
		</div>
	</div>
@endsection
