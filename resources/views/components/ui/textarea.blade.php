@props([
    'label'    => null,
    'error'    => null,
    'hint'     => null,
    'required' => false,
    'rows'     => 4,
    'id'       => null,
])

@php
$inputId  = $id ?? ('textarea-' . ($attributes->get('name') ?? uniqid()));
$hasError = !empty($error);
$base     = 'w-full rounded-lg border bg-white text-sm text-[var(--color-foreground)] placeholder:text-[var(--color-muted-foreground)] transition-colors duration-150 focus:outline-none focus:ring-2 focus:ring-[var(--color-ring)] disabled:cursor-not-allowed disabled:opacity-50 resize-y px-3 py-2';
$border   = $hasError ? 'border-red-400 focus:ring-red-300' : 'border-[var(--color-border)] focus:border-[var(--color-ring)]';
@endphp

<div class="flex flex-col gap-1.5">
    @if($label)
        <label for="{{ $inputId }}" class="text-sm font-medium text-[var(--color-foreground)]">
            {{ $label }}
            @if($required) <span class="text-red-500 ml-0.5">*</span> @endif
        </label>
    @endif

    <textarea
        id="{{ $inputId }}"
        rows="{{ $rows }}"
        {{ $attributes->merge(['class' => "$base $border"]) }}
        @if($required) required @endif
    ></textarea>

    @if($hint && !$hasError) <p class="text-xs text-[var(--color-muted-foreground)]">{{ $hint }}</p> @endif
    @if($hasError)
        <p class="text-xs text-red-500">{{ $error }}</p>
    @endif
</div>
