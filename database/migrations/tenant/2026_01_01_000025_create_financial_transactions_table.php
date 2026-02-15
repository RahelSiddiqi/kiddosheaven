<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('financial_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_number')->unique();
            $table->string('transaction_type'); // capital_in, capital_out, purchase, sale, expense, profit_distribution, adjustment
            $table->string('payment_method')->nullable(); // cash, bank, mobile_banking, check

            $table->decimal('amount', 12, 2);
            $table->decimal('expense_amount', 12, 2)->default(0);
            $table->decimal('cost_amount', 12, 2)->default(0);
            $table->decimal('revenue_amount', 12, 2)->default(0);

            $table->string('reference_type')->nullable();
            $table->unsignedBigInteger('reference_id')->nullable();

            $table->foreignId('partner_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('investor_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('capital_account_id')->nullable()->constrained()->nullOnDelete();

            $table->text('description')->nullable();
            $table->text('notes')->nullable();

            $table->string('status')->default('completed'); // pending, completed, cancelled, reversed
            $table->dateTime('transaction_date')->useCurrent();

            $table->timestamps();

            $table->index(['transaction_type', 'transaction_date']);
            $table->index(['partner_id', 'transaction_date']);
            $table->index(['investor_id', 'transaction_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('financial_transactions');
    }
};
