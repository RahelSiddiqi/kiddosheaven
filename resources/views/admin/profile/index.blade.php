@extends('admin.layouts.app')

@section('title', 'My Profile — Kiddo\'s Heaven')

@section('content')
	<div class="grid grid-cols-12 gap-4 md:gap-6">
		<div class="col-span-12 lg:col-span-8">

			@if (session('success'))
				<div class="mb-4 flex items-center gap-3 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700 dark:border-green-800 dark:bg-green-500/10 dark:text-green-400">
					<svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
					</svg>
					{{ session('success') }}
				</div>
			@endif

			<!-- Profile Info Form -->
			<form action="{{ route('admin.profile.update') }}" method="POST"
				class="mb-6 rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/3">
				@csrf
				@method('PUT')

				<div class="mb-6">
					<h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">Profile Information</h3>
					<p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Update your account name, email and phone number.</p>
				</div>

				<!-- Avatar -->
				<div class="mb-6 flex items-center gap-4">
					<div class="flex h-16 w-16 items-center justify-center rounded-full bg-blue-100 text-2xl font-bold text-blue-600 dark:bg-blue-500/20 dark:text-blue-400">
						{{ strtoupper(substr($user->name, 0, 1)) }}
					</div>
					<div>
						<p class="text-sm font-medium text-gray-800 dark:text-white/90">{{ $user->name }}</p>
						<p class="text-xs text-gray-500 dark:text-gray-400">{{ $user->is_admin ? 'Administrator' : 'Staff' }}</p>
					</div>
				</div>

				<div class="grid gap-4 md:grid-cols-2">
					<div>
						<label for="name" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Full Name</label>
						<input type="text" name="name" id="name" required
							value="{{ old('name', $user->name) }}"
							class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-blue-800">
						@error('name')
							<p class="mt-1 text-sm text-red-500">{{ $message }}</p>
						@enderror
					</div>

					<div>
						<label for="email" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Email Address</label>
						<input type="email" name="email" id="email" required
							value="{{ old('email', $user->email) }}"
							class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-blue-800">
						@error('email')
							<p class="mt-1 text-sm text-red-500">{{ $message }}</p>
						@enderror
					</div>

					<div>
						<label for="phone" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Phone</label>
						<input type="text" name="phone" id="phone"
							value="{{ old('phone', $user->phone) }}"
							placeholder="+880 1xxx-xxxxxx"
							class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-blue-800">
						@error('phone')
							<p class="mt-1 text-sm text-red-500">{{ $message }}</p>
						@enderror
					</div>
				</div>

				<div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
					<button type="submit"
						class="h-10.5 inline-flex items-center justify-center rounded-lg bg-blue-500 px-6 py-2.5 text-sm font-medium text-white shadow-theme-xs hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500/20">
						Save Changes
					</button>
				</div>
			</form>

			<!-- Change Password Form -->
			<form action="{{ route('admin.profile.password') }}" method="POST"
				class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/3">
				@csrf
				@method('PUT')

				<div class="mb-6">
					<h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">Change Password</h3>
					<p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Ensure your account is using a strong password.</p>
				</div>

				<div class="grid gap-4 md:grid-cols-2">
					<div class="md:col-span-2">
						<label for="current_password" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Current Password</label>
						<input type="password" name="current_password" id="current_password" autocomplete="current-password"
							class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-blue-800">
						@error('current_password')
							<p class="mt-1 text-sm text-red-500">{{ $message }}</p>
						@enderror
					</div>

					<div>
						<label for="password" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">New Password</label>
						<input type="password" name="password" id="password" autocomplete="new-password"
							class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-blue-800">
						@error('password')
							<p class="mt-1 text-sm text-red-500">{{ $message }}</p>
						@enderror
					</div>

					<div>
						<label for="password_confirmation" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Confirm New Password</label>
						<input type="password" name="password_confirmation" id="password_confirmation" autocomplete="new-password"
							class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-blue-800">
					</div>
				</div>

				<div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
					<button type="submit"
						class="h-10.5 inline-flex items-center justify-center rounded-lg bg-blue-500 px-6 py-2.5 text-sm font-medium text-white shadow-theme-xs hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500/20">
						Update Password
					</button>
				</div>
			</form>
		</div>

		<!-- Right sidebar info -->
		<div class="col-span-12 lg:col-span-4">
			<div class="rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/3">
				<h4 class="mb-4 text-sm font-semibold text-gray-800 dark:text-white/90">Account Details</h4>
				<ul class="space-y-3 text-sm">
					<li class="flex justify-between">
						<span class="text-gray-500 dark:text-gray-400">Member Since</span>
						<span class="font-medium text-gray-800 dark:text-white/90">{{ $user->created_at->format('M d, Y') }}</span>
					</li>
					<li class="flex justify-between">
						<span class="text-gray-500 dark:text-gray-400">Role</span>
						<span class="inline-flex items-center rounded-full bg-blue-50 px-2.5 py-0.5 text-xs font-medium text-blue-700 dark:bg-blue-500/10 dark:text-blue-400">
							{{ $user->is_admin ? 'Administrator' : 'Staff' }}
						</span>
					</li>
					<li class="flex justify-between">
						<span class="text-gray-500 dark:text-gray-400">Status</span>
						<span class="inline-flex items-center rounded-full bg-green-50 px-2.5 py-0.5 text-xs font-medium text-green-700 dark:bg-green-500/10 dark:text-green-400">
							Active
						</span>
					</li>
				</ul>
			</div>
		</div>
	</div>
@endsection
