<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Links purchase batches to investments for better financial tracking
     */
    public function up(): void
    {
        Schema::table('purchase_batches', function (Blueprint $table) {
            // Track which investment funded this purchase (optional - can be cash/other)
            $table->foreignId('investment_id')->nullable()->after('partner_id')
                ->constrained('investments')->nullOnDelete()
                ->comment('Investment that funded this purchase');

            // Track payment method for this batch
            $table->enum('payment_method', ['cash', 'investment', 'loan', 'partner_capital', 'other'])
                ->default('cash')->after('investment_id')
                ->comment('How this purchase was funded');

            // Add status field if it doesn't exist (some migrations might have it)
            if (!Schema::hasColumn('purchase_batches', 'status')) {
                $table->enum('status', ['active', 'partially_sold', 'sold', 'expired', 'damaged'])
                    ->default('active')->after('quantity_reserved');
            }

            // Add index for investment tracking
            $table->index('investment_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchase_batches', function (Blueprint $table) {
            $table->dropForeign(['investment_id']);
            $table->dropIndex(['investment_id']);
            $table->dropColumn(['investment_id', 'payment_method']);
            // Note: We don't drop 'status' column as it might have been added by another migration
        });
    }
};
