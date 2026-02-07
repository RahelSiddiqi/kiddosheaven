<div x-data="editPaymentModal()" x-show="show"
	@open-edit-payment-modal.window="open($event.detail.paymentId, $event.detail.partnerId, $event.detail.amount, $event.detail.date, $event.detail.description, $event.detail.status)"
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
			<h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">Edit Payment</h3>
			<button @click="close()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
				<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
					<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
				</svg>
			</button>
		</div>

		<form :action="actionUrl" method="POST" @submit.prevent="submitForm">
			@csrf
			@method('PUT')

			<div class="space-y-4">
				<div>
					<label for="edit_payment_amount" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Amount
						(৳)
						*</label>
					<input type="number" name="amount" id="edit_payment_amount" step="0.01" min="0" required
						x-model="amount"
						class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-800 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-blue-800">
				</div>

				<div>
					<label for="edit_payment_date" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Payment
						Date
						*</label>
					<input type="date" name="payment_date" id="edit_payment_date" required x-model="date"
						class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-800 dark:text-white/90 dark:focus:border-blue-800">
				</div>

				<div>
					<label for="edit_payment_status"
						class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Status</label>
					<select name="status" id="edit_payment_status" x-model="status"
						class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-800 dark:text-white/90 dark:focus:border-blue-800">
						<option value="completed">Completed</option>
						<option value="pending">Pending</option>
						<option value="cancelled">Cancelled</option>
					</select>
				</div>

				<div>
					<label for="edit_payment_description"
						class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Description</label>
					<textarea name="description" id="edit_payment_description" rows="2" placeholder="Payment description..."
					 x-model="description"
					 class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500/10 dark:border-gray-700 dark:bg-gray-800 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-blue-800"></textarea>
				</div>
			</div>

			<div class="mt-6 flex gap-4">
				<button type="submit"
					class="h-10.5 flex-1 inline-flex items-center justify-center rounded-lg border border-blue-500 bg-blue-500 px-4 py-2.5 text-sm font-medium text-white shadow-theme-xs hover:bg-blue-600">
					Update Payment
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
	function editPaymentModal() {
		return {
			show: false,
			paymentId: null,
			partnerId: null,
			amount: '',
			date: '',
			description: '',
			status: 'completed',
			actionUrl: '',

			open(paymentId, partnerId, amount, date, description, status) {
				this.paymentId = paymentId;
				this.partnerId = partnerId;
				this.amount = amount;
				this.date = date;
				this.description = description || '';
				this.status = status || 'completed';
				this.actionUrl = `/admin/partners/${partnerId}/payments/${paymentId}`;
				this.show = true;
			},

			close() {
				this.show = false;
				this.paymentId = null;
				this.partnerId = null;
				this.amount = '';
				this.date = '';
				this.description = '';
				this.status = 'completed';
			},

			submitForm() {
				this.$el.submit();
			}
		}
	}
</script>
