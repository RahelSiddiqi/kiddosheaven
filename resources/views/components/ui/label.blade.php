@props(['required' => false])

<label {{ $attributes->merge(['class' => 'text-sm font-medium text-[var(--color-foreground)] leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70']) }}>
    {{ $slot }}
    @if($required) <span class="text-red-500 ml-0.5">*</span> @endif
</label>
