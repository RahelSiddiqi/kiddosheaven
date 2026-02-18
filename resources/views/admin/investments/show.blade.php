@extends('admin.layouts.app')

@section('title', 'Investment Details — Kiddo\'s Heaven')

@push('scripts')
	<script>
		document.addEventListener('alpine:init', () => {
			Alpine.data('investmentShow', () => ({
				showStatusModal: false,
				currentStatus: '{{ $investment->status }}',
				statuses: ['active', 'completed', 'sold'],
				toastMessage: '',
				toastType: 'success',
				toastShow: false,

				showToast(message, type = 'success') {
					this.toastMessage = message;
					this.toastType = type;
					this.toastShow = true;
					setTimeout(() => {
						this.toastShow = false;
					}, 3000);
				},

				openStatusModal() {
					this.showStatusModal = true;
				},

				closeStatusModal() {
					this.showStatusModal = false;
				},

				async updateStatus(newStatus) {
					try {
						const formData = new FormData();
						formData.append('status', newStatus);
						formData.append('_token', '{{ csrf_token() }}');
						formData.append('_method', 'PUT');

						const response = await fetch(
							'{{ route('admin.investments.update-status', $investment) }}', {
								method: 'POST',
								headers: {
									'X-Requested-With': 'XMLHttpRequest'
								},
								body: formData
							});

						const data = await response.json();
						if (data.success) {
							this.showToast(data.message || 'Status updated successfully!');
							this.currentStatus = newStatus;
							this.closeStatusModal();
							setTimeout(() => {
								window.location.reload();
							}, 1500);
						} else {
							this.showToast(data.message || 'Error updating status', 'error');
						}
					} catch (error) {
						console.error('Error:', error);
						this.showToast('Error updating status', 'error');
					}
				}
			}))
		})
	</script>
@endpush

