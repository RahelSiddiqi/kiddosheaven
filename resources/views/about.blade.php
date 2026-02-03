@extends('layouts.app')

@section('title', 'About Us — Kiddo\'s Heaven')

@section('content')
	<div class="container mx-auto bg-white rounded-xl shadow p-8 mt-8">
		<h1 class="text-4xl font-extrabold mb-6 text-[var(--color-primary-dark)]">About Kiddo's Heaven</h1>
		<p class="text-lg text-gray-700 mb-4">
			Welcome to Kiddo\'s Heaven, your trusted destination for delightful, safe, and sustainable toys! Founded by parents
			for
			parents, our mission is to inspire creativity, learning, and joy in every child.
		</p>
		<div class="grid md:grid-cols-2 gap-8 mb-6">
			<div>
				<h2 class="text-2xl font-bold mb-2 text-[var(--color-primary)]">Our Story</h2>
				<p class="text-gray-600 mb-2">Kiddo's Heaven began with a simple idea: to make playtime magical and meaningful. We
					carefully curate every toy in our collection, focusing on quality, safety, and the power of imagination.</p>
				<p class="text-gray-600">We believe in toys that last, spark curiosity, and bring families together. Our team is
					passionate about helping you find the perfect gift for every milestone and moment.</p>
			</div>
			<div>
				<h2 class="text-2xl font-bold mb-2 text-[var(--color-primary)]">Why Choose Us?</h2>
				<ul class="list-disc pl-5 text-gray-600 space-y-1">
					<li>Handpicked, high-quality toys</li>
					<li>Safe, non-toxic, and eco-friendly materials</li>
					<li>Fast, reliable shipping and cash on delivery</li>
					<li>Friendly customer support</li>
				</ul>
			</div>
		</div>
		<div class="bg-[var(--color-light)] rounded-lg p-6 mt-6">
			<h2 class="text-xl font-bold mb-2 text-[var(--color-primary-dark)]">Thank You!</h2>
			<p class="text-gray-700">We’re grateful to be part of your family’s playtime adventures. Thank you for choosing
				Kiddo's Heaven!</p>
		</div>
	</div>
@endsection
