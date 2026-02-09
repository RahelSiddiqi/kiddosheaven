<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Tracks all financial transactions for complete audit trail
     */
    public function up(): void
    {
        Schema::create('financial_transactions', function (Blueprint $table) {
            $table->id();
            $table->uuid('transaction_number')->unique();

            // Transaction type
            $table->string('transaction_type'); // capital_in, capital_out, purchase, sale, expense, profit_distribution, adjustment
            $table->string('payment_method')->nullable(); // cash, bank, mobile_banking, check

            // Amounts
            $table->decimal('amount', 12, 2);
            $table->decimal('expense_amount', 12, 2)->default(0); // For calculating profit
            $table->decimal('cost_amount', 12, 2)->default(0); // COGS
            $table->decimal('revenue_amount', 12, 2)->default(0); // Revenue from sale

            // References
            $table->string('reference_type')->nullable(); // order, purchase_batch, investment, expense, partner, investor
            $table->unsignedBigInteger('reference_id')->nullable();

            // Related entities
            $table->foreignId('partner_id')->nullable()->constrained('partners')->nullOnDelete();
            $table->foreignId('investor_id')->nullable()->constrained('investors')->nullOnDelete();
            $table->foreignId('capital_account_id')->nullable()->constrained('capital_accounts')->nullOnDelete();

            // Notes
            $table->text('description')->nullable();
            $table->text('notes')->nullable();

            // Status
            $table->string('status')->default('completed'); // pending, completed, cancelled, reversed

            // Timestamps
            $table->dateTime('transaction_date')->useCurrent();
            $table->timestamps();

            // Indexes
            $table->index(['transaction_type', 'transaction_date']);
            $table->index(['partner_id', 'transaction_date']);
            $table->index(['investor_id', 'transaction_date']);
            $table->index(['transaction_number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('financial_transactions');
    }
};
