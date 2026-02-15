<!DOCTYPE html>
<html lang="en" style="max-width: 100vw; box-sizing: border-box;">

	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta name="description"
			content="Kiddo's Heaven - Premium toys and games for children. Safe, educational, and fun toys for kids of all ages.">
		<meta name="csrf-token" content="{{ csrf_token() }}">

		<title>@yield('title', 'Kiddo\'s Heaven')</title>

		{{-- Favicon --}}
		<link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">

		{{-- Fonts --}}
		<link rel="preconnect" href="https://fonts.googleapis.com">
		<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
		<link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;500;600;700;800&display=swap" rel="stylesheet">

		{{-- Styles --}}
		@vite(['resources/css/app.css', 'resources/js/app.js'])
		@livewireStyles

		{{-- Inline Styles for SSR compatibility --}}
		<style>
			:root {
				--color-primary: #018790;
				--color-primary-dark: #005461;
				--color-accent: #00b7b5;
				--color-light: #f4f4f4;
			}

			.font-nunito {
				font-family: 'Nunito', sans-serif;
			}

			html,
			body {
				overflow-x: hidden !important;
				max-width: 100vw;
			}
		</style>
	</head>

	<body class="font-nunito antialiased text-gray-800 bg-gray-50"
		style="overflow-y: auto; position: relative; min-height: 100vh; box-sizing: border-box; margin: 0; padding: 0; width: 100%; max-width: 100%;">
		{{-- Mobile Top Fixed Navigation --}}
		<nav class="md:hidden bg-white shadow-sm border-b border-gray-100"
			style="position: fixed; top: 0; left: 0; right: 0; width: 100%; max-width: 100vw; z-index: 9999; box-sizing: border-box;">
			{{-- Top Bar --}}
			<div class="bg-primary text-white text-sm py-2">
				<div class="container mx-auto px-4 flex items-center justify-between">
					<div class="flex items-center gap-4">
						<span class="flex items-center gap-1">
							<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
									d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
							</svg>
							hello@kiddosheaven.local
						</span>
					</div>
				</div>
			</div>

			{{-- Main Navbar --}}
			<div class="container mx-auto px-4">
				<div class="flex items-center justify-between h-14">
					{{-- Logo --}}
					<a href="{{ route('home') }}" class="flex items-center gap-2">
						<span class="text-2xl">🧸</span>
						<span class="text-lg font-bold text-primary tracking-tight">Kiddo's Heaven</span>
					</a>

					{{-- Actions --}}
					<div class="flex items-center gap-2">
						{{-- Cart --}}
						@php
							$cartSession = session('cart');
							$cartCount = is_array($cartSession) && isset($cartSession['items']) ? count($cartSession['items']) : 0;
						@endphp
						<a href="{{ route('cart.index') }}" class="relative p-2 rounded-full hover:bg-gray-100 transition group">
							<svg class="w-6 h-6 text-gray-600 group-hover:text-primary transition" fill="none" stroke="currentColor"
								viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
									d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
							</svg>
							@if ($cartCount > 0)
								<span
									class="absolute -top-1 -right-1 w-5 h-5 bg-primary text-white text-xs font-bold rounded-full flex items-center justify-center">
									{{ $cartCount }}
								</span>
							@endif
						</a>

						{{-- Mobile Menu Toggle --}}
						<button type="button" class="p-2 rounded-full hover:bg-gray-100 transition"
							onclick="document.getElementById('mobile-menu').classList.toggle('hidden')">
							<svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
							</svg>
						</button>
					</div>
				</div>
			</div>

			{{-- Mobile Menu --}}
			<div id="mobile-menu" class="hidden border-t border-gray-100 bg-white">
				<div class="px-4 py-4 space-y-2">
					<a href="{{ route('home') }}" class="block px-4 py-2 rounded-lg text-gray-600 hover:bg-gray-50 transition">Home</a>
					<a href="{{ route('catalog') }}"
						class="block px-4 py-2 rounded-lg text-gray-600 hover:bg-gray-50 transition">Shop</a>
					<a href="{{ route('about') }}"
						class="block px-4 py-2 rounded-lg text-gray-600 hover:bg-gray-50 transition">About</a>
					<a href="{{ route('contact') }}"
						class="block px-4 py-2 rounded-lg text-gray-600 hover:bg-gray-50 transition">Contact</a>
				</div>
			</div>
		</nav>

		{{-- Desktop Navigation (sticky) --}}
		<nav class="hidden md:block bg-white shadow-sm border-b border-gray-100 sticky top-0 z-40">
			{{-- Top Bar --}}
			<div class="bg-primary text-white text-sm py-2">
				<div class="container mx-auto px-4 flex items-center justify-between">
					<div class="hidden md:flex items-center gap-4">
						<span class="flex items-center gap-1">
							<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
									d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
							</svg>
							hello@kiddosheaven.local
						</span>
						<span class="flex items-center gap-1">
							<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
									d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
							</svg>
							+880 1 234 567 890
						</span>
					</div>
					<div class="flex items-center gap-4 ml-auto">
						<a href="#" class="hover:text-accent transition flex items-center gap-1">
							<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
									d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
							</svg>
							Help
						</a>
						@auth
							<a href="{{ route('account') }}" class="hover:text-accent transition flex items-center gap-1">
								<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
										d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
								</svg>
								My Account
							</a>
						@else
							<a href="{{ route('login') }}" class="hover:text-accent transition flex items-center gap-1">
								<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
										d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
								</svg>
								Login
							</a>
						@endauth
					</div>
				</div>
			</div>

			{{-- Main Navbar --}}
			<div class="container mx-auto px-4">
				<div class="flex items-center justify-between h-16">
					{{-- Logo --}}
					<a href="{{ route('home') }}" class="flex items-center gap-2">
						<span class="text-3xl">🧸</span>
						<span class="hidden sm:block text-xl font-bold text-primary tracking-tight">Kiddo's Heaven</span>
					</a>

					{{-- Desktop Navigation --}}
					<div class="hidden md:flex items-center gap-8">
						<a href="{{ route('home') }}"
							class="font-medium text-gray-600 hover:text-primary transition {{ request()->routeIs('home') ? 'text-primary' : '' }}">
							Home
						</a>
						<a href="{{ route('catalog') }}"
							class="font-medium text-gray-600 hover:text-primary transition {{ request()->routeIs('catalog') || request()->routeIs('products.*') ? 'text-primary' : '' }}">
							Catalog
						</a>
						<a href="{{ route('about') }}"
							class="font-medium text-gray-600 hover:text-primary transition {{ request()->routeIs('about') ? 'text-primary' : '' }}">
							About
						</a>
						<a href="{{ route('contact') }}"
							class="font-medium text-gray-600 hover:text-primary transition {{ request()->routeIs('contact') ? 'text-primary' : '' }}">
							Contact
						</a>
					</div>

					{{-- Actions --}}
					<div class="flex items-center gap-4">
						{{-- Search --}}
						<form action="{{ route('search') }}" method="get" class="hidden lg:flex items-center">
							<div class="relative">
								<input type="text" name="q" placeholder="Search toys..."
									class="w-64 pl-10 pr-4 py-2 rounded-full border border-gray-200 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition text-sm">
								<svg class="w-5 h-5 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" fill="none"
									stroke="currentColor" viewBox="0 0 24 24">
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
										d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
								</svg>
							</div>
						</form>

						{{-- Cart --}}
						@php
							$cartSession = session('cart');
							$cartCount = is_array($cartSession) && isset($cartSession['items']) ? count($cartSession['items']) : 0;
						@endphp
						<a href="{{ route('cart.index') }}" class="relative p-2 rounded-full hover:bg-gray-100 transition group">
							<svg class="w-6 h-6 text-gray-600 group-hover:text-primary transition" fill="none" stroke="currentColor"
								viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
									d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
							</svg>
							@if ($cartCount > 0)
								<span
									class="absolute -top-1 -right-1 w-5 h-5 bg-primary text-white text-xs font-bold rounded-full flex items-center justify-center">
									{{ $cartCount }}
								</span>
							@endif
						</a>

						{{-- Mobile Menu Toggle --}}
						<button type="button" class="md:hidden p-2 rounded-full hover:bg-gray-100 transition"
							onclick="document.getElementById('mobile-menu').classList.toggle('hidden')">
							<svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
							</svg>
						</button>
					</div>
				</div>
			</div>

			{{-- Mobile Menu --}}
			<div id="mobile-menu" class="hidden md:hidden border-t border-gray-100 bg-white">
				<div class="px-4 py-4 space-y-2">
					<a href="{{ route('home') }}"
						class="block px-4 py-2 rounded-lg text-gray-600 hover:bg-gray-50 transition">Home</a>
					<a href="{{ route('catalog') }}"
						class="block px-4 py-2 rounded-lg text-gray-600 hover:bg-gray-50 transition">Shop/a>
						<a href="{{ route('about') }}"
							class="block px-4 py-2 rounded-lg text-gray-600 hover:bg-gray-50 transition">About</a>
						<a href="{{ route('contact') }}"
							class="block px-4 py-2 rounded-lg text-gray-600 hover:bg-gray-50 transition">Contact</a>
				</div>
			</div>
		</nav>

		{{-- Main Content --}}
		<main class="md:hidden container mx-auto px-4 pt-20 pb-24"
			style="padding-top: max(5rem, env(safe-area-inset-top)); padding-bottom: max(6rem, env(safe-area-inset-bottom)); max-width: 100vw; box-sizing: border-box;">
			@yield('content')
		</main>

		<main class="hidden md:block container mx-auto px-4 py-8">
			@yield('content')
		</main>

		{{-- Mobile Bottom Navigation --}}
		<div class="md:hidden bg-white border-t border-gray-200 z-[9999]"
			style="position: fixed; bottom: 0; left: 0; right: 0; width: 100%; max-width: 100vw; height: 4rem; padding-bottom: env(safe-area-inset-bottom); box-shadow: 0 -2px 10px rgba(0,0,0,0.1); box-sizing: border-box;">
			<div class="flex items-center justify-around h-full max-w-lg mx-auto">
				<a href="{{ route('home') }}"
					class="flex flex-col items-center justify-center flex-1 h-full text-gray-500 hover:text-primary transition {{ request()->routeIs('home') ? 'text-primary' : '' }}">
					<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
							d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
					</svg>
					<span class="text-xs mt-1">Home</span>
				</a>
				<a href="{{ route('catalog') }}"
					class="flex flex-col items-center justify-center flex-1 h-full text-gray-500 hover:text-primary transition {{ request()->routeIs('catalog') || request()->routeIs('products.*') ? 'text-primary' : '' }}">
					<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
							d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
					</svg>
					<span class="text-xs mt-1">Shop</span>
				</a>
				<a href="{{ route('cart.index') }}"
					class="relative flex flex-col items-center justify-center flex-1 h-full text-gray-500 hover:text-primary transition">
					<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
							d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
					</svg>
					@if ($cartCount > 0)
						<span
							class="absolute top-1 right-1/4 w-4 h-4 bg-primary text-white text-[10px] font-bold rounded-full flex items-center justify-center">
							{{ $cartCount }}
						</span>
					@endif
					<span class="text-xs mt-1">Cart</span>
				</a>
				@auth
					<a href="{{ route('account') }}"
						class="flex flex-col items-center justify-center flex-1 h-full text-gray-500 hover:text-primary transition {{ request()->routeIs('account') ? 'text-primary' : '' }}">
						<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
								d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
						</svg>
						<span class="text-xs mt-1">Account</span>
					</a>
				@else
					<a href="{{ route('login') }}"
						class="flex flex-col items-center justify-center flex-1 h-full text-gray-500 hover:text-primary transition">
						<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
								d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
						</svg>
						<span class="text-xs mt-1">Login</span>
					</a>
				@endauth
			</div>
		</div>

		{{-- Footer --}}
		<footer class="bg-gray-800 text-gray-300 mt-auto">
			{{-- Newsletter --}}
			<div class="bg-primary">
				<div class="container mx-auto px-4 py-8">
					<div class="flex flex-col md:flex-row items-center justify-between gap-4">
						<div class="flex items-center gap-3">
							<div class="w-10 h-10 rounded-full bg-white/10 flex items-center justify-center">
								<svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
										d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
								</svg>
							</div>
							<div>
								<p class="text-white font-bold">Subscribe to our newsletter</p>
								<p class="text-white/70 text-sm">Get the latest offers and new arrivals</p>
							</div>
						</div>
						<form action="#" class="flex w-full md:w-auto">
							<input type="email" placeholder="Your email address"
								class="flex-1 md:w-64 px-4 py-2 rounded-l-lg border-0 focus:ring-2 focus:ring-primary outline-none text-gray-800">
							<button type="submit"
								class="px-6 py-2 bg-primary-dark text-white font-bold rounded-r-lg hover:bg-gray-900 transition">Subscribe</button>
						</form>
					</div>
				</div>
			</div>

			{{-- Main Footer --}}
			<div class="container mx-auto px-4 py-12">
				<div class="grid grid-cols-2 md:grid-cols-4 gap-8">
					{{-- Company --}}
					<div>
						<h3 class="text-white font-bold mb-4">Company</h3>
						<ul class="space-y-2">
							<li><a href="{{ route('about') }}" class="hover:text-white transition">About Us</a></li>
							<li><a href="#" class="hover:text-white transition">Careers</a></li>
							<li><a href="#" class="hover:text-white transition">Press</a></li>
							<li><a href="{{ route('contact') }}" class="hover:text-white transition">Contact</a></li>
						</ul>
					</div>
					{{-- Quick Links --}}
					<div>
						<h3 class="text-white font-bold mb-4">Quick Links</h3>
						<ul class="space-y-2">
							<li><a href="{{ route('catalog') }}" class="hover:text-white transition">Shop/a></li>
							<li><a href="#" class="hover:text-white transition">New Arrivals</a></li>
							<li><a href="#" class="hover:text-white transition">Best Sellers</a></li>
							<li><a href="#" class="hover:text-white transition">Sale</a></li>
						</ul>
					</div>
					{{-- Customer Service --}}
					<div>
						<h3 class="text-white font-bold mb-4">Customer Service</h3>
						<ul class="space-y-2">
							<li><a href="#" class="hover:text-white transition">FAQs</a></li>
							<li><a href="#" class="hover:text-white transition">Shipping Info</a></li>
							<li><a href="#" class="hover:text-white transition">Returns & Refunds</a></li>
							<li><a href="#" class="hover:text-white transition">Track Order</a></li>
						</ul>
					</div>
					{{-- Contact --}}
					<div>
						<h3 class="text-white font-bold mb-4">Contact Us</h3>
						<ul class="space-y-2">
							<li class="flex items-center gap-2">
								<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
										d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
										d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
								</svg>
								123 Toy Street, Dhaka
							</li>
							<li class="flex items-center gap-2">
								<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
										d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
								</svg>
								hello@kiddosheaven.local
							</li>
							<li class="flex items-center gap-2">
								<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
									<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
										d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
								</svg>
								+880 1 234 567 890
							</li>
						</ul>
					</div>
				</div>
			</div>

			{{-- Bottom Bar --}}
			<div class="border-t border-gray-700">
				<div class="container mx-auto px-4 py-6">
					<div class="flex flex-col md:flex-row items-center justify-between gap-4">
						<p class="text-sm">&copy; {{ date('Y') }} Kiddo's Heaven. All rights reserved.</p>
						<div class="flex items-center gap-4">
							<a href="#" class="text-sm hover:text-white transition">Privacy Policy</a>
							<a href="#" class="text-sm hover:text-white transition">Terms of Service</a>
						</div>
					</div>
				</div>
			</div>
		</footer>

		{{-- Flash Messages --}}
		@if (session('success'))
			<div x-data="{ show: true }" x-show="show"
				class="fixed bottom-4 right-4 z-50 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg flex items-center gap-2">
				<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
					<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
				</svg>
				{{ session('success') }}
				<button @click="show = false" class="ml-4 hover:opacity-75">
					<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
					</svg>
				</button>
			</div>
		@endif

		@livewireScripts
	</body>

</html>
