@extends('layouts.app')

@section('title', 'Register — Kiddo\'s Heaven')

@section('content')
	{{-- Breadcrumb --}}
	<nav class="text-xs sm:text-sm mb-4 sm:mb-6">
		<ol class="flex items-center gap-1.5 sm:gap-2 text-gray-500">
			<li><a href="{{ route('home') }}" class="hover:text-primary">Home</a></li>
			<li><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg></li>
			<li class="text-gray-800 font-medium">Register</li>
		</ol>
	</nav>

	<div class="max-w-md mx-auto">
		{{-- Header --}}
		<div class="text-center mb-6 sm:mb-8">
			<h1 class="text-xl sm:text-2xl md:text-3xl font-bold text-gray-900">Create Account</h1>
			<p class="text-gray-500 mt-1 text-sm sm:text-base">Join Kiddo's Heaven today</p>
		</div>

		{{-- Register Card --}}
		<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 sm:p-8">
			{{-- OTP Notice --}}
			<div class="flex items-start gap-3 p-3 bg-blue-50 border border-blue-200 rounded-lg mb-5 text-sm text-blue-700">
				<svg class="w-5 h-5 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
					<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" />
				</svg>
				<span>We'll send a <strong>6-digit OTP</strong> to your phone number to verify your account.</span>
			</div>
			<form action="{{ route('register') }}" method="post" class="space-y-4">
				@csrf

				@if ($errors->any())
					<div class="p-3 bg-red-50 border border-red-200 rounded-lg text-sm text-red-700">
						@foreach ($errors->all() as $error)
							<p>{{ $error }}</p>
						@endforeach
					</div>
				@endif
				<div>
					<label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">Full Name</label>
					<input type="text" name="name" value="{{ old('name') }}" placeholder="John Doe" required
						class="w-full rounded-lg border border-gray-200 px-3 sm:px-4 py-2.5 sm:py-3 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition text-sm sm:text-base">
					@error('name') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
				</div>

				<div>
					<label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">Email Address</label>
					<input type="email" name="email" value="{{ old('email') }}" placeholder="your@email.com" required
						class="w-full rounded-lg border border-gray-200 px-3 sm:px-4 py-2.5 sm:py-3 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition text-sm sm:text-base">
					@error('email') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
				</div>

				<div>
					<label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">Phone Number</label>
					<input type="tel" name="phone" value="{{ old('phone') }}" placeholder="+880 1XXXXXXXXX" required
						class="w-full rounded-lg border border-gray-200 px-3 sm:px-4 py-2.5 sm:py-3 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition text-sm sm:text-base">
					@error('phone') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
				</div>

				<div>
					<label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">Password</label>
					<input type="password" name="password" placeholder="Min. 8 characters" required
						class="w-full rounded-lg border border-gray-200 px-3 sm:px-4 py-2.5 sm:py-3 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition text-sm sm:text-base">
					@error('password') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
				</div>

				<div>
					<label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">Confirm Password</label>
					<input type="password" name="password_confirmation" placeholder="Repeat your password" required
						class="w-full rounded-lg border border-gray-200 px-3 sm:px-4 py-2.5 sm:py-3 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition text-sm sm:text-base">
				</div>

				<button type="submit"
					class="w-full flex items-center justify-center gap-2 px-4 sm:px-6 py-3 sm:py-4 rounded-xl bg-primary text-white font-bold text-sm sm:text-base hover:bg-primary-dark transition shadow-lg shadow-primary/30">
					Create Account
				</button>
			</form>

			{{-- Login Link --}}
			<p class="text-center text-sm text-gray-600 mt-6">
				Already have an account?
				<a href="{{ route('login') }}" class="text-primary font-medium hover:text-primary-dark">Sign in</a>
			</p>
		</div>
	</div>
@endsection