@section('content')
	<div class="grid grid-cols-12 gap-4 md:gap-6" x-data="investmentShow">
		<!-- Toast Notification -->
		<div x-show="toastShow" x-transition.opacity.duration.300ms
			class="fixed top-4 right-4 z-99999 px-4 py-3 rounded-lg shadow-lg text-white flex items-center gap-2 min-w-70"
			:class="toastType === 'success' ? 'bg-green-500' : 'bg-red-500'" style="display: none;">
			<svg x-show="toastType === 'success'" class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
				<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
			</svg>
			<svg x-show="toastType === 'error'" class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
				<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
			</svg>
			<span x-text="toastMessage" class="text-sm font-medium"></span>
		</div>

		<!-- Stats Cards -->
		<div class="col-span-12 lg:col-span-4">
			<div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
				<div class="flex items-center justify-between">
					<div>
						<span class="text-sm text-gray-500 dark:text-gray-400">Invested Amount</span>
						<h4 class="mt-1 font-bold text-gray-800 text-title-sm dark:text-white/90">
							{{ number_format($investment->amount, 0) }}
						</h4>
					</div>
					<div class="w-12 h-12 rounded-xl bg-blue-100 dark:bg-blue-500/15 flex items-center justify-center">
						<svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
								d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
						</svg>
					</div>
				</div>
			</div>
		</div>

		<div class="col-span-12 lg:col-span-4">
			<div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
				<div class="flex items-center justify-between">
					<div>
						<span class="text-sm text-gray-500 dark:text-gray-400">Status</span>
						<h4 class="mt-1 font-bold text-gray-800 text-title-sm dark:text-white/90">
							{{ ucfirst($investment->status) }}
						</h4>
					</div>
					<div class="w-12 h-12 rounded-xl bg-purple-100 dark:bg-purple-500/15 flex items-center justify-center">
						<svg class="w-6 h-6 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
								d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
						</svg>
					</div>
				</div>
			</div>
		</div>

		<div class="col-span-12 lg:col-span-4">
			<div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">
				<div class="flex items-center justify-between">
					<div>
						<span class="text-sm text-gray-500 dark:text-gray-400">Investment Date</span>
						<h4 class="mt-1 font-bold text-gray-800 text-title-sm dark:text-white/90">
							{{ $investment->investment_date->format('M d, Y') }}
						</h4>
					</div>
					<div class="w-12 h-12 rounded-xl bg-yellow-100 dark:bg-yellow-500/15 flex items-center justify-center">
						<svg class="w-6 h-6 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
								d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
						</svg>
					</div>
				</div>
			</div>
		</div>

		<!-- Investment Details -->
		<div class="col-span-12 lg:col-span-4">
			<div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
				<div class="flex items-center justify-between mb-4">
					<h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">Investment Details</h3>
					<div class="flex gap-2">
						<button @click="openStatusModal()"
							class="p-1.5 text-gray-400 hover:text-blue-500 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors"
							title="Change Status">
							<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
									d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4" />
							</svg>
						</button>
						<a href="{{ route('admin.investments.edit', $investment) }}"
							class="p-1.5 text-gray-400 hover:text-blue-500 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors">
							<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
									d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
							</svg>
						</a>
					</div>
				</div>
				<div class="space-y-4">
					<div>
						<p class="text-sm text-gray-500 dark:text-gray-400">Title</p>
						<p class="font-medium text-gray-800 dark:text-white/90">{{ $investment->title }}</p>
					</div>
					<div>
						<p class="text-sm text-gray-500 dark:text-gray-400">Type</p>
						<span
							class="text-theme-xs inline-block rounded-full px-2.5 py-0.5 font-medium bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300">
							{{ ucfirst($investment->type) }}
						</span>
					</div>
					<div>
						<p class="text-sm text-gray-500 dark:text-gray-400">Status</p>
						<span
							class="text-theme-xs inline-block rounded-full px-2.5 py-0.5 font-medium {{ $investment->status_badge_class }}">
							{{ ucfirst($investment->status) }}
						</span>
					</div>
					<div>
						<p class="text-sm text-gray-500 dark:text-gray-400">Amount</p>
						<p class="font-medium text-gray-800 dark:text-white/90">{{ number_format($investment->amount, 0) }}</p>
					</div>
					<div>
						<p class="text-sm text-gray-500 dark:text-gray-400">Investment Date</p>
						<p class="font-medium text-gray-800 dark:text-white/90">{{ $investment->investment_date->format('M d, Y') }}</p>
					</div>
					@if ($investment->description)
						<div>
							<p class="text-sm text-gray-500 dark:text-gray-400">Description</p>
							<p class="font-medium text-gray-800 dark:text-white/90 whitespace-pre-line">{{ $investment->description }}</p>
						</div>
					@endif
					@if ($investment->notes)
						<div>
							<p class="text-sm text-gray-500 dark:text-gray-400">Notes</p>
							<p class="font-medium text-gray-800 dark:text-white/90 whitespace-pre-line">{{ $investment->notes }}</p>
						</div>
					@endif
					<div>
						<p class="text-sm text-gray-500 dark:text-gray-400">Created At</p>
						<p class="font-medium text-gray-800 dark:text-white/90">{{ $investment->created_at->format('M d, Y') }}</p>
					</div>
				</div>
			</div>
		</div>

		<!-- Investment Summary -->
		<div class="col-span-12 lg:col-span-8">
			<div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">
				<h3 class="text-lg font-semibold text-gray-800 dark:text-white/90 mb-4">Investment Summary</h3>
				<div class="grid grid-cols-2 gap-4">
					<div class="p-4 rounded-xl bg-gray-50 dark:bg-gray-800/50">
						<p class="text-sm text-gray-500 dark:text-gray-400">Invested Amount</p>
						<p class="text-2xl font-bold text-gray-800 dark:text-white/90">{{ number_format($investment->amount, 0) }}</p>
					</div>
					<div class="p-4 rounded-xl bg-gray-50 dark:bg-gray-800/50">
						<p class="text-sm text-gray-500 dark:text-gray-400">Type</p>
						<p class="text-2xl font-bold text-gray-800 dark:text-white/90">{{ ucfirst($investment->type) }}</p>
					</div>
					<div class="p-4 rounded-xl bg-gray-50 dark:bg-gray-800/50">
						<p class="text-sm text-gray-500 dark:text-gray-400">Current Status</p>
						<p class="text-2xl font-bold text-gray-800 dark:text-white/90">{{ ucfirst($investment->status) }}</p>
					</div>
					<div class="p-4 rounded-xl bg-gray-50 dark:bg-gray-800/50">
						<p class="text-sm text-gray-500 dark:text-gray-400">Investment Date</p>
						<p class="text-2xl font-bold text-gray-800 dark:text-white/90">
							{{ $investment->investment_date->format('M d, Y') }}</p>
					</div>
				</div>
			</div>
		</div>
	</div>

	<!-- Status Change Modal -->
	<div x-show="showStatusModal" x-cloak class="fixed inset-0 z-10000 overflow-y-auto">
		<div class="flex min-h-screen items-center justify-center p-4">
			<div x-show="showStatusModal" @click="closeStatusModal()"
				class="fixed inset-0 bg-black/50 transition-opacity z-10000">
			</div>
			<div class="relative w-full max-w-sm rounded-2xl bg-white dark:bg-gray-800 shadow-xl z-10001"
				x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 scale-95"
				x-transition:enter-end="opacity-100 scale-100">
				<div class="flex items-center justify-between p-5 border-b border-gray-200 dark:border-gray-700">
					<h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">Change Status</h3>
					<button @click="closeStatusModal()" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg">
						<svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
						</svg>
					</button>
				</div>
				<div class="p-5">
					<p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Select a new status for this investment:</p>
					<div class="space-y-2">
						@foreach (['active', 'completed', 'sold'] as $status)
							<button @click="updateStatus('{{ $status }}')"
								class="w-full px-4 py-3 text-left rounded-lg border transition-colors {{ $investment->status === $status ? 'border-blue-500 bg-blue-50 dark:bg-blue-500/10 text-blue-600 dark:text-blue-400' : 'border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700' }}">
								<span class="inline-flex items-center gap-2">
									<span
										class="w-2.5 h-2.5 rounded-full {{ match ($status) {'active' => 'bg-green-500','completed' => 'bg-blue-500','sold' => 'bg-purple-500'} }}"></span>
									{{ ucfirst($status) }}
								</span>
							</button>
						@endforeach
					</div>
				</div>
			</div>
		</div>
	</div>
@endsection
