@extends('admin.layout')

@section('title', 'Edit Product — Admin')

@section('content')
	<div class="container bg-white rounded-xl shadow p-6 mx-auto">
		<div class="flex items-center justify-between mb-6 border-b pb-4">
			<h2 class="text-xl font-bold text-[var(--color-primary-dark)]">Edit Product</h2>
			<a href="{{ route('admin.products.index') }}"
				class="inline-flex items-center px-4 py-2 rounded border border-[var(--color-primary)] text-[var(--color-primary)] bg-white hover:bg-[var(--color-primary)] hover:text-white transition font-semibold">Back
				to Products</a>
		</div>

		@if ($errors->any())
			<div class="mb-4 p-4 rounded bg-red-50 border border-red-200 text-red-700">
				<ul class="list-disc pl-5">
					@foreach ($errors->all() as $error)
						<li>{{ $error }}</li>
					@endforeach
				</ul>
			</div>
		@endif

		<form action="{{ route('admin.products.update', $product) }}" method="post" enctype="multipart/form-data">
			@csrf
			@method('PUT')

			<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
				<div class="md:col-span-2 flex flex-col gap-1">
					<label for="name" class="font-semibold">Product Name *</label>
					<input type="text" id="name" name="name" value="{{ old('name', $product->name) }}" required
						class="block w-full rounded-md border border-gray-300 focus:border-[var(--color-primary)] focus:ring-[var(--color-primary)] shadow-sm py-2 px-3 text-sm">
					@error('name')
						<div class="text-red-600 text-xs mt-1">{{ $message }}</div>
					@enderror
				</div>

				<div class="flex flex-col gap-1">
					<label for="catalog_id" class="font-semibold">Category *</label>
					<select id="catalog_id" name="catalog_id" required
						class="block w-full rounded-md border border-gray-300 focus:border-[var(--color-primary)] focus:ring-[var(--color-primary)] shadow-sm py-2 px-3 text-sm">
						@error('catalog_id')
							<div class="text-red-600 text-xs mt-1">{{ $message }}</div>
						@enderror
						<option value="">Select category</option>
						@foreach ($catalogs as $catalog)
							<option value="{{ $catalog->id }}"
								{{ old('catalog_id', $product->catalog_id) == $catalog->id ? 'selected' : '' }}>
								{{ $catalog->name }}</option>
						@endforeach
					</select>
				</div>

				<div class="flex flex-col gap-1">
					<label for="price" class="font-semibold">Price (in cents) *</label>
					<input type="number" id="price" name="price" value="{{ old('price', $product->price) }}" min="1"
						required
						class="block w-full rounded-md border border-gray-300 focus:border-[var(--color-primary)] focus:ring-[var(--color-primary)] shadow-sm py-2 px-3 text-sm">
					@error('price')
						<div class="text-red-600 text-xs mt-1">{{ $message }}</div>
					@enderror
					<small class="text-gray-500 text-xs">e.g., 3000 = $30.00</small>
				</div>

				<div class="md:col-span-2 flex flex-col gap-1">
					<label for="images" class="font-semibold">Product Images</label>
					<input type="file" id="images" name="images[]" multiple accept="image/*"
						class="block w-full rounded-md border border-gray-300 focus:border-[var(--color-primary)] focus:ring-[var(--color-primary)] shadow-sm py-2 px-3 text-sm">
					@error('images')
						<div class="text-red-600 text-xs mt-1">{{ $message }}</div>
					@enderror
					@error('images.0')
						<div class="text-red-600 text-xs mt-1">{{ $message }}</div>
					@enderror
					<div id="image-preview" class="flex flex-wrap gap-2 mt-2"></div>
					@if ($product->images && is_array($product->images))
						<div class="mt-2 flex flex-wrap gap-4">
							@foreach ($product->images as $img)
								<div class="relative group flex flex-col items-center">
									<div class="relative">
										<img src="{{ asset('storage/' . $img) }}" alt="{{ $product->name }}"
											class="h-20 w-20 object-cover rounded border mb-1 @if ($product->primary_image === $img) ring-4 ring-[var(--color-primary)] @endif">
										<button type="submit" name="delete_image" value="{{ $img }}"
											class="absolute top-1 right-1 bg-white/80 rounded-full p-1 shadow hover:bg-[var(--color-danger)] hover:text-white text-[var(--color-danger)] transition cursor-pointer"
											title="Delete image">
											<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
												stroke="currentColor">
												<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
													d="M6 7h12M9 7V5a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v2m2 0v12a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2V7m3 4v6m4-6v6" />
											</svg>
										</button>
									</div>
									<label
										class="text-xs cursor-pointer flex items-center gap-1 mt-1 px-2 py-1 rounded @if ($product->primary_image === $img) bg-[var(--color-primary)] text-white font-bold @else bg-gray-100 text-gray-700 @endif transition">
										<input type="radio" name="primary_image" value="{{ $img }}"
											@if ($product->primary_image === $img) checked @endif class="accent-[var(--color-primary)]">
										<span>Primary</span>
									</label>
								</div>
							@endforeach
						</div>
					@endif
				</div>

				<div class="md:col-span-2 flex flex-col gap-1">
					<label for="short_description" class="font-semibold">Short Description</label>
					<input type="text" id="short_description" name="short_description"
						value="{{ old('short_description', $product->short_description) }}" maxlength="500"
						class="block w-full rounded-md border border-gray-300 focus:border-[var(--color-primary)] focus:ring-[var(--color-primary)] shadow-sm py-2 px-3 text-sm">
					@error('short_description')
						<div class="text-red-600 text-xs mt-1">{{ $message }}</div>
					@enderror
				</div>

				<div class="md:col-span-2 flex flex-col gap-1">
					<label for="description" class="font-semibold">Description</label>
					<textarea id="description" name="description" rows="4"
					 class="block w-full rounded-md border border-gray-300 focus:border-[var(--color-primary)] focus:ring-[var(--color-primary)] shadow-sm py-2 px-3 text-sm">{{ old('description', $product->description) }}</textarea>
					@error('description')
						<div class="text-red-600 text-xs mt-1">{{ $message }}</div>
					@enderror
				</div>

				<div class="flex items-center gap-2">
					<input type="checkbox" name="is_featured" value="1" id="is_featured"
						{{ old('is_featured', $product->is_featured) ? 'checked' : '' }} class="accent-[var(--color-primary)]">
					<label for="is_featured" class="font-semibold">Featured Product</label>
				</div>
			</div>

			<div class="mt-8 flex gap-4">
				<button type="submit"
					class="inline-flex items-center px-6 py-2 rounded bg-[var(--color-primary)] text-white font-bold shadow hover:bg-[var(--color-primary-dark)] transition">Update
					Product</button>
				<a href="{{ route('admin.products.index') }}"
					class="inline-flex items-center px-6 py-2 rounded border border-[var(--color-danger)] text-[var(--color-danger)] bg-white hover:bg-[var(--color-danger)] hover:text-white transition font-semibold">Cancel</a>
			</div>
		</form>
	</div>
	@push('scripts')
		<script>
			document.addEventListener('DOMContentLoaded', function() {
				const input = document.getElementById('images');
				const preview = document.getElementById('image-preview');
				if (input) {
					input.addEventListener('change', function(e) {
						preview.innerHTML = '';
						Array.from(e.target.files).forEach(file => {
							if (!file.type.startsWith('image/')) return;
							const reader = new FileReader();
							reader.onload = function(ev) {
								const img = document.createElement('img');
								img.src = ev.target.result;
								img.className = 'h-20 w-20 object-cover rounded border';
								preview.appendChild(img);
							};
							reader.readAsDataURL(file);
						});
					});
				}
			});
		</script>
	@endpush
@endsection
