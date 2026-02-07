@extends('admin.layouts.app')

@section('title', 'Brands — Kiddo\'s Heaven')

@section('content')
	<div class="grid grid-cols-12 gap-4 md:gap-6">
		<div class="col-span-12">
			<div x-data="brandManager({
    initialSearch: '{{ request('search', '') }}',
    storeRoute: '{{ route('admin.brands.store') }}',
    baseUrl: '/admin/brands',
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

				<div class="rounded-2xl border border-gray-200 bg-white pt-4 dark:border-gray-800 dark:bg-white/3">
					<!-- Header -->
					<div class="flex flex-col gap-2 px-5 mb-4 sm:flex-row sm:items-center sm:justify-between sm:px-6">
						<div>
							<h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">Brands</h3>
						</div>
						<div class="flex flex-col gap-3 sm:flex-row sm:items-center">
							<form @submit.prevent="searchBrands()">
								<div class="relative">
									<button type="button" @click="searchBrands()" class="absolute -translate-y-1/2 left-4 top-1/2">
										<svg class="fill-gray-500 dark:fill-gray-400" width="20" height="20" viewBox="0 0 20 20" fill="none"
											xmlns="http://www.w3.org/2000/svg">
											<path fill-rule="evenodd" clip-rule="evenodd"
												d="M3.04199 9.37381C3.04199 5.87712 5.87735 3.04218 9.37533 3.04218C12.8733 3.04218 15.7087 5.87712 15.7087 9.37381C15.7087 12.8705 12.8733 15.7055 9.37533 15.7055C5.87735 15.7055 3.04199 12.8705 3.04199 9.37381ZM9.37533 1.54218C5.04926 1.54218 1.54199 5.04835 1.54199 9.37381C1.54199 13.6993 5.04926 17.2055 9.37533 17.2055C11.2676 17.2055 13.0032 16.5346 14.3572 15.4178L17.1773 18.2381C17.4702 18.531 17.945 18.5311 18.2379 18.2382C18.5308 17.9453 18.5309 17.4704 18.238 17.1775L15.4182 14.3575C16.5367 13.0035 17.2087 11.2671 17.2087 9.37381C17.2087 5.04835 13.7014 1.54218 9.37533 1.54218Z"
												fill="" />
										</svg>
									</button>
									<input type="text" x-model="searchTerm" @keydown="handleKeydown($event)" placeholder="Search brands..."
										class="h-10.5 w-full rounded-lg border border-gray-300 bg-transparent py-2.5 pl-10.5 pr-4 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-blue-800 xl:w-75" />
								</div>
							</form>
							<button @click="openCreateModal()"
								class="h-10.5 inline-flex items-center justify-center rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 hover:text-gray-800 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/3 dark:hover:text-gray-200">
								<svg class="mr-2" width="20" height="20" viewBox="0 0 20 20" fill="none"
									xmlns="http://www.w3.org/2000/svg">
									<path d="M10 4.16667V15.8333M4.16667 10H15.8333" stroke="currentColor" stroke-width="2" stroke-linecap="round"
										stroke-linejoin="round" />
								</svg>
								Add Brand
							</button>
						</div>
					</div>

					<!-- Table -->
					<div class="overflow-hidden">
						<div class="max-w-full px-5 overflow-x-auto">
							<table class="min-w-full">
								<thead>
									<tr class="border-gray-200 border-y dark:border-gray-700">
										<th scope="col" class="px-4 py-3 font-normal text-gray-500 text-start text-theme-sm dark:text-gray-400">Logo
										</th>
										<th scope="col" class="px-4 py-3 font-normal text-gray-500 text-start text-theme-sm dark:text-gray-400">Name
										</th>
										<th scope="col" class="px-4 py-3 font-normal text-gray-500 text-start text-theme-sm dark:text-gray-400">Slug
										</th>
										<th scope="col" class="px-4 py-3 font-normal text-gray-500 text-start text-theme-sm dark:text-gray-400">
											Status</th>
										<th scope="col" class="relative px-4 py-3 capitalize"><span class="sr-only">Actions</span></th>
									</tr>
								</thead>
								<tbody class="divide-y divide-gray-200 dark:divide-gray-700">
									@forelse ($brands as $brand)
										<tr>
											<td class="py-4 whitespace-nowrap">
												<div class="flex items-center">
													@if ($brand->logo)
														<img src="{{ asset('storage/' . $brand->logo) }}" alt="{{ $brand->name }}"
															class="w-10 h-10 rounded-lg object-cover">
													@else
														<div class="w-10 h-10 rounded-lg bg-gray-50 flex items-center justify-center">
															<svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
																<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
																	d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01" />
															</svg>
														</div>
													@endif
												</div>
											</td>
											<td class="px-4 py-4 whitespace-nowrap">
												<div class="text-sm font-medium text-gray-900 dark:text-white">{{ $brand->name }}</div>
											</td>
											<td class="px-4 py-4 whitespace-nowrap">
												<div class="text-sm text-gray-500 dark:text-gray-400">{{ $brand->slug }}</div>
											</td>
											<td class="px-4 py-4 whitespace-nowrap">
												@if ($brand->is_active)
													<span
														class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-50 text-green-600 dark:bg-green-500/15 dark:text-green-500">
														Active
													</span>
												@else
													<span
														class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-50 text-gray-600 dark:bg-gray-500/15 dark:text-gray-400">
														Inactive
													</span>
												@endif
											</td>
											<td class="px-4 py-4 text-sm font-medium text-right whitespace-nowrap">
												<div class="flex items-center gap-2 justify-end">
													<button @click="openEditModal({{ json_encode($brand) }})"
														class="p-1.5 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg">
														<x-icons.edit />
													</button>
													<button @click="openDeleteConfirm({{ $brand->id }}, '{{ addslashes($brand->name) }}')"
														class="p-1.5 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg">
														<x-icons.delete />
													</button>
												</div>
											</td>
										</tr>
									@empty
										<tr>
											<td colspan="5" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">No brands found</td>
										</tr>
									@endforelse
								</tbody>
							</table>
						</div>
					</div>

					<!-- Pagination -->
					@if ($brands->hasPages())
						<div class="px-6 py-4 border-t border-gray-200 dark:border-white/5">
							<div class="flex items-center justify-between">
								<button @click="window.location.href='{{ $brands->previousPageUrl() }}'"
									{{ !$brands->previousPageUrl() ? 'disabled' : '' }}
									class="flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-3 py-3 text-theme-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 hover:text-gray-800 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/3 dark:hover:text-gray-200 sm:px-3.5 {{ !$brands->previousPageUrl() ? 'opacity-50 cursor-not-allowed' : '' }}">
									<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
										<path fill-rule="evenodd" clip-rule="evenodd"
											d="M2.58301 9.99868C2.58272 10.1909 2.65588 10.3833 2.80249 10.53L7.79915 15.5301C8.09194 15.8231 8.56682 15.8233 8.85981 15.5305C9.15281 15.2377 9.15297 14.7629 8.86018 14.4699L5.14009 10.7472L16.6675 10.7472C17.0817 10.7472 17.4175 10.4114 17.4175 9.99715C17.4175 9.58294 17.0817 9.24715 16.6675 9.24715L5.14554 9.24715L8.86017 5.53016C9.15297 5.23717 9.15282 4.7623 8.85983 4.4695C8.56684 4.1767 8.09197 4.17685 7.79917 4.46984L2.84167 9.43049C2.68321 9.568 2.58301 9.77087 2.58301 9.99715C2.58301 9.99766 2.58301 9.99817 2.58301 9.99868Z"
											fill="currentColor" />
									</svg>
									<span class="hidden sm:inline">Previous</span>
								</button>

								<span class="block text-sm font-medium text-gray-700 dark:text-gray-400 sm:hidden">Page
									{{ $brands->currentPage() }} of {{ $brands->lastPage() }}</span>

								<ul class="hidden items-center gap-0.5 sm:flex">
									@foreach ($brands->links()->elements[0] as $page => $url)
										<li>
											<button @click="window.location.href='{{ $url }}'"
												class="flex h-10 w-10 items-center justify-center rounded-lg text-theme-sm font-medium {{ $page == $brands->currentPage() ? 'bg-blue-500 text-white' : 'text-gray-700 hover:bg-blue-500/8 hover:text-blue-500 dark:text-gray-400 dark:hover:text-blue-500' }}">
												{{ $page }}
											</button>
										</li>
									@endforeach
								</ul>

								<button @click="window.location.href='{{ $brands->nextPageUrl() }}'"
									{{ !$brands->nextPageUrl() ? 'disabled' : '' }}
									class="flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-3 py-3 text-theme-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 hover:text-gray-800 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/3 dark:hover:text-gray-200 sm:px-3.5 {{ !$brands->nextPageUrl() ? 'opacity-50 cursor-not-allowed' : '' }}">
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

				<!-- Create/Edit Modal -->
				@include('admin.brands.partials.add-modal')
				@include('admin.brands.partials.edit-modal')

				<!-- Delete Confirmation Modal -->
				<div x-show="showDeleteModal" x-transition.opacity.duration.300ms
					class="fixed inset-0 z-99999 flex items-center justify-center bg-black/50" style="display: none;">
					<div @click.away="showDeleteModal = false" x-transition:enter="transition ease-out duration-300"
						x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
						x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 scale-100"
						x-transition:leave-end="opacity-0 scale-95"
						class="w-full max-w-md rounded-2xl bg-white p-6 shadow-lg dark:bg-gray-800">
						<!-- Icon -->
						<div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-red-100">
							<svg class="h-8 w-8 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
									d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
							</svg>
						</div>

						<!-- Content -->
						<div class="text-center">
							<h3 class="mb-2 text-lg font-semibold text-gray-800 dark:text-white">Delete Brand</h3>
							<p class="mb-6 text-sm text-gray-500 dark:text-gray-400">
								Are you sure you want to delete <strong x-text="deleteBrandName"
									class="text-gray-700 dark:text-gray-200"></strong>?
								This action cannot be undone.
							</p>

							<!-- Buttons -->
							<div class="flex gap-3">
								<button @click="showDeleteModal = false"
									class="flex-1 rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600">
									Cancel
								</button>
								<button @click="confirmDelete()" :disabled="isDeleting"
									class="flex-1 rounded-lg bg-red-500 px-4 py-2.5 text-sm font-medium text-white hover:bg-red-600 disabled:opacity-50 disabled:cursor-not-allowed">
									<span x-show="!isDeleting">Delete</span>
									<span x-show="isDeleting" class="flex items-center justify-center gap-2">
										<svg class="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24">
											<circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
											</circle>
											<path class="opacity-75" fill="currentColor"
												d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
											</path>
										</svg>
										Deleting...
									</span>
								</button>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
