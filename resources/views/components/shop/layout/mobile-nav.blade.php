@php
	$cartSession = session('cart');
	$cartCount = is_array($cartSession) && isset($cartSession['items']) ? count($cartSession['items']) : 0;
@endphp

<div class="md:hidden fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 z-50 shadow-lg"
	style="padding-bottom: env(safe-area-inset-bottom);">
	<div class="flex items-center justify-around h-14">
		<a href="{{ route('home') }}"
			class="flex flex-col items-center justify-center flex-1 h-full {{ request()->routeIs('home') ? 'text-primary' : 'text-gray-500' }} hover:text-primary transition">
			<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
				<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
					d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
			</svg>
			<span class="text-[10px] mt-0.5">Home</span>
		</a>
		<a href="{{ route('catalog') }}"
			class="flex flex-col items-center justify-center flex-1 h-full {{ request()->routeIs('catalog') || request()->routeIs('products.*') ? 'text-primary' : 'text-gray-500' }} hover:text-primary transition">
			<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
				<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
					d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
			</svg>
			<span class="text-[10px] mt-0.5">Shop</span>
		</a>
		<a href="{{ route('cart.index') }}"
			class="relative flex flex-col items-center justify-center flex-1 h-full text-gray-500 hover:text-primary transition">
			<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
				<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
					d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
			</svg>
			@if ($cartCount > 0)
				<span
					class="absolute top-1 right-1/4 w-4 h-4 bg-primary text-white text-[9px] font-bold rounded-full flex items-center justify-center">{{ $cartCount > 9 ? '9+' : $cartCount }}</span>
			@endif
			<span class="text-[10px] mt-0.5">Cart</span>
		</a>
		<a href="{{ route('track.order') }}"
			class="flex flex-col items-center justify-center flex-1 h-full {{ request()->routeIs('track.order') ? 'text-primary' : 'text-gray-500' }} hover:text-primary transition">
			<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
				<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
					d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
			</svg>
			<span class="text-[10px] mt-0.5">Track</span>
		</a>
		@auth
			<a href="{{ route('account') }}"
				class="flex flex-col items-center justify-center flex-1 h-full {{ request()->routeIs('account') || request()->routeIs('customer.*') ? 'text-primary' : 'text-gray-500' }} hover:text-primary transition">
				<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
					<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
						d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
				</svg>
				<span class="text-[10px] mt-0.5">Account</span>
			</a>
		@else
			<a href="{{ route('login') }}"
				class="flex flex-col items-center justify-center flex-1 h-full text-gray-500 hover:text-primary transition">
				<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
					<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
						d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
				</svg>
				<span class="text-[10px] mt-0.5">Login</span>
			</a>
		@endauth
	</div>
</div>
