{{-- shop/partials/filters.blade.php --}}
{{-- This partial contains the filter sidebar/drawer content for both mobile and desktop --}}

{{-- Category Filter --}}
<div class="mb-6">
	<h4 class="font-semibold text-gray-800 mb-3 flex items-center gap-2">
		<svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
			<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
				d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
		</svg>
		Categories
	</h4>
	<div class="space-y-1">
		<a href="{{ route('catalog') }}"
			class="flex items-center justify-between px-3 py-2.5 rounded-lg transition text-sm {{ !$activeCategory ? 'bg-primary/10 text-primary font-medium' : 'text-gray-600 hover:bg-gray-50' }}">
			<span>All Products</span>
			<span class="text-xs text-gray-400">{{ $products->total() }}</span>
		</a>
		@foreach ($categories as $category)
			<div class="category-item">
				<a href="{{ route('catalog', ['category_id' => $category->id]) }}"
					class="flex items-center justify-between px-3 py-2.5 rounded-lg transition text-sm {{ $activeCategory == $category->id ? 'bg-primary/10 text-primary font-medium' : 'text-gray-600 hover:bg-gray-50' }}">
					<span>{{ $category->name }}</span>
					@if ($category->children && $category->children->count() > 0)
						<span class="category-toggle text-gray-400 hover:text-primary cursor-pointer"
							onclick="event.preventDefault(); event.stopPropagation(); this.closest('.category-item').classList.toggle('expanded')">
							<svg class="w-4 h-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
							</svg>
						</span>
					@else
						<span class="text-xs text-gray-400">→</span>
					@endif
				</a>
				@if ($category->children && $category->children->count() > 0)
					<div class="category-children hidden pl-4 mt-1 space-y-1 border-l-2 border-gray-100 ml-2">
						@foreach ($category->children as $child)
							<a href="{{ route('catalog', ['category_id' => $child->id]) }}"
								class="flex items-center justify-between px-3 py-2 rounded-lg transition text-sm {{ $activeCategory == $child->id ? 'bg-primary/10 text-primary font-medium' : 'text-gray-500 hover:bg-gray-50' }}">
								<span>{{ $child->name }}</span>
								<span class="text-xs text-gray-400">→</span>
							</a>
						@endforeach
					</div>
				@endif
			</div>
		@endforeach
	</div>
</div>

{{-- Brand Filter --}}
@if ($brands->isNotEmpty())
	<div class="mb-6">
		<h4 class="font-semibold text-gray-800 mb-3 flex items-center gap-2">
			<svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
				<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
					d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
			</svg>
			Brands
		</h4>
		<div class="space-y-1">
			@foreach ($brands as $brand)
				<a href="{{ route('catalog', array_merge(request()->except('page'), ['brand_id' => $brand->id])) }}"
					class="flex items-center justify-between px-3 py-2.5 rounded-lg transition text-sm {{ $activeBrand == $brand->id ? 'bg-primary/10 text-primary font-medium' : 'text-gray-600 hover:bg-gray-50' }}">
					<span>{{ $brand->name }}</span>
					<span class="text-xs text-gray-400">→</span>
				</a>
			@endforeach
		</div>
	</div>
@endif

{{-- Price Filter --}}
<div class="mb-6">
	<h4 class="font-semibold text-gray-800 mb-3 flex items-center gap-2">
		<svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
			<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
				d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
		</svg>
		Price Range
	</h4>
	<div class="space-y-1">
		<a href="{{ route('catalog', array_merge(request()->except('page'), ['price' => 'under-10'])) }}"
			class="flex items-center gap-2 px-3 py-2.5 rounded-lg transition text-sm {{ $activePrice == 'under-10' ? 'bg-primary/10 text-primary font-medium' : 'text-gray-600 hover:bg-gray-50' }}">
			<span>Under ৳1,000</span>
		</a>
		<a href="{{ route('catalog', array_merge(request()->except('page'), ['price' => '10-25'])) }}"
			class="flex items-center gap-2 px-3 py-2.5 rounded-lg transition text-sm {{ $activePrice == '10-25' ? 'bg-primary/10 text-primary font-medium' : 'text-gray-600 hover:bg-gray-50' }}">
			<span>৳1,000 - ৳2,500</span>
		</a>
		<a href="{{ route('catalog', array_merge(request()->except('page'), ['price' => '25-50'])) }}"
			class="flex items-center gap-2 px-3 py-2.5 rounded-lg transition text-sm {{ $activePrice == '25-50' ? 'bg-primary/10 text-primary font-medium' : 'text-gray-600 hover:bg-gray-50' }}">
			<span>৳2,500 - ৳5,000</span>
		</a>
		<a href="{{ route('catalog', array_merge(request()->except('page'), ['price' => 'over-50'])) }}"
			class="flex items-center gap-2 px-3 py-2.5 rounded-lg transition text-sm {{ $activePrice == 'over-50' ? 'bg-primary/10 text-primary font-medium' : 'text-gray-600 hover:bg-gray-50' }}">
			<span>Over ৳5,000</span>
		</a>
	</div>
</div>

{{-- Age Group Filter --}}
<div class="mb-6">
	<h4 class="font-semibold text-gray-800 mb-3 flex items-center gap-2">
		<svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
			<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
				d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
		</svg>
		Age Group
	</h4>
	<div class="space-y-1">
		<a href="{{ route('catalog', array_merge(request()->all(), ['age' => '0-2'])) }}"
			class="flex items-center gap-2 px-3 py-2.5 rounded-lg text-gray-600 hover:bg-gray-50 transition text-sm">
			<span>0-2 years</span>
		</a>
		<a href="{{ route('catalog', array_merge(request()->all(), ['age' => '3-5'])) }}"
			class="flex items-center gap-2 px-3 py-2.5 rounded-lg text-gray-600 hover:bg-gray-50 transition text-sm">
			<span>3-5 years</span>
		</a>
		<a href="{{ route('catalog', array_merge(request()->all(), ['age' => '6-8'])) }}"
			class="flex items-center gap-2 px-3 py-2.5 rounded-lg text-gray-600 hover:bg-gray-50 transition text-sm">
			<span>6-8 years</span>
		</a>
		<a href="{{ route('catalog', array_merge(request()->all(), ['age' => '9+'])) }}"
			class="flex items-center gap-2 px-3 py-2.5 rounded-lg text-gray-600 hover:bg-gray-50 transition text-sm">
			<span>9+ years</span>
		</a>
	</div>
</div>

{{-- Features Filter --}}
<div>
	<h4 class="font-semibold text-gray-800 mb-3 flex items-center gap-2">
		<svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
			<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
				d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
		</svg>
		Features
	</h4>
	<div class="space-y-1">
		<a href="{{ route('catalog', array_merge(request()->all(), ['featured' => '1'])) }}"
			class="flex items-center gap-2 px-3 py-2.5 rounded-lg text-gray-600 hover:bg-gray-50 transition text-sm">
			<span>Featured</span>
		</a>
		<a href="{{ route('catalog', array_merge(request()->all(), ['new' => '1'])) }}"
			class="flex items-center gap-2 px-3 py-2.5 rounded-lg text-gray-600 hover:bg-gray-50 transition text-sm">
			<span>New Arrivals</span>
		</a>
		<a href="{{ route('catalog', array_merge(request()->all(), ['sale' => '1'])) }}"
			class="flex items-center gap-2 px-3 py-2.5 rounded-lg text-gray-600 hover:bg-gray-50 transition text-sm">
			<span>On Sale</span>
		</a>
	</div>
</div>
