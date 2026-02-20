<div>
    @if(session('success'))
        <div class="bg-green-50 text-green-800 px-4 py-3 rounded mb-4">{{ session('success') }}</div>
    @endif

    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold">Returns & Refunds</h1>
    </div>

    {{-- Approve Modal --}}
    @if($showApproveForm)
    <div class="fixed inset-0 bg-black/40 flex items-center justify-center z-50">
        <div class="bg-white rounded-xl shadow-xl p-6 w-full max-w-md">
            <h2 class="text-lg font-semibold mb-4">Approve Return</h2>
            <div class="space-y-3">
                <div>
                    <label class="block text-sm font-medium mb-1">Refund Amount (৳)</label>
                    <input wire:model="refundAmount" type="number" step="0.01" class="input w-full" />
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Refund Method</label>
                    <select wire:model="refundMethod" class="input w-full">
                        <option value="original_payment">Original Payment Method</option>
                        <option value="store_credit">Store Credit</option>
                        <option value="bank_transfer">Bank Transfer</option>
                    </select>
                </div>
            </div>
            <div class="flex gap-3 mt-4">
                <button wire:click="approve" class="btn-primary">Approve & Refund</button>
                <button wire:click="$set('showApproveForm', false)" class="btn-secondary">Cancel</button>
            </div>
        </div>
    </div>
    @endif

    {{-- Filters + Table --}}
    <div class="bg-white shadow rounded-lg overflow-hidden">
        <div class="p-4 border-b flex gap-3">
            <input wire:model.live.debounce.300ms="search" type="text" placeholder="Search by return # or order #..." class="input w-72" />
            <select wire:model.live="filterStatus" class="input w-40">
                <option value="">All Statuses</option>
                <option value="pending">Pending</option>
                <option value="approved">Approved</option>
                <option value="declined">Declined</option>
                <option value="received">Received</option>
                <option value="processed">Processed</option>
            </select>
        </div>
        <table class="w-full text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-left">Return #</th>
                    <th class="px-4 py-3 text-left">Order</th>
                    <th class="px-4 py-3 text-left">Customer</th>
                    <th class="px-4 py-3 text-left">Type</th>
                    <th class="px-4 py-3 text-left">Status</th>
                    <th class="px-4 py-3 text-left">Refund</th>
                    <th class="px-4 py-3 text-left">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse($returns as $return)
                <tr>
                    <td class="px-4 py-3 font-mono text-xs">{{ $return->return_number }}</td>
                    <td class="px-4 py-3">
                        <a href="{{ route('admin.orders.show', $return->order_id) }}" class="text-blue-600 hover:underline">
                            #{{ $return->order->order_number }}
                        </a>
                    </td>
                    <td class="px-4 py-3">{{ $return->order->customer_name }}</td>
                    <td class="px-4 py-3">{{ ucfirst(str_replace('_', ' ', $return->type)) }}</td>
                    <td class="px-4 py-3">
                        <span class="badge badge-{{ $return->status === 'approved' ? 'green' : ($return->status === 'declined' ? 'red' : ($return->status === 'processed' ? 'blue' : 'gray')) }}">
                            {{ $return->status_label }}
                        </span>
                    </td>
                    <td class="px-4 py-3">
                        {{ $return->refund_amount ? '৳' . number_format($return->refund_amount, 0) : '—' }}
                    </td>
                    <td class="px-4 py-3 flex gap-2">
                        @if($return->status === 'pending')
                            <button wire:click="openApprove({{ $return->id }})" class="text-green-600 hover:underline text-xs">Approve</button>
                            <button wire:click="decline({{ $return->id }})" wire:confirm="Decline this return?" class="text-red-500 hover:underline text-xs">Decline</button>
                        @elseif($return->status === 'approved')
                            <button wire:click="markReceived({{ $return->id }})" class="text-blue-600 hover:underline text-xs">Mark Received</button>
                        @elseif($return->status === 'received')
                            <button wire:click="process({{ $return->id }})" class="text-purple-600 hover:underline text-xs">Process</button>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="px-4 py-8 text-center text-gray-400">No returns found.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="p-4">{{ $returns->links() }}</div>
    </div>
</div>
