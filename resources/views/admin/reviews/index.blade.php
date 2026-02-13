@extends('admin.layouts.app')

@section('title', 'Reviews — Kiddo\'s Heaven')

@section('content')
	<div class="grid grid-cols-12 gap-4 md:gap-6">
		<div class="col-span-12">
			<div x-data="reviewManager({
    initialSearch: '{{ request('search', '') }}',
    baseUrl: '/admin/reviews',
    csrf: '{{ csrf_token() }}'
})">
				<!-- Toast Notification -->
				<div x-show="toastShow" x-transition.opacity.duration.300ms
					class="fixed top-4 right-4 z-99999 px-4 py-3 rounded-lg shadow-lg text-white flex items-center gap-2 min-w-70"
					:class="toastType === 'success' ? 'bg-green-500' : 'bg-red-500'" style="display: none;">
					<svg x-show="toastType === 'success'" class="w-5 h-5 shrink-0" fill="none" stroke="currentColor"
						viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
					</svg>
					<svg x-show="toastType === 'error'" class="w-5 h-5 shrink-0" fill="none" stroke="currentColor"
						viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
					</svg>
					<span x-text="toastMessage" class="text-sm font-medium"></span>
				</div>

				<!-- Delete Confirmation Modal -->
				<x-admin.ui.delete-modal modalId="deleteReviewModal" title="Delete Review"
					message="Are you sure you want to delete this review? This action cannot be undone." />

				<!-- Stats Cards -->
				<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
					<x-admin.ui.stat-card title="Total Reviews" :value="$stats['total']" icon="star" color="blue" />
					<x-admin.ui.stat-card title="Pending" :value="$stats['pending']" icon="clock" color="yellow" />
					<x-admin.ui.stat-card title="Approved" :value="$stats['approved']" icon="check" color="green" />
					<x-admin.ui.stat-card title="Avg Rating" :value="number_format($stats['avg_rating'], 1)" icon="star" color="purple" />
				</div>

				<!-- Bulk Actions -->
				<form id="bulkActionForm" action="{{ route('admin.reviews.bulk-approve') }}" method="POST"
					class="flex items-center gap-3 mb-4">
					@csrf
					<span class="text-sm font-medium text-gray-700 dark:text-gray-400">
						<span id="selectedCount">0</span> selected
					</span>
					<button type="submit"
						class="h-10.5 inline-flex items-center justify-center rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-green-600 shadow-theme-xs hover:bg-green-50 dark:border-gray-700 dark:bg-gray-800 dark:text-green-400 dark:hover:bg-green-900/30"
						id="bulkApproveBtn" disabled>
						<svg class="mr-2" width="20" height="20" viewBox="0 0 20 20" fill="none"
							xmlns="http://www.w3.org/2000/svg">
							<path
								d="M10.0001 2.91659C6.21676 2.91659 2.91676 6.21659 2.91676 9.99993C2.91676 13.7833 6.21676 17.0833 10.0001 17.0833C13.7834 17.0833 17.0834 13.7833 17.0834 9.99993C17.0834 6.21659 13.7834 2.91659 10.0001 2.91659Z"
								stroke="currentColor" stroke-width="1.5" />
							<path d="M7.08325 10L9.16659 12.0833L12.9166 8.33331" stroke="currentColor" stroke-width="1.5"
								stroke-linecap="round" stroke-linejoin="round" />
						</svg>
						Approve Selected
					</button>
				</form>

				<div class="rounded-2xl border border-gray-200 bg-white pt-4 dark:border-gray-800 dark:bg-white/[0.03]">
					<!-- Header -->
					<div class="flex flex-col gap-2 px-5 mb-4 sm:flex-row sm:items-center sm:justify-between sm:px-6">
						<div>
							<h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">Reviews</h3>
						</div>
						<div class="flex flex-col gap-3 sm:flex-row sm:items-center">
							<form @submit.prevent="searchReviews()">
								<div class="relative">
									<button type="button" @click="searchReviews()" class="absolute -translate-y-1/2 left-4 top-1/2">
										<svg class="fill-gray-500 dark:fill-gray-400" width="20" height="20" viewBox="0 0 20 20" fill="none"
											xmlns="http://www.w3.org/2000/svg">
											<path fill-rule="evenodd" clip-rule="evenodd"
												d="M3.04199 9.37381C3.04199 5.87712 5.87735 3.04218 9.37533 3.04218C12.8733 3.04218 15.7087 5.87712 15.7087 9.37381C15.7087 12.8705 12.8733 15.7055 9.37533 15.7055C5.87735 15.7055 3.04199 12.8705 3.04199 9.37381ZM9.37533 1.54218C5.04926 1.54218 1.54199 5.04835 1.54199 9.37381C1.54199 13.6993 5.04926 17.2055 9.37533 17.2055C11.2676 17.2055 13.0032 16.5346 14.3572 15.4178L17.1773 18.2381C17.4702 18.531 17.945 18.5311 18.2379 18.2382C18.5308 17.9453 18.5309 17.4704 18.238 17.1775L15.4182 14.3575C16.5367 13.0035 17.2087 11.2671 17.2087 9.37381C17.2087 5.04835 13.7014 1.54218 9.37533 1.54218Z"
												fill="" />
										</svg>
									</button>
									<input type="text" x-model="searchTerm" @keydown="handleKeydown($event)" placeholder="Search reviews..."
										class="h-10.5 w-full rounded-lg border border-gray-300 bg-transparent py-2.5 pl-10.5 pr-4 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-blue-800 xl:w-64" />
								</div>
							</form>
						</div>
					</div>

					<!-- Table -->
					<div class="overflow-hidden">
						<div class="max-w-full px-5 overflow-x-auto">
							<table class="min-w-full">
								<thead>
									<tr class="border-gray-200 border-y dark:border-gray-700">
										<th scope="col"
											class="px-4 py-3 font-normal text-gray-500 text-start text-theme-sm dark:text-gray-400 w-12">
											<input type="checkbox" id="selectAll" class="w-4 h-4 rounded cursor-pointer">
										</th>
										<th scope="col" class="px-4 py-3 font-normal text-gray-500 text-start text-theme-sm dark:text-gray-400">
											Product</th>
										<th scope="col" class="px-4 py-3 font-normal text-gray-500 text-start text-theme-sm dark:text-gray-400">
											Customer</th>
										<th scope="col" class="px-4 py-3 font-normal text-gray-500 text-start text-theme-sm dark:text-gray-400">
											Rating</th>
										<th scope="col" class="px-4 py-3 font-normal text-gray-500 text-start text-theme-sm dark:text-gray-400">
											Review</th>
										<th scope="col" class="px-4 py-3 font-normal text-gray-500 text-start text-theme-sm dark:text-gray-400">
											Status</th>
										<th scope="col" class="px-4 py-3 font-normal text-gray-500 text-start text-theme-sm dark:text-gray-400">
											Date</th>
										<th scope="col" class="relative px-4 py-3 capitalize"><span class="sr-only">Actions</span></th>
									</tr>
								</thead>
								<tbody class="divide-y divide-gray-200 dark:divide-gray-700">
									@forelse ($reviews as $review)
										<tr>
											<td class="py-4 whitespace-nowrap">
												<input type="checkbox" name="review_ids[]" value="{{ $review->id }}"
													class="review-checkbox w-4 h-4 rounded cursor-pointer" {{ $review->is_approved ? 'disabled' : '' }}>
											</td>
											<td class="py-4 whitespace-nowrap">
												@if ($review->product)
													<a href="{{ route('admin.products.show', $review->product) }}"
														class="font-medium text-sm text-gray-900 hover:underline dark:text-white">
														{{ $review->product->name }}
													</a>
												@else
													<span class="text-sm text-gray-500 dark:text-gray-400">Deleted Product</span>
												@endif
											</td>
											<td class="py-4 whitespace-nowrap">
												@if ($review->user)
													<div class="text-sm font-medium text-gray-900 dark:text-white">{{ $review->user->name }}</div>
												@else
													<span class="text-sm text-gray-500 dark:text-gray-400">Guest</span>
												@endif
											</td>
											<td class="py-4 whitespace-nowrap">
												<div class="flex items-center gap-1">
													@for ($i = 1; $i <= 5; $i++)
														<svg class="w-4 h-4 {{ $i <= $review->rating ? 'text-yellow-400' : 'text-gray-300 dark:text-gray-600' }}"
															fill="currentColor" viewBox="0 0 20 20">
															<path
																d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
														</svg>
													@endfor
												</div>
											</td>
											<td class="py-4 max-w-xs">
												@if ($review->title)
													<p class="font-medium text-sm text-gray-900 dark:text-white truncate">{{ $review->title }}</p>
												@endif
												<p class="text-xs text-gray-500 dark:text-gray-400 truncate">{{ Str::limit($review->content, 50) }}</p>
											</td>
											<td class="py-4 whitespace-nowrap">
												@if ($review->is_approved)
													<span
														class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400">Approved</span>
												@else
													<span
														class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400">Pending</span>
												@endif
												@if ($review->is_verified_purchase)
													<span
														class="ml-1 px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400">Verified</span>
												@endif
											</td>
											<td class="py-4 whitespace-nowrap">
												<div class="text-sm text-gray-900 dark:text-white">{{ $review->created_at->format('M d, Y') }}</div>
											</td>
											<td class="px-4 py-4 text-sm font-medium text-right whitespace-nowrap">
												<div class="flex items-center gap-2 justify-end">
													<a href="{{ route('admin.reviews.show', $review) }}"
														class="p-1.5 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg">
														<svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
															<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
																d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
															<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
																d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
														</svg>
													</a>
													@if (!$review->is_approved)
														<form action="{{ route('admin.reviews.approve', $review) }}" method="POST" class="inline">
															@csrf
															<button type="submit" class="p-1.5 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg"
																title="Approve">
																<svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
																	<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
																</svg>
															</button>
														</form>
													@endif
													<button
														@click="$dispatch('open-delete-modal', { url: '/admin/reviews/{{ $review->id }}', id: {{ $review->id }}, name: '{{ addslashes($review->title ?? 'Review') }}' })"
														class="p-1.5 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg" title="Delete">
														<x-icons.delete />
													</button>
												</div>
											</td>
										</tr>
									@empty
										<tr>
											<td colspan="8" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">No reviews found</td>
										</tr>
									@endforelse
								</tbody>
							</table>
						</div>
					</div>

					<!-- Pagination -->
					@if ($reviews->hasPages())
						<div class="px-6 py-4 border-t border-gray-200 dark:border-white/5">
							<div class="flex items-center justify-between">
								<button @click="window.location.href='{{ $reviews->appends(request()->query())->previousPageUrl() }}'"
									{{ !$reviews->appends(request()->query())->previousPageUrl() ? 'disabled' : '' }}
									class="flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-3 py-3 text-theme-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 hover:text-gray-800 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/3 dark:hover:text-gray-200 sm:px-3.5 {{ !$reviews->appends(request()->query())->previousPageUrl() ? 'opacity-50 cursor-not-allowed' : '' }}">
									<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
										<path fill-rule="evenodd" clip-rule="evenodd"
											d="M2.58301 9.99868C2.58272 10.1909 2.65588 10.3833 2.80249 10.53L7.79915 15.5301C8.09194 15.8231 8.56682 15.8233 8.85981 15.5305C9.15281 15.2377 9.15297 14.7629 8.86018 14.4699L5.14009 10.7472L16.6675 10.7472C17.0817 10.7472 17.4175 10.4114 17.4175 9.99715C17.4175 9.58294 17.0817 9.24715 16.6675 9.24715L5.14554 9.24715L8.86017 5.53016C9.15297 5.23717 9.15282 4.7623 8.85983 4.4695C8.56684 4.1767 8.09197 4.17685 7.79917 4.46984L2.84167 9.43049C2.68321 9.568 2.58301 9.77087 2.58301 9.99715C2.58301 9.99766 2.58301 9.99817 2.58301 9.99868Z"
											fill="currentColor" />
									</svg>
									<span class="hidden sm:inline">Previous</span>
								</button>

								<span class="block text-sm font-medium text-gray-700 dark:text-gray-400 sm:hidden">Page
									{{ $reviews->currentPage() }} of {{ $reviews->lastPage() }}</span>

								<ul class="hidden items-center gap-0.5 sm:flex">
									@foreach ($reviews->appends(request()->query())->links()->elements[0] as $page => $url)
										<li>
											<button @click="window.location.href='{{ $url }}'"
												class="flex h-10 w-10 items-center justify-center rounded-lg text-theme-sm font-medium {{ $page == $reviews->currentPage() ? 'bg-blue-500 text-white' : 'text-gray-700 hover:bg-blue-500/8 hover:text-blue-500 dark:text-gray-400 dark:hover:text-blue-500' }}">
												{{ $page }}
											</button>
										</li>
									@endforeach
								</ul>

								<button @click="window.location.href='{{ $reviews->appends(request()->query())->nextPageUrl() }}'"
									{{ !$reviews->appends(request()->query())->nextPageUrl() ? 'disabled' : '' }}
									class="flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-3 py-3 text-theme-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 hover:text-gray-800 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/3 dark:hover:text-gray-200 sm:px-3.5 {{ !$reviews->appends(request()->query())->nextPageUrl() ? 'opacity-50 cursor-not-allowed' : '' }}">
									<span class="hidden sm:inline">Next</span>
									<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
										<path fill-rule="evenodd" clip-rule="evenodd"
											d="M17.4175 9.9986C17.4178 10.1909 17.3446 10.3832 17.198 10.53L12.2013 15.5301C11.9085 15.8231 11.4337 15.8233 11.1407 15.5305C10.8477 15.2377 10.8475 14.7629 11.1403 14.4699L14.8604 10.7472L3.33301 10.7472C2.91879 10.7472 2.58301 10.4114 2.58301 9.99715C2.58301 9.58294 2.91879 9.24715 3.33301 9.24715L14.8549 9.24715L11.1403 5.53016C10.8475 5.23717 10.8477 4.7623 11.1407 4.4695C11.4336 4.1767 11.9085 4.17685 12.2013 4.46984L17.1588 9.43049C17.3173 9.568 17.4175 9.77087 17.4175 9.99715C17.4175 9.99763 17.4175 9.99812 17.4175 9.9986Z"
											fill="currentColor" />
									</svg>
								</button>
							</div>
						</div>
					@endif
				</div>
			</div>
		</div>
	</div>
