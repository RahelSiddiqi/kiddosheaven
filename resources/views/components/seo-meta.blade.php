@props([
    'title' => config('app.name'),
    'description' => '',
    'keywords' => '',
    'ogImage' => '',
    'ogType' => 'website',
    'noindex' => false,
])

@php
    $siteName = config('app.name', 'KiddosHeaven');
    $fullTitle = $title !== $siteName ? $title . ' | ' . $siteName : $siteName;
@endphp

<title>{{ $fullTitle }}</title>
<meta name="description" content="{{ $description ?: ($siteSettings['meta_description'] ?? '') }}">
@if($keywords)
    <meta name="keywords" content="{{ $keywords }}">
@endif
@if($noindex)
    <meta name="robots" content="noindex, nofollow">
@endif

{{-- Open Graph --}}
<meta property="og:title" content="{{ $fullTitle }}">
<meta property="og:description" content="{{ $description ?: ($siteSettings['meta_description'] ?? '') }}">
<meta property="og:type" content="{{ $ogType }}">
<meta property="og:site_name" content="{{ $siteName }}">
@if($ogImage)
    <meta property="og:image" content="{{ $ogImage }}">
@endif
<meta property="og:url" content="{{ url()->current() }}">

{{-- Twitter Card --}}
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $fullTitle }}">
<meta name="twitter:description" content="{{ $description ?: ($siteSettings['meta_description'] ?? '') }}">
@if($ogImage)
    <meta name="twitter:image" content="{{ $ogImage }}">
@endif
