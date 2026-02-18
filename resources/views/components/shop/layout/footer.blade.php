@php
	$siteEmail = \App\Models\Setting::where('key', 'site_email')->value('value') ?? 'hello@kiddosheaven.com';
	$sitePhone = \App\Models\Setting::where('key', 'site_phone')->value('value') ?? '+880 1234 567 890';
	$siteAddress = \App\Models\Setting::where('key', 'site_address')->value('value') ?? '123 Toy Street, Dhaka';
@endphp

<footer class="bg-gray-900 text-gray-400 mt-auto pb-18 md:pb-0">
	{{-- Newsletter --}}
	<div class="bg-primary">
		<div class="container mx-auto px-4 py-8">
			<div class="flex flex-col sm:flex-row items-center justify-between gap-4">
				<div class="flex items-center gap-3">
					<div class="w-10 h-10 rounded-full bg-white/10 flex items-center justify-center flex-shrink-0">
						<svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
								d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
						</svg>
					</div>
					<div>
						<p class="text-white font-bold text-sm sm:text-base">Subscribe to our newsletter</p>
						<p class="text-white/70 text-xs sm:text-sm">Get the latest offers & new arrivals</p>
					</div>
				</div>
				<form action="#" class="flex w-full sm:w-auto">
					<input type="email" placeholder="Your email address"
						class="flex-1 sm:w-52 md:w-64 px-4 py-2.5 rounded-l-lg border border-gray-600 focus:ring-2 focus:ring-white/30 outline-none text-sm text-gray-800">
					<button type="submit"
						class="px-5 py-2.5 bg-primary-dark text-white font-semibold rounded-r-lg hover:bg-gray-900 transition text-sm whitespace-nowrap">Subscribe</button>
				</form>
			</div>
		</div>
	</div>

	{{-- Main Footer --}}
	<div class="container mx-auto px-4 py-10 sm:py-12">
		<div class="grid grid-cols-2 sm:grid-cols-4 gap-8">
			{{-- Company --}}
			<div>
				<h3 class="text-white font-bold mb-4 text-sm uppercase tracking-wider">Company</h3>
				<ul class="space-y-2.5">
					<li><a href="{{ route('about') }}" class="text-sm hover:text-white transition">About Us</a></li>
					<li><a href="#" class="text-sm hover:text-white transition">Careers</a></li>
					<li><a href="{{ route('contact') }}" class="text-sm hover:text-white transition">Contact</a></li>
				</ul>
			</div>
			{{-- Shop --}}
			<div>
				<h3 class="text-white font-bold mb-4 text-sm uppercase tracking-wider">Shop</h3>
				<ul class="space-y-2.5">
					<li><a href="{{ route('catalog') }}" class="text-sm hover:text-white transition">All Products</a></li>
					<li><a href="{{ route('catalog', ['featured' => 1]) }}" class="text-sm hover:text-white transition">Featured</a>
					</li>
					<li><a href="{{ route('track.order') }}" class="text-sm hover:text-white transition">Track Order</a></li>
				</ul>
			</div>
			{{-- Support --}}
			<div>
				<h3 class="text-white font-bold mb-4 text-sm uppercase tracking-wider">Support</h3>
				<ul class="space-y-2.5">
					<li><a href="#" class="text-sm hover:text-white transition">FAQs</a></li>
					<li><a href="{{ route('contact') }}" class="text-sm hover:text-white transition">Help Center</a></li>
					<li><a href="#" class="text-sm hover:text-white transition">Returns & Refunds</a></li>
				</ul>
			</div>
			{{-- Contact --}}
			<div>
				<h3 class="text-white font-bold mb-4 text-sm uppercase tracking-wider">Contact Us</h3>
				<ul class="space-y-3 text-sm">
					<li class="flex items-start gap-2">
						<svg class="w-4 h-4 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
								d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
						</svg>
						<span>{{ $siteAddress }}</span>
					</li>
					<li class="flex items-center gap-2">
						<svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
								d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
						</svg>
						<span>{{ $siteEmail }}</span>
					</li>
					<li class="flex items-center gap-2">
						<svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
								d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
						</svg>
						<span>{{ $sitePhone }}</span>
					</li>
				</ul>
			</div>
		</div>
	</div>

	{{-- Bottom Bar --}}
	<div class="border-t border-gray-800">
		<div class="container mx-auto px-4 py-5">
			<div class="flex flex-col sm:flex-row items-center justify-between gap-3">
				<p class="text-xs sm:text-sm">&copy; {{ date('Y') }} Kiddo's Heaven. All rights reserved.</p>
				<div class="flex items-center gap-5">
					<a href="#" class="text-xs sm:text-sm hover:text-white transition">Privacy Policy</a>
					<a href="#" class="text-xs sm:text-sm hover:text-white transition">Terms of Service</a>
				</div>
			</div>
		</div>
	</div>
</footer>
