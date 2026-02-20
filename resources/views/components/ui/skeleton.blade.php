@props(['lines' => 1, 'class' => ''])

@if($lines > 1)
    <div class="space-y-2 {{ $class }}">
        @for($i = 0; $i < $lines; $i++)
            <div @class([
                'h-4 rounded-md bg-[var(--color-muted)] animate-pulse',
                'w-3/4' => $i === $lines - 1 && $lines > 1,
                'w-full' => !($i === $lines - 1 && $lines > 1),
            ])></div>
        @endfor
    </div>
@else
    <div {{ $attributes->merge(['class' => "rounded-md bg-[var(--color-muted)] animate-pulse $class"]) }}>
        {{ $slot }}
    </div>
@endif
