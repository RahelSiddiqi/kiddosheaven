@extends('layouts.app')

@section('title', 'Contact Us — Kiddo\'s Heaven')

@section('content')
	{{-- Page Header --}}
	<div class="mb-8">
		<h1 class="text-3xl font-bold text-gray-900">Contact Us</h1>
		<p class="text-gray-500 mt-1">We'd love to hear from you</p>
	</div>

	<div class="grid lg:grid-cols-2 gap-8 mb-12">
		{{-- Contact Form --}}
		<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
			<h2 class="text-xl font-bold text-gray-900 mb-6">Send us a Message</h2>
			<form class="space-y-4">
				<div class="grid md:grid-cols-2 gap-4">
					<div>
						<label class="block text-sm font-medium text-gray-700 mb-1">Your Name</label>
						<input type="text" placeholder="John Doe"
							class="w-full rounded-lg border border-gray-200 px-4 py-3 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition">
					</div>
					<div>
						<label class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
						<input type="email" placeholder="john@example.com"
							class="w-full rounded-lg border border-gray-200 px-4 py-3 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition">
					</div>
				</div>
				<div>
					<label class="block text-sm font-medium text-gray-700 mb-1">Subject</label>
					<input type="text" placeholder="How can we help?"
						class="w-full rounded-lg border border-gray-200 px-4 py-3 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition">
				</div>
				<div>
					<label class="block text-sm font-medium text-gray-700 mb-1">Message</label>
					<textarea placeholder="Write your message here..."
					 class="w-full rounded-lg border border-gray-200 px-4 py-3 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition resize-none"
					 rows="5"></textarea>
				</div>
				<button type="submit"
					class="w-full px-6 py-4 rounded-xl bg-primary text-white font-bold text-lg hover:bg-primary-dark transition shadow-lg shadow-primary/30">
					Send Message
				</button>
			</form>
		</div>

		{{-- Contact Info & Business Hours --}}
		<div class="space-y-6">
			{{-- Contact Info Card --}}
			<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
				<h2 class="text-xl font-bold text-gray-900 mb-6">Get in Touch</h2>
				<div class="space-y-4">
					<div class="flex items-start gap-4">
						<div class="w-12 h-12 rounded-xl bg-primary/10 flex items-center justify-center flex-shrink-0">
							<svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
									d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
							</svg>
						</div>
						<div>
							<h3 class="font-medium text-gray-800">Address</h3>
							<p class="text-gray-600 text-sm">123 Toy Street, Gulshan Avenue<br>Dhaka 1212, Bangladesh</p>
						</div>
					</div>
					<div class="flex items-start gap-4">
						<div class="w-12 h-12 rounded-xl bg-primary/10 flex items-center justify-center flex-shrink-0">
							<svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
									d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
							</svg>
						</div>
						<div>
							<h3 class="font-medium text-gray-800">Email</h3>
							<p class="text-gray-600 text-sm">hello@kiddosheaven.local</p>
						</div>
					</div>
					<div class="flex items-start gap-4">
						<div class="w-12 h-12 rounded-xl bg-primary/10 flex items-center justify-center flex-shrink-0">
							<svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
								<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
									d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
							</svg>
						</div>
						<div>
							<h3 class="font-medium text-gray-800">Phone</h3>
							<p class="text-gray-600 text-sm">+880 1 234 567 890</p>
						</div>
					</div>
				</div>

				{{-- Social Links --}}
				<div class="flex items-center gap-3 mt-6 pt-6 border-t border-gray-100">
					<a href="#"
						class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center hover:bg-primary hover:text-white transition">
						<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
							<path
								d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z" />
						</svg>
					</a>
					<a href="#"
						class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center hover:bg-primary hover:text-white transition">
						<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
							<path
								d="M12.017 0C5.396 0 .029 5.367.029 11.987c0 5.079 3.158 9.417 7.618 11.162-.105-.949-.199-2.403.041-3.439.219-.937 1.406-5.957 1.406-5.957s-.359-.72-.359-1.781c0-1.663.967-2.911 2.168-2.911 1.024 0 1.518.769 1.518 1.688 0 1.029-.653 2.567-.992 3.992-.285 1.193.6 2.165 1.775 2.165 2.128 0 3.768-2.245 3.768-5.487 0-2.861-2.063-4.869-5.008-4.869-3.41 0-5.409 2.562-5.409 5.199 0 1.033.394 2.143.889 2.741.099.12.112.225.085.345-.09.375-.293 1.199-.334 1.363-.053.225-.172.271-.401.165-1.495-.69-2.433-2.878-2.433-4.646 0-3.776 2.748-7.252 7.92-7.252 4.158 0 7.392 2.967 7.392 6.923 0 4.135-2.607 7.462-6.233 7.462-1.214 0-2.354-.629-2.758-1.379l-.749 2.848c-.269 1.045-1.004 2.352-1.498 3.146 1.123.345 2.306.535 3.55.535 6.607 0 11.985-5.365 11.985-11.987C23.97 5.39 18.592.026 11.985.026L12.017 0z" />
						</svg>
					</a>
					<a href="#"
						class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center hover:bg-primary hover:text-white transition">
						<svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
							<path
								d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z" />
						</svg>
					</a>
				</div>
			</div>

			{{-- Business Hours - Bordered Card Style --}}
			<div class="bg-white rounded-2xl border-2 border-gray-200 p-8">
				<h2 class="text-xl font-bold text-gray-900 mb-6 flex items-center gap-2">
					<svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
							d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
					</svg>
					Business Hours
				</h2>
				<div class="space-y-3">
					<div class="flex items-center justify-between py-2 border-b border-gray-100">
						<span class="text-gray-600">Saturday - Thursday</span>
						<span class="font-medium text-gray-800">10:00 AM - 8:00 PM</span>
					</div>
					<div class="flex items-center justify-between py-2 border-b border-gray-100">
						<span class="text-gray-600">Friday</span>
						<span class="font-medium text-gray-800">2:00 PM - 8:00 PM</span>
					</div>
					<div class="flex items-center justify-between py-2">
						<span class="text-gray-600">Online Support</span>
						<span class="font-medium text-green-600">24/7 Available</span>
					</div>
				</div>
			</div>
		</div>
	</div>

	{{-- Map Section --}}
	<section class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 mb-12">
		<div class="aspect-[21/9] rounded-xl overflow-hidden bg-gray-100 flex items-center justify-center">
			<div class="text-center">
				<span class="text-6xl block mb-4">🗺️</span>
				<p class="text-gray-500">Map integration placeholder</p>
				<p class="text-sm text-gray-400">Add your Google Maps embed here</p>
			</div>
		</div>
	</section>

	{{-- FAQ Section --}}
	<section class="mb-12">
		<h2 class="text-2xl font-bold text-gray-900 mb-6 text-center">Frequently Asked Questions</h2>
		<div class="grid md:grid-cols-2 gap-6">
			<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
				<h3 class="font-bold text-gray-800 mb-2 flex items-center gap-2">
					<svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
							d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
					</svg>
					Do you deliver nationwide?
				</h3>
				<p class="text-gray-600 text-sm">Yes! We deliver to all major cities across the country with express shipping
					options available.</p>
			</div>
			<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
				<h3 class="font-bold text-gray-800 mb-2 flex items-center gap-2">
					<svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
							d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
					</svg>
					What payment methods do you accept?
				</h3>
				<p class="text-gray-600 text-sm">We accept bKash, Nagad, all major credit/debit cards, and cash on delivery.</p>
			</div>
			<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
				<h3 class="font-bold text-gray-800 mb-2 flex items-center gap-2">
					<svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
							d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
					</svg>
					Can I return a product?
				</h3>
				<p class="text-gray-600 text-sm">Yes, we offer a 30-day return policy for all unopened and unused items in original
					packaging.</p>
			</div>
			<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
				<h3 class="font-bold text-gray-800 mb-2 flex items-center gap-2">
					<svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
							d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
					</svg>
					Are your toys safe and non-toxic?
				</h3>
				<p class="text-gray-600 text-sm">Absolutely! All our products meet international safety standards and are certified
					non-toxic.</p>
			</div>
		</div>
	</section>
@endsection