@endsection

@push('scripts')
	<script>
		document.addEventListener('alpine:init', () => {
			Alpine.data('brandManager', (config) => ({
				searchTerm: config.initialSearch,
				showModal: false,
				showEditModal: false,
				modalMode: 'create',
				editingBrand: null,
				toastMessage: '',
				toastType: 'success',
				toastShow: false,
				showDeleteModal: false,
				deleteBrandId: null,
				deleteBrandName: '',
				isDeleting: false,
				isSubmitting: false,
				formData: {
					name: '',
					slug: '',
					description: '',
					logo: null,
					is_active: true
				},
				formErrors: null,

				showToast(message, type = 'success') {
					this.toastMessage = message;
					this.toastType = type;
					this.toastShow = true;
					setTimeout(() => {
						this.toastShow = false;
					}, 3000);
				},

				openCreateModal() {
					this.modalMode = 'create';
					this.formData = {
						name: '',
						slug: '',
						description: '',
						logo: null,
						is_active: true
					};
					this.formErrors = null;
					this.showModal = true;
				},

				openEditModal(brand) {
					this.modalMode = 'edit';
					this.editingBrand = brand;
					this.formErrors = null;
					document.getElementById('edit_brand_name').value = brand.name;
					document.getElementById('edit_brand_slug').value = brand.slug;
					document.getElementById('edit_brand_description').value = brand.description || '';
					document.getElementById('edit_brand_is_active').checked = brand.is_active;
					document.getElementById('editBrandForm').action = `/admin/brands/${brand.id}`;
					this.showEditModal = true;
				},

				closeModal() {
					this.showModal = false;
					this.editingBrand = null;
					this.formData = {
						name: '',
						slug: '',
						description: '',
						logo: null,
						is_active: true
					};
					this.formErrors = null;
				},

				closeEditModal() {
					this.showEditModal = false;
					this.editingBrand = null;
					this.formErrors = null;
				},

				async saveBrand() {
					this.isSubmitting = true;
					this.formErrors = null;

					try {
						const formData = new FormData();
						formData.append('name', this.formData.name);
						formData.append('slug', this.formData.slug);
						formData.append('description', this.formData.description);
						formData.append('is_active', this.formData.is_active ? 1 : 0);
						if (this.formData.logo) {
							formData.append('logo', this.formData.logo);
						}
						formData.append('_token', config.csrf);

						const response = await fetch(config.storeRoute, {
							method: 'POST',
							headers: {
								'X-Requested-With': 'XMLHttpRequest'
							},
							body: formData
						});

						const text = await response.text();
						let data;
						try {
							data = JSON.parse(text);
						} catch (e) {
							console.error('Response:', text.substring(0, 500));
							throw new Error('Invalid JSON response');
						}

						if (response.ok && data.success) {
							this.showToast(data.message || 'Brand created successfully!');
							this.showModal = false;
							setTimeout(() => {
								window.location.reload();
							}, 1500);
						} else if (data.errors) {
							this.formErrors = data.errors;
							this.showToast('Please fix the validation errors', 'error');
						} else {
							this.showToast(data.message || 'Error creating brand', 'error');
						}
					} catch (error) {
						console.error('Error:', error);
						this.showToast('An unexpected error occurred', 'error');
					} finally {
						this.isSubmitting = false;
					}
				},

				async updateBrand() {
					this.isSubmitting = true;
					this.formErrors = null;

					try {
						const formData = new FormData();
						formData.append('name', document.getElementById('edit_brand_name').value);
						formData.append('slug', document.getElementById('edit_brand_slug').value);
						formData.append('description', document.getElementById(
							'edit_brand_description').value);
						formData.append('is_active', document.getElementById('edit_brand_is_active')
							.checked ? 1 : 0);
						const logoFile = document.getElementById('edit_brand_logo').files[0];
						if (logoFile) {
							formData.append('logo', logoFile);
						}
						formData.append('_token', config.csrf);
						formData.append('_method', 'PUT');

						const response = await fetch(`/admin/brands/${this.editingBrand.id}`, {
							method: 'POST',
							headers: {
								'X-Requested-With': 'XMLHttpRequest'
							},
							body: formData
						});

						const text = await response.text();
						let data;
						try {
							data = JSON.parse(text);
						} catch (e) {
							console.error('Response:', text.substring(0, 500));
							throw new Error('Invalid JSON response');
						}

						if (response.ok && data.success) {
							this.showToast(data.message || 'Brand updated successfully!');
							this.showEditModal = false;
							setTimeout(() => {
								window.location.reload();
							}, 1500);
						} else if (data.errors) {
							this.formErrors = data.errors;
							this.showToast('Please fix the validation errors', 'error');
						} else {
							this.showToast(data.message || 'Error updating brand', 'error');
						}
					} catch (error) {
						console.error('Error:', error);
						this.showToast('An unexpected error occurred', 'error');
					} finally {
						this.isSubmitting = false;
					}
				},

				openDeleteConfirm(id, name) {
					this.deleteBrandId = id;
					this.deleteBrandName = name;
					this.showDeleteModal = true;
				},

				async confirmDelete() {
					if (!this.deleteBrandId) return;
					this.isDeleting = true;
					try {
						const formData = new FormData();
						formData.append('_method', 'DELETE');
						formData.append('_token', config.csrf);

						const response = await fetch(`${config.baseUrl}/${this.deleteBrandId}`, {
							method: 'POST',
							body: formData
						});

						const text = await response.text();
						let data;
						try {
							data = JSON.parse(text);
						} catch (e) {
							this.showToast('Something went wrong. Please try again.', 'error');
							this.isDeleting = false;
							this.showDeleteModal = false;
							return;
						}
						if (response.ok && data.success) {
							this.showToast(data.message || 'Brand deleted successfully!');
							this.showDeleteModal = false;
							this.isDeleting = false;
							setTimeout(() => {
								window.location.reload();
							}, 1500);
						} else {
							this.showToast(data.message || 'Unable to delete brand. Please try again.',
								'error');
							this.isDeleting = false;
						}
					} catch (error) {
						console.error('Delete error:', error);
						this.showToast('Network error. Please check your connection.', 'error');
						this.isDeleting = false;
						this.showDeleteModal = false;
					}
				},

				searchBrands() {
					const url = new URL(window.location);
					url.searchParams.set('search', this.searchTerm);
					url.searchParams.set('page', 1);
					window.location.href = url.toString();
				},

				handleKeydown(e) {
					if (e.key === 'Enter') this.searchBrands();
				},

				init() {
					this.$el.addEventListener('close-edit-modal', () => {
						this.showEditModal = false;
						this.editingBrand = null;
						this.formErrors = null;
					});
				}
			}))
		})
	</script>
@endpush
