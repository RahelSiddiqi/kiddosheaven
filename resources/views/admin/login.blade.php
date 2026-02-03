@extends('admin.layout')

@section('title', 'Admin Login')

@section('content')
	<div class="flex justify-center items-center min-h-[60vh]">
		<form method="POST" action="{{ route('admin.login') }}" class="bg-white p-8 rounded-xl shadow w-full max-w-sm">
			@csrf
			<h2 class="text-xl font-bold mb-6 text-center text-[var(--admin-primary-dark)]">Admin Login</h2>
			<p class="text-gray-500 mb-6 text-sm text-center">Sign in to access the admin dashboard.</p>
			@if ($errors->any())
				<div class="mb-4 text-red-600 text-sm">
					{{ $errors->first() }}
				</div>
			@endif
			<div class="mb-4">
				<label for="email" class="block font-semibold mb-1">Email</label>
				<input id="email" type="email" name="email" required autofocus
					class="block w-full rounded border border-gray-300 px-3 py-2">
			</div>
			<div class="mb-4">
				<label for="password" class="block font-semibold mb-1">Password</label>
				<input id="password" type="password" name="password" required
					class="block w-full rounded border border-gray-300 px-3 py-2">
			</div>
			<div class="mb-6 flex items-center">
				<input type="checkbox" name="remember" id="remember" class="mr-2">
				<label for="remember" class="text-sm">Remember me</label>
			</div>
			<button type="submit"
				class="w-full bg-[var(--color-primary)] text-white font-bold py-2 rounded hover:bg-[var(--color-primary-dark)] transition">Login</button>
		</form>
	</div>
@endsection
