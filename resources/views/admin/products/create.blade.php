@extends('admin.layout')

@section('title', 'Create Product — Admin')

@section('content')
	<div class="container bg-white rounded-xl shadow p-6 mx-auto">
		<div class="flex items-center justify-between mb-6 border-b pb-4">
			<h2 class="text-xl font-bold text-primary-dark">Create Product</h2>
			<a href="{{ route('admin.products.index') }}"
				class="inline-flex items-center px-4 py-2 rounded border border-primary text-primary bg-white hover:bg-primary hover:text-white transition font-semibold">Back
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

		<form action="{{ route('admin.products.store') }}" method="post" enctype="multipart/form-data">
			@csrf

			<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
				<div class="md:col-span-2 flex flex-col gap-1">
					<label for="name" class="font-semibold">Product Name *</label>
					<input type="text" id="name" name="name" value="{{ old('name') }}" required
						class="block w-full rounded-md border border-gray-300 focus:border-primary focus:ring-primary shadow-sm py-2 px-3 text-sm">
					@error('name')
						<div class="text-red-600 text-xs mt-1">{{ $message }}</div>
					@enderror
				</div>

				<div class="flex flex-col gap-1">
					<label for="catalog_id" class="font-semibold">Category *</label>
					<select id="catalog_id" name="catalog_id" required
						class="block w-full rounded-md border border-gray-300 focus:border-primary focus:ring-primary shadow-sm py-2 px-3 text-sm">
						@error('catalog_id')
							<div class="text-red-600 text-xs mt-1">{{ $message }}</div>
						@enderror
						<option value="">Select category</option>
						@foreach ($catalogs as $catalog)
							<option value="{{ $catalog->id }}" {{ old('catalog_id') == $catalog->id ? 'selected' : '' }}>
								{{ $catalog->name }}</option>
						@endforeach
					</select>
				</div>

				<div class="flex flex-col gap-1">
					<label for="price" class="font-semibold">Price (in cents) *</label>
					<input type="number" id="price" name="price" value="{{ old('price') }}" min="1" required
						class="block w-full rounded-md border border-gray-300 focus:border-primary focus:ring-primary shadow-sm py-2 px-3 text-sm">
					@error('price')
						<div class="text-red-600 text-xs mt-1">{{ $message }}</div>
					@enderror
					<small class="text-gray-500 text-xs">e.g., 3000 = $30.00</small>
				</div>

				<div class="md:col-span-2 flex flex-col gap-1">
					<label for="images" class="font-semibold">Product Images</label>
					<input type="file" id="images" name="images[]" multiple accept="image/*"
						class="block w-full rounded-md border border-gray-300 focus:border-primary focus:ring-primary shadow-sm py-2 px-3 text-sm">
					@error('images')
						<div class="text-red-600 text-xs mt-1">{{ $message }}</div>
					@enderror
					@error('images.0')
						<div class="text-red-600 text-xs mt-1">{{ $message }}</div>
					@enderror
					<div id="image-preview" class="flex flex-wrap gap-2 mt-2"></div>
				</div>

				<div class="md:col-span-2 flex flex-col gap-1">
					<label for="short_description" class="font-semibold">Short Description</label>
					<input type="text" id="short_description" name="short_description" value="{{ old('short_description') }}"
						maxlength="500"
						class="block w-full rounded-md border border-gray-300 focus:border-primary focus:ring-primary shadow-sm py-2 px-3 text-sm">
					@error('short_description')
						<div class="text-red-600 text-xs mt-1">{{ $message }}</div>
					@enderror
				</div>

				<div class="md:col-span-2 flex flex-col gap-1">
					<label for="description" class="font-semibold">Description</label>
					<textarea id="description" name="description" rows="4"
					 class="block w-full rounded-md border border-gray-300 focus:border-primary focus:ring-primary shadow-sm py-2 px-3 text-sm">{{ old('description') }}</textarea>
					@error('description')
						<div class="text-red-600 text-xs mt-1">{{ $message }}</div>
					@enderror
				</div>

				<div class="flex items-center gap-2">
					<input type="checkbox" name="is_featured" value="1" id="is_featured" {{ old('is_featured') ? 'checked' : '' }}
						class="accent-primary">
					<label for="is_featured" class="font-semibold">Featured Product</label>
				</div>
			</div>

			<div class="mt-8 flex gap-4">
				<button type="submit"
					class="inline-flex items-center px-6 py-2 rounded bg-primary text-white font-bold shadow hover:bg-primary-dark transition">Create
					Product</button>
				<a href="{{ route('admin.products.index') }}"
					class="inline-flex items-center px-6 py-2 rounded border border-danger text-danger bg-white hover:bg-danger hover:text-white transition font-semibold">Cancel</a>
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
