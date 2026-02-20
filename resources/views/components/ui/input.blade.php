@props([
    'label'       => null,
    'error'       => null,
    'hint'        => null,
    'leading'     => null,
    'trailing'    => null,
    'required'    => false,
    'id'          => null,
])

@php
$inputId = $id ?? ($attributes->get('name') ? 'input-' . $attributes->get('name') : uniqid('input-'));
$hasError = !empty($error);
$inputBase = 'w-full rounded-lg border bg-white text-sm text-[var(--color-foreground)] placeholder:text-[var(--color-muted-foreground)] transition-colors duration-150 focus:outline-none focus:ring-2 focus:ring-[var(--color-ring)] focus:ring-offset-0 disabled:cursor-not-allowed disabled:opacity-50';
$inputBorder = $hasError
    ? 'border-red-400 focus:ring-red-300'
    : 'border-[var(--color-border)] focus:border-[var(--color-ring)]';
$inputPadding = 'px-3 py-2 h-10';
@endphp

<div class="flex flex-col gap-1.5">
    @if($label)
        <label for="{{ $inputId }}" class="text-sm font-medium text-[var(--color-foreground)]">
            {{ $label }}
            @if($required) <span class="text-red-500 ml-0.5">*</span> @endif
        </label>
    @endif

    <div class="relative flex items-center">
        @if($leading)
            <div class="absolute left-3 text-[var(--color-muted-foreground)] pointer-events-none">
                {{ $leading }}
            </div>
        @endif

        <input
            id="{{ $inputId }}"
            {{ $attributes->merge(['class' => "$inputBase $inputBorder $inputPadding" . ($leading ? ' pl-9' : '') . ($trailing ? ' pr-9' : '')]) }}
            @if($required) required @endif
        />

        @if($trailing)
            <div class="absolute right-3 text-[var(--color-muted-foreground)] pointer-events-none">
                {{ $trailing }}
            </div>
        @endif
    </div>

    @if($hint && !$hasError)
        <p class="text-xs text-[var(--color-muted-foreground)]">{{ $hint }}</p>
    @endif

    @if($hasError)
        <p class="text-xs text-red-500 flex items-center gap-1">
            <svg class="w-3 h-3 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
            </svg>
            {{ $error }}
        </p>
    @endif
</div>
