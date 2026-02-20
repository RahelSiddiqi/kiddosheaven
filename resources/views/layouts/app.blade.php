<!DOCTYPE html>
<html lang="en" style="overflow-x:hidden;max-width:100%">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Kiddo's Heaven - Premium toys and games for children. Safe, educational, and fun toys for kids of all ages.">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Kiddo\'s Heaven')</title>

    {{-- Favicons --}}
    <link rel="icon" type="image/x-icon" href="{{ asset('storage/logo/favicon/favicon.ico') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('storage/logo/favicon/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('storage/logo/favicon/favicon-16x16.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('storage/logo/favicon/apple-touch-icon.png') }}">
    <link rel="manifest" href="{{ asset('storage/logo/favicon/site.webmanifest') }}">
    <meta name="theme-color" content="#018790">

    {{-- Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    {{-- Styles --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root {
            --color-primary: #018790;
            --color-primary-dark: #005461;
            --color-accent: #00b7b5;
            --color-light: #f4f4f4;
        }
        .font-nunito { font-family: 'Nunito', sans-serif; }
    </style>

    @stack('styles')
</head>

<body class="font-nunito antialiased text-gray-800 bg-gray-50 overflow-x-hidden">
    {{-- Header / Navigation --}}
    <x-shop.layout.header />

    {{-- Main Content --}}
    <main class="min-h-screen">
        <div class="container mx-auto px-4 py-6 md:py-8 pb-24 md:pb-8">
            @yield('content')
        </div>
    </main>

    {{-- Footer --}}
    <x-shop.layout.footer />

    {{-- Mobile Bottom Navigation --}}
    <x-shop.layout.mobile-nav />

    {{-- Flash Messages --}}
    @if (session('success'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
            x-transition:leave="transition ease-in duration-300" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
            class="fixed bottom-20 md:bottom-6 right-4 z-60 bg-green-500 text-white px-5 py-3 rounded-lg shadow-lg flex items-center gap-2 max-w-sm">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            <span class="text-sm">{{ session('success') }}</span>
            <button @click="show = false" class="ml-2 hover:opacity-75 shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
    @endif

    @if (session('error'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
            x-transition:leave="transition ease-in duration-300" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
            class="fixed bottom-20 md:bottom-6 right-4 z-60 bg-red-500 text-white px-5 py-3 rounded-lg shadow-lg flex items-center gap-2 max-w-sm">
            <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <span class="text-sm">{{ session('error') }}</span>
            <button @click="show = false" class="ml-2 hover:opacity-75 shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
    @endif

    {{-- Dynamic Toast Container (for AJAX responses) --}}
    <div x-data="{
            toasts: [],
            add(type, message) {
                const id = Date.now();
                this.toasts.push({ id, type, message, show: true });
                setTimeout(() => this.remove(id), 4000);
            },
            remove(id) { this.toasts = this.toasts.filter(t => t.id !== id); }
        }"
        @toast.window="add($event.detail.type, $event.detail.message)"
        class="fixed bottom-20 md:bottom-6 right-4 z-[70] flex flex-col gap-2 pointer-events-none">
        <template x-for="toast in toasts" :key="toast.id">
            <div x-show="toast.show"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-x-8"
                x-transition:enter-end="opacity-100 translate-x-0"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                :class="{
                    'bg-green-500': toast.type === 'success',
                    'bg-red-500': toast.type === 'error',
                    'bg-blue-500': toast.type === 'info',
                    'bg-yellow-500': toast.type === 'warning',
                }"
                class="pointer-events-auto flex items-center gap-2 text-white px-4 py-3 rounded-lg shadow-lg text-sm max-w-xs">
                <template x-if="toast.type === 'success'">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                </template>
                <template x-if="toast.type === 'error'">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </template>
                <template x-if="toast.type === 'info'">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </template>
                <span x-text="toast.message"></span>
                <button @click="remove(toast.id)" class="ml-1 hover:opacity-75 shrink-0">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        </template>
    </div>

    @stack('scripts')
</body>

</html>
