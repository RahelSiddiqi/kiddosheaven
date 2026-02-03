@extends('admin.layout')

@section('title', 'Catalogs — Admin')

@section('content')
	<!-- Modal -->
	<div id="catalogModal" class="fixed inset-0 bg-black/30 flex items-center justify-center z-50 hidden">
		<div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-md">
			<h3 class="text-lg font-bold mb-4">Add New Catalog</h3>
			<form method="POST" action="{{ route('admin.catalogs.store') }}">
				@csrf
				<div class="mb-4">
					<label for="name" class="block text-sm font-medium text-gray-700 mb-1">Catalog Name</label>
					<input type="text" name="name" id="name" required
						class="w-full border rounded px-3 py-2 focus:outline-none focus:ring focus:border-[--admin-primary]">
				</div>
				<div class="mb-4">
					<label class="inline-flex items-center">
						<input type="checkbox" name="show_on_home" value="1" class="accent-primary">
						<span class="ml-2 text-sm">Show on Home Page</span>
					</label>
				</div>
				<div class="flex justify-end gap-2">
					<button type="button" id="closeCatalogModal"
						class="inline-flex items-center px-4 py-2 rounded bg-gray-200 text-gray-700 border border-gray-300 hover:bg-gray-300 hover:text-gray-900 transition font-semibold">Cancel</button>
					<button type="submit"
						class="inline-flex items-center px-4 py-2 rounded bg-[color:var(--color-primary)] text-white border border-[color:var(--color-primary-dark)] hover:bg-[color:var(--color-primary-dark)] hover:text-white transition font-semibold">Add</button>
				</div>
			</form>
		</div>
	</div>

	<!-- Catalogs Table -->
	<div class="bg-white rounded-xl shadow p-6 mb-8">
		<div class="flex items-center justify-between pb-4 mb-4">
			<h2 class="text-xl font-bold text-[--admin-primary-dark]">Catalogs</h2>
			<button id="openCatalogModal"
				class="inline-flex items-center px-4 py-2 rounded bg-[color:var(--color-primary)] text-white border border-[color:var(--color-primary-dark)] hover:bg-[color:var(--color-primary-dark)] hover:text-white transition font-semibold">
				<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
					<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
				</svg>
				Add Catalog
			</button>
		</div>
		@if ($catalogs->isEmpty())
			<p>No catalogs yet. <button id="openCatalogModalInline"
					class="text-[--admin-primary] underline hover:text-[--admin-accent]">Add your first catalog</button></p>
			<script>
				document.getElementById('openCatalogModalInline').onclick = function() {
					document.getElementById('catalogModal').classList.remove('hidden');
				};
			</script>
		@else
			<div class="overflow-x-auto">
				<table class="min-w-full text-sm">
					<thead>
						<tr class="bg-[--admin-bg] text-[--admin-primary-dark] border-b border-gray-200">
							<th class="py-2 px-4 font-semibold">#</th>
							<th class="py-2 px-4 font-semibold">Name</th>
							<th class="py-2 px-4 font-semibold">Actions</th>
						</tr>
					</thead>
					<tbody>
						@foreach ($catalogs as $catalog)
							<tr class="border-b border-gray-200 last:border-0" data-catalog-id="{{ $catalog->id }}">
								<td class="py-2 px-4">{{ $loop->iteration }}</td>
								<td class="py-2 px-4">
									{{ $catalog->name }}
									@if ($catalog->show_on_home)
										<span class="ml-2 px-2 py-1 rounded bg-green-100 text-green-700 text-xs catalog-home-badge">Home</span>
									@endif
								</td>
								<td class="py-2 px-4">
									<div class="flex justify-end gap-2">
										<!-- Edit Button: opens modal -->
										<button type="button" onclick="openEditCatalogModal({{ $catalog->id }}, '{{ addslashes($catalog->name) }}')"
											class="inline-flex items-center gap-1 px-2 py-1 rounded border bg-primary/10 text-primary border-primary hover:bg-primary hover:text-white hover:border-primary focus:outline-none focus:ring-2 focus:ring-accent transition text-xs">
											<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
												stroke="currentColor">
												<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
													d="M15.232 5.232l3.536 3.536M9 13l6.293-6.293a1 1 0 011.414 0l2.586 2.586a1 1 0 010 1.414L13 17H9v-4z" />
											</svg>
											Edit
										</button>
										<!-- Delete Button: form -->
										<form action="{{ route('admin.catalogs.destroy', $catalog) }}" method="post" class="inline">
											@csrf
											@method('DELETE')
											<button type="submit"
												class="group inline-flex items-center gap-1 px-2 py-1 rounded border bg-danger/10 text-danger border-danger hover:bg-danger hover:text-white hover:border-danger focus:outline-none focus:ring-2 focus:ring-accent transition text-xs cursor-pointer"
												onclick="return confirm('Are you sure you want to delete this catalog?')">
												<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 group-hover:text-white transition" fill="none"
													viewBox="0 0 24 24" stroke="currentColor">
													<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
														d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5-4h4a2 2 0 012 2v2H7V5a2 2 0 012-2zm0 0v2m0-2v2" />
												</svg>
												Delete
											</button>
										</form>
										<!-- Edit Catalog Modal -->
										<div id="editCatalogModal" class="fixed inset-0 bg-black/30 flex items-center justify-center z-50 hidden">
											<div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-md">
												<h3 class="text-lg font-bold mb-4">Edit Catalog</h3>
												<form id="editCatalogForm" method="POST">
													@csrf
													@method('PUT')
													<div class="mb-4">
														<label for="edit_name" class="block text-sm font-medium text-gray-700 mb-1">Catalog Name</label>
														<input type="text" name="name" id="edit_name" required
															class="w-full border rounded px-3 py-2 focus:outline-none focus:ring focus:border-[--admin-primary]">
													</div>
													<div class="mb-4">
														<label class="inline-flex items-center">
															<input type="checkbox" name="show_on_home" id="edit_show_on_home" value="1" class="accent-primary">
															<span class="ml-2 text-sm">Show on Home Page</span>
														</label>
													</div>
													<div class="flex justify-end gap-2">
														<button type="button" id="closeEditCatalogModal"
															class="inline-flex items-center px-4 py-2 rounded bg-gray-200 text-gray-700 border border-gray-300 hover:bg-gray-300 hover:text-gray-900 transition font-semibold">Cancel</button>
														<button type="submit"
															class="inline-flex items-center px-4 py-2 rounded bg-[color:var(--color-primary)] text-white border border-[color:var(--color-primary-dark)] hover:bg-[color:var(--color-primary-dark)] hover:text-white transition font-semibold">Save</button>
													</div>
												</form>
											</div>
										</div>
									</div>
								</td>
							</tr>
						@endforeach
					</tbody>
				</table>
			</div>
		@endif
	</div>

	<script>
		document.getElementById('openCatalogModal').onclick = function() {
			document.getElementById('catalogModal').classList.remove('hidden');
		};
		document.getElementById('closeCatalogModal').onclick = function() {
			document.getElementById('catalogModal').classList.add('hidden');
		};

		// Edit modal logic
		function openEditCatalogModal(id, name) {
			document.getElementById('editCatalogModal').classList.remove('hidden');
			document.getElementById('edit_name').value = name;
			var form = document.getElementById('editCatalogForm');
			form.action = '/admin/catalogs/' + id;
			// Set checkbox state based on catalog row
			var row = document.querySelector('tr[data-catalog-id="' + id + '"]');
			var isHome = row && row.querySelector('.catalog-home-badge');
			document.getElementById('edit_show_on_home').checked = !!isHome;
		}
		document.getElementById('closeEditCatalogModal').onclick = function() {
			document.getElementById('editCatalogModal').classList.add('hidden');
		};
	</script>
@endsection
