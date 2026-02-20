<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">

	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<meta name="csrf-token" content="{{ csrf_token() }}">

		<title>@yield('title', 'Dashboard') — Kiddo's Heaven Admin</title>

		{{-- Favicons --}}
		<link rel="icon" type="image/x-icon" href="{{ asset('storage/logo/favicon/favicon.ico') }}">
		<link rel="icon" type="image/png" sizes="32x32" href="{{ asset('storage/logo/favicon/favicon-32x32.png') }}">
		<link rel="icon" type="image/png" sizes="16x16" href="{{ asset('storage/logo/favicon/favicon-16x16.png') }}">
		<link rel="apple-touch-icon" sizes="180x180" href="{{ asset('storage/logo/favicon/apple-touch-icon.png') }}">
		<link rel="manifest" href="{{ asset('storage/logo/favicon/site.webmanifest') }}">
		<meta name="theme-color" content="#018790">

		@stack('styles')

        @vite(['resources/css/admin.css', 'resources/js/admin.js'])
		<!-- SortableJS -->
		<script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.15.0/Sortable.min.js"></script>

		<!-- Alpine.js -->
		<script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

		<!-- Theme Store -->
		<script>
			document.addEventListener('alpine:init', () => {
				Alpine.store('theme', {
					init() {
						const savedTheme = localStorage.getItem('theme');
						const systemTheme = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' :
							'light';
						this.theme = savedTheme || systemTheme;
						this.updateTheme();
					},
					theme: 'light',
					toggle() {
						this.theme = this.theme === 'light' ? 'dark' : 'light';
						localStorage.setItem('theme', this.theme);
						this.updateTheme();
					},
					updateTheme() {
						const html = document.documentElement;
						const body = document.body;
						if (this.theme === 'dark') {
							html.classList.add('dark');
							body.classList.add('dark', 'bg-gray-900');
						} else {
							html.classList.remove('dark');
							body.classList.remove('dark', 'bg-gray-900');
						}
					}
				});

				Alpine.store('sidebar', {
					// Initialize based on screen size
					isExpanded: window.innerWidth >= 1280, // true for desktop, false for mobile
					isMobileOpen: false,
					isHovered: false,

					toggleExpanded() {
						this.isExpanded = !this.isExpanded;
						// When toggling desktop sidebar, ensure mobile menu is closed
						this.isMobileOpen = false;
					},

					toggleMobileOpen() {
						this.isMobileOpen = !this.isMobileOpen;
						// Don't modify isExpanded when toggling mobile menu
					},

					setMobileOpen(val) {
						this.isMobileOpen = val;
					},

					setHovered(val) {
						// Only allow hover effects on desktop when sidebar is collapsed
						if (window.innerWidth >= 1280 && !this.isExpanded) {
							this.isHovered = val;
						}
					}
				});
			});
		</script>

		<!-- Apply dark mode immediately to prevent flash -->
		<script>
			(function() {
				const savedTheme = localStorage.getItem('theme');
				const systemTheme = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
				const theme = savedTheme || systemTheme;
				if (theme === 'dark') {
					document.documentElement.classList.add('dark');
					if (document.body) {
						document.body.classList.add('dark', 'bg-gray-900');
					}
				} else {
					document.documentElement.classList.remove('dark');
					document.body.classList.remove('dark', 'bg-gray-900');
				}
			})();
		</script>
    <style>
        /* ── Standard (dark theme) ── */
        .ql-toolbar.ql-snow {
            border-radius: 5px 5px 0 0 !important;
            border-color: #344054 !important;
            color: white !important;
        }

        .quill-editor-container .ql-container.ql-snow {
            border-radius: 0 0 5px 5px !important;
            border-color: #344054 !important;
            background-color: var(--color-gray-900) !important;
        }
        .ql-container.ql-snow {
            border-radius: 0 0 5px 5px !important;
            border-color: #344054 !important;
            background-color: var(--color-gray-900) !important;
        }
        .ql-snow .ql-stroke {
            stroke: #e5e7eb !important;

        }
        /* ── Light theme overrides (only border color + background) ── */
        html:not(.dark) .ql-toolbar.ql-snow {
            border-color: #d1d5db !important;
            background-color: #f9fafb !important;
            color: #1f2937 !important;
        }

        html:not(.dark) .quill-editor-container .ql-container.ql-snow {
            border-color: #d1d5db !important;
            background-color: #ffffff !important;
        }
        html:not(.dark) .ql-container.ql-snow {
            border-color: #d1d5db !important;
            background-color: #ffffff !important;
        }

        html:not(.dark) .ql-editor {
            color: #1f2937 !important;
            background-color: white !important;
        }

        html:not(.dark) .ql-snow .ql-stroke {
            stroke: #1f2937 !important;
        }
    </style>
	</head>

	<body x-data="{ 'loaded': true }" x-init="$store.sidebar.isExpanded = window.innerWidth >= 1280;
