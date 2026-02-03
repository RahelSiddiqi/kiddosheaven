<!DOCTYPE html>
<html lang="en">

	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title>@yield('title', 'KiddosHeaven')</title>


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
			<div class="max-w-6xl mx-auto flex items-center justify-between px-4 py-3">
				<a href="{{ route('home') }}" class="flex items-center gap-2 font-bold text-xl text-[--color-primary-dark]">
					<span
						class="w-9 h-9 rounded-full bg-gradient-to-br from-[--color-primary] to-[--color-accent] flex items-center justify-center text-white text-lg shadow">K</span>
					<span>KiddosHeaven</span>
				</a>
				<nav class="flex gap-5 text-[15px] items-center flex-wrap">
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
						class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-white shadow text-sm font-medium"><span>Cart</span><span
							class="min-w-5 h-5 rounded-full bg-[var(--color-primary)] text-white text-xs flex items-center justify-center">{{ $cartCount }}</span></a>
				</nav>
			</div>
		</header>

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
							class="w-8 h-8 rounded-full bg-gradient-to-br from-[--color-primary] to-[--color-accent] flex items-center justify-center text-white text-base shadow">K</span>
						<span>KiddosHeaven</span>
					</div>
					<div class="text-gray-500 text-sm">Playful toys for happy kids.</div>
				</div>
				<div class="flex flex-col gap-2 items-center md:items-end">
					<div class="flex gap-4 mb-1">
						<a href="https://facebook.com/kiddosheaven" target="_blank" class="text-blue-600 hover:text-blue-800"
							title="Facebook">
							<!-- Heroicons: Facebook (outline) -->
							<svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24" class="w-6 h-6">
								<path
									d="M22 12c0-5.522-4.477-10-10-10S2 6.478 2 12c0 5.005 3.657 9.128 8.438 9.877v-6.987h-2.54v-2.89h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.242 0-1.632.771-1.632 1.562v1.875h2.773l-.443 2.89h-2.33v6.987C18.343 21.128 22 17.005 22 12" />
							</svg>
						</a>
						<a href="https://instagram.com/kiddosheaven" target="_blank" class="text-pink-500 hover:text-pink-700"
							title="Instagram">
							<!-- Heroicons: Instagram (brand) -->
							<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
								stroke="currentColor" class="w-6 h-6">
								<rect width="18" height="18" x="3" y="3" rx="4.5" stroke="currentColor" stroke-width="1.5" />
								<circle cx="12" cy="12" r="4.5" stroke="currentColor" stroke-width="1.5" />
								<circle cx="17.5" cy="6.5" r="1" fill="currentColor" />
							</svg>
						</a>
						<a href="https://tiktok.com/@kiddosheaven" target="_blank" class="text-black hover:text-gray-700" title="TikTok">
							<!-- Heroicons: TikTok (custom) -->
							<svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24" class="w-6 h-6">
								<path
									d="M12.75 2h2.25v2.25a3.75 3.75 0 0 0 3.75 3.75h1.5V10a6.75 6.75 0 1 1-6.75-6.75v2.25a4.5 4.5 0 1 0 4.5 4.5V7.5a6 6 0 0 1-4.5-5.5z" />
							</svg>
						</a>
						<a href="tel:+12139745898" class="text-green-600 hover:text-green-800" title="Call">
							<!-- Heroicons: Phone -->
							<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
								stroke="currentColor" class="w-6 h-6">
								<path stroke-linecap="round" stroke-linejoin="round"
									d="M2.25 6.75c0-1.243 1.007-2.25 2.25-2.25h2.086c.966 0 1.797.684 2.012 1.632l.379 1.518a2.25 2.25 0 0 1-.516 2.175l-.845.845a16.015 16.015 0 0 0 6.364 6.364l.845-.845a2.25 2.25 0 0 1 2.175-.516l1.518.379c.948.215 1.632 1.046 1.632 2.012v2.086c0 1.243-1.007 2.25-2.25 2.25h-.75C6.477 21.75 2.25 17.523 2.25 12.75v-.75z" />
							</svg>
						</a>
						<a href="mailto:hello@kiddosheaven.com" class="text-gray-600 hover:text-[var(--color-primary)]" title="Email">
							<!-- Heroicons: Envelope -->
							<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
								stroke="currentColor" class="w-6 h-6">
								<path stroke-linecap="round" stroke-linejoin="round"
									d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25H4.5a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-.659 1.591l-7.091 7.091a2.25 2.25 0 0 1-3.182 0L3.409 8.584A2.25 2.25 0 0 1 2.75 6.993V6.75" />
							</svg>
						</a>
					</div>
					<div class="text-sm text-gray-600">
						Call us: <a href="tel:+12139745898" class="underline hover:text-[--color-primary]">+1 213 974-5898</a> ·
						Email: <a href="mailto:hello@kiddosheaven.com"
							class="underline hover:text-[--color-primary]">hello@kiddosheaven.com</a>
					</div>
				</div>
			</div>
		</footer>
	</body>

</html>
