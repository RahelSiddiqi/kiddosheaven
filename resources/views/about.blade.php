@extends('layouts.app')

@section('title', 'About Us — Kiddo\'s Heaven')

@section('content')
	{{-- Hero --}}
	<div class="bg-[#F5F5F5] rounded-2xl p-8 md:p-12 mb-10">
		<div class="max-w-3xl">
			<h1 class="text-3xl md:text-4xl font-bold text-[#1E1414] mb-4">About {{ $siteSettings['site_name'] ?? "Kiddo's Heaven" }}</h1>
			<p class="text-[#666] text-lg">Making childhood magical since {{ $siteSettings['about_founded_year'] ?? '2020' }}</p>
		</div>
	</div>

	{{-- Story --}}
	<div class="grid lg:grid-cols-2 gap-8 md:gap-12 mb-12 md:mb-14">
		<div class="order-2 lg:order-1">
			<h2 class="text-xl md:text-2xl font-bold text-[#1E1414] mb-4">Where Imagination Comes to Play</h2>
			<div class="space-y-4 text-[#666]">
				<p>Welcome to Kiddo's Heaven, your ultimate destination for premium toys and games that inspire creativity, learning, and endless fun for children of all ages.</p>
				<p>Founded with a passion for bringing joy to children's faces, we've curated an exceptional collection of toys that meet the highest standards of safety, quality, and educational value. From cuddly stuffed animals to innovative educational games, every item in our store is carefully selected to delight and develop young minds.</p>
				<p>We believe that play is essential for healthy childhood development. That's why we work with renowned brands and emerging designers to bring you the best toys from around the world.</p>
			</div>
		</div>
		<div class="order-1 lg:order-2 bg-[#E8F4F4] rounded-2xl aspect-[4/3] flex items-center justify-center">
			<span class="text-8xl">🏰</span>
		</div>
	</div>

	{{-- Stats --}}
	<div class="grid grid-cols-2 md:grid-cols-4 gap-4 md:gap-6 mb-12 md:mb-14">
		<div class="bg-white rounded-xl p-5 md:p-6 text-center shadow-sm border border-[#E8E6E6]">
			<p class="text-2xl md:text-3xl font-bold text-[#018790]">{{ $siteSettings['about_happy_customers'] ?? '50K+' }}</p>
			<p class="text-[#666] text-sm mt-1">Happy Customers</p>
		</div>
		<div class="bg-white rounded-xl p-5 md:p-6 text-center shadow-sm border border-[#E8E6E6]">
			<p class="text-2xl md:text-3xl font-bold text-[#018790]">{{ $siteSettings['about_products_count'] ?? '2K+' }}</p>
			<p class="text-[#666] text-sm mt-1">Products</p>
		</div>
		<div class="bg-white rounded-xl p-5 md:p-6 text-center shadow-sm border border-[#E8E6E6]">
			<p class="text-2xl md:text-3xl font-bold text-[#018790]">{{ $siteSettings['about_rating'] ?? '5' }}★</p>
			<p class="text-[#666] text-sm mt-1">Rating</p>
		</div>
		<div class="bg-white rounded-xl p-5 md:p-6 text-center shadow-sm border border-[#E8E6E6]">
			<p class="text-2xl md:text-3xl font-bold text-[#018790]">{{ $siteSettings['about_years_experience'] ?? '5+' }}</p>
			<p class="text-[#666] text-sm mt-1">Years Experience</p>
		</div>
	</div>

	{{-- Values --}}
	<div class="mb-12 md:mb-14">
		<h2 class="text-xl md:text-2xl font-bold text-[#1E1414] text-center mb-8">Our Values</h2>
		<div class="grid md:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6">
			<div class="bg-white rounded-xl p-5 md:p-6 text-center shadow-sm border border-[#E8E6E6]">
				<div class="w-12 h-12 mx-auto mb-3 rounded-full bg-[#E8F4F4] flex items-center justify-center">
					<svg class="w-6 h-6 text-[#018790]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
				</div>
				<h3 class="font-bold text-[#1E1414] mb-2">Safety First</h3>
				<p class="text-[#666] text-sm">Every toy meets strict safety standards</p>
			</div>
			<div class="bg-white rounded-xl p-5 md:p-6 text-center shadow-sm border border-[#E8E6E6]">
				<div class="w-12 h-12 mx-auto mb-3 rounded-full bg-[#E8F4F4] flex items-center justify-center">
					<svg class="w-6 h-6 text-[#018790]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519a1 1 0 00.95.69 4.674h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
				</div>
				<h3 class="font-bold text-[#1E1414] mb-2">Quality</h3>
				<p class="text-[#666] text-sm">Premium products built to last</p>
			</div>
			<div class="bg-white rounded-xl p-5 md:p-6 text-center shadow-sm border border-[#E8E6E6]">
				<div class="w-12 h-12 mx-auto mb-3 rounded-full bg-[#E8F4F4] flex items-center justify-center">
					<svg class="w-6 h-6 text-[#018790]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
				</div>
				<h3 class="font-bold text-[#1E1414] mb-2">Fun & Learning</h3>
				<p class="text-[#666] text-sm">Toys that educate while entertaining</p>
			</div>
			<div class="bg-white rounded-xl p-5 md:p-6 text-center shadow-sm border border-[#E8E6E6]">
				<div class="w-12 h-12 mx-auto mb-3 rounded-full bg-[#E8F4F4] flex items-center justify-center">
					<svg class="w-6 h-6 text-[#018790]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
				</div>
				<h3 class="font-bold text-[#1E1414] mb-2">Love & Care</h3>
				<p class="text-[#666] text-sm">Every child deserves the best</p>
			</div>
		</div>
	</div>

	{{-- Why Choose Us --}}
	<div class="grid md:grid-cols-2 gap-4 md:gap-6 mb-12 md:mb-14">
		<div class="bg-[#F5F5F5] rounded-xl p-5 md:p-6">
			<div class="flex items-start gap-4">
				<div class="w-12 h-12 rounded-xl bg-[#018790] flex items-center justify-center flex-shrink-0">
					<svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
				</div>
				<div>
					<h3 class="font-bold text-[#1E1414] mb-2">Fast Delivery</h3>
					@php
						$threshold = $siteSettings['free_shipping_threshold'] ?? 0;
						$currency  = $siteSettings['currency'] ?? 'BDT';
						$currSym   = $currency === 'BDT' ? '৳' : ($currency === 'USD' ? '$' : $currency);
						$codOn     = ($siteSettings['cod_enabled'] ?? '1') == '1';
					@endphp
					<p class="text-[#666] text-sm">
						{{ $threshold > 0 ? 'Free delivery on orders over '.$currSym.number_format($threshold,0).'. ' : '' }}
						{{ $codOn ? 'Cash on delivery available.' : '' }}
					</p>
				</div>
			</div>
		</div>
		<div class="bg-[#F5F5F5] rounded-xl p-5 md:p-6">
			<div class="flex items-start gap-4">
				<div class="w-12 h-12 rounded-xl bg-[#018790] flex items-center justify-center flex-shrink-0">
					<svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
				</div>
				<div>
					<h3 class="font-bold text-[#1E1414] mb-2">Easy Returns</h3>
					<p class="text-[#666] text-sm">{{ ($siteSettings['return_policy_days'] ?? 30) }}-day return policy on all unopened items.</p>
				</div>
			</div>
		</div>
		<div class="bg-[#F5F5F5] rounded-xl p-5 md:p-6">
			<div class="flex items-start gap-4">
				<div class="w-12 h-12 rounded-xl bg-[#018790] flex items-center justify-center flex-shrink-0">
					<svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
				</div>
				<div>
					<h3 class="font-bold text-[#1E1414] mb-2">24/7 Support</h3>
					<p class="text-[#666] text-sm">Dedicated customer service team always ready.</p>
				</div>
			</div>
		</div>
		<div class="bg-[#F5F5F5] rounded-xl p-5 md:p-6">
			<div class="flex items-start gap-4">
				<div class="w-12 h-12 rounded-xl bg-[#018790] flex items-center justify-center flex-shrink-0">
					<svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
				</div>
				<div>
					<h3 class="font-bold text-[#1E1414] mb-2">Secure Payments</h3>
					<p class="text-[#666] text-sm">Multiple payment options including bKash, Nagad.</p>
				</div>
			</div>
		</div>
	</div>

	{{-- CTA --}}
	<div class="bg-gradient-to-r from-[#018790] to-[#00B7B5] rounded-2xl p-8 md:p-12 text-center text-white mb-8">
		<h2 class="text-2xl md:text-3xl font-bold mb-4">Ready to Make Some Memories?</h2>
		<p class="text-white/80 mb-6 max-w-xl mx-auto">Explore our collection of handpicked toys that will bring joy and laughter to your home.</p>
		<a href="{{ route('catalog') }}" class="inline-flex items-center gap-2 px-8 py-4 rounded-xl bg-white text-[#018790] font-bold hover:bg-gray-100 transition shadow-lg">
			Shop Now
			<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
		</a>
	</div>
@endsection
