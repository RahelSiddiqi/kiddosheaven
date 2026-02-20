@extends('layouts.app')

@section('title', 'My Addresses — Kiddo\'s Heaven')

@section('content')
    {{-- Page Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6 sm:mb-8">
        <div>
            <h1 class="text-xl sm:text-2xl md:text-3xl font-bold text-gray-900">My Addresses</h1>
            <p class="text-gray-500 mt-1 text-sm sm:text-base">Manage your shipping and billing addresses</p>
        </div>
        <button type="button" onclick="openAddressModal()"
            class="inline-flex items-center gap-2 px-4 py-2.5 bg-primary text-white rounded-lg hover:bg-primary-dark transition text-sm font-medium w-full sm:w-auto justify-center">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Add New Address
        </button>
    </div>

    {{-- Messages --}}
    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg mb-6" role="alert">
            <p class="text-sm">{{ session('success') }}</p>
        </div>
    @endif

    {{-- Address Grid --}}
    @if($addresses->isEmpty())
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-12 text-center">
            <div class="w-20 h-20 rounded-full bg-gray-100 flex items-center justify-center mx-auto mb-4">
                <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 mb-2">No addresses yet</h3>
            <p class="text-gray-500 mb-6">Add your first address to make checkout faster</p>
            <button type="button" onclick="openAddressModal()" class="inline-flex items-center gap-2 px-6 py-3 bg-primary text-white rounded-lg hover:bg-primary-dark transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Add Address
            </button>
        </div>
    @else
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($addresses as $address)
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 relative group hover:shadow-md transition">
                    {{-- Default Badge --}}
                    @if($address->is_default)
                        <span class="absolute top-4 right-4 inline-flex items-center gap-1 text-xs font-medium bg-primary/10 text-primary px-2.5 py-1 rounded-full">
                            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            Default
                        </span>
                    @endif

                    {{-- Type Badge --}}
                    <div class="mb-4">
                        <span class="inline-flex items-center text-xs font-medium text-gray-600 bg-gray-100 px-2.5 py-1 rounded-full uppercase">
                            {{ ucfirst($address->type) }}
                        </span>
                    </div>

                    {{-- Address Info --}}
                    <div class="space-y-2 mb-4">
                        <h3 class="font-semibold text-gray-900">{{ $address->name }}</h3>
                        <p class="text-sm text-gray-600">{{ $address->phone }}</p>
                        <p class="text-sm text-gray-600 leading-relaxed">
                            {{ $address->address_line1 }}<br>
                            @if($address->address_line2)
                                {{ $address->address_line2 }}<br>
                            @endif
                            {{ $address->city }}
                            @if($address->district), {{ $address->district }}@endif
                            @if($address->postal_code) - {{ $address->postal_code }}@endif
                        </p>
                    </div>

                    {{-- Actions --}}
                    <div class="flex items-center gap-2 pt-4 border-t">
                        @if(!$address->is_default)
                            <button type="button" onclick="setDefaultAddress({{ $address->id }})"
                                class="flex-1 text-sm text-gray-600 hover:text-primary font-medium py-2 px-3 rounded-lg hover:bg-gray-50 transition">
                                Set Default
                            </button>
                        @endif
                        <button type="button" onclick="editAddress({{ $address->id }})"
                            class="flex-1 text-sm text-gray-600 hover:text-primary font-medium py-2 px-3 rounded-lg hover:bg-gray-50 transition">
                            Edit
                        </button>
                        <button type="button" onclick="deleteAddress({{ $address->id }})"
                            class="text-sm text-red-600 hover:text-red-700 font-medium py-2 px-3 rounded-lg hover:bg-red-50 transition">
                            Delete
                        </button>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    {{-- Back to Account --}}
    <div class="mt-8">
        <a href="{{ route('account') }}" class="inline-flex items-center gap-2 text-gray-600 hover:text-gray-900 text-sm font-medium">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Back to Account
        </a>
    </div>

    {{-- Address Modal --}}
    <div id="addressModal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
            <div class="p-6 border-b border-gray-100">
                <h3 class="text-xl font-bold text-gray-900" id="modalTitle">Add Address</h3>
            </div>

            <form id="addressForm" class="p-6 space-y-4">
                @csrf
                <input type="hidden" id="addressId" name="address_id">
                <input type="hidden" id="formMethod" name="_method" value="POST">

                <div class="grid sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Address Type *</label>
                        <select name="type" id="addressType" required
                            class="w-full rounded-lg border border-gray-200 px-4 py-2.5 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none">
                            <option value="shipping">Shipping</option>
                            <option value="billing">Billing</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Full Name *</label>
                        <input type="text" name="name" id="addressName" required
                            class="w-full rounded-lg border border-gray-200 px-4 py-2.5 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Phone Number *</label>
                    <input type="tel" name="phone" id="addressPhone" required
                        class="w-full rounded-lg border border-gray-200 px-4 py-2.5 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Address Line 1 *</label>
                    <input type="text" name="address_line1" id="addressLine1" required placeholder="House/Flat No, Street"
                        class="w-full rounded-lg border border-gray-200 px-4 py-2.5 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Address Line 2</label>
                    <input type="text" name="address_line2" id="addressLine2" placeholder="Area, Landmark"
                        class="w-full rounded-lg border border-gray-200 px-4 py-2.5 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none">
                </div>

                <div class="grid sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">City *</label>
                        <input type="text" name="city" id="addressCity" required
                            class="w-full rounded-lg border border-gray-200 px-4 py-2.5 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">District</label>
                        <input type="text" name="district" id="addressDistrict"
                            class="w-full rounded-lg border border-gray-200 px-4 py-2.5 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Postal Code</label>
                        <input type="text" name="postal_code" id="addressPostalCode"
                            class="w-full rounded-lg border border-gray-200 px-4 py-2.5 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none">
                    </div>
                </div>

                <div class="flex items-center">
                    <input type="checkbox" name="is_default" id="isDefault" value="1"
                        class="rounded border-gray-300 text-primary focus:ring-primary h-4 w-4">
                    <label for="isDefault" class="ml-2 text-sm text-gray-700">Set as default address</label>
                </div>

                <div class="flex gap-3 pt-4">
                    <button type="button" onclick="closeAddressModal()"
                        class="flex-1 px-4 py-2.5 border border-gray-200 text-gray-700 rounded-lg hover:bg-gray-50 transition font-medium">
                        Cancel
                    </button>
                    <button type="submit"
                        class="flex-1 px-4 py-2.5 bg-primary text-white rounded-lg hover:bg-primary-dark transition font-medium">
                        Save Address
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
<script>
function openAddressModal(addressData = null) {
    const modal = document.getElementById('addressModal');
    const form = document.getElementById('addressForm');
    const title = document.getElementById('modalTitle');

    if (addressData) {
        title.textContent = 'Edit Address';
        document.getElementById('addressId').value = addressData.id;
        document.getElementById('formMethod').value = 'PUT';
        document.getElementById('addressType').value = addressData.type;
        document.getElementById('addressName').value = addressData.name;
        document.getElementById('addressPhone').value = addressData.phone;
        document.getElementById('addressLine1').value = addressData.address_line1;
        document.getElementById('addressLine2').value = addressData.address_line2 || '';
        document.getElementById('addressCity').value = addressData.city;
        document.getElementById('addressDistrict').value = addressData.district || '';
        document.getElementById('addressPostalCode').value = addressData.postal_code || '';
        document.getElementById('isDefault').checked = addressData.is_default;
    } else {
        title.textContent = 'Add Address';
        form.reset();
        document.getElementById('formMethod').value = 'POST';
    }

    modal.classList.remove('hidden');
}

function closeAddressModal() {
    document.getElementById('addressModal').classList.add('hidden');
}

function editAddress(id) {
    fetch(`/account/addresses/${id}`)
        .then(r => r.json())
        .then(data => openAddressModal(data))
        .catch(err => alert('Error loading address'));
}

function setDefaultAddress(id) {
    if (!confirm('Set this as your default address?')) return;

    fetch(`/account/addresses/${id}/default`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
        }
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            location.reload();
        }
    })
    .catch(err => alert('Error updating default address'));
}

function deleteAddress(id) {
    if (!confirm('Are you sure you want to delete this address?')) return;

    fetch(`/account/addresses/${id}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
        }
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            location.reload();
        }
    })
    .catch(err => alert('Error deleting address'));
}

document.getElementById('addressForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const formData = new FormData(this);
    const method = document.getElementById('formMethod').value;
    const addressId = document.getElementById('addressId').value;
    const url = addressId ? `/account/addresses/${addressId}` : '/account/addresses';

    fetch(url, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
        },
        body: formData
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            location.reload();
        }
    })
    .catch(err => alert('Error saving address'));
});
</script>
@endpush
