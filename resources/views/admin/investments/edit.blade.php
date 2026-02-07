@extends('admin.layouts.app')

@section('title', 'Edit Investment — Kiddo\'s Heaven')

@section('content')
	<div class="grid grid-cols-12 gap-4 md:gap-6">
		<div class="col-span-12 lg:col-span-8">
			<form action="{{ route('admin.investments.update', $investment) }}" method="POST"
				class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/3">
				@csrf
				@method('PUT')

				<!-- Header -->
				<div class="mb-6">
					<h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">Edit Investment</h3>
					<p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Update investment details</p>
				</div>

				<!-- Form Fields -->
				<div class="space-y-5">
					<div>
						<label for="title" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Title *</label>
						<input type="text" name="title" id="title" required value="{{ old('title', $investment->title) }}"
							class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-blue-800">
						@error('title')
							<p class="mt-1 text-sm text-red-500">{{ $message }}</p>
						@enderror
					</div>

					<div class="grid grid-cols-1 gap-4 md:grid-cols-2">
						<div>
							<label for="type" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Type *</label>
							<select name="type" id="type" required
								class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-blue-800">
								<option value="">Select Type</option>
								<option value="equipment" {{ old('type', $investment->type) == 'equipment' ? 'selected' : '' }}>Equipment
								</option>
								<option value="property" {{ old('type', $investment->type) == 'property' ? 'selected' : '' }}>Property</option>
								<option value="marketing" {{ old('type', $investment->type) == 'marketing' ? 'selected' : '' }}>Marketing
								</option>
								<option value="research" {{ old('type', $investment->type) == 'research' ? 'selected' : '' }}>Research</option>
								<option value="expansion" {{ old('type', $investment->type) == 'expansion' ? 'selected' : '' }}>Expansion
								</option>
								<option value="other" {{ old('type', $investment->type) == 'other' ? 'selected' : '' }}>Other</option>
							</select>
							@error('type')
								<p class="mt-1 text-sm text-red-500">{{ $message }}</p>
							@enderror
						</div>

						<div>
							<label for="amount" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Amount (৳)
								*</label>
							<input type="number" name="amount" id="amount" step="0.01" min="0" required
								value="{{ old('amount', $investment->amount) }}"
								class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-blue-800">
							@error('amount')
								<p class="mt-1 text-sm text-red-500">{{ $message }}</p>
							@enderror
						</div>
					</div>

					<div class="grid grid-cols-1 gap-4 md:grid-cols-2">
						<div>
							<label for="current_value" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Current Value
								(৳)</label>
							<input type="number" name="current_value" id="current_value" step="0.01" min="0"
								value="{{ old('current_value', $investment->current_value) }}"
								class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-blue-800">
							@error('current_value')
								<p class="mt-1 text-sm text-red-500">{{ $message }}</p>
							@enderror
						</div>

						<div>
							<label for="investment_date" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Investment
								Date *</label>
							<input type="date" name="investment_date" id="investment_date" required
								value="{{ old('investment_date', $investment->investment_date->format('Y-m-d')) }}"
								class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-blue-800">
							@error('investment_date')
								<p class="mt-1 text-sm text-red-500">{{ $message }}</p>
							@enderror
						</div>
					</div>

					<div class="grid grid-cols-1 gap-4 md:grid-cols-2">
						<div>
							<label for="status" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Status</label>
							<select name="status" id="status"
								class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-blue-800">
								<option value="active" {{ old('status', $investment->status) == 'active' ? 'selected' : '' }}>Active</option>
								<option value="completed" {{ old('status', $investment->status) == 'completed' ? 'selected' : '' }}>Completed
								</option>
								<option value="sold" {{ old('status', $investment->status) == 'sold' ? 'selected' : '' }}>Sold</option>
							</select>
						</div>

						@if ($investment->status == 'sold')
							<div>
								<label for="sold_date" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Sold
									Date</label>
								<input type="date" name="sold_date" id="sold_date"
									value="{{ old('sold_date', $investment->sold_date?->format('Y-m-d')) }}"
									class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-blue-800">
							</div>
						@endif
					</div>

					<div>
						<label for="description"
							class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Description</label>
						<textarea name="description" id="description" rows="4"
						 class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-blue-800">{{ old('description', $investment->description) }}</textarea>
					</div>

					<div>
						<label for="notes" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Notes</label>
						<textarea name="notes" id="notes" rows="3"
						 class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-blue-800">{{ old('notes', $investment->notes) }}</textarea>
					</div>
				</div>

				<!-- Submit Button -->
				<div class="mt-8 pt-6 border-t border-gray-200 dark:border-gray-700">
					<div class="flex gap-4">
						<button type="submit"
							class="h-10.5 inline-flex items-center justify-center rounded-lg border border-blue-500 bg-blue-500 px-6 py-2.5 text-sm font-medium text-white shadow-theme-xs hover:bg-blue-600">
							Update Investment
						</button>
						<a href="{{ route('admin.investments.index') }}"
							class="h-10.5 inline-flex items-center justify-center rounded-lg border border-gray-300 bg-white px-6 py-2.5 text-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/3">
							Cancel
						</a>
					</div>
				</div>
			</form>
		</div>
	</div>
@endsection
