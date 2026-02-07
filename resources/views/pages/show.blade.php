@extends('layouts.app')

@section('title', $page->meta_title ?? $page->title)

@push('meta')
	@if ($page->meta_title)
		<meta name="title" content="{{ $page->meta_title }}">
	@endif
	@if ($page->meta_description)
		<meta name="description" content="{{ $page->meta_description }}">
	@endif
@endpush

@section('content')
	<div class="bg-gray-50 dark:bg-slate-950 min-h-screen py-8">
		<div class="container mx-auto px-4">
			<!-- Breadcrumb -->
			<nav class="flex items-center space-x-2 text-sm text-gray-500 dark:text-gray-400 mb-6">
				<a href="{{ route('home') }}" class="hover:text-blue-500 transition">Home</a>
				<span>/</span>
				<span class="text-gray-900 dark:text-white">{{ $page->title }}</span>
			</nav>

			<!-- Page Header -->
			<div class="mb-8">
				<h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">
					{{ $page->title }}
				</h1>
				@if ($page->meta_description)
					<p class="text-gray-600 dark:text-gray-400 max-w-2xl">
						{{ $page->meta_description }}
					</p>
				@endif
			</div>

			<!-- Page Content -->
			<div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-gray-200 dark:border-slate-800 p-8">
				<article class="prose prose-lg dark:prose-invert max-w-none">
					@if ($page->content)
						{!! $page->content !!}
					@else
						<p class="text-gray-500 dark:text-gray-400 text-center py-8">
							No content has been added to this page yet.
						</p>
					@endif
				</article>
			</div>

			<!-- Page Footer -->
			<div class="mt-8 flex items-center justify-between text-sm text-gray-500 dark:text-gray-400">
				<span>Last updated: {{ $page->updated_at->format('F d, Y') }}</span>
				@auth('admin')
					<a href="{{ route('admin.cms.pages.edit', $page) }}" class="text-blue-500 hover:underline flex items-center gap-1">
						<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
							<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
								d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
						</svg>
						Edit this page
					</a>
				@endauth
			</div>
		</div>
	</div>
@endsection
