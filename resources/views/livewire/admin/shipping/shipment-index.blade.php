<div>
    @if(session('success'))
        <div class="bg-green-50 text-green-800 px-4 py-3 rounded mb-4">{{ session('success') }}</div>
    @endif

    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold">Shipments</h1>
    </div>

    {{-- Mark Shipped Modal --}}
    @if($showShipForm)
    <div class="fixed inset-0 bg-black/40 flex items-center justify-center z-50">
        <div class="bg-white rounded-xl shadow-xl p-6 w-full max-w-md">
            <h2 class="text-lg font-semibold mb-4">Mark as Shipped</h2>
            <div class="space-y-3">
                <div>
                    <label class="block text-sm font-medium mb-1">Tracking Number</label>
                    <input wire:model="trackingNumber" type="text" class="input w-full" />
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Carrier</label>
                    <select wire:model="carrier" class="input w-full">
                        <option value="">Select carrier</option>
                        <option value="steadfast">SteadFast</option>
                        <option value="pathao">Pathao</option>
                        <option value="redx">RedX</option>
                        <option value="dhl">DHL</option>
                        <option value="fedex">FedEx</option>
                        <option value="ups">UPS</option>
                        <option value="local">Local Delivery</option>
                        <option value="self">Self Pickup</option>
                    </select>
                </div>
            </div>
            <div class="flex gap-3 mt-4">
                <button wire:click="markShipped" class="btn-primary">Confirm Shipped</button>
                <button wire:click="$set('showShipForm', false)" class="btn-secondary">Cancel</button>
            </div>
        </div>
    </div>
    @endif

    {{-- Filters --}}
    <div class="bg-white shadow rounded-lg overflow-hidden">
        <div class="p-4 border-b flex gap-3">
            <input wire:model.live.debounce.300ms="search" type="text" placeholder="Search order # or tracking..." class="input w-72" />
            <select wire:model.live="filterStatus" class="input w-40">
                <option value="">All Statuses</option>
                <option value="processing">Processing</option>
                <option value="in_transit">In Transit</option>
                <option value="out_for_delivery">Out for Delivery</option>
                <option value="delivered">Delivered</option>
                <option value="returned">Returned</option>
            </select>
        </div>
        <table class="w-full text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left">Order</th>
                    <th class="px-4 py-3 text-left">Tracking</th>
                    <th class="px-4 py-3 text-left">Carrier</th>
                    <th class="px-4 py-3 text-left">Status</th>
                    <th class="px-4 py-3 text-left">Date</th>
                    <th class="px-4 py-3 text-left">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse($shipments as $shipment)
                <tr>
                    <td class="px-4 py-3 font-medium">
                        <a href="{{ route('admin.orders.show', $shipment->order_id) }}" class="text-blue-600 hover:underline">
                            #{{ $shipment->order->order_number }}
                        </a>
                    </td>
                    <td class="px-4 py-3">
                        @if($shipment->tracking_number)
                            @if($shipment->tracking_link)
                                <a href="{{ $shipment->tracking_link }}" target="_blank" class="text-blue-600 hover:underline">{{ $shipment->tracking_number }}</a>
                            @else
                                {{ $shipment->tracking_number }}
                            @endif
                        @else
                            <span class="text-gray-400">—</span>
                        @endif
                    </td>
                    <td class="px-4 py-3">{{ $shipment->carrier ? strtoupper($shipment->carrier) : '—' }}</td>
                    <td class="px-4 py-3">
                        <span class="badge badge-{{ $shipment->status === 'delivered' ? 'green' : ($shipment->status === 'in_transit' ? 'blue' : 'gray') }}">
                            {{ ucfirst(str_replace('_', ' ', $shipment->status)) }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-gray-500">{{ $shipment->created_at->format('M d') }}</td>
                    <td class="px-4 py-3 flex gap-2">
                        @if(in_array($shipment->status, ['processing']))
                            <button wire:click="openShipForm({{ $shipment->id }})" class="text-blue-600 hover:underline text-xs">Ship</button>
                        @endif
                        @if(in_array($shipment->status, ['in_transit', 'out_for_delivery']))
                            <button wire:click="markDelivered({{ $shipment->id }})" wire:confirm="Mark as delivered?" class="text-green-600 hover:underline text-xs">Delivered</button>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="px-4 py-8 text-center text-gray-400">No shipments found.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="p-4">{{ $shipments->links() }}</div>
    </div>
</div>
