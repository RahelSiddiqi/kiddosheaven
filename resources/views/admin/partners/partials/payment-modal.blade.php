<div x-data="paymentModal()" x-show="show" @open-payment-modal.window="open($event.detail.partnerId)"
	class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm transition-opacity"
	x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
	x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
	style="display: none;">
	<div @click.outside="close()"
		class="w-full max-w-md transform rounded-2xl border border-gray-200 bg-white p-6 shadow-xl transition-all dark:border-gray-800 dark:bg-gray-900"
		x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 scale-95 translate-y-4"
		x-transition:enter-end="opacity-100 scale-100 translate-y-0" x-transition:leave="ease-in duration-200"
		x-transition:leave-start="opacity-100 scale-100 translate-y-0"
		x-transition:leave-end="opacity-0 scale-95 translate-y-4">

		<div class="flex items-center justify-between mb-4">
			<h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">Add Payment</h3>
			<button @click="close()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
				<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
					<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
				</svg>
			</button>
		</div>

		<form :action="actionUrl" method="POST" @submit.prevent="submitForm">
			@csrf
			<input type="hidden" name="partner_id" :value="partnerId">

			<div class="space-y-4">
				<div>
					<label for="payment_amount" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Amount (৳)
						*</label>
					<input type="number" name="amount" id="payment_amount" step="0.01" min="0" required x-model="amount"
						class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-800 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-blue-800">
				</div>

				<div>
					<label for="payment_date" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Payment Date
						*</label>
					<input type="date" name="payment_date" id="payment_date" required
						:value="new Date().toISOString().split('T')[0]"
						class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-800 dark:text-white/90 dark:focus:border-blue-800">
				</div>

				<div>
					<label for="payment_description"
						class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Description</label>
					<textarea name="description" id="payment_description" rows="2" placeholder="Payment description..."
					 class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-800 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-blue-800"></textarea>
				</div>
			</div>

			<div class="mt-6 flex gap-4">
				<button type="submit"
					class="h-10.5 flex-1 inline-flex items-center justify-center rounded-lg border border-blue-500 bg-blue-500 px-4 py-2.5 text-sm font-medium text-white shadow-theme-xs hover:bg-blue-600">
					Save Payment
				</button>
				<button type="button" @click="close()"
					class="h-10.5 flex-1 inline-flex items-center justify-center rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/3">
					Cancel
				</button>
			</div>
		</form>
	</div>
</div>

<script>
	function paymentModal() {
		return {
			show: false,
			partnerId: null,
			actionUrl: '',
			amount: '',

			open(partnerId) {
				this.partnerId = partnerId;
				this.actionUrl = `/admin/partners/${partnerId}/payments`;
				this.show = true;
			},

			close() {
				this.show = false;
				this.partnerId = null;
				this.amount = '';
			},

			submitForm() {
				this.$el.submit();
			}
		}
	}
</script>
