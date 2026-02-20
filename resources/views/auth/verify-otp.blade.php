@extends('layouts.app')

@section('title', 'Verify Phone — Kiddo\'s Heaven')

@section('content')
	{{-- Breadcrumb --}}
	<nav class="text-xs sm:text-sm mb-4 sm:mb-6">
		<ol class="flex items-center gap-1.5 sm:gap-2 text-gray-500">
			<li><a href="{{ route('home') }}" class="hover:text-primary">Home</a></li>
			<li><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg></li>
			<li><a href="{{ route('register') }}" class="hover:text-primary">Register</a></li>
			<li><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg></li>
			<li class="text-gray-800 font-medium">Verify Phone</li>
		</ol>
	</nav>

	<div class="max-w-md mx-auto"
		x-data="{
			otp: '',
			loading: false,
			resending: false,
			countdown: 120,
			canResend: false,
			startCountdown() {
				this.canResend = false;
				this.countdown = 120;
				const timer = setInterval(() => {
					this.countdown--;
					if (this.countdown <= 0) {
						clearInterval(timer);
						this.canResend = true;
					}
				}, 1000);
			},
			resendOtp() {
				this.resending = true;
				fetch('{{ route('register.otp.send') }}', {
					method: 'POST',
					headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
					body: JSON.stringify({})
				})
				.then(r => r.json())
				.then(data => {
					if (data.success) {
						window.dispatchEvent(new CustomEvent('toast', { detail: { type: 'success', message: data.message } }));
						this.startCountdown();
					} else {
						window.dispatchEvent(new CustomEvent('toast', { detail: { type: 'error', message: data.message } }));
					}
				})
				.finally(() => this.resending = false);
			}
		}"
		x-init="startCountdown()">

		{{-- Header --}}
		<div class="text-center mb-6 sm:mb-8">
			<div class="w-16 h-16 bg-primary/10 rounded-full flex items-center justify-center mx-auto mb-4">
				<svg class="w-8 h-8 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
					<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" />
				</svg>
			</div>
			<h1 class="text-xl sm:text-2xl font-bold text-gray-900">Verify Your Phone</h1>
			<p class="text-gray-500 mt-1 text-sm">
				We sent a 6-digit code to
				<span class="font-medium text-gray-800">{{ $phone }}</span>
			</p>
		</div>

		{{-- OTP Card --}}
		<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 sm:p-8">
			@if ($errors->any())
				<div class="mb-4 p-3 bg-red-50 border border-red-200 rounded-lg text-sm text-red-700">
					{{ $errors->first() }}
				</div>
			@endif

			<form action="{{ route('register.otp.verify') }}" method="post" class="space-y-5">
				@csrf
				<div>
					<label class="block text-xs sm:text-sm font-medium text-gray-700 mb-2">Enter 6-Digit OTP</label>
					<input
						type="text"
						name="otp"
						x-model="otp"
						maxlength="6"
						inputmode="numeric"
						pattern="[0-9]{6}"
						placeholder="— — — — — —"
						autofocus
						required
						class="w-full rounded-lg border border-gray-200 px-4 py-3 text-center text-2xl font-bold tracking-[0.5em] focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition">
					@error('otp')
						<p class="text-red-600 text-xs mt-1">{{ $message }}</p>
					@enderror
				</div>

				<button
					type="submit"
					:disabled="otp.length < 6"
					class="w-full flex items-center justify-center gap-2 px-4 py-3 sm:py-4 rounded-xl bg-primary text-white font-bold text-sm sm:text-base hover:bg-primary-dark transition shadow-lg shadow-primary/30 disabled:opacity-50 disabled:cursor-not-allowed">
					<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
					</svg>
					Verify & Create Account
				</button>
			</form>

			{{-- Resend --}}
			<div class="mt-5 text-center text-sm text-gray-500">
				<span x-show="!canResend">
					Resend in <span class="font-medium text-gray-700" x-text="countdown"></span>s
				</span>
				<button
					x-show="canResend"
					@click="resendOtp()"
					:disabled="resending"
					class="text-primary font-medium hover:text-primary-dark disabled:opacity-50">
					<span x-show="!resending">Resend OTP</span>
					<span x-show="resending">Sending…</span>
				</button>
			</div>

			<p class="text-center text-sm text-gray-500 mt-4">
				Wrong number?
				<a href="{{ route('register') }}" class="text-primary font-medium hover:text-primary-dark">Go back</a>
			</p>
		</div>
	</div>
@endsection
