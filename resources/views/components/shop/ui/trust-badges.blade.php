@props(['layout' => 'horizontal'])

@php
    $currency      = $siteSettings['currency'] ?? '৳';
    $threshold     = $siteSettings['free_shipping_threshold'] ?? 0;
    $returnDays    = $siteSettings['return_policy_days'] ?? 30;
    $codEnabled    = ($siteSettings['cod_enabled'] ?? '0') == '1';
    $safeNonToxic  = ($siteSettings['safe_non_toxic'] ?? '1') == '1';

    $badges = [];

    if ($safeNonToxic) {
        $badges[] = [
            'icon'  => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z',
            'title' => 'Safe & Certified',
            'desc'  => '100% non-toxic materials',
        ];
    }

    if ($threshold > 0) {
        $badges[] = [
            'icon'  => 'M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0',
            'title' => 'Free Shipping',
            'desc'  => 'On orders over ' . $currency . number_format($threshold, 0),
        ];
    }

    if ($codEnabled) {
        $badges[] = [
            'icon'  => 'M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z',
            'title' => 'Cash on Delivery',
            'desc'  => 'Pay when you receive',
        ];
    }

    if ($returnDays > 0) {
        $badges[] = [
            'icon'  => 'M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15',
            'title' => 'Easy Returns',
            'desc'  => $returnDays . '-day return policy',
        ];
    }

    $badges[] = [
        'icon'  => 'M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z',
        'title' => '24/7 Support',
        'desc'  => 'Dedicated customer care',
    ];
@endphp

<div class="{{ $layout === 'horizontal' ? 'grid grid-cols-2 md:grid-cols-4 gap-4 md:gap-6' : 'space-y-4' }}">
    @foreach ($badges as $badge)
        <div class="flex {{ $layout === 'horizontal' ? 'flex-col items-center text-center' : 'items-center gap-3' }} p-4 rounded-xl bg-gray-50">
            <div class="{{ $layout === 'horizontal' ? 'w-12 h-12 mb-3' : 'w-10 h-10 flex-shrink-0' }} rounded-full bg-primary/10 flex items-center justify-center">
                <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $badge['icon'] }}"/>
                </svg>
            </div>
            <div>
                <h4 class="font-bold text-gray-900 text-sm">{{ $badge['title'] }}</h4>
                <p class="text-xs text-gray-500 mt-0.5">{{ $badge['desc'] }}</p>
            </div>
        </div>
    @endforeach
</div>
