@extends('layouts.app')

@section('title', 'Checkout — KiddosHeaven')

@section('content')
	<div class="flex flex-col md:flex-row gap-8">
		<div class="flex-1 bg-white rounded-xl shadow p-6 mb-8">
			<div class="kh-panel-header">
				<h2>Delivery Details</h2>
				<div class="kh-section-muted">All orders are paid with Cash on Delivery.</div>
			</div>

			@if ($errors->any())
				<div class="kh-alert kh-alert-error">
					Please review the form and fix the highlighted fields.
				</div>
			@endif

			<form action="{{ route('checkout.place') }}" method="post" class="mt-2">
				@csrf
				<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
					<div class="flex flex-col gap-1 col-span-1">
						<label for="customer_name" class="font-semibold">Full name *</label>
						<input id="customer_name" name="customer_name" value="{{ old('customer_name') }}" required
							class="rounded border border-gray-300 px-3 py-2">
					</div>
					<div class="flex flex-col gap-1 col-span-1">
						<label for="customer_phone" class="font-semibold">Phone number *</label>
						<input id="customer_phone" name="customer_phone" value="{{ old('customer_phone') }}" required
							class="rounded border border-gray-300 px-3 py-2">
					</div>
					<div class="flex flex-col gap-1 col-span-1">
						<label for="customer_email" class="font-semibold">Email (optional)</label>
						<input id="customer_email" name="customer_email" type="email" value="{{ old('customer_email') }}"
							class="rounded border border-gray-300 px-3 py-2">
					</div>
					<div class="flex flex-col gap-1 md:col-span-2 col-span-1">
						<label for="address_line" class="font-semibold">Address *</label>
						<input id="address_line" name="address_line" value="{{ old('address_line') }}" required
							class="rounded border border-gray-300 px-3 py-2">
					</div>
					<div class="flex flex-col gap-1 col-span-1">
						<label for="city" class="font-semibold">City *</label>
						<input id="city" name="city" value="{{ old('city') }}" required
							class="rounded border border-gray-300 px-3 py-2">
					</div>
					<div class="flex flex-col gap-1 col-span-1">
						<label for="postal_code" class="font-semibold">Postal / ZIP</label>
						<input id="postal_code" name="postal_code" value="{{ old('postal_code') }}"
							class="rounded border border-gray-300 px-3 py-2">
					</div>
					<div class="flex flex-col gap-1 md:col-span-2 col-span-1">
						<label for="notes" class="font-semibold">Notes for the courier (optional)</label>
						<textarea id="notes" name="notes" class="rounded border border-gray-300 px-3 py-2">{{ old('notes') }}</textarea>
					</div>
				</div>
				<div class="mt-6 flex flex-col md:flex-row justify-between items-center gap-4">
					<div class="text-sm text-gray-500">
						You will pay in cash upon delivery. No online payment is required.
					</div>
					<button type="submit"
						class="bg-[var(--color-primary)] text-white font-bold px-6 py-2 rounded hover:bg-[var(--color-primary-dark)] transition">
						Place COD Order
					</button>
				</div>
			</form>
		</div>

		<div class="w-full md:max-w-xs flex-shrink-0 bg-white rounded-xl shadow p-6 mb-8">
			<div class="mb-4 border-b pb-3">
				<h2 class="text-lg font-bold text-[color:var(--color-primary-dark)]">Order Summary</h2>
			</div>
			@if (empty($cart['items']))
				<p class="text-gray-400">Your cart is empty. Add some toys first.</p>
			@else
				<div class="flex flex-col gap-4">
					@foreach ($cart['items'] as $item)
						<div class="flex items-center gap-3 border-b pb-3 last:border-0 last:pb-0">
							@php
								$img = $item['primary_image'] ?? ($item['images'][0] ?? null);
							@endphp
							@if ($img)
								<img src="{{ asset('storage/' . $img) }}" alt="{{ $item['name'] }}" class="w-14 h-14 object-cover rounded">
							@endif
							<div class="flex-1">
								<div class="font-semibold text-[color:var(--color-primary-dark)]">{{ $item['name'] }}</div>
								<div class="text-xs text-gray-400">Qty {{ $item['quantity'] }}</div>
							</div>
							<div class="font-bold text-[color:var(--color-primary)]">${{ number_format($item['line_total'] / 100, 2) }}</div>
						</div>
					@endforeach
				</div>
				<div class="mt-6 flex flex-col gap-2">
					<div class="flex items-center justify-between">
						<span class="text-gray-500">Subtotal</span>
						<span
							class="font-bold text-[color:var(--color-primary-dark)]">${{ number_format($cart['subtotal'] / 100, 2) }}</span>
					</div>
					<div class="flex items-center justify-between">
						<span class="text-gray-500">Payment method</span>
						<span class="font-bold text-[color:var(--color-primary-dark)]">Cash on Delivery</span>
					</div>
				</div>
			@endif
		</div>
	</div>
@endsection
