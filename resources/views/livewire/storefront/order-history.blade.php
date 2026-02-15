<div class="space-y-8">
    {{-- Breadcrumb --}}
    <nav class="text-sm">
        <ol class="flex items-center gap-2 text-gray-500">
            <li><a href="{{ route('home') }}" wire:navigate class="hover:text-primary">Home</a></li>
            <li><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg></li>
            <li><a href="{{ route('account') }}" wire:navigate class="hover:text-primary">Account</a></li>
            <li><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg></li>
            <li class="text-gray-800 font-medium">Orders</li>
        </ol>
    </nav>

    <h1 class="text-3xl md:text-4xl font-bold text-gray-900">My Orders</h1>

    {{-- Status Filter --}}
    <div class="flex gap-2 overflow-x-auto">
        <button wire:click="setStatus('')"
            class="px-4 py-2 rounded-lg border text-sm font-medium whitespace-nowrap transition {{ !$status ? 'border-primary bg-primary/5 text-primary' : 'border-gray-200 text-gray-600 hover:bg-gray-50' }}">
            All
        </button>
        @foreach (['pending', 'processing', 'shipped', 'delivered', 'cancelled'] as $s)
            <button wire:click="setStatus('{{ $s }}')"
                class="px-4 py-2 rounded-lg border text-sm font-medium whitespace-nowrap transition {{ $status === $s ? 'border-primary bg-primary/5 text-primary' : 'border-gray-200 text-gray-600 hover:bg-gray-50' }}">
                {{ ucfirst($s) }}
            </button>
        @endforeach
    </div>

    {{-- Orders List --}}
    @if ($orders->isEmpty())
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-12 text-center">
            <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
            </svg>
            <h3 class="text-xl font-bold text-gray-800 mb-2">No orders found</h3>
            <p class="text-gray-500 mb-6">{{ $status ? 'No ' . $status . ' orders.' : 'You haven\'t placed any orders yet.' }}</p>
            <a href="{{ route('catalog') }}" wire:navigate
                class="inline-flex items-center gap-2 px-6 py-3 bg-primary text-white font-bold rounded-lg hover:bg-primary-dark transition">
                Start Shopping
            </a>
        </div>
    @else
        <div class="space-y-4">
            @foreach ($orders as $order)
                <a href="{{ route('customer.orders.show', $order->id) }}" wire:navigate
                    class="block bg-white rounded-xl shadow-sm border border-gray-100 p-6 hover:border-primary/30 hover:shadow-md transition">
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                        <div>
                            <div class="flex items-center gap-3 mb-2">
                                <span class="font-bold text-gray-900 text-lg">#{{ $order->order_number }}</span>
                                <span @class([
                                    'px-3 py-1 rounded-full text-xs font-bold',
                                    'bg-yellow-100 text-yellow-700' => $order->status === 'pending',
                                    'bg-blue-100 text-blue-700' => $order->status === 'processing',
                                    'bg-green-100 text-green-700' => $order->status === 'delivered',
                                    'bg-red-100 text-red-700' => $order->status === 'cancelled',
                                    'bg-purple-100 text-purple-700' => $order->status === 'shipped',
                                ])>{{ ucfirst($order->status) }}</span>
                            </div>
                            <p class="text-sm text-gray-500">
                                {{ $order->created_at->format('M d, Y \a\t h:i A') }}
                                · {{ $order->items->count() }} {{ Str::plural('item', $order->items->count()) }}
                            </p>
                        </div>
                        <div class="text-right">
                            <p class="text-2xl font-bold text-primary-dark">৳{{ number_format($order->total, 2) }}</p>
                            <p class="text-sm text-gray-500">{{ ucfirst($order->payment_method ?? 'N/A') }}</p>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>

        <div class="mt-6">
            {{ $orders->links() }}
        </div>
    @endif
</div>
