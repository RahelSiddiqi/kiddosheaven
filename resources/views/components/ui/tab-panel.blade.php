@props(['name'])

<div x-show="activeTab === '{{ $name }}'" x-cloak>
    {{ $slot }}
</div>
