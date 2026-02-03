<!DOCTYPE html>
<html lang="en">

	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title>@yield('title', 'Admin — Kiddo\'s Heaven')</title>
		@vite(['resources/css/app.css', 'resources/js/app.js'])
		@stack('head')
	</head>

	<body>
		<header class="sticky top-0 z-40 bg-white/95 backdrop-blur border-b border-[--admin-primary]/30">
			<div class="max-w-6xl mx-auto px-4 py-3">
				<div class="flex flex-col md:flex-row md:items-center md:justify-between">
					<!-- Logo and text row -->
					<div class="flex items-center gap-2 justify-between w-full md:w-auto">
						<img src="{{ asset('storage/logo/logo.png') }}" alt="Kiddo\'s Heaven Logo" class="h-12 w-auto md:h-10">
						<span class="font-bold text-lg text-[--admin-primary-dark] hidden md:inline">Kiddo's Heaven Admin</span>
						<!-- Hamburger button for mobile -->
						<button id="admin-nav-toggle"
							class="md:hidden p-2 rounded focus:outline-none focus:ring-2 focus:ring-[--admin-primary] ml-auto"
							aria-label="Open menu">
							<svg class="w-7 h-7 text-[--admin-primary-dark]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
							</svg>
						</button>
					</div>
					<!-- Menu row (mobile: below, scrollable if needed) -->
					<nav id="admin-nav"
						class="hidden md:flex gap-4 text-[15px] items-center flex-col md:flex-row md:static w-full md:w-auto bg-white md:bg-transparent shadow md:shadow-none z-50 md:z-auto p-4 md:p-0 overflow-x-auto whitespace-nowrap scrollbar-thin scrollbar-thumb-gray-300 pr-6 pl-3 -mx-4 mt-2 md:mt-0">
						<a href="{{ route('admin.dashboard') }}"
							class="px-3 py-2 rounded-md transition block md:inline {{ request()->routeIs('admin.dashboard') ? 'font-bold text-[--admin-primary] bg-[--admin-bg] border border-[--admin-primary]' : 'text-gray-700 hover:text-[--admin-primary]' }}">Dashboard</a>
						<a href="{{ route('admin.catalogs.index') }}"
							class="px-3 py-2 rounded-md transition block md:inline {{ request()->routeIs('admin.catalogs.*') ? 'font-bold text-[--admin-primary] bg-[--admin-bg] border border-[--admin-primary]' : 'text-gray-700 hover:text-[--admin-primary]' }}">Catalogs</a>
						<a href="{{ route('admin.products.index') }}"
							class="px-3 py-2 rounded-md transition block md:inline {{ request()->routeIs('admin.products.*') ? 'font-bold text-[--admin-primary] bg-[--admin-bg] border border-[--admin-primary]' : 'text-gray-700 hover:text-[--admin-primary]' }}">Products</a>
						<a href="{{ route('admin.orders.index') }}"
							class="px-3 py-2 rounded-md transition block md:inline {{ request()->routeIs('admin.orders.*') ? 'font-bold text-[--admin-primary] bg-[--admin-bg] border border-[--admin-primary]' : 'text-gray-700 hover:text-[--admin-primary]' }}">Orders</a>
						<a href="{{ route('home') }}" target="_blank"
							class="px-3 py-2 rounded-md text-gray-500 hover:text-[--admin-primary] block md:inline">View Site</a>
						<form action="{{ route('logout') }}" method="post" class="inline">
							@csrf
							<button type="submit"
								class="px-3 py-2 rounded-md bg-[--admin-primary] text-white font-semibold hover:bg-[--admin-primary-dark] transition border-none w-full md:w-auto">Logout</button>
						</form>
					</nav>
				</div>
			</div>
			<script>
				// Simple JS for toggling the mobile nav
				document.addEventListener('DOMContentLoaded', function() {
					const btn = document.getElementById('admin-nav-toggle');
					const nav = document.getElementById('admin-nav');
					if (btn && nav) {
						btn.addEventListener('click', function() {
							nav.classList.toggle('hidden');
						});
					}
				});
			</script>
		</header>

		<div class="max-w-6xl mx-auto px-4 py-8">
			@if (session('success'))
				<div class="mb-4 rounded-lg bg-green-50 border border-green-200 text-green-800 px-4 py-3">{{ session('success') }}
				</div>
			@endif
			@if (session('error'))
				<div class="mb-4 rounded-lg bg-red-50 border border-red-200 text-red-700 px-4 py-3">{{ session('error') }}</div>
			@endif
			@yield('content')
		</div>
	</body>

</html>
