@extends('layouts.app')

@section('title', 'Login — Kiddo\'s Heaven')

@section('content')
	{{-- Breadcrumb --}}
	<nav class="text-xs sm:text-sm mb-4 sm:mb-6">
		<ol class="flex items-center gap-1.5 sm:gap-2 text-gray-500">
			<li><a href="{{ route('home') }}" class="hover:text-primary">Home</a></li>
			<li><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg></li>
			<li class="text-gray-800 font-medium">Login</li>
		</ol>
	</nav>

	<div class="max-w-md mx-auto">
		{{-- Header --}}
		<div class="text-center mb-6 sm:mb-8">
			<h1 class="text-xl sm:text-2xl md:text-3xl font-bold text-gray-900">Welcome Back!</h1>
			<p class="text-gray-500 mt-1 text-sm sm:text-base">Sign in to your account</p>
		</div>

		{{-- Login Card --}}
		<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 sm:p-8"
			x-data="{
				tab: 'email',
				phone: '',
				otp: '',
				otpSent: false,
				loading: false,
				countdown: 0,
				canResend: true,
				errorMsg: '',
				sendOtp() {
					if (!this.phone) return;
					this.loading = true;
					this.errorMsg = '';
					fetch('{{ route('otp.login.send') }}', {
						method: 'POST',
						headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
						body: JSON.stringify({ phone: this.phone })
					})
					.then(r => r.json())
					.then(data => {
						if (data.success) {
							this.otpSent = true;
							this.startCountdown();
							window.dispatchEvent(new CustomEvent('toast', { detail: { type: 'success', message: data.message } }));
						} else {
							this.errorMsg = data.message;
						}
					})
					.catch(() => { this.errorMsg = 'Something went wrong. Please try again.'; })
					.finally(() => this.loading = false);
				},
				verifyOtp() {
					if (this.otp.length < 6) return;
					this.loading = true;
					this.errorMsg = '';
					fetch('{{ route('otp.login.verify') }}', {
						method: 'POST',
						headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
						body: JSON.stringify({ phone: this.phone, otp: this.otp })
					})
					.then(r => r.json())
					.then(data => {
						if (data.success) {
							window.location.href = data.redirect;
						} else {
							this.errorMsg = data.message;
							this.loading = false;
						}
					})
					.catch(() => { this.errorMsg = 'Something went wrong. Please try again.'; this.loading = false; });
				},
				startCountdown() {
					this.canResend = false;
					this.countdown = 120;
					const t = setInterval(() => {
						this.countdown--;
						if (this.countdown <= 0) { clearInterval(t); this.canResend = true; }
					}, 1000);
				}
			}">

			{{-- Tabs --}}
			<div class="flex rounded-xl bg-gray-100 p-1 mb-6">
				<button type="button" @click="tab='email'; errorMsg=''"
					:class="tab === 'email' ? 'bg-white shadow text-gray-900' : 'text-gray-500 hover:text-gray-700'"
					class="flex-1 py-2 px-3 rounded-lg text-sm font-medium transition text-center">
					Email & Password
				</button>
				<button type="button" @click="tab='otp'; errorMsg=''"
					:class="tab === 'otp' ? 'bg-white shadow text-gray-900' : 'text-gray-500 hover:text-gray-700'"
					class="flex-1 py-2 px-3 rounded-lg text-sm font-medium transition text-center">
					Phone OTP
				</button>
			</div>

			{{-- ── TAB: Email & Password ── --}}
			<div x-show="tab === 'email'" x-cloak>
				@if ($errors->any())
					<div class="mb-4 p-3 rounded-lg bg-red-50 border border-red-200 text-sm text-red-700">
						@foreach ($errors->all() as $error)
							<p>{{ $error }}</p>
						@endforeach
					</div>
				@endif

				<form action="{{ route('login') }}" method="post" class="space-y-4">
					@csrf
					<div>
						<label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">Email Address</label>
						<input type="email" name="email" value="{{ old('email') }}" placeholder="your@email.com" required autofocus
							class="w-full rounded-lg border px-3 sm:px-4 py-2.5 sm:py-3 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition text-sm sm:text-base @error('email') border-red-300 @else border-gray-200 @enderror">
						@error('email') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
					</div>
					<div>
						<label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">Password</label>
						<input type="password" name="password" placeholder="••••••••" required
							class="w-full rounded-lg border px-3 sm:px-4 py-2.5 sm:py-3 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition text-sm sm:text-base @error('password') border-red-300 @else border-gray-200 @enderror">
						@error('password') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
					</div>

					<div class="flex items-center justify-between">
						<label class="flex items-center gap-2 cursor-pointer">
							<input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}
								class="w-4 h-4 rounded border-gray-300 text-primary focus:ring-primary">
							<span class="text-sm text-gray-600">Remember me</span>
						</label>
					</div>

					<button type="submit"
						class="w-full flex items-center justify-center gap-2 px-4 sm:px-6 py-3 sm:py-4 rounded-xl bg-primary text-white font-bold text-sm sm:text-base hover:bg-primary-dark transition shadow-lg shadow-primary/30">
						<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
								d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
						</svg>
						Sign In
					</button>
				</form>
			</div>

			{{-- ── TAB: Phone OTP ── --}}
			<div x-show="tab === 'otp'" x-cloak class="space-y-4">
				<div x-show="errorMsg" x-cloak class="p-3 bg-red-50 border border-red-200 rounded-lg text-sm text-red-700" x-text="errorMsg"></div>

				<div x-show="!otpSent">
					<label class="block text-xs sm:text-sm font-medium text-gray-700 mb-1">Phone Number</label>
					<input type="tel" x-model="phone" placeholder="01XXXXXXXXX" inputmode="numeric"
						@keydown.enter.prevent="sendOtp()"
						class="w-full rounded-lg border border-gray-200 px-4 py-2.5 sm:py-3 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition text-sm sm:text-base">
					<p class="text-xs text-gray-400 mt-1">Enter your registered phone number</p>
				</div>

				<button x-show="!otpSent" type="button" @click="sendOtp()"
					:disabled="loading || phone.length < 10"
					class="w-full flex items-center justify-center gap-2 px-4 py-3 rounded-xl bg-primary text-white font-bold text-sm hover:bg-primary-dark transition shadow-lg shadow-primary/30 disabled:opacity-50 disabled:cursor-not-allowed">
					<span x-show="!loading">Send OTP</span>
					<span x-show="loading" class="flex items-center gap-2">
						<svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
							<circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
							<path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
						</svg>
						Sending…
					</span>
				</button>

				<div x-show="otpSent" class="space-y-4">
					<div>
						<div class="flex items-center justify-between mb-1">
							<label class="block text-xs sm:text-sm font-medium text-gray-700">Enter OTP</label>
							<span class="text-xs text-gray-400" x-text="'Sent to ' + phone"></span>
						</div>
						<input type="text" x-model="otp" maxlength="6" inputmode="numeric" pattern="[0-9]{6}"
							placeholder="— — — — — —"
							class="w-full rounded-lg border border-gray-200 px-4 py-3 text-center text-2xl font-bold tracking-[0.5em] focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition">
					</div>

					<button type="button" @click="verifyOtp()"
						:disabled="loading || otp.length < 6"
						class="w-full flex items-center justify-center gap-2 px-4 py-3 rounded-xl bg-primary text-white font-bold text-sm hover:bg-primary-dark transition shadow-lg shadow-primary/30 disabled:opacity-50 disabled:cursor-not-allowed">
						<span x-show="!loading">Verify & Sign In</span>
						<span x-show="loading" class="flex items-center gap-2">
							<svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
								<circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
								<path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
							</svg>
							Verifying…
						</span>
					</button>

					<div class="text-center text-sm text-gray-500">
						<span x-show="!canResend">Resend in <span class="font-medium text-gray-700" x-text="countdown"></span>s</span>
						<button x-show="canResend" type="button" @click="sendOtp()"
							class="text-primary font-medium hover:text-primary-dark">Resend OTP</button>
					</div>

					<button type="button" @click="otpSent=false; otp=''; errorMsg=''"
						class="w-full text-center text-sm text-gray-400 hover:text-gray-600">
						← Change phone number
					</button>
				</div>
			</div>

			{{-- Register Link --}}
			<p class="text-center text-sm text-gray-600 mt-6">
				Don't have an account?
				<a href="{{ route('register') }}" class="text-primary font-medium hover:text-primary-dark">Sign up</a>
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
@endsection
