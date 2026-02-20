<div>
    {{-- Mobile Top Fixed Navigation --}}
    <nav class="md:hidden bg-white shadow-sm border-b border-gray-100"
        style="position: fixed; top: 0; left: 0; right: 0; width: 100%; max-width: 100vw; z-index: 9999;">
        {{-- Top Bar --}}
        <div class="bg-primary text-white text-sm py-2">
            <div class="container mx-auto px-4 flex items-center justify-between">
                <span class="flex items-center gap-1 text-xs">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                    hello@kiddosheaven.local
                </span>
            </div>
        </div>

        {{-- Main Navbar --}}
        <div class="container mx-auto px-4">
            <div class="flex items-center justify-between h-14">
                {{-- Logo --}}
                <a href="{{ route('home') }}" wire:navigate class="flex items-center gap-2">
                    <span class="text-2xl">🧸</span>
                    <span class="text-lg font-bold text-primary">Kiddo's Heaven</span>
                </a>

                {{-- Actions --}}
                <div class="flex items-center gap-2">
                    {{-- Cart --}}
                    <button wire:click="$dispatch('open-cart-drawer')" class="relative p-2 rounded-full hover:bg-gray-100 transition">
                        <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                        </svg>
                        @if ($cartCount > 0)
                            <span class="absolute -top-1 -right-1 w-5 h-5 bg-primary text-white text-xs font-bold rounded-full flex items-center justify-center">
                                {{ $cartCount }}
                            </span>
                        @endif
                    </button>

                    {{-- Mobile Menu Toggle --}}
                    <button wire:click="toggleMobileMenu" class="p-2 rounded-full hover:bg-gray-100 transition">
                        <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        {{-- Mobile Menu --}}
        @if ($isMobileMenuOpen)
            <div class="border-t border-gray-100 bg-white">
                <div class="px-4 py-4 space-y-2">
                    <a href="{{ route('home') }}" wire:navigate class="block px-4 py-2 rounded-lg text-gray-600 hover:bg-gray-50">Home</a>
                    <a href="{{ route('catalog') }}" wire:navigate class="block px-4 py-2 rounded-lg text-gray-600 hover:bg-gray-50">Shop</a>
                    <a href="{{ route('about') }}" wire:navigate class="block px-4 py-2 rounded-lg text-gray-600 hover:bg-gray-50">About</a>
                    <a href="{{ route('contact') }}" wire:navigate class="block px-4 py-2 rounded-lg text-gray-600 hover:bg-gray-50">Contact</a>
                </div>
            </div>
        @endif
    </nav>

    {{-- Desktop Navigation (sticky) --}}
    <nav class="hidden md:block bg-white shadow-sm border-b border-gray-100 sticky top-0 z-40">
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
                    <span class="flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                        </svg>
                        +880 1 234 567 890
                    </span>
                </div>
                <div class="flex items-center gap-4">
                    @auth
                        <a href="{{ route('account') }}" wire:navigate class="hover:text-accent transition">My Account</a>
                    @else
                        <a href="{{ route('login') }}" wire:navigate class="hover:text-accent transition">Login</a>
                    @endauth
                </div>
            </div>
        </div>

        {{-- Main Navbar --}}
        <div class="container mx-auto px-4">
            <div class="flex items-center justify-between h-16">
                {{-- Logo --}}
                <a href="{{ route('home') }}" wire:navigate class="flex items-center gap-2">
                    <span class="text-3xl">🧸</span>
                    <span class="text-xl font-bold text-primary">Kiddo's Heaven</span>
                </a>

                {{-- Desktop Navigation --}}
                <div class="flex items-center gap-8">
                    <a href="{{ route('home') }}" wire:navigate class="font-medium text-gray-600 hover:text-primary transition">Home</a>
                    <a href="{{ route('catalog') }}" wire:navigate class="font-medium text-gray-600 hover:text-primary transition">Catalog</a>
                    <a href="{{ route('about') }}" wire:navigate class="font-medium text-gray-600 hover:text-primary transition">About</a>
                    <a href="{{ route('contact') }}" wire:navigate class="font-medium text-gray-600 hover:text-primary transition">Contact</a>
                </div>

                {{-- Actions --}}
                <div class="flex items-center gap-4">
                    {{-- Search --}}
                    <form wire:submit.prevent="search" class="hidden lg:flex items-center">
                        <div class="relative">
                            <input type="text" wire:model="searchQuery" placeholder="Search toys..."
                                class="w-64 pl-10 pr-4 py-2 rounded-full border border-gray-200 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none text-sm">
                            <svg class="w-5 h-5 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                    </form>

                    {{-- Currency Selector --}}
                    <livewire:storefront.currency-selector />

                    {{-- Cart --}}
                    <button wire:click="$dispatch('open-cart-drawer')" class="relative p-2 rounded-full hover:bg-gray-100 transition">
                        <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                        </svg>
                        @if ($cartCount > 0)
                            <span class="absolute -top-1 -right-1 w-5 h-5 bg-primary text-white text-xs font-bold rounded-full flex items-center justify-center">
                                {{ $cartCount }}
                            </span>
                        @endif
                    </button>
                </div>
            </div>
        </div>
    </nav>
</div>
