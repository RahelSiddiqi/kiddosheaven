<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="bg-gray-50 min-h-screen flex flex-col">
    <nav class="bg-white border-b border-gray-200 px-6 py-4 flex justify-between items-center">
        <a href="{{ route('home') }}" class="text-xl font-bold text-gray-900">{{ config('app.name', 'KiddosHeaven') }}</a>
        @auth
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="text-sm text-gray-500 hover:text-gray-700">Logout</button>
        </form>
        @else
        <a href="{{ route('login') }}" class="text-sm text-gray-500 hover:text-gray-700">Login</a>
        @endauth
    </nav>
    <main class="flex-1">
        {{ $slot }}
    </main>
    @livewireScripts
</body>
</html>
