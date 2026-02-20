@extends('layouts.app')

@section('title', 'Search — Kiddo\'s Heaven')

@section('content')
	{{-- Page Header --}}
	<div class="mb-6 sm:mb-8">
		<h1 class="text-xl sm:text-2xl md:text-3xl font-bold text-gray-900">Search Results</h1>
		<p class="text-gray-500 mt-1 text-sm sm:text-base">
			@if($products->count() > 0)
				Found {{ $products->total() }} results for "{{ $query }}"
			@else
				No results found for "{{ $query }}"
			@endif
		</p>
	</div>

	@if ($products->count() > 0)
		{{-- Results Grid --}}
		<div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-3 sm:gap-4 md:gap-5">
			@foreach ($products as $product)
				<x-shop.product.card :product="$product" />
			@endforeach
		</div>

		{{-- Pagination --}}
		@if ($products->hasPages())
			<div class="mt-8 sm:mt-10">
				{{ $products->links() }}
			</div>
		@endif
	@else
		{{-- Empty State --}}
		<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-8 sm:p-12 text-center">
			<span class="text-5xl sm:text-6xl mb-4 block">🔍</span>
			<h3 class="text-xl sm:text-2xl font-bold text-gray-800 mb-2">No products found</h3>
			<p class="text-gray-500 mb-6 sm:mb-8 max-w-md mx-auto">We couldn't find any products matching "{{ $query }}". Try different keywords or browse our catalog.</p>
			<div class="flex flex-col sm:flex-row gap-3 sm:gap-4 justify-center">
				<a href="{{ route('catalog') }}"
					class="inline-flex items-center justify-center gap-2 px-5 sm:px-6 py-2.5 sm:py-3 rounded-xl bg-primary text-white font-bold text-sm sm:text-base hover:bg-primary-dark transition">
					<svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
					</svg>
					Browse Catalog
				</a>
				<a href="{{ route('home') }}"
					class="inline-flex items-center justify-center gap-2 px-5 sm:px-6 py-2.5 sm:py-3 rounded-xl border border-gray-200 text-gray-600 font-bold text-sm sm:text-base hover:bg-gray-50 transition">
					Go Home
				</a>
			</div>
		</div>

		{{-- Suggestions --}}
		@php
			$categories = \App\Models\Category::where('is_active', true)->whereNull('parent_id')->with('children')->take(4)->get();
		@endphp
		@if($categories->isNotEmpty())
			<section class="mt-10 sm:mt-12">
				<h3 class="text-lg sm:text-xl font-bold text-gray-900 mb-4 sm:mb-6">Popular Categories</h3>
				<div class="grid grid-cols-2 sm:grid-cols-4 gap-3 sm:gap-4">
					@foreach($categories as $category)
						<a href="{{ route('catalog', ['category_id' => $category->id]) }}"
							class="group bg-white rounded-xl p-4 sm:p-6 text-center shadow-sm border border-gray-100 hover:shadow-lg hover:border-primary/30 transition-all duration-300">
							<span class="text-3xl sm:text-4xl block mb-2 sm:mb-3 group-hover:scale-110 transition">{{ $category->icon ?? '🧸' }}</span>
							<h4 class="font-bold text-gray-800 text-sm sm:text-base group-hover:text-primary transition">{{ $category->name }}</h4>
						</a>
					@endforeach
				</div>
			</section>
		@endif
	@endif
@endsection
