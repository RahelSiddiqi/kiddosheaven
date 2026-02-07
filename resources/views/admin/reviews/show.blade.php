@extends('admin.layouts.app')

@section('title', 'Review Details — Kiddo\'s Heaven')

@section('header_title', 'Review Details')

@section('content')
	<div class="space-y-6">
		<!-- Header Actions -->
		<div class="flex flex-wrap items-center justify-between gap-4">
			<div class="flex items-center gap-3">
				<a href="{{ route('admin.reviews.index') }}" class="p-2 rounded-lg hover:bg-opacity-50 transition"
					:class="{ 'hover:bg-slate-700 bg-slate-800': isDarkMode, 'hover:bg-gray-100 bg-gray-100': !isDarkMode }">
					<svg class="w-5 h-5" :class="{ 'text-gray-400': isDarkMode, 'text-gray-500': !isDarkMode }" fill="none"
						stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
					</svg>
				</a>
				<div>
					<h1 class="text-lg font-semibold" :class="{ 'text-white': isDarkMode, 'text-gray-900': !isDarkMode }">
						Review Details
					</h1>
					<p class="text-sm" :class="{ 'text-gray-400': isDarkMode, 'text-gray-500': !isDarkMode }">
						{{ $review->created_at->format('M d, Y H:i') }}
					</p>
				</div>
			</div>

			<div class="flex items-center gap-2">
				@if (!$review->is_approved)
					<form action="{{ route('admin.reviews.approve', $review) }}" method="POST" class="inline">
						@csrf
						<button type="submit" class="btn-success">
							<svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
							</svg>
							Approve Review
						</button>
					</form>
				@endif
				<form action="{{ route('admin.reviews.destroy', $review) }}" method="POST" class="inline">
					@csrf
					@method('DELETE')
					<button type="submit" class="btn-danger" onclick="return confirm('Are you sure you want to delete this review?')">
						<svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
								d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
						</svg>
						Delete
					</button>
				</form>
			</div>
		</div>

		<div class="grid gap-6 md:grid-cols-3">
			<!-- Review Content -->
			<div class="md:col-span-2 space-y-6">
				<!-- Rating -->
				<div class="card"
					:class="{ 'dark bg-slate-900 border-slate-800': isDarkMode, 'bg-white border-gray-200': !isDarkMode }">
					<div class="card-content">
						<div class="flex items-center gap-4">
							<div class="flex items-center gap-1">
								@for ($i = 1; $i <= 5; $i++)
									<svg class="w-8 h-8 {{ $i <= $review->rating ? 'text-yellow-400' : 'text-gray-300 dark:text-gray-600' }}"
										fill="currentColor" viewBox="0 0 20 20">
										<path
											d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
									</svg>
								@endfor
							</div>
							<span class="text-2xl font-bold" :class="{ 'text-white': isDarkMode, 'text-gray-900': !isDarkMode }">
								{{ $review->rating }}/5
							</span>
							<span class="text-sm" :class="{ 'text-gray-400': isDarkMode, 'text-gray-500': !isDarkMode }">
								@switch($review->rating)
									@case(1)
										Poor
									@break

									@case(2)
										Fair
									@break

									@case(3)
										Good
									@break

									@case(4)
										Very Good
									@break

									@case(5)
										Excellent
									@break
								@endswitch
							</span>
						</div>
					</div>
				</div>

				<!-- Review Text -->
				<div class="card"
					:class="{ 'dark bg-slate-900 border-slate-800': isDarkMode, 'bg-white border-gray-200': !isDarkMode }">
					<div class="card-header" :class="{ 'dark border-slate-800': isDarkMode, 'border-gray-200': !isDarkMode }">
						<h2 class="card-title" :class="{ 'text-white': isDarkMode, 'text-gray-900': !isDarkMode }">
							{{ $review->title ?? 'Review' }}
						</h2>
					</div>
					<div class="card-content">
						<p class="prose dark:prose-invert max-w-none"
							:class="{ 'text-gray-300': isDarkMode, 'text-gray-700': !isDarkMode }">
							{{ $review->content }}
						</p>
					</div>
				</div>
			</div>

			<!-- Sidebar -->
			<div class="space-y-6">
				<!-- Status -->
				<div class="card"
					:class="{ 'dark bg-slate-900 border-slate-800': isDarkMode, 'bg-white border-gray-200': !isDarkMode }">
					<div class="card-header" :class="{ 'dark border-slate-800': isDarkMode, 'border-gray-200': !isDarkMode }">
						<h2 class="card-title" :class="{ 'text-white': isDarkMode, 'text-gray-900': !isDarkMode }">
							Status
						</h2>
					</div>
					<div class="card-content space-y-3">
						<div class="flex items-center justify-between">
							<span class="text-sm" :class="{ 'text-gray-400': isDarkMode, 'text-gray-500': !isDarkMode }">Approval</span>
							<span
								class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $review->is_approved ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400' : 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400' }}">
								{{ $review->is_approved ? 'Approved' : 'Pending' }}
							</span>
						</div>
						<div class="flex items-center justify-between">
							<span class="text-sm" :class="{ 'text-gray-400': isDarkMode, 'text-gray-500': !isDarkMode }">Verified</span>
							<span
								class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $review->is_verified_purchase ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400' : 'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-400' }}">
								{{ $review->is_verified_purchase ? 'Verified Purchase' : 'Not Verified' }}
							</span>
						</div>
					</div>
				</div>

				<!-- Product Info -->
				<div class="card"
					:class="{ 'dark bg-slate-900 border-slate-800': isDarkMode, 'bg-white border-gray-200': !isDarkMode }">
					<div class="card-header" :class="{ 'dark border-slate-800': isDarkMode, 'border-gray-200': !isDarkMode }">
						<h2 class="card-title" :class="{ 'text-white': isDarkMode, 'text-gray-900': !isDarkMode }">
							Product
						</h2>
					</div>
					<div class="card-content">
						@if ($review->product)
							<div class="flex items-center gap-3">
								@if ($review->product->image)
									<img src="{{ asset('storage/' . $review->product->image) }}" alt="{{ $review->product->name }}"
										class="w-16 h-16 rounded-lg object-cover">
								@else
									<div class="w-16 h-16 rounded-lg bg-gray-200 dark:bg-slate-700 flex items-center justify-center">
										<svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
											<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
												d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
										</svg>
									</div>
								@endif
								<div>
									<a href="{{ route('admin.products.show', $review->product) }}" class="font-medium hover:underline"
										:class="{ 'text-white': isDarkMode, 'text-gray-900': !isDarkMode }">
										{{ $review->product->name }}
									</a>
									<p class="text-xs" :class="{ 'text-gray-400': isDarkMode, 'text-gray-500': !isDarkMode }">
										SKU: {{ $review->product->sku }}
									</p>
									<p class="text-sm font-medium" :class="{ 'text-white': isDarkMode, 'text-gray-900': !isDarkMode }">
										৳{{ number_format($review->product->price, 2) }}
									</p>
								</div>
							</div>
						@else
							<p class="text-sm" :class="{ 'text-gray-400': isDarkMode, 'text-gray-500': !isDarkMode }">
								Product has been deleted.
							</p>
						@endif
					</div>
				</div>

				<!-- Customer Info -->
				<div class="card"
					:class="{ 'dark bg-slate-900 border-slate-800': isDarkMode, 'bg-white border-gray-200': !isDarkMode }">
					<div class="card-header" :class="{ 'dark border-slate-800': isDarkMode, 'border-gray-200': !isDarkMode }">
						<h2 class="card-title" :class="{ 'text-white': isDarkMode, 'text-gray-900': !isDarkMode }">
							Customer
						</h2>
					</div>
					<div class="card-content">
						@if ($review->user)
							<div class="flex items-center gap-3">
								<div class="w-12 h-12 rounded-full bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center">
									<svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
										<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
											d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
									</svg>
								</div>
								<div>
									<p class="font-medium" :class="{ 'text-white': isDarkMode, 'text-gray-900': !isDarkMode }">
										{{ $review->user->name }}
									</p>
									<p class="text-xs" :class="{ 'text-gray-400': isDarkMode, 'text-gray-500': !isDarkMode }">
										{{ $review->user->email }}
									</p>
								</div>
							</div>
							<a href="{{ route('admin.customers.show', $review->user) }}"
								class="mt-3 block text-sm text-blue-500 hover:underline">
								View Customer Profile
							</a>
						@else
							<div class="flex items-center gap-3">
								<div class="w-12 h-12 rounded-full bg-gray-100 dark:bg-slate-700 flex items-center justify-center">
									<svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
										<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
											d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
									</svg>
								</div>
								<div>
									<p class="font-medium" :class="{ 'text-white': isDarkMode, 'text-gray-900': !isDarkMode }">
										Guest Customer
									</p>
									@if ($review->order)
										<p class="text-xs" :class="{ 'text-gray-400': isDarkMode, 'text-gray-500': !isDarkMode }">
											Order #{{ $review->order->id }}
										</p>
									@endif
								</div>
							</div>
						@endif
					</div>
				</div>

				<!-- Order Info -->
				@if ($review->order)
					<div class="card"
						:class="{ 'dark bg-slate-900 border-slate-800': isDarkMode, 'bg-white border-gray-200': !isDarkMode }">
						<div class="card-header" :class="{ 'dark border-slate-800': isDarkMode, 'border-gray-200': !isDarkMode }">
							<h2 class="card-title" :class="{ 'text-white': isDarkMode, 'text-gray-900': !isDarkMode }">
								Order Information
							</h2>
						</div>
						<div class="card-content">
							<a href="{{ route('admin.orders.show', $review->order) }}"
								class="flex items-center justify-between p-3 rounded-lg hover:bg-opacity-50 transition"
								:class="{ 'hover:bg-slate-800 bg-slate-800/50': isDarkMode, 'hover:bg-gray-100 bg-gray-50': !isDarkMode }">
								<div>
									<p class="font-medium" :class="{ 'text-white': isDarkMode, 'text-gray-900': !isDarkMode }">
										Order #{{ $review->order->id }}
									</p>
									<p class="text-xs" :class="{ 'text-gray-400': isDarkMode, 'text-gray-500': !isDarkMode }">
										{{ $review->order->created_at->format('M d, Y') }}
									</p>
								</div>
								<span
									class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium capitalize
								{{ $review->order->status === 'delivered' ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400' : '' }}
								{{ $review->order->status === 'pending' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400' : '' }}
								{{ $review->order->status === 'processing' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400' : '' }}
								{{ $review->order->status === 'shipped' ? 'bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-400' : '' }}
								{{ $review->order->status === 'cancelled' ? 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400' : '' }}">
									{{ $review->order->status }}
								</span>
							</a>
						</div>
					</div>
				@endif
			</div>
		</div>
	</div>
@endsection
