@props([
    'striped' => false,
    'hover'   => true,
    'bordered' => false,
])

<div class="overflow-hidden rounded-xl border border-[var(--color-border)] shadow-[var(--shadow-xs)]">
    <div class="overflow-x-auto">
        <table {{ $attributes->merge(['class' => 'w-full text-sm']) }}>
            @if(isset($head))
                <thead class="bg-[var(--color-muted)] border-b border-[var(--color-border)]">
                    <tr>{{ $head }}</tr>
                </thead>
            @endif
            <tbody @class([
                'divide-y divide-[var(--color-border)]',
                '[&>tr:nth-child(even)]:bg-[var(--color-muted)]' => $striped,
                '[&>tr:hover]:bg-[var(--color-muted)]' => $hover,
            ])>
                {{ $slot }}
            </tbody>
            @if(isset($foot))
                <tfoot class="bg-[var(--color-muted)] border-t border-[var(--color-border)]">
                    <tr>{{ $foot }}</tr>
                </tfoot>
            @endif
        </table>
    </div>
</div>
