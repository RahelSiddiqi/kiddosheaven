@extends('layouts.app')

@section('title', 'Thank you — Kiddo\'s Heaven')

@section('content')
	<div class="kh-panel" style="max-width:640px;margin:0 auto;">
		<div class="kh-panel-header">
			<h2>Thank you for your order!</h2>
		</div>
		<p class="kh-section-muted" style="margin-bottom:12px;">
			Your Cash on Delivery order has been received. Our team will prepare your toys and contact you shortly
			to confirm the delivery details.
		</p>

		<div style="font-size:14px;margin-bottom:12px;">
			<strong>Order #{{ $order->id }}</strong><br>
			{{ $order->customer_name }}<br>
			{{ $order->address_line }}, {{ $order->city }}
		</div>

		<div class="kh-cart-summary">
			<div>Total amount</div>
			<strong>${{ number_format($order->total_amount / 100, 2) }}</strong>
		</div>

		<a href="{{ route('home') }}" class="kh-btn-secondary" style="margin-top:16px;display:inline-flex;">
			Back to Home
		</a>
	</div>
@endsection
