@extends('layouts.app')

@section('title', 'Login — Kiddo\'s Heaven')

@section('content')
	<div class="min-h-[80vh] flex items-center justify-center py-12 px-4">
		<div class="w-full max-w-md">
			{{-- Logo/Header --}}
			<div class="text-center mb-8">
				<a href="{{ route('home') }}" class="inline-flex items-center gap-2 mb-4">
					<span class="text-4xl">🧸</span>
					<span class="text-2xl font-bold text-primary">Kiddo's Heaven</span>
				</a>
				<h1 class="text-2xl font-bold text-gray-900">Welcome Back!</h1>
				<p class="text-gray-500 mt-1">Sign in to your account</p>
			</div>

			{{-- Login Card --}}
			<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
				{{-- Social Login Buttons --}}
				<div class="flex gap-4 mb-6">
					<button type="button"
						class="flex-1 flex items-center justify-center gap-2 px-4 py-3 rounded-xl border border-gray-200 hover:bg-gray-50 transition">
						<svg class="w-5 h-5" viewBox="0 0 24 24">
							<path fill="#4285F4"
								d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" />
							<path fill="#34A853"
								d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" />
							<path fill="#FBBC05"
								d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" />
							<path fill="#EA4335"
								d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" />
						</svg>
						<span class="text-sm font-medium text-gray-700">Google</span>
					</button>
					<button type="button"
						class="flex-1 flex items-center justify-center gap-2 px-4 py-3 rounded-xl border border-gray-200 hover:bg-gray-50 transition">
						<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
							<path
								d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z" />
						</svg>
						<span class="text-sm font-medium text-gray-700">Facebook</span>
					</button>
				</div>

				{{-- Divider --}}
				<div class="relative mb-6">
					<div class="absolute inset-0 flex items-center">
						<div class="w-full border-t border-gray-200"></div>
					</div>
					<div class="relative flex justify-center text-sm">
						<span class="px-2 bg-white text-gray-500">or continue with email</span>
					</div>
				</div>

				{{-- Login Form --}}
				<form action="{{ route('login') }}" method="post" class="space-y-4">
					@csrf
					<div>
						<label class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
						<input type="email" name="email" placeholder="your@email.com" required
							class="w-full rounded-lg border border-gray-200 px-4 py-3 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition">
					</div>
					<div>
						<label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
						<input type="password" name="password" placeholder="••••••••" required
							class="w-full rounded-lg border border-gray-200 px-4 py-3 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition">
					</div>

					<div class="flex items-center justify-between">
						<label class="flex items-center gap-2 cursor-pointer">
							<input type="checkbox" name="remember"
								class="w-4 h-4 rounded border-gray-300 text-primary focus:ring-primary">
							<span class="text-sm text-gray-600">Remember me</span>
						</label>
						<a href="#" class="text-sm text-primary hover:text-primary-dark font-medium">Forgot password?</a>
					</div>

					<button type="submit"
						class="w-full flex items-center justify-center gap-2 px-6 py-4 rounded-xl bg-primary text-white font-bold text-lg hover:bg-primary-dark transition shadow-lg shadow-primary/30">
						<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
								d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
						</svg>
						Sign In
					</button>
				</form>

				{{-- Register Link --}}
				<p class="text-center text-sm text-gray-600 mt-6">
					Don't have an account?
					<a href="#" class="text-primary font-medium hover:text-primary-dark">Sign up</a>
				</p>
			</div>

			{{-- Trust Badge --}}
			<div class="mt-6 text-center">
				<div class="inline-flex items-center gap-2 text-sm text-gray-500">
					<svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
					</svg>
					<span>Secure login powered by Laravel</span>
				</div>
			</div>
		</div>
	</div>
@endsection
