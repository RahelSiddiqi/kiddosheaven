@extends('admin.layouts.app')

@section('title', 'Edit: ' . $page->title . ' — Kiddo\'s Heaven')

@section('header_title', 'Edit: ' . $page->title)

@section('content')
	<form action="{{ route('admin.cms.pages.update', $page) }}" method="POST" class="space-y-6">
		@csrf
		@method('PUT')

		<div class="grid gap-6 md:grid-cols-2">
			<!-- Basic Info -->
			<div class="card"
				:class="{ 'dark bg-slate-900 border-slate-800': isDarkMode, 'bg-white border-gray-200': !isDarkMode }">
				<div class="card-header" :class="{ 'dark border-slate-800': isDarkMode, 'border-gray-200': !isDarkMode }">
					<h2 class="card-title" :class="{ 'text-white': isDarkMode, 'text-gray-900': !isDarkMode }">
						Basic Information
					</h2>
					<p class="card-description" :class="{ 'text-gray-400': isDarkMode, 'text-gray-500': !isDarkMode }">
						Update the page details
					</p>
				</div>
				<div class="card-content space-y-4">
					<div>
						<label for="title" class="label">Page Title *</label>
						<input type="text" id="title" name="title" value="{{ old('title', $page->title) }}"
							class="input @error('title') border-red-500 @enderror"
							:class="{ 'dark bg-slate-800 border-slate-700 text-white': isDarkMode, 'bg-white border-gray-300': !isDarkMode }"
							required>
						@error('title')
							<p class="text-red-500 text-sm mt-1">{{ $message }}</p>
						@enderror
					</div>

					<div>
						<label for="slug" class="label">URL Slug</label>
						<div class="flex items-center gap-2">
							<span class="text-sm" :class="{ 'text-gray-400': isDarkMode, 'text-gray-500': !isDarkMode }">/page/</span>
							<input type="text" id="slug" name="slug" value="{{ old('slug', $page->slug) }}"
								class="input @error('slug') border-red-500 @enderror"
								:class="{ 'dark bg-slate-800 border-slate-700 text-white': isDarkMode, 'bg-white border-gray-300': !isDarkMode }">
						</div>
						@error('slug')
							<p class="text-red-500 text-sm mt-1">{{ $message }}</p>
						@enderror
					</div>

					<div>
						<label for="template" class="label">Template</label>
						<select id="template" name="template" class="input @error('template') border-red-500 @enderror"
							:class="{ 'dark bg-slate-800 border-slate-700 text-white': isDarkMode, 'bg-white border-gray-300': !isDarkMode }">
							<option value="default" {{ $page->template === 'default' ? 'selected' : '' }}>Default</option>
							<option value="full-width" {{ $page->template === 'full-width' ? 'selected' : '' }}>Full Width</option>
							<option value="sidebar" {{ $page->template === 'sidebar' ? 'selected' : '' }}>With Sidebar</option>
						</select>
					</div>

					<div>
						<label class="flex items-center gap-2 cursor-pointer">
							<input type="checkbox" name="is_active" value="1" {{ $page->is_active ? 'checked' : '' }}
								class="w-4 h-4 rounded text-blue-600">
							<span class="text-sm" :class="{ 'text-gray-300': isDarkMode, 'text-gray-700': !isDarkMode }">Active</span>
						</label>
					</div>
				</div>
			</div>

			<!-- SEO -->
			<div class="card"
				:class="{ 'dark bg-slate-900 border-slate-800': isDarkMode, 'bg-white border-gray-200': !isDarkMode }">
				<div class="card-header" :class="{ 'dark border-slate-800': isDarkMode, 'border-gray-200': !isDarkMode }">
					<h2 class="card-title" :class="{ 'text-white': isDarkMode, 'text-gray-900': !isDarkMode }">
						SEO Settings
					</h2>
					<p class="card-description" :class="{ 'text-gray-400': isDarkMode, 'text-gray-500': !isDarkMode }">
						Optimize this page for search engines
					</p>
				</div>
				<div class="card-content space-y-4">
					<div>
						<label for="meta_title" class="label">Meta Title</label>
						<input type="text" id="meta_title" name="meta_title" value="{{ old('meta_title', $page->meta_title) }}"
							placeholder="Page title for search engines (50-60 characters)"
							class="input @error('meta_title') border-red-500 @enderror"
							:class="{ 'dark bg-slate-800 border-slate-700 text-white': isDarkMode, 'bg-white border-gray-300': !isDarkMode }">
						@error('meta_title')
							<p class="text-red-500 text-sm mt-1">{{ $message }}</p>
						@enderror
					</div>

					<div>
						<label for="meta_description" class="label">Meta Description</label>
						<textarea id="meta_description" name="meta_description" rows="3"
						 placeholder="Brief description for search results (150-160 characters)"
						 class="input @error('meta_description') border-red-500 @enderror"
						 :class="{ 'dark bg-slate-800 border-slate-700 text-white': isDarkMode, 'bg-white border-gray-300': !isDarkMode }">{{ old('meta_description', $page->meta_description) }}</textarea>
						@error('meta_description')
							<p class="text-red-500 text-sm mt-1">{{ $message }}</p>
						@enderror
					</div>
				</div>
			</div>
		</div>

		<!-- Content -->
		<div class="card"
			:class="{ 'dark bg-slate-900 border-slate-800': isDarkMode, 'bg-white border-gray-200': !isDarkMode }">
			<div class="card-header" :class="{ 'dark border-slate-800': isDarkMode, 'border-gray-200': !isDarkMode }">
				<h2 class="card-title" :class="{ 'text-white': isDarkMode, 'text-gray-900': !isDarkMode }">
					Page Content
				</h2>
				<p class="card-description" :class="{ 'text-gray-400': isDarkMode, 'text-gray-500': !isDarkMode }">
					Update the content for this page
				</p>
			</div>
			<div class="card-content">
				<textarea id="content" name="content" class="wysiwyg-editor @error('content') border-red-500 @enderror"
				 :class="{ 'dark bg-slate-800 border-slate-700 text-white': isDarkMode, 'bg-white border-gray-300': !isDarkMode }">{{ old('content', $page->content) }}</textarea>
				@error('content')
					<p class="text-red-500 text-sm mt-1">{{ $message }}</p>
				@enderror
			</div>
		</div>

		<!-- Actions -->
		<div class="flex items-center justify-between">
			<div class="flex items-center gap-2">
				<a href="{{ route('page.show', $page->slug) }}" target="_blank" class="btn-secondary"
					:class="{
					    'dark bg-slate-800 text-white hover:bg-slate-700': isDarkMode,
					    'bg-gray-100 text-gray-700 hover:bg-gray-200': !
					        isDarkMode
					}">
					<svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
							d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
					</svg>
					View Page
				</a>
			</div>
			<div class="flex items-center gap-4">
				<a href="{{ route('admin.cms.pages.index') }}" class="btn-secondary"
					:class="{
					    'dark bg-slate-800 text-white hover:bg-slate-700': isDarkMode,
					    'bg-gray-100 text-gray-700 hover:bg-gray-200': !
					        isDarkMode
					}">
					Cancel
				</a>
				<button type="submit" class="btn-primary">
					Save Changes
				</button>
			</div>
		</div>
	</form>

	@push('scripts')
		<script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
		<script>
			tinymce.init({
				selector: '.wysiwyg-editor',
				height: 400,
				menubar: false,
				plugins: [
					'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
					'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
					'insertdatetime', 'media', 'table', 'help', 'wordcount'
				],
				toolbar: 'undo redo | blocks | ' +
					'bold italic forecolor | alignleft aligncenter ' +
					'alignright alignjustify | bullist numlist outdent indent | ' +
					'removeformat | help',
				content_style: 'body { font-family:system-ui, -apple-system, sans-serif; font-size:14px }',
				skin: (window.matchMedia('(prefers-color-scheme: dark)').matches || document.documentElement.classList
					.contains('dark')) ? 'oxide-dark' : 'oxide',
				content_css: (window.matchMedia('(prefers-color-scheme: dark)').matches || document.documentElement
					.classList.contains('dark')) ? 'dark' : 'default'
			});
		</script>
	@endpush
@endsection
