@extends('admin.layout')

@section('title', 'Products — Admin')

@section('content')
	<div class="bg-white rounded-xl shadow p-6 mb-8">
		<div class="flex items-center justify-between pb-4 mb-4">
			<h2 class="text-xl font-bold text-[--admin-primary-dark]">Products</h2>
			<a href="{{ route('admin.products.create') }}"
				class="inline-flex items-center px-4 py-2 rounded bg-[color:var(--color-primary)] text-white border border-[color:var(--color-primary-dark)] hover:bg-[color:var(--color-primary-dark)] hover:text-white transition font-semibold">
				<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
					<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
				</svg>
				Add Product
			</a>
		</div>
		@if ($products->isEmpty())
			<p>No products yet. <a href="{{ route('admin.products.create') }}"
					class="text-[--admin-primary] underline hover:text-[--admin-accent]">Add your first product</a>
			</p>
		@else
			<div class="overflow-x-auto">
				<table class="min-w-full text-sm">
					<thead>
						<tr class="bg-[--admin-bg] text-[--admin-primary-dark] border-b border-gray-200">
							<th class="py-2 px-3 font-semibold">Image</th>
							<th class="py-2 px-3 font-semibold">Name</th>
							<th class="py-2 px-3 font-semibold">Category</th>
							<th class="py-2 px-3 font-semibold">Price</th>
							<th class="py-2 px-3 font-semibold">Featured</th>
							<th class="py-2 px-3 font-semibold">Actions</th>
						</tr>
					</thead>
					<tbody>
						@foreach ($products as $product)
							<tr class="border-b border-gray-200 last:border-0">
								<td class="py-2 px-3">
									@php
										$img = $product->primary_image ?? ($product->images[0] ?? null);
									@endphp
									@if ($img)
										<img src="{{ asset('storage/' . $img) }}" alt="{{ $product->name }}" class="w-12 h-12 object-cover rounded">
									@else
										<div class="w-12 h-12 bg-gray-100 rounded flex items-center justify-center text-gray-400">
											<svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
												stroke="currentColor">
												<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
													d="M3 7v10a4 4 0 004 4h10a4 4 0 004-4V7a4 4 0 00-4-4H7a4 4 0 00-4 4z" />
											</svg>
										</div>
									@endif
								</td>
								<td class="py-2 px-3">{{ $product->name }}</td>
								<td class="py-2 px-3">{{ $product->catalog->name ?? '-' }}</td>
								<td class="py-2 px-3">${{ number_format($product->price / 100, 2) }}</td>
								<td class="py-2 px-3">
									@if ($product->is_featured)
										<span class="text-[--admin-accent] font-bold">✓</span>
									@else
										<span class="text-[--admin-primary-dark]">—</span>
									@endif
								</td>
								<td class="py-2 px-3">
									<div class="flex justify-end gap-2">
										<a href="{{ route('admin.products.edit', $product) }}"
											class="inline-flex items-center gap-1 px-2 py-1 rounded border bg-primary/10 text-primary border-primary hover:bg-primary hover:text-white hover:border-primary focus:outline-none focus:ring-2 focus:ring-accent transition text-xs">
											<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
												stroke="currentColor">
												<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
													d="M15.232 5.232l3.536 3.536M9 13l6.293-6.293a1 1 0 011.414 0l2.586 2.586a1 1 0 010 1.414L13 17H9v-4z" />
											</svg>
											Edit
										</a>
										<form action="{{ route('admin.products.destroy', $product) }}" method="post" class="inline">
											@csrf
											@method('DELETE')
											<button type="submit"
												class="group inline-flex items-center gap-1 px-2 py-1 rounded border bg-danger/10 text-danger border-danger hover:bg-danger hover:text-white hover:border-danger focus:outline-none focus:ring-2 focus:ring-accent transition text-xs cursor-pointer"
												onclick="return confirm('Are you sure?')">
												<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 group-hover:text-white transition" fill="none"
													viewBox="0 0 24 24" stroke="currentColor">
													<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
														d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5-4h4a2 2 0 012 2v2H7V5a2 2 0 012-2zm0 0v2m0-2v2" />
												</svg>
												Delete
											</button>
										</form>
									</div>
								</td>
							</tr>
						@endforeach
					</tbody>
				</table>
			</div>
			<div class="mt-6">
				{{ $products->links('vendor.pagination.default') }}
			</div>
		@endif
	</div>
@endsection
