<x-mail::message>
# {{ $reminderNumber === 1 ? 'You left something behind…' : ($reminderNumber === 2 ? 'Your cart is still waiting' : 'Last chance — your cart expires soon') }}

@if ($reminderNumber === 1)
Hi there! You added some items to your cart but didn't complete your purchase.
@elseif ($reminderNumber === 2)
We noticed you didn't finish your order. Your items are still in your cart — grab them before they sell out!
@else
This is your final reminder. Your cart will expire soon. Don't miss out!
@endif

<x-mail::table>
| Product | Qty | Price |
|:--------|:---:|------:|
@foreach ($items as $item)
| {{ $item['name'] ?? $item['product_name'] ?? '—' }} | {{ $item['qty'] ?? $item['quantity'] ?? 1 }} | ৳{{ number_format($item['price'] ?? 0, 0) }} |
@endforeach
</x-mail::table>

<x-mail::button :url="$recoveryUrl" color="primary">
    Complete Your Purchase
</x-mail::button>

If you have any questions, just reply to this email.

Thanks,
{{ config('app.name') }}

---
<small>If you did not abandon a cart, you can safely ignore this email. <a href="{{ $recoveryUrl }}">Unsubscribe</a></small>
</x-mail::message>
