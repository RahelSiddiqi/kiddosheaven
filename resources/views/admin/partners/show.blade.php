@extends('admin.layouts.app')

@section('title', 'Partner Details — Kiddo\'s Heaven')

@push('scripts')
	<script>
		document.addEventListener('alpine:init', () => {
			Alpine.data('partnerShow', () => ({
				showStatusModal: false,
				currentStatus: '{{ $partner->status }}',
				statuses: ['active', 'inactive', 'suspended'],
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

						const response = await fetch('{{ route('admin.partners.update-status', $partner) }}', {
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
	<div class="grid grid-cols-12 gap-4 md:gap-6" x-data="partnerShow">
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

		<!-- Left Column - Partner Information -->
		<div class="col-span-12 lg:col-span-4 space-y-6">
			<!-- Partner Info Card -->
			<div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/3">
				<div class="flex items-center justify-between mb-6">
					<h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">Partner Information</h3>
					<div class="flex gap-2">
						<button @click="openStatusModal()"
							class="p-1.5 text-gray-400 hover:text-blue-500 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors"
							title="Change Status">
							<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
									d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4" />
							</svg>
						</button>
						<a href="{{ route('admin.partners.edit', $partner) }}"
							class="p-1.5 text-gray-400 hover:text-blue-500 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors">
							<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
									d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
							</svg>
						</a>
					</div>
				</div>

				<div class="flex items-center gap-4 mb-6">
					<div class="flex h-16 w-16 items-center justify-center rounded-xl bg-blue-100 text-blue-600 dark:bg-blue-500/15 dark:text-blue-400">
						<span class="text-xl font-bold">{{ strtoupper(substr($partner->name, 0, 2)) }}</span>
					</div>
					<div>
						<h4 class="text-lg font-semibold text-gray-800 dark:text-white/90">{{ $partner->name }}</h4>
						<p class="text-sm text-gray-500 dark:text-gray-400">{{ ucfirst(str_replace('_', ' ', $partner->type)) }}</p>
					</div>
				</div>

				<div class="space-y-3">
					<div class="flex items-center justify-between py-2 border-b border-gray-100 dark:border-gray-700">
						<span class="text-sm text-gray-500 dark:text-gray-400">Status</span>
						<span class="text-sm font-medium {{ $partner->status_badge_class }}">
							{{ ucfirst($partner->status) }}
						</span>
					</div>
					<div class="flex items-center justify-between py-2 border-b border-gray-100 dark:border-gray-700">
						<span class="text-sm text-gray-500 dark:text-gray-400">Commission Rate</span>
						<span class="text-sm font-medium text-gray-800 dark:text-white/90">{{ $partner->commission_rate ?? 0 }}%</span>
					</div>
					<div class="flex items-center justify-between py-2 border-b border-gray-100 dark:border-gray-700">
						<span class="text-sm text-gray-500 dark:text-gray-400">Created</span>
						<span class="text-sm font-medium text-gray-800 dark:text-white/90">{{ $partner->created_at->format('M d, Y') }}</span>
					</div>
					<div class="flex items-center justify-between py-2">
						<span class="text-sm text-gray-500 dark:text-gray-400">Total Paid</span>
						<span class="text-sm font-medium text-green-600 dark:text-green-400">
							৳{{ number_format($partner->payments->where('status', 'completed')->sum('amount'), 2) }}
						</span>
					</div>
				</div>
			</div>

			<!-- Contact Information -->
			@php
				$contactInfo = is_string($partner->contact_info)
				    ? json_decode($partner->contact_info, true) ?? []
				    : ($partner->contact_info ?? []);
			@endphp
			<div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/3">
				<h3 class="text-lg font-semibold text-gray-800 dark:text-white/90 mb-4">Contact Information</h3>
				<div class="space-y-4">
					@if (!empty($contactInfo['email']))
						<div class="flex items-center gap-3">
							<div class="flex h-10 w-10 items-center justify-center rounded-lg bg-gray-100 dark:bg-gray-700">
								<svg class="w-5 h-5 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
										d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
								</svg>
							</div>
							<div>
								<p class="text-sm text-gray-500 dark:text-gray-400">Email</p>
								<p class="font-medium text-gray-800 dark:text-white/90">{{ $contactInfo['email'] }}</p>
							</div>
						</div>
					@endif
					@if (!empty($contactInfo['phone']))
						<div class="flex items-center gap-3">
							<div class="flex h-10 w-10 items-center justify-center rounded-lg bg-gray-100 dark:bg-gray-700">
								<svg class="w-5 h-5 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
										d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
								</svg>
							</div>
							<div>
								<p class="text-sm text-gray-500 dark:text-gray-400">Phone</p>
								<p class="font-medium text-gray-800 dark:text-white/90">{{ $contactInfo['phone'] }}</p>
							</div>
						</div>
					@endif
					@if (!empty($contactInfo['address']))
						<div class="flex items-start gap-3">
							<div class="flex h-10 w-10 items-center justify-center rounded-lg bg-gray-100 dark:bg-gray-700 shrink-0">
								<svg class="w-5 h-5 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
										d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
								</svg>
							</div>
							<div>
								<p class="text-sm text-gray-500 dark:text-gray-400">Address</p>
								<p class="font-medium text-gray-800 dark:text-white/90">{{ $contactInfo['address'] }}</p>
							</div>
						</div>
					@endif
					@if (empty($contactInfo['email']) && empty($contactInfo['phone']) && empty($contactInfo['address']))
						<p class="text-gray-500 dark:text-gray-400 text-center py-4">No contact information available</p>
					@endif
				</div>
			</div>

			<!-- Bank Details -->
			@php
				$bankDetails = is_string($partner->bank_details)
				    ? json_decode($partner->bank_details, true) ?? []
				    : ($partner->bank_details ?? []);
			@endphp
			<div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/3">
				<h3 class="text-lg font-semibold text-gray-800 dark:text-white/90 mb-4">Bank Details</h3>
				<div class="space-y-4">
					@if (!empty($bankDetails['bank_name']))
						<div>
							<p class="text-sm text-gray-500 dark:text-gray-400">Bank Name</p>
							<p class="font-medium text-gray-800 dark:text-white/90">{{ $bankDetails['bank_name'] }}</p>
						</div>
					@endif
					@if (!empty($bankDetails['account_number']))
						<div>
							<p class="text-sm text-gray-500 dark:text-gray-400">Account Number</p>
							<p class="font-medium text-gray-800 dark:text-white/90">{{ $bankDetails['account_number'] }}</p>
						</div>
					@endif
					@if (!empty($bankDetails['account_name']))
						<div>
							<p class="text-sm text-gray-500 dark:text-gray-400">Account Name</p>
							<p class="font-medium text-gray-800 dark:text-white/90">{{ $bankDetails['account_name'] }}</p>
						</div>
					@endif
					@if (!empty($bankDetails['routing_number']))
						<div>
							<p class="text-sm text-gray-500 dark:text-gray-400">Routing Number</p>
							<p class="font-medium text-gray-800 dark:text-white/90">{{ $bankDetails['routing_number'] }}</p>
						</div>
					@endif
					@if (empty($bankDetails))
						<p class="text-gray-500 dark:text-gray-400 text-center py-4">No bank details available</p>
					@endif
				</div>
			</div>

			<!-- Notes -->
			@if ($partner->notes)
				<div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/3">
					<h3 class="text-lg font-semibold text-gray-800 dark:text-white/90 mb-4">Notes</h3>
					<p class="text-gray-600 dark:text-gray-300 whitespace-pre-line">{{ $partner->notes }}</p>
				</div>
			@endif
		</div>

		<!-- Right Column - Payments -->
		<div class="col-span-12 lg:col-span-8">
			<div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
				<div class="flex flex-col gap-4 p-5 sm:flex-row sm:items-center sm:justify-between">
					<div>
						<h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">Payments</h3>
						<p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Payment history for this partner</p>
					</div>
					<button type="button" x-data="{ partnerId: {{ $partner->id }} }" @click="$dispatch('open-payment-modal', { partnerId: partnerId })"
						class="h-10.5 inline-flex items-center justify-center rounded-lg border border-blue-500 bg-blue-500 px-6 py-2.5 text-sm font-medium text-white shadow-theme-xs hover:bg-blue-600">
						<svg class="mr-2" width="20" height="20" viewBox="0 0 20 20" fill="none">
							<path d="M10 4.16667V15.8333M4.16667 10H15.8333" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
								stroke-linejoin="round" />
						</svg>
						Add Payment
					</button>
				</div>

				<div class="overflow-x-auto">
					<table class="w-full min-w-[600px]">
						<thead>
							<tr class="border-b border-gray-100 dark:border-gray-800">
								<th class="px-5 py-3 text-left">
									<p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">Date</p>
								</th>
								<th class="px-5 py-3 text-left">
									<p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">Description</p>
								</th>
								<th class="px-5 py-3 text-right">
									<p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">Amount</p>
								</th>
								<th class="px-5 py-3 text-center">
									<p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">Status</p>
								</th>
								<th class="px-5 py-3 text-right">
									<p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">Actions</p>
								</th>
							</tr>
						</thead>
						<tbody>
							@forelse($partner->payments->sortByDesc('payment_date') as $payment)
								<tr class="border-b border-gray-100 dark:border-gray-800 hover:bg-gray-50 dark:hover:bg-white/[0.02]">
									<td class="px-5 py-4">
										<span class="text-gray-800 dark:text-white/90">{{ \Carbon\Carbon::parse($payment->payment_date)->format('M d, Y') }}</span>
									</td>
									<td class="px-5 py-4">
										<span class="text-gray-500 dark:text-gray-400">{{ $payment->description ?? 'Payment' }}</span>
									</td>
									<td class="px-5 py-4 text-right">
										<span class="font-medium text-gray-800 dark:text-white/90">৳{{ number_format($payment->amount, 2) }}</span>
									</td>
									<td class="px-5 py-4 text-center">
										<span
											class="text-theme-xs inline-block rounded-full px-2.5 py-0.5 font-medium {{ $payment->status_badge_class }}">
											{{ ucfirst($payment->status) }}
										</span>
									</td>
									<td class="px-5 py-4 text-right">
										<div class="flex items-center justify-end gap-2">
											<button
												@click="$dispatch('open-edit-payment-modal', { partnerId: {{ $partner->id }}, paymentId: {{ $payment->id }}, amount: {{ $payment->amount }}, date: '{{ $payment->payment_date->format('Y-m-d') }}', description: '{{ $payment->description ?? '' }}', status: '{{ $payment->status }}' })"
												class="p-1.5 text-gray-400 hover:text-blue-500 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors"
												title="Edit Payment">
												<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
													<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
														d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
												</svg>
											</button>
											<form action="{{ route('admin.partners.payments.destroy', [$partner, $payment]) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this payment?');">
												@csrf
												@method('DELETE')
												<button type="submit"
													class="p-1.5 text-gray-400 hover:text-red-500 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors"
													title="Delete Payment">
													<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
														<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
															d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
													</svg>
												</button>
											</form>
										</div>
									</td>
								</tr>
							@empty
								<tr>
									<td colspan="5" class="px-5 py-8 text-center">
										<p class="text-gray-500 dark:text-gray-400">No payments found</p>
									</td>
								</tr>
							@endforelse
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>

	<!-- Status Change Modal -->
	<div x-show="showStatusModal" x-cloak class="fixed inset-0 z-10000 overflow-y-auto">
		<div class="flex min-h-screen items-center justify-center p-4">
			<div x-show="showStatusModal" @click="closeStatusModal()" class="fixed inset-0 bg-black/50 transition-opacity z-10000">
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
					<p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Select a new status for this partner:</p>
					<div class="space-y-2">
						@foreach(['active', 'inactive', 'suspended'] as $status)
							<button @click="updateStatus('{{ $status }}')"
								class="w-full px-4 py-3 text-left rounded-lg border transition-colors {{ $partner->status === $status ? 'border-blue-500 bg-blue-50 dark:bg-blue-500/10 text-blue-600 dark:text-blue-400' : 'border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700' }}">
								<span class="inline-flex items-center gap-2">
									<span
										class="w-2.5 h-2.5 rounded-full {{ match($status) { 'active' => 'bg-green-500', 'inactive' => 'bg-gray-500', 'suspended' => 'bg-red-500' } }}"></span>
									{{ ucfirst($status) }}
								</span>
							</button>
						@endforeach
					</div>
				</div>
			</div>
		</div>
	</div>

	<!-- Payment Modal -->
	@include('admin.partners.partials.payment-modal')

	<!-- Edit Payment Modal -->
	@include('admin.partners.partials.edit-payment-modal')
@endsection
