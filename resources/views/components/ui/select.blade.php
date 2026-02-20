@props([
    'label'    => null,
    'error'    => null,
    'hint'     => null,
    'required' => false,
    'options'  => [],
    'placeholder' => null,
    'id'       => null,
])

@php
$inputId  = $id ?? ('select-' . ($attributes->get('name') ?? uniqid()));
$hasError = !empty($error);
$base     = 'w-full rounded-lg border bg-white text-sm text-[var(--color-foreground)] transition-colors duration-150 focus:outline-none focus:ring-2 focus:ring-[var(--color-ring)] disabled:cursor-not-allowed disabled:opacity-50 h-10 px-3 appearance-none cursor-pointer pr-9';
$border   = $hasError ? 'border-red-400 focus:ring-red-300' : 'border-[var(--color-border)] focus:border-[var(--color-ring)]';
@endphp

<div class="flex flex-col gap-1.5">
    @if($label)
        <label for="{{ $inputId }}" class="text-sm font-medium text-[var(--color-foreground)]">
            {{ $label }}
            @if($required) <span class="text-red-500 ml-0.5">*</span> @endif
        </label>
    @endif

    <div class="relative">
        <select id="{{ $inputId }}" {{ $attributes->merge(['class' => "$base $border"]) }} @if($required) required @endif>
            @if($placeholder)
                <option value="">{{ $placeholder }}</option>
            @endif
            @foreach($options as $value => $label)
                <option value="{{ $value }}">{{ $label }}</option>
            @endforeach
            {{ $slot }}
        </select>
        <div class="absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none text-[var(--color-muted-foreground)]">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
        </div>
    </div>

    @if($hint && !$hasError) <p class="text-xs text-[var(--color-muted-foreground)]">{{ $hint }}</p> @endif
    @if($hasError) <p class="text-xs text-red-500">{{ $error }}</p> @endif
</div>