const checkMobile = () => {
    if (window.innerWidth < 1280) {
        $store.sidebar.setMobileOpen(false);
        $store.sidebar.isExpanded = false;
    } else {
        $store.sidebar.isMobileOpen = false;
        $store.sidebar.isExpanded = true;
    }
};
window.addEventListener('resize', checkMobile);">

		{{-- preloader --}}
		<x-admin.common.preloader />
		{{-- preloader end --}}

		<div class="min-h-screen xl:flex">
			@include('admin.layouts.backdrop')
			@include('admin.layouts.sidebar')

			<div class="flex-1 transition-all duration-300 ease-in-out"
				:class="{
				    'xl:ml-72.5': $store.sidebar.isExpanded || $store.sidebar.isHovered,
				    'xl:ml-22.5': !$store.sidebar.isExpanded && !$store.sidebar.isHovered,
				    'ml-0': $store.sidebar.isMobileOpen
				}">
				<!-- app header start -->
				@include('admin.layouts.app-header')
				<!-- app header end -->
				<div class="p-4 mx-auto max-w-(--breakpoint-2xl) md:p-6">
					@yield('content')
				</div>
			</div>
		</div>

		{{-- ===== TOAST NOTIFICATION SYSTEM ===== --}}
		<div
			x-data="{
				toasts: [],
				add(toast) {
					toast.id = Date.now();
					this.toasts.push(toast);
					setTimeout(() => this.remove(toast.id), toast.duration ?? 5000);
				},
				remove(id) {
					this.toasts = this.toasts.filter(t => t.id !== id);
				}
			}"
			x-on:toast.window="add($event.detail)"
			class="fixed bottom-5 right-5 z-[9999] flex flex-col gap-3 w-80 pointer-events-none"
		>
			@if (session('success'))
				<script>window.addEventListener('DOMContentLoaded',()=>window.dispatchEvent(new CustomEvent('toast',{detail:{type:'success',message:{{ Js::from(session('success')) }},duration:5000}})));</script>
			@endif
			@if (session('error'))
				<script>window.addEventListener('DOMContentLoaded',()=>window.dispatchEvent(new CustomEvent('toast',{detail:{type:'error',message:{{ Js::from(session('error')) }},duration:8000}})));</script>
			@endif
			@if (session('warning'))
				<script>window.addEventListener('DOMContentLoaded',()=>window.dispatchEvent(new CustomEvent('toast',{detail:{type:'warning',message:{{ Js::from(session('warning')) }},duration:6000}})));</script>
			@endif
			@if (session('info'))
				<script>window.addEventListener('DOMContentLoaded',()=>window.dispatchEvent(new CustomEvent('toast',{detail:{type:'info',message:{{ Js::from(session('info')) }},duration:5000}})));</script>
			@endif

			<template x-for="toast in toasts" :key="toast.id">
				<div
					x-show="true"
					x-transition:enter="transition ease-out duration-300"
					x-transition:enter-start="opacity-0 translate-y-4 scale-95"
					x-transition:enter-end="opacity-100 translate-y-0 scale-100"
					x-transition:leave="transition ease-in duration-200"
					x-transition:leave-start="opacity-100 translate-y-0 scale-100"
					x-transition:leave-end="opacity-0 translate-y-2 scale-95"
					:class="{
						'border-green-200 bg-green-50 text-green-800 dark:border-green-700 dark:bg-green-900/80 dark:text-green-300': toast.type === 'success',
						'border-red-200 bg-red-50 text-red-800 dark:border-red-700 dark:bg-red-900/80 dark:text-red-300': toast.type === 'error',
						'border-yellow-200 bg-yellow-50 text-yellow-800 dark:border-yellow-700 dark:bg-yellow-900/80 dark:text-yellow-300': toast.type === 'warning',
						'border-blue-200 bg-blue-50 text-blue-800 dark:border-blue-700 dark:bg-blue-900/80 dark:text-blue-300': toast.type === 'info',
					}"
					class="pointer-events-auto flex items-start gap-3 rounded-xl border px-4 py-3 shadow-lg text-sm backdrop-blur-sm"
				>
					{{-- icon --}}
					<span class="mt-0.5 shrink-0">
						<template x-if="toast.type === 'success'">
							<svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
						</template>
						<template x-if="toast.type === 'error'">
							<svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
						</template>
						<template x-if="toast.type === 'warning'">
							<svg class="w-5 h-5 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/></svg>
						</template>
						<template x-if="toast.type === 'info'">
							<svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
						</template>
					</span>
					<span class="flex-1 leading-snug" x-text="toast.message"></span>
					<button @click="remove(toast.id)" class="shrink-0 opacity-60 hover:opacity-100 transition mt-0.5">
						<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
					</button>
				</div>
			</template>
		</div>

	</body>

	@stack('scripts')

</html>
