<!DOCTYPE html>
<html lang="en">

	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title>@yield('title', 'Kiddo\'s Heaven')</title>


		<link rel="preconnect" href="https://fonts.googleapis.com">
		<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
		<link href="https://fonts.googleapis.com/css2?family=Baloo+2:wght@400;500;600;700&display=swap" rel="stylesheet">
		@vite(['resources/css/app.css', 'resources/js/app.js'])

		@stack('head')
	</head>
	@php
		$cart = session('cart', ['items' => [], 'subtotal' => 0]);
		$cartCount = isset($cart['items']) ? collect($cart['items'])->sum('quantity') : 0;
	@endphp

	<body>
		<header class="sticky top-0 z-40 bg-[--color-light]/95 backdrop-blur border-b border-gray-200">
			<div class="max-w-6xl mx-auto px-4 py-3">
				<div class="flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
					<!-- Top row: logo + text left, cart right (mobile), logo+text left, cart right (desktop) -->
					<div class="flex items-center justify-between w-full md:w-auto">
						<a href="{{ route('home') }}" class="flex items-center gap-3">
							<img src="{{ asset('storage/logo/logo.png') }}" alt="Kiddo\'s Heaven Logo" class="h-12 w-auto md:h-10">
							<span class="font-bold text-xl text-[--color-primary-dark]">Kiddo's Heaven</span>
						</a>
						<a href="{{ route('cart.index') }}"
							class="md:hidden inline-flex items-center gap-2 px-4 py-2 rounded-full bg-white shadow text-sm font-medium ml-2 md:ml-8">
							<span>Cart</span>
							<span
								class="min-w-5 h-5 rounded-full bg-[var(--color-primary)] text-white text-xs flex items-center justify-center">{{ $cartCount }}</span>
						</a>
					</div>
					<!-- Menu row: scrollable on mobile, single row -->
					<nav class="flex overflow-x-auto space-x-5">
						<a href="{{ route('home') }}"
							class="pb-1 relative transition text-gray-700 hover:text-[var(--color-primary)] {{ request()->routeIs('home') ? 'font-bold text-[var(--color-primary)] after:absolute after:left-0 after:bottom-0 after:w-6 after:h-1.5 after:rounded-full after:bg-[var(--color-primary)] after:opacity-90' : '' }}">Home</a>
						<a href="{{ route('catalog') }}"
							class="pb-1 relative transition text-gray-700 hover:text-[var(--color-primary)] {{ request()->routeIs('catalog') ? 'font-bold text-[var(--color-primary)] after:absolute after:left-0 after:bottom-0 after:w-6 after:h-1.5 after:rounded-full after:bg-[var(--color-primary)] after:opacity-90' : '' }}">Catalog</a>
						<a href="{{ route('checkout.show') }}"
							class="pb-1 relative transition text-gray-700 hover:text-[var(--color-primary)] {{ request()->routeIs('checkout.*') ? 'font-bold text-[var(--color-primary)] after:absolute after:left-0 after:bottom-0 after:w-6 after:h-1.5 after:rounded-full after:bg-[var(--color-primary)] after:opacity-90' : '' }}">Delivery</a>
						<a href="{{ route('about') }}"
							class="pb-1 relative transition text-gray-700 hover:text-[var(--color-primary)] {{ request()->routeIs('about') ? 'font-bold text-[var(--color-primary)] after:absolute after:left-0 after:bottom-0 after:w-6 after:h-1.5 after:rounded-full after:bg-[var(--color-primary)] after:opacity-90' : '' }}">About</a>
						<a href="{{ route('contact') }}"
							class="pb-1 relative transition text-gray-700 hover:text-[var(--color-primary)] {{ request()->routeIs('contact') ? 'font-bold text-[var(--color-primary)] after:absolute after:left-0 after:bottom-0 after:w-6 after:h-1.5 after:rounded-full after:bg-[var(--color-primary)] after:opacity-90' : '' }}">Contact</a>
						<a href="{{ route('cart.index') }}"
							class="hidden lg:inline-flex items-center gap-2 px-4 py-2 rounded-full bg-white shadow text-sm font-medium ml-2 md:ml-8">
							<span>Cart</span>
							<span
								class="min-w-5 h-5 rounded-full bg-[var(--color-primary)] text-white text-xs flex items-center justify-center">{{ $cartCount }}</span>
						</a>
					</nav>
				</div>
			</div>
			<script>
				// Simple JS for toggling the mobile nav
				document.addEventListener('DOMContentLoaded', function() {
					const btn = document.getElementById('main-nav-toggle');
					const nav = document.getElementById('main-nav');
					if (btn && nav) {
						btn.addEventListener('click', function() {
							nav.classList.toggle('hidden');
						});
					}
				});
			</script>
		</header>
		<script>
			document.addEventListener('DOMContentLoaded', function() {
				var nav = document.querySelector('nav');
				if (nav) {
					var active = nav.querySelector('.font-bold');
					if (active) {
						active.scrollIntoView({
							behavior: 'smooth',
							inline: 'center',
							block: 'nearest'
						});
					}
				}
			});
		</script>

		<main class="py-10">
			<div class="max-w-6xl mx-auto px-4">
				@if (session('success'))
					<div class="mb-4 rounded-lg bg-green-50 border border-green-200 text-green-800 px-4 py-3">{{ session('success') }}
					</div>
				@endif
				@if (session('error'))
					<div class="mb-4 rounded-lg bg-red-50 border border-red-200 text-red-700 px-4 py-3">{{ session('error') }}</div>
				@endif
				@yield('content')
			</div>
		</main>

		<footer class="border-t border-gray-200 mt-12 bg-[--color-light]" id="contact">
			<div class="max-w-6xl mx-auto flex flex-col md:flex-row items-center justify-between gap-6 px-4 py-8">
				<div class="flex flex-col gap-2 items-center md:items-start">
					<div class="flex items-center gap-2 font-bold text-xl text-[--color-primary-dark]">
						<span
							class="w-8 h-8 rounded-full bg-gradient-to-br from-[--color-primary] to-[--color-accent] flex items-center justify-center text-white text-base shadow">
							<img src="{{ asset('storage/logo/logo.png') }}" alt="Kiddo's Heaven Logo" class="h-8 w-auto mx-auto">
						</span>
						<span>Kiddo's Heaven</span>
					</div>
					<div class="text-gray-500 text-sm">Playful toys for happy life.</div>
				</div>
				<div class="flex flex-col gap-2 items-center md:items-end">
					<div class="flex gap-4 mb-1">
						<a href="https://facebook.com/kiddosheaven" target="_blank" class="text-blue-600 hover:text-blue-800"
							title="Facebook">
							<!-- Heroicons: Facebook (outline) -->
							<img src="{{ asset('storage/logo/tk.png') }}" alt="Kiddo\'s Heaven Tiktok" class="h-8 w-auto mx-auto">
						</a>
						<a href="https://instagram.com/kiddosheaven" target="_blank" class="text-pink-500 hover:text-pink-700"
							title="Instagram">
							<!-- Heroicons: Instagram (brand) -->
							<img src="{{ asset('storage/logo/fb.png') }}" alt="Kiddo\'s Heaven Facebook" class="h-8 w-auto mx-auto">
						</a>
						<a href="https://tiktok.com/@kiddosheaven" target="_blank" class="text-black hover:text-gray-700" title="TikTok">
							<!-- Heroicons: TikTok (custom) -->
							<img src="{{ asset('storage/logo/is.png') }}" alt="Kiddo\'s Heaven Instagram" class="h-8 w-auto mx-auto">
						</a>
						<a href="tel:+12139745898" class="text-green-600 hover:text-green-800" title="Call">
							<!-- Heroicons: Phone -->
							<img src="{{ asset('storage/logo/call.png') }}" alt="Kiddo\'s Heaven Phone" class="h-8 w-auto mx-auto">
						</a>
						<a href="mailto:hello@kiddosheaven.com" class="text-gray-600 hover:text-[var(--color-primary)]" title="Email">
							<!-- Heroicons: Envelope -->
							<img src="{{ asset('storage/logo/email.png') }}" alt="Kiddo\'s Heaven Email" class="h-8 w-auto mx-auto">
						</a>
					</div>
					<div class="text-sm text-gray-600">
						Call us: <a href="tel:+12139745898" class="underline hover:text-[--color-primary]">+1 213 974-5898</a> ·
						<br>
						Email: <a href="mailto:hello@kiddosheaven.com"
							class="underline hover:text-[--color-primary]">hello@kiddosheaven.com</a>
					</div>
				</div>
			</div>
		</footer>
	</body>

</html>
