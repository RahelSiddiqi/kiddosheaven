<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Kiddo's Heaven - Premium toys and games for children">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? config('app.name', "Kiddo's Heaven") }}</title>

    {{-- Favicon --}}
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">

    {{-- Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    {{-- Vite Assets --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Dynamic Theme CSS Variables --}}
    <style>
        :root {
            @isset($themeColors)
                @foreach ($themeColors as $key => $value)
                    --color-{{ $key }}: {{ $value }};
                @endforeach
            @else
                --color-primary: #018790;
                --color-primary-dark: #005461;
                --color-accent: #00b7b5;
            @endisset
            --color-light: #f4f4f4;
        }
        .font-nunito { font-family: {{ ($themeTypography['font_family_base'] ?? 'Nunito') }}, sans-serif; }
        html, body { overflow-x: hidden !important; max-width: 100vw; }
    </style>
</head>

<body class="font-nunito antialiased text-gray-800 bg-gray-50 min-h-screen flex flex-col">
    {{-- Navigation --}}
    @livewire('storefront.navigation')

    {{-- Main Content - single slot --}}
    <main class="flex-1 container mx-auto px-4 py-6 md:py-8 mt-[120px] md:mt-0 mb-16 md:mb-0">
        {{ $slot }}
    </main>

    {{-- Mobile Bottom Navigation --}}
    <div class="md:hidden fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 z-[9999]"
        style="height: 4rem; padding-bottom: env(safe-area-inset-bottom); box-shadow: 0 -2px 10px rgba(0,0,0,0.1);">
        <div class="flex items-center justify-around h-full max-w-lg mx-auto">
            <a href="{{ route('home') }}" wire:navigate class="flex flex-col items-center justify-center flex-1 h-full text-gray-500 hover:text-primary transition {{ request()->routeIs('home') ? 'text-primary' : '' }}">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
                <span class="text-xs mt-1">Home</span>
            </a>
            <a href="{{ route('catalog') }}" wire:navigate class="flex flex-col items-center justify-center flex-1 h-full text-gray-500 hover:text-primary transition {{ request()->routeIs('catalog') || request()->routeIs('products.*') ? 'text-primary' : '' }}">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                </svg>
                <span class="text-xs mt-1">Shop</span>
            </a>
            <a href="{{ route('cart.index') }}" wire:navigate class="relative flex flex-col items-center justify-center flex-1 h-full text-gray-500 hover:text-primary transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                </svg>
                <span class="text-xs mt-1">Cart</span>
            </a>
            @auth
                <a href="{{ route('account') }}" wire:navigate class="flex flex-col items-center justify-center flex-1 h-full text-gray-500 hover:text-primary transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    <span class="text-xs mt-1">Account</span>
                </a>
            @else
                <a href="{{ route('login') }}" wire:navigate class="flex flex-col items-center justify-center flex-1 h-full text-gray-500 hover:text-primary transition">
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
    <footer class="hidden md:block bg-gray-800 text-gray-300 mt-auto">
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
                    <form class="flex w-full md:w-auto">
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
                <div>
                    <h3 class="text-white font-bold mb-4">Company</h3>
                    <ul class="space-y-2">
                        <li><a href="{{ route('about') }}" wire:navigate class="hover:text-white transition">About Us</a></li>
                        <li><a href="{{ route('contact') }}" wire:navigate class="hover:text-white transition">Contact</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-white font-bold mb-4">Quick Links</h3>
                    <ul class="space-y-2">
                        <li><a href="{{ route('catalog') }}" wire:navigate class="hover:text-white transition">Shop</a></li>
                        <li><a href="{{ route('catalog', ['sort' => 'newest']) }}" wire:navigate class="hover:text-white transition">New Arrivals</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-white font-bold mb-4">Customer Service</h3>
                    <ul class="space-y-2">
                        <li><a href="#" class="hover:text-white transition">FAQs</a></li>
                        <li><a href="#" class="hover:text-white transition">Shipping Info</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="text-white font-bold mb-4">Contact Us</h3>
                    <ul class="space-y-2 text-sm">
                        <li>123 Toy Street, Dhaka</li>
                        <li>hello@kiddosheaven.local</li>
                        <li>+880 1 234 567 890</li>
                    </ul>
                </div>
            </div>
        </div>

        {{-- Bottom Bar --}}
        <div class="border-t border-gray-700">
            <div class="container mx-auto px-4 py-6">
                <p class="text-sm text-center">&copy; {{ date('Y') }} Kiddo's Heaven. All rights reserved.</p>
            </div>
        </div>
    </footer>

    {{-- Cart Drawer --}}
    @livewire('storefront.cart-drawer')

    {{-- Toast Notifications (works with SPA navigation) --}}
    <div x-data="{
        toasts: [],
        add(msg, type = 'success') {
            const id = Date.now();
            this.toasts.push({ id, msg, type });
            setTimeout(() => this.toasts = this.toasts.filter(t => t.id !== id), 3000);
        },
        init() {
            @if (session('success'))
                this.add('{{ session('success') }}');
            @endif
            @if (session('cart-success'))
                this.add('{{ session('cart-success') }}');
            @endif
            @if (session('error'))
                this.add('{{ session('error') }}', 'error');
            @endif
        }
    }"
    @notify.window="add($event.detail.message, $event.detail.type || 'success')"
    class="fixed bottom-20 md:bottom-4 right-4 z-[99999] space-y-2">
        <template x-for="toast in toasts" :key="toast.id">
            <div x-show="true"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-2"
                x-transition:enter-end="opacity-100 translate-y-0"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0"
                x-transition:leave-end="opacity-0 translate-y-2"
                :class="toast.type === 'error' ? 'bg-red-500' : 'bg-green-500'"
                class="text-white px-6 py-3 rounded-lg shadow-lg flex items-center gap-2">
                <svg x-show="toast.type !== 'error'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <span x-text="toast.msg"></span>
            </div>
        </template>
    </div>
</body>
</html>
