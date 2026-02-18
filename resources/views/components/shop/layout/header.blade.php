@php
	$siteEmail = \App\Models\Setting::where('key', 'site_email')->value('value') ?? 'hello@kiddosheaven.com';
	$sitePhone = \App\Models\Setting::where('key', 'site_phone')->value('value') ?? '+880 1234 567 890';
	$cartSession = session('cart');
	$cartCount = is_array($cartSession) && isset($cartSession['items']) ? count($cartSession['items']) : 0;
@endphp

<nav class="bg-white shadow-sm border-b border-gray-100 sticky top-0 z-50">
	{{-- Top Bar --}}
	<div class="bg-primary text-white text-xs py-2">
		<div class="container mx-auto px-4 flex items-center justify-between">
			<div class="hidden lg:flex items-center gap-4">
				<span class="flex items-center gap-1.5">
					<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
							d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
					</svg>
					{{ $siteEmail }}
				</span>
				<span class="flex items-center gap-1.5">
					<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
							d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
					</svg>
					{{ $sitePhone }}
				</span>
			</div>
			<div class="flex items-center gap-3 sm:gap-4 ml-auto text-xs">
				<a href="{{ route('track.order') }}" class="hover:text-accent transition flex items-center gap-1">
					<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
							d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
					</svg>
					<span class="hidden sm:inline">Track Order</span>
				</a>
				@auth
					<a href="{{ route('account') }}" class="hover:text-accent transition flex items-center gap-1">
						<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
								d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
						</svg>
						<span class="hidden sm:inline">My Account</span>
					</a>
				@else
					<a href="{{ route('login') }}" class="hover:text-accent transition flex items-center gap-1">
						<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
								d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
						</svg>
						<span class="hidden sm:inline">Login</span>
					</a>
				@endauth
			</div>
		</div>
	</div>

	{{-- Main Navbar --}}
	<div class="container mx-auto px-4">
		<div class="flex items-center justify-between h-16">
			{{-- Logo --}}
			<a href="{{ route('home') }}" class="flex items-center flex-shrink-0">
				<img src="{{ asset('storage/logo/logo.png') }}" alt="Kiddo's Heaven" class="h-10 sm:h-12 w-auto">
			</a>

			{{-- Desktop Navigation --}}
			<div class="hidden lg:flex flex-1 items-center justify-center gap-8">
				<a href="{{ route('home') }}"
					class="flex items-center font-bold text-sm text-gray-600 hover:text-primary transition {{ request()->routeIs('home') ? 'text-primary!' : '' }}">
					<svg
						class="w-4 h-4 sm:w-5 sm:h-5 text-gray-600 group-hover:text-primary transition {{ request()->routeIs('home') ? 'text-primary!' : '' }}"
						fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
							d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
					</svg>
					<span class="ml-1">Home</span>
				</a>
				<a href="{{ route('catalog') }}"
					class="flex items-center font-bold text-sm text-gray-600 hover:text-primary transition {{ request()->routeIs('catalog') || request()->routeIs('products.*') ? 'text-primary!' : '' }}">
					<svg
						class="w-4 h-4 sm:w-5 sm:h-5 text-gray-600 group-hover:text-primary transition {{ request()->routeIs('catalog') ? 'text-primary!' : '' }}"
						fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
							d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
					</svg>
					<span class="ml-1">Shop</span>
				</a>
				<a href="{{ route('about') }}"
					class="flex items-center font-bold text-sm text-gray-600 hover:text-primary transition {{ request()->routeIs('about') ? 'text-primary!' : '' }}">
					<svg
						class="w-4 h-4 sm:w-5 sm:h-5 text-gray-600 group-hover:text-primary transition {{ request()->routeIs('about') ? 'text-primary!' : '' }}"
						fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
							d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
					</svg>
					<span class="ml-1">About</span>
				</a>
				<a href="{{ route('contact') }}"
					class="flex items-center font-bold text-sm text-gray-600 hover:text-primary transition {{ request()->routeIs('contact') ? 'text-primary!' : '' }}">
					<svg
						class="w-4 h-4 sm:w-5 sm:h-5 text-gray-600 group-hover:text-primary transition {{ request()->routeIs('contact') ? 'text-primary!' : '' }}"
						fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
							d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
					</svg>
					<span class="ml-1">Contact</span>
				</a>
			</div>

			{{-- Actions --}}
			<div class="flex items-center gap-1 sm:gap-2">
				{{-- Desktop Search --}}
				<form action="{{ route('search') }}" method="get" class="hidden lg:flex items-center">
					<div class="relative">
						<input type="text" name="q" placeholder="Search toys..." value="{{ request('q') }}"
							class="w-44 xl:w-56 pl-9 pr-4 py-2 text-sm rounded-full border border-gray-200 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition">
						<svg class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" fill="none"
							stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
								d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
						</svg>
					</div>
				</form>

				{{-- Mobile Search Toggle --}}
				<button type="button" x-data @click="$dispatch('toggle-search')"
					class="lg:hidden p-2 rounded-full hover:bg-gray-100 transition">
					<svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
							d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
					</svg>
				</button>

				{{-- Wishlist --}}
				@auth
					<a href="{{ route('wishlist.index') }}" class="relative p-2 rounded-full hover:bg-gray-100 transition group">
						<svg class="w-5 h-5 sm:w-6 sm:h-6 text-gray-600 group-hover:text-primary transition" fill="none"
							stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
								d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
						</svg>
					</a>
				@endauth

				{{-- Cart --}}
				<a href="{{ route('cart.index') }}" class="relative p-2 rounded-full hover:bg-gray-100 transition group">
					<svg class="w-5 h-5 sm:w-6 sm:h-6 text-gray-600 group-hover:text-primary transition" fill="none"
						stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
							d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
					</svg>
					@if ($cartCount > 0)
						<span
							class="absolute -top-0.5 -right-0.5 w-5 h-5 bg-primary text-white text-[10px] font-bold rounded-full flex items-center justify-center">{{ $cartCount > 9 ? '9+' : $cartCount }}</span>
					@endif
				</a>

				{{-- Mobile Menu Toggle --}}
				<button type="button" class="lg:hidden p-2 rounded-full hover:bg-gray-100 transition" x-data
					@click="$dispatch('toggle-mobile-menu')">
					<svg class="w-5 h-5 sm:w-6 sm:h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
					</svg>
				</button>
			</div>
		</div>
	</div>

	{{-- Mobile Search Bar --}}
	<div x-data="{ open: false }" @toggle-search.window="open = !open" x-show="open" x-collapse
		class="lg:hidden border-t border-gray-100 px-4 py-3 bg-white">
		<form action="{{ route('search') }}" method="get" class="flex items-center gap-2">
			<div class="relative flex-1">
				<input type="text" name="q" placeholder="Search toys..." value="{{ request('q') }}"
					class="w-full pl-10 pr-4 py-2.5 text-sm rounded-full border border-gray-200 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition">
				<svg class="w-5 h-5 absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400" fill="none"
					stroke="currentColor" viewBox="0 0 24 24">
					<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
						d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
				</svg>
			</div>
			<button type="button" @click="open = false" class="p-2 text-gray-500 hover:text-gray-700">
				<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
					<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
				</svg>
			</button>
		</form>
	</div>

	{{-- Mobile Menu --}}
	<div x-data="{ open: false }" @toggle-mobile-menu.window="open = !open" x-show="open" x-collapse
		class="lg:hidden border-t border-gray-100 bg-white">
		<div class="px-4 py-3 space-y-1">
			<a href="{{ route('home') }}"
				class="block px-4 py-2.5 rounded-lg text-gray-600 hover:bg-gray-50 transition text-sm font-medium {{ request()->routeIs('home') ? 'bg-primary/5 text-primary' : '' }}">Home</a>
			<a href="{{ route('catalog') }}"
				class="block px-4 py-2.5 rounded-lg text-gray-600 hover:bg-gray-50 transition text-sm font-medium {{ request()->routeIs('catalog') ? 'bg-primary/5 text-primary' : '' }}">Catalog</a>
			@auth
				<a href="{{ route('wishlist.index') }}"
					class="block px-4 py-2.5 rounded-lg text-gray-600 hover:bg-gray-50 transition text-sm font-medium {{ request()->routeIs('wishlist.index') ? 'bg-primary/5 text-primary' : '' }}">Wishlist</a>
			@endauth
			<a href="{{ route('track.order') }}"
				class="block px-4 py-2.5 rounded-lg text-gray-600 hover:bg-gray-50 transition text-sm font-medium">Track Order</a>
			<a href="{{ route('about') }}"
				class="block px-4 py-2.5 rounded-lg text-gray-600 hover:bg-gray-50 transition text-sm font-medium {{ request()->routeIs('about') ? 'bg-primary/5 text-primary' : '' }}">About</a>
			<a href="{{ route('contact') }}"
				class="block px-4 py-2.5 rounded-lg text-gray-600 hover:bg-gray-50 transition text-sm font-medium {{ request()->routeIs('contact') ? 'bg-primary/5 text-primary' : '' }}">Contact</a>
		</div>
	</div>
</nav>
