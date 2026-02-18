<!DOCTYPE html>
<html lang="en" class="light">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Admin Login — Kiddo\'s Heaven')</title>

    {{-- Favicons --}}
    <link rel="icon" type="image/x-icon" href="{{ asset('storage/logo/favicon/favicon.ico') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('storage/logo/favicon/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('storage/logo/favicon/favicon-16x16.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('storage/logo/favicon/apple-touch-icon.png') }}">
    <link rel="manifest" href="{{ asset('storage/logo/favicon/site.webmanifest') }}">
    <meta name="theme-color" content="#018790">

    @vite(['resources/css/app.css', 'resources/css/admin.css', 'resources/js/app.js'])

    <!-- Theme initialization - prevents flash of wrong theme -->
    <script>
        (function() {
            const storedTheme = localStorage.getItem('admin_theme');
            const systemPrefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;

            if (storedTheme === 'dark' || (!storedTheme && systemPrefersDark)) {
                document.documentElement.classList.remove('light');
                document.documentElement.classList.add('dark');
                document.documentElement.style.setProperty('color-scheme', 'dark');
            } else {
                document.documentElement.classList.remove('dark');
                document.documentElement.classList.add('light');
                document.documentElement.style.setProperty('color-scheme', 'light');
            }
        })();
    </script>

    <style>
        /* Scrollbar styles for login page */
        .login-wrapper {
            overflow-y: auto;
            scrollbar-width: thin;
            scrollbar-color: #90A4AE transparent;
        }

        .login-wrapper::-webkit-scrollbar {
            width: 8px;
            background-color: transparent;
        }

        .login-wrapper::-webkit-scrollbar-track {
            background-color: transparent;
        }

        .login-wrapper::-webkit-scrollbar-thumb {
            background-color: #90A4AE;
            border-radius: 6px;
        }

        .login-wrapper::-webkit-scrollbar-thumb:hover {
            background-color: #78909C;
        }

        .dark .login-wrapper {
            scrollbar-color: #475569 transparent;
        }

        .dark .login-wrapper::-webkit-scrollbar-thumb {
            background-color: #475569;
        }

        .dark .login-wrapper::-webkit-scrollbar-thumb:hover {
            background-color: #64748b;
        }
    </style>
</head>

<body class="login-wrapper min-h-screen flex items-center justify-center" style="background-color: #f8fafc;">
    {{-- Background Pattern --}}
    <div class="fixed inset-0 -z-10 overflow-hidden" style="background-color: #f8fafc;">
        <div class="absolute inset-0 bg-gradient-to-br from-gray-50 to-gray-100"></div>
        <div class="absolute inset-0 opacity-30">
            <svg class="h-full w-full" viewBox="0 0 100 100" preserveAspectRatio="none">
                <defs>
                    <pattern id="grid" width="10" height="10" patternUnits="userSpaceOnUse">
                        <path d="M 10 0 L 0 0 0 10" fill="none" stroke="currentColor" stroke-width="0.5" style="stroke: #cbd5e1;"/>
                    </pattern>
                </defs>
                <rect width="100" height="100" fill="url(#grid)" />
            </svg>
        </div>
    </div>

    @yield('content')

    <!-- Toast Notifications -->
    <div id="toastContainer" class="fixed top-4 right-4 z-50 space-y-2"></div>

    <script>
        @if (session('success'))
            showToast('{{ session('success') }}', 'success');
        @endif

        @if (session('error'))
            showToast('{{ session('error') }}', 'error');
        @endif

        function showToast(message, type = 'success') {
            const toast = document.createElement('div');
            const bgColor = type === 'success'
                ? 'bg-green-50 border-green-200 text-green-700'
                : 'bg-red-50 border-red-200 text-red-700';
            const iconPath = type === 'success'
                ? 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'
                : 'M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z';

            toast.className = 'flex items-center gap-3 p-4 rounded-lg shadow-lg border animate-slide-in-right cursor-pointer ' + bgColor;
            toast.innerHTML = `
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="${iconPath}" />
                </svg>
                <span class="text-sm font-medium">${message}</span>
                <button onclick="this.parentElement.remove()" class="ml-4 opacity-50 hover:opacity-100" style="color: inherit;">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            `;
            document.getElementById('toastContainer').appendChild(toast);
            setTimeout(() => toast.remove(), 5000);
        }
    </script>

    @stack('scripts')
</body>

</html>
