<div class="space-y-8">
    {{-- Page Header --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-3xl md:text-4xl font-bold text-gray-900">My Account</h1>
            <p class="text-gray-500 mt-1">Welcome back, {{ $user->name }}</p>
        </div>
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit"
                class="inline-flex items-center gap-2 px-4 py-2 rounded-lg border border-gray-200 text-gray-600 hover:bg-gray-50 transition text-sm font-medium">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                </svg>
                Logout
            </button>
        </form>
    </div>

    <div class="grid lg:grid-cols-3 gap-8">
        {{-- Profile Section --}}
        <div class="lg:col-span-1">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <div class="text-center mb-6">
                    <div class="w-20 h-20 rounded-full bg-primary/10 flex items-center justify-center mx-auto mb-3">
                        <span class="text-3xl font-bold text-primary">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                    </div>
                    <h2 class="font-bold text-gray-900">{{ $user->name }}</h2>
                    <p class="text-sm text-gray-500">{{ $user->email }}</p>
                </div>

                <form wire:submit="updateProfile" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                        <input type="text" wire:model="name"
                            class="w-full rounded-lg border border-gray-200 px-4 py-3 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition">
                        @error('name') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                        <input type="tel" wire:model="phone"
                            class="w-full rounded-lg border border-gray-200 px-4 py-3 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition">
                        @error('phone') <p class="text-red-600 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>
                    <button type="submit" wire:loading.attr="disabled"
                        class="w-full px-4 py-3 rounded-lg bg-primary text-white font-bold hover:bg-primary-dark transition disabled:opacity-50">
                        <span wire:loading.remove wire:target="updateProfile">Update Profile</span>
                        <span wire:loading wire:target="updateProfile">Saving...</span>
                    </button>
                </form>
            </div>

            {{-- Quick Links --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mt-6">
                <h3 class="font-bold text-gray-900 mb-4">Quick Links</h3>
                <div class="space-y-2">
                    <a href="{{ route('customer.orders.index') }}" wire:navigate
                        class="flex items-center gap-3 p-3 rounded-lg hover:bg-gray-50 transition">
                        <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                        <span class="font-medium text-gray-700">All Orders</span>
                    </a>
                    <a href="{{ route('catalog') }}" wire:navigate
                        class="flex items-center gap-3 p-3 rounded-lg hover:bg-gray-50 transition">
                        <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                        </svg>
                        <span class="font-medium text-gray-700">Continue Shopping</span>
                    </a>
                </div>
            </div>
        </div>

        {{-- Recent Orders --}}
        <div class="lg:col-span-2">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-bold text-gray-900">Recent Orders</h2>
                    <a href="{{ route('customer.orders.index') }}" wire:navigate
                        class="text-primary hover:text-primary-dark font-medium text-sm">View All</a>
                </div>

                @if ($recentOrders->isEmpty())
                    <div class="text-center py-8">
                        <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                        <p class="text-gray-500 mb-4">No orders yet</p>
                        <a href="{{ route('catalog') }}" wire:navigate
                            class="inline-flex items-center gap-2 px-6 py-3 bg-primary text-white font-bold rounded-lg hover:bg-primary-dark transition">
                            Start Shopping
                        </a>
                    </div>
                @else
                    <div class="space-y-4">
                        @foreach ($recentOrders as $order)
                            <a href="{{ route('customer.orders.show', $order->id) }}" wire:navigate
                                class="block p-4 rounded-xl border border-gray-100 hover:border-primary/30 hover:shadow-sm transition">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="font-bold text-gray-900">#{{ $order->order_number }}</span>
                                    <span @class([
                                        'px-3 py-1 rounded-full text-xs font-bold',
                                        'bg-yellow-100 text-yellow-700' => $order->status === 'pending',
                                        'bg-blue-100 text-blue-700' => $order->status === 'processing',
                                        'bg-green-100 text-green-700' => $order->status === 'delivered',
                                        'bg-red-100 text-red-700' => $order->status === 'cancelled',
                                        'bg-purple-100 text-purple-700' => $order->status === 'shipped',
                                    ])>{{ ucfirst($order->status) }}</span>
                                </div>
                                <div class="flex items-center justify-between text-sm text-gray-500">
                                    <span>{{ $order->created_at->format('M d, Y') }}</span>
                                    <span class="font-bold text-primary-dark">৳{{ number_format($order->total, 2) }}</span>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
