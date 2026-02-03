<!DOCTYPE html>
<html lang="en">

	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title>@yield('title', 'Admin — KiddosHeaven')</title>
		@vite(['resources/css/app.css', 'resources/js/app.js'])
		@stack('head')
	</head>

	<body>
		<header class="sticky top-0 z-40 bg-white/95 backdrop-blur border-b border-[--admin-primary]/30">
			<div class="max-w-6xl mx-auto flex items-center justify-between px-4 py-3">
				<div class="font-bold text-lg flex items-center gap-2 text-[--admin-primary-dark]">
					<span
						class="w-8 h-8 rounded-full bg-gradient-to-br from-[--admin-primary] to-[--admin-accent] flex items-center justify-center text-white text-base shadow">🎁</span>
					KiddosHeaven Admin
				</div>
				<nav class="flex gap-4 text-[15px] items-center flex-wrap">
					<a href="{{ route('admin.dashboard') }}"
						class="px-3 py-1 rounded transition {{ request()->routeIs('admin.dashboard') ? 'font-bold text-[--admin-primary] bg-[--admin-bg] border border-[--admin-primary]' : 'text-gray-700 hover:text-[--admin-primary]' }}">Dashboard</a>
					<a href="{{ route('admin.products.index') }}"
						class="px-3 py-1 rounded transition {{ request()->routeIs('admin.products.*') ? 'font-bold text-[--admin-primary] bg-[--admin-bg] border border-[--admin-primary]' : 'text-gray-700 hover:text-[--admin-primary]' }}">Products</a>
					<a href="{{ route('admin.orders.index') }}"
						class="px-3 py-1 rounded transition {{ request()->routeIs('admin.orders.*') ? 'font-bold text-[--admin-primary] bg-[--admin-bg] border border-[--admin-primary]' : 'text-gray-700 hover:text-[--admin-primary]' }}">Orders</a>
					<a href="{{ route('home') }}" target="_blank"
						class="px-3 py-1 rounded text-gray-500 hover:text-[--admin-primary]">View Site</a>
					<form action="{{ route('logout') }}" method="post" class="inline">
						@csrf
						<button type="submit"
							class="px-3 py-1 rounded bg-[--admin-primary] text-white font-semibold hover:bg-[--admin-primary-dark] transition border-none">Logout</button>
					</form>
				</nav>
			</div>
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
