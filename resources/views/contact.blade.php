@extends('layouts.app')

@section('title', 'Contact Us — Kiddo\'s Heaven')

@section('content')
	{{-- Page Header --}}
	<div class="mb-6 sm:mb-8">
		<h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Contact Us</h1>
		<p class="text-gray-500 mt-1 text-sm sm:text-base">We'd love to hear from you</p>
	</div>

	<div class="grid lg:grid-cols-2 gap-6 lg:gap-8 mb-12">
		{{-- Contact Form --}}
		<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 sm:p-8">
			<h2 class="text-lg sm:text-xl font-bold text-gray-900 mb-5 sm:mb-6">Send us a Message</h2>

			@if (session('success'))
				<div class="mb-5 p-4 bg-green-50 border border-green-200 rounded-xl text-green-700 text-sm flex items-start gap-3">
					<svg class="w-5 h-5 text-green-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
					<span>{{ session('success') }}</span>
				</div>
			@endif

			<form action="{{ route('contact.send') }}" method="POST" class="space-y-4">
				@csrf
				<div class="grid sm:grid-cols-2 gap-4">
					<div>
						<label class="block text-sm font-medium text-gray-700 mb-1">Your Name *</label>
						<input type="text" name="name" value="{{ old('name') }}" placeholder="John Doe" required
							class="w-full rounded-lg border border-gray-200 px-4 py-3 text-sm sm:text-base focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition">
						@error('name') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
					</div>
					<div>
						<label class="block text-sm font-medium text-gray-700 mb-1">Email Address *</label>
						<input type="email" name="email" value="{{ old('email') }}" placeholder="john@example.com" required
							class="w-full rounded-lg border border-gray-200 px-4 py-3 text-sm sm:text-base focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition">
						@error('email') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
					</div>
				</div>
				<div>
					<label class="block text-sm font-medium text-gray-700 mb-1">Subject *</label>
					<input type="text" name="subject" value="{{ old('subject') }}" placeholder="How can we help?" required
						class="w-full rounded-lg border border-gray-200 px-4 py-3 text-sm sm:text-base focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition">
					@error('subject') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
				</div>
				<div>
					<label class="block text-sm font-medium text-gray-700 mb-1">Message *</label>
					<textarea name="message" placeholder="Write your message here..." rows="5" required
						class="w-full rounded-lg border border-gray-200 px-4 py-3 text-sm sm:text-base focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition resize-none">{{ old('message') }}</textarea>
					@error('message') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
				</div>
				<button type="submit"
					class="w-full px-6 py-3.5 rounded-xl bg-primary text-white font-bold text-sm sm:text-base hover:bg-primary-dark transition shadow-lg shadow-primary/30">
					Send Message
				</button>
			</form>
		</div>

		{{-- Contact Info & Business Hours --}}
		<div class="space-y-6">
			{{-- Contact Info Card --}}
			<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 sm:p-8">
				<h2 class="text-lg sm:text-xl font-bold text-gray-900 mb-5 sm:mb-6">Get in Touch</h2>
				<div class="space-y-4">
					<div class="flex items-start gap-4">
						<div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl bg-primary/10 flex items-center justify-center shrink-0">
							<svg class="w-5 h-5 sm:w-6 sm:h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
									d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
							</svg>
						</div>
						<div>
							<h3 class="font-medium text-gray-800 text-sm sm:text-base">Address</h3>
							<p class="text-gray-600 text-xs sm:text-sm">{!! nl2br(e($siteSettings['site_address'] ?? '')) !!}</p>
						</div>
					</div>
					<div class="flex items-start gap-4">
						<div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl bg-primary/10 flex items-center justify-center shrink-0">
							<svg class="w-5 h-5 sm:w-6 sm:h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
									d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
							</svg>
						</div>
						<div>
							<h3 class="font-medium text-gray-800 text-sm sm:text-base">Email</h3>
							<p class="text-gray-600 text-xs sm:text-sm">
								<a href="mailto:{{ $siteSettings['site_email'] ?? '' }}" class="hover:text-primary transition">{{ $siteSettings['site_email'] ?? '' }}</a>
							</p>
						</div>
					</div>
					<div class="flex items-start gap-4">
						<div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl bg-primary/10 flex items-center justify-center shrink-0">
							<svg class="w-5 h-5 sm:w-6 sm:h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
									d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
							</svg>
						</div>
						<div>
							<h3 class="font-medium text-gray-800 text-sm sm:text-base">Phone</h3>
							<p class="text-gray-600 text-xs sm:text-sm">
								<a href="tel:{{ $siteSettings['site_phone'] ?? '' }}" class="hover:text-primary transition">{{ $siteSettings['site_phone'] ?? '' }}</a>
							</p>
						</div>
					</div>
				</div>

				{{-- Social Links --}}
				<div class="flex items-center gap-3 mt-6 pt-6 border-t border-gray-100">
					@if (!empty($siteSettings['social_facebook']))
					<a href="{{ $siteSettings['social_facebook'] }}" target="_blank" rel="noopener" class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center hover:bg-primary hover:text-white transition">
						<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z" /></svg>
					</a>
					@endif
					@if (!empty($siteSettings['social_instagram']))
					<a href="{{ $siteSettings['social_instagram'] }}" target="_blank" rel="noopener" class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center hover:bg-primary hover:text-white transition">
						<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z" /></svg>
					</a>
					@endif
				</div>
			</div>

			{{-- Business Hours --}}
			<div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 sm:p-8">
				<h2 class="text-lg sm:text-xl font-bold text-gray-900 mb-5 sm:mb-6 flex items-center gap-2">
					<svg class="w-5 h-5 sm:w-6 sm:h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
							d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
					</svg>
					Business Hours
				</h2>
				<div class="space-y-3">
					<div class="flex items-center justify-between py-2 border-b border-gray-100">
						<span class="text-gray-600 text-sm">{{ $siteSettings['contact_hours_weekday'] ?? 'Saturday - Thursday' }}</span>
						<span class="font-medium text-gray-800 text-sm">{{ $siteSettings['contact_hours_weekday_time'] ?? '10:00 AM - 8:00 PM' }}</span>
					</div>
					<div class="flex items-center justify-between py-2 border-b border-gray-100">
						<span class="text-gray-600 text-sm">Friday</span>
						<span class="font-medium text-gray-800 text-sm">{{ $siteSettings['contact_hours_friday_time'] ?? '2:00 PM - 8:00 PM' }}</span>
					</div>
					<div class="flex items-center justify-between py-2">
						<span class="text-gray-600 text-sm">Online Support</span>
						<span class="font-medium text-green-600 text-sm">24/7 Available</span>
					</div>
				</div>
			</div>
		</div>
	</div>

	{{-- FAQ Section --}}
	<section class="mb-12">
		<h2 class="text-xl sm:text-2xl font-bold text-gray-900 mb-5 sm:mb-6 text-center">Frequently Asked Questions</h2>
		<div class="grid sm:grid-cols-2 gap-4 sm:gap-6">
			<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 sm:p-6">
				<h3 class="font-bold text-gray-800 mb-2 flex items-center gap-2 text-sm sm:text-base">
					<svg class="w-5 h-5 text-primary shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
							d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
					</svg>
					Do you deliver nationwide?
				</h3>
				<p class="text-gray-600 text-xs sm:text-sm">Yes! We deliver to all major cities across the country with express shipping options available.</p>
			</div>
			<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 sm:p-6">
				<h3 class="font-bold text-gray-800 mb-2 flex items-center gap-2 text-sm sm:text-base">
					<svg class="w-5 h-5 text-primary shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
							d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
					</svg>
					What payment methods do you accept?
				</h3>
				<p class="text-gray-600 text-xs sm:text-sm">We accept bKash, Nagad, all major credit/debit cards, and cash on delivery.</p>
			</div>
			<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 sm:p-6">
				<h3 class="font-bold text-gray-800 mb-2 flex items-center gap-2 text-sm sm:text-base">
					<svg class="w-5 h-5 text-primary shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
							d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
					</svg>
					Can I return a product?
				</h3>
				<p class="text-gray-600 text-xs sm:text-sm">Yes, we offer a 30-day return policy for all unopened and unused items in original packaging.</p>
			</div>
			<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 sm:p-6">
				<h3 class="font-bold text-gray-800 mb-2 flex items-center gap-2 text-sm sm:text-base">
					<svg class="w-5 h-5 text-primary shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
							d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
					</svg>
					Are your toys safe and non-toxic?
				</h3>
				<p class="text-gray-600 text-xs sm:text-sm">Absolutely! All our products meet international safety standards and are certified non-toxic.</p>
			</div>
		</div>
	</section>
@endsection
