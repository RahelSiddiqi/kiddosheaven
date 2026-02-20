@props(['orientation' => 'horizontal', 'label' => null])

@if($orientation === 'vertical')
    <div {{ $attributes->merge(['class' => 'border-l border-[var(--color-border)] h-full mx-2']) }}></div>
@elseif($label)
    <div {{ $attributes->merge(['class' => 'relative flex items-center gap-3 my-4']) }}>
        <div class="flex-1 border-t border-[var(--color-border)]"></div>
        <span class="text-xs text-[var(--color-muted-foreground)] font-medium px-1">{{ $label }}</span>
        <div class="flex-1 border-t border-[var(--color-border)]"></div>
    </div>
@else
    <hr {{ $attributes->merge(['class' => 'border-t border-[var(--color-border)] my-4']) }}>
@endif