@endsection

@push('scripts')
	<script>
		document.addEventListener('alpine:init', () => {
			Alpine.data('reviewManager', (config) => ({
				searchTerm: config.initialSearch,
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

				searchReviews() {
					const url = new URL(window.location);
					url.searchParams.set('search', this.searchTerm);
					url.searchParams.set('page', 1);
					window.location.href = url.toString();
				},

				handleKeydown(e) {
					if (e.key === 'Enter') this.searchReviews();
				},

				init() {
					// Checkbox functionality
					const selectAll = document.getElementById('selectAll');
					const reviewCheckboxes = document.querySelectorAll('.review-checkbox');
					const selectedCount = document.getElementById('selectedCount');
					const bulkApproveBtn = document.getElementById('bulkApproveBtn');

					const updateSelectedCount = () => {
						const count = document.querySelectorAll(
							'.review-checkbox:checked:not(:disabled)').length;
						selectedCount.textContent = count;
						bulkApproveBtn.disabled = count === 0;
					};

					selectAll?.addEventListener('change', function() {
						reviewCheckboxes.forEach(cb => {
							if (!cb.disabled) {
								cb.checked = this.checked;
							}
						});
						updateSelectedCount();
					});

					reviewCheckboxes.forEach(cb => {
						cb.addEventListener('change', updateSelectedCount);
					});
				},
			}))
		})
	</script>
@endpush
