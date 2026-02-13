@extends('admin.layouts.app')

@section('title', 'Purchase Batches — Kiddo\'s Heaven')

@section('content')
	<div x-data="batchManager" class="grid grid-cols-12 gap-4 md:gap-6">
		<div class="col-span-12">
			<div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
				<!-- Header -->
				<div class="flex flex-col gap-2 px-5 mb-4 sm:flex-row sm:items-center sm:justify-between sm:px-6 pt-4">
					<div>
						<h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">Purchase Batches</h3>
						<p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Track inventory batches with FIFO/LIFO costing</p>
					</div>
					<div class="flex flex-col gap-3 sm:flex-row sm:items-center">
						<form method="GET" class="flex gap-2">
							<select name="product_id" onchange="this.form.submit()"
								class="h-10.5 rounded-lg border border-gray-300 bg-transparent px-3 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-blue-800">
								<option value="">All Products</option>
								@foreach ($products as $product)
									<option value="{{ $product->id }}" {{ request('product_id') == $product->id ? 'selected' : '' }}>
										{{ $product->name }}
									</option>
								@endforeach
							</select>
							<select name="status" onchange="this.form.submit()"
								class="h-10.5 rounded-lg border border-gray-300 bg-transparent px-3 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-blue-800">
								<option value="">All Status</option>
								<option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
								<option value="exhausted" {{ request('status') == 'exhausted' ? 'selected' : '' }}>Exhausted</option>
							</select>
						</form>
						<button @click="openCreateModal()"
							class="h-10.5 inline-flex items-center justify-center rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 hover:text-gray-800 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/3 dark:hover:text-gray-200">
							<svg class="mr-2" width="20" height="20" viewBox="0 0 20 20" fill="none"
								xmlns="http://www.w3.org/2000/svg">
								<path d="M10 4.16667V15.8333M4.16667 10H15.8333" stroke="currentColor" stroke-width="2" stroke-linecap="round"
									stroke-linejoin="round" />
							</svg>
							Add Batch
						</button>
					</div>
				</div>

				<!-- Table -->
				<div class="overflow-hidden">
					<div class="max-w-full overflow-x-auto">
						<table class="min-w-full">
							<thead>
								<tr class="border-gray-200 border-y dark:border-gray-700">
									<th scope="col" class="px-4 py-3 font-normal text-gray-500 text-start text-theme-sm dark:text-gray-400">Batch
										#</th>
									<th scope="col" class="px-4 py-3 font-normal text-gray-500 text-start text-theme-sm dark:text-gray-400">
										Product</th>
									<th scope="col" class="px-4 py-3 font-normal text-gray-500 text-start text-theme-sm dark:text-gray-400">Unit
										Cost</th>
									<th scope="col" class="px-4 py-3 font-normal text-gray-500 text-start text-theme-sm dark:text-gray-400">
										Received</th>
									<th scope="col" class="px-4 py-3 font-normal text-gray-500 text-start text-theme-sm dark:text-gray-400">
										Remaining</th>
									<th scope="col" class="px-4 py-3 font-normal text-gray-500 text-start text-theme-sm dark:text-gray-400">Total
										Value</th>
									<th scope="col" class="px-4 py-3 font-normal text-gray-500 text-start text-theme-sm dark:text-gray-400">
										Expiry</th>
									<th scope="col" class="px-4 py-3 font-normal text-gray-500 text-start text-theme-sm dark:text-gray-400">
										Status</th>
									<th scope="col" class="relative px-4 py-3 capitalize"><span class="sr-only">Actions</span></th>
								</tr>
							</thead>
							<tbody class="divide-y divide-gray-200 dark:divide-gray-700">
								@forelse($batches as $batch)
									<tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 cursor-pointer"
										@click="window.location='{{ route('admin.purchase-batches.show', $batch) }}'">
										<td class="px-4 py-4 whitespace-nowrap">
											<div class="text-sm font-medium text-brand-600 dark:text-brand-400">{{ $batch->batch_number }}</div>
										</td>
										<td class="px-4 py-4 whitespace-nowrap">
											<div class="text-sm text-gray-500 dark:text-gray-400">{{ $batch->product->name }}</div>
										</td>
										<td class="px-4 py-4 whitespace-nowrap">
											<div class="text-sm text-gray-500 dark:text-gray-400">{{ number_format($batch->unit_cost, 2) }} BDT</div>
										</td>
										<td class="px-4 py-4 whitespace-nowrap">
											<div class="text-sm text-gray-500 dark:text-gray-400">{{ $batch->quantity_received }}</div>
										</td>
										<td class="px-4 py-4 whitespace-nowrap">
											<div class="text-sm font-medium text-gray-900 dark:text-white">{{ $batch->remaining_quantity }}</div>
										</td>
										<td class="px-4 py-4 whitespace-nowrap">
											<div class="text-sm text-gray-500 dark:text-gray-400">
												{{ number_format($batch->unit_cost * $batch->remaining_quantity, 2) }} BDT</div>
										</td>
										<td class="px-4 py-4 whitespace-nowrap">
											<div class="text-sm text-gray-500 dark:text-gray-400">
												@if ($batch->expiry_date)
													{{ \Carbon\Carbon::parse($batch->expiry_date)->format('M d, Y') }}
													@if (\Carbon\Carbon::parse($batch->expiry_date)->isPast())
														<span class="text-red-500 ml-1">⚠</span>
													@endif
												@else
													-
												@endif
											</div>
										</td>
										<td class="px-4 py-4 whitespace-nowrap">
											@if ($batch->remaining_quantity > 0)
												<span
													class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-50 text-green-600 dark:bg-green-500/15 dark:text-green-500">
													Active
												</span>
											@else
												<span
													class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-50 text-gray-600 dark:bg-gray-500/15 dark:text-gray-400">
													Exhausted
												</span>
											@endif
										</td>
										<td class="px-4 py-4 text-sm font-medium text-right whitespace-nowrap">
											<div class="flex items-center gap-2 justify-end">
												<button @click="openEditModal({{ json_encode($batch) }})"
													class="p-1.5 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg">
													<x-icons.edit />
												</button>
												<button @click="openDeleteConfirm({{ $batch->id }}, '{{ addslashes($batch->batch_number) }}')"
													class="p-1.5 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg">
													<x-icons.delete />
												</button>
											</div>
										</td>
									</tr>
								@empty
									<tr>
										<td colspan="9" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">
											No purchase batches found. Click "Add Batch" to create your first batch.
										</td>
									</tr>
								@endforelse
							</tbody>
						</table>
					</div>
				</div>

				<!-- Pagination -->
				@if ($batches->hasPages())
					<div class="px-6 py-4 border-t border-gray-200 dark:border-white/5">
						{{ $batches->appends(request()->query())->links() }}
					</div>
				@endif
			</div>
		</div>

		<!-- Create/Edit Modal -->
		<div x-show="showModal" x-cloak class="fixed inset-0 z-10000 overflow-y-auto">
			<div class="flex min-h-screen items-center justify-center p-4">
				<div x-show="showModal" @click="closeModal()" class="fixed inset-0 bg-black/50 transition-opacity z-10000"></div>
				<div class="relative w-full max-w-lg rounded-2xl bg-white dark:bg-gray-800 shadow-xl z-10001"
					x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 scale-95"
					x-transition:enter-end="opacity-100 scale-100">
					<div class="flex items-center justify-between p-5 border-b border-gray-200 dark:border-gray-700">
						<h3 class="text-lg font-semibold text-gray-800 dark:text-white/90"
							x-text="modalMode === 'create' ? 'Add New Batch' : 'Edit Batch'"></h3>
						<button @click="closeModal()" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg">
							<svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
							</svg>
						</button>
					</div>
					<div class="p-5">
						<form action="{{ route('admin.purchase-batches.store') }}" method="POST">
							@csrf
							<input type="hidden" name="_method" x-model="formData._method" value="POST">
							<div class="mb-4">
								<label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Product</label>
								<select name="product_id" x-model="formData.product_id" required
									class="h-10.5 w-full rounded-lg border border-gray-300 bg-transparent py-2.5 px-4 text-sm text-gray-800 shadow-theme-xs focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-blue-800">
									<option value="">Select Product</option>
									@foreach ($products as $product)
										<option value="{{ $product->id }}">{{ $product->name }}</option>
									@endforeach
								</select>
							</div>
							<div class="mb-4">
								<label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Batch Number</label>
								<input type="text" name="batch_number" x-model="formData.batch_number" required
									class="h-10.5 w-full rounded-lg border border-gray-300 bg-transparent py-2.5 px-4 text-sm text-gray-800 shadow-theme-xs focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-blue-800"
									placeholder="e.g., PB-2024-001">
							</div>
							<div class="grid grid-cols-2 gap-4 mb-4">
								<div>
									<label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Unit Cost (BDT)</label>
									<input type="number" name="unit_cost" x-model="formData.unit_cost" step="0.01" min="0" required
										class="h-10.5 w-full rounded-lg border border-gray-300 bg-transparent py-2.5 px-4 text-sm text-gray-800 shadow-theme-xs focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-blue-800"
										placeholder="0.00">
								</div>
								<div>
									<label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Quantity</label>
									<input type="number" name="quantity" x-model="formData.quantity" min="1" required
										class="h-10.5 w-full rounded-lg border border-gray-300 bg-transparent py-2.5 px-4 text-sm text-gray-800 shadow-theme-xs focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-blue-800"
										placeholder="0">
								</div>
							</div>
							<div class="mb-4">
								<label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Supplier</label>
								<input type="text" name="supplier" x-model="formData.supplier"
									class="h-10.5 w-full rounded-lg border border-gray-300 bg-transparent py-2.5 px-4 text-sm text-gray-800 shadow-theme-xs focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-blue-800"
									placeholder="Supplier name">
							</div>
							<div class="mb-4">
								<label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Expiry Date</label>
								<input type="date" name="expiry_date" x-model="formData.expiry_date"
									class="h-10.5 w-full rounded-lg border border-gray-300 bg-transparent py-2.5 px-4 text-sm text-gray-800 shadow-theme-xs focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-blue-800">
							</div>
							<div class="mb-4">
								<label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Notes</label>
								<textarea name="notes" x-model="formData.notes" rows="2"
								 class="w-full rounded-lg border border-gray-300 bg-transparent py-2.5 px-4 text-sm text-gray-800 shadow-theme-xs focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-blue-800"
								 placeholder="Optional notes..."></textarea>
							</div>
							<div class="flex justify-end gap-3 mt-6">
								<button type="button" @click="closeModal()"
									class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-600">
									Cancel
								</button>
								<button type="submit"
									class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700">
									<span x-text="modalMode === 'create' ? 'Create' : 'Update'"></span>
								</button>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>

		<!-- Delete Confirmation Modal -->
		<div x-show="showDeleteModal" x-transition.opacity.duration.300ms
			class="fixed inset-0 z-99999 flex items-center justify-center bg-black/50" style="display: none;">
			<div @click.away="showDeleteModal = false" x-transition:enter="transition ease-out duration-300"
				x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
				x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 scale-100"
				x-transition:leave-end="opacity-0 scale-95"
				class="w-full max-w-md rounded-2xl bg-white p-6 shadow-lg dark:bg-gray-800">
				<div class="text-center">
					<h3 class="mb-2 text-lg font-semibold text-gray-800 dark:text-white">Delete Batch</h3>
					<p class="mb-6 text-sm text-gray-500 dark:text-gray-400">
						Are you sure you want to delete batch <strong x-text="deleteBatchNumber"
							class="text-gray-700 dark:text-gray-200"></strong>?
					</p>
					<div class="flex gap-3">
						<button @click="showDeleteModal = false"
							class="flex-1 rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600">
							Cancel
						</button>
						<form :action="`/admin/purchase-batches/${deleteBatchId}`" method="POST" class="flex-1">
							@csrf
							@method('DELETE')
							<button type="submit"
								class="w-full rounded-lg bg-red-500 px-4 py-2.5 text-sm font-medium text-white hover:bg-red-600">
								Delete
							</button>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>

	@push('scripts')
		<script>
			document.addEventListener('alpine:init', () => {
				Alpine.data('batchManager', () => ({
					showModal: false,
					modalMode: 'create',
					deleteBatchId: null,
					deleteBatchNumber: '',
					formData: {
						product_id: '',
						batch_number: '',
						unit_cost: '',
						quantity: '',
						supplier: '',
						expiry_date: '',
						notes: '',
						_method: 'POST'
					},

					openCreateModal() {
						this.modalMode = 'create';
						this.formData = {
							product_id: '',
							batch_number: '',
							unit_cost: '',
							quantity: '',
							supplier: '',
							expiry_date: '',
							notes: '',
							_method: 'POST'
						};
						this.showModal = true;
					},

					openEditModal(batch) {
						this.modalMode = 'edit';
						this.formData = {
							product_id: batch.product_id,
							batch_number: batch.batch_number,
							unit_cost: batch.unit_cost,
							quantity: batch.quantity_received,
							supplier: batch.supplier || '',
							expiry_date: batch.expiry_date || '',
							notes: batch.notes || '',
							_method: 'PUT'
						};
						this.showModal = true;
					},

					closeModal() {
						this.showModal = false;
						this.formData = {
							product_id: '',
							batch_number: '',
							unit_cost: '',
							quantity: '',
							supplier: '',
							expiry_date: '',
							notes: '',
							_method: 'POST'
						};
					},

					openDeleteConfirm(id, number) {
						this.deleteBatchId = id;
						this.deleteBatchNumber = number;
						this.showDeleteModal = true;
					}
				}))
			})
		</script>
	@endpush
@endsection
