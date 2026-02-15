<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Create missing inventory and financial tables
     */
    public function up(): void
    {
        // Create inventory_movements table if not exists
        if (!Schema::hasTable('inventory_movements')) {
            Schema::create('inventory_movements', function (Blueprint $table) {
                $table->id();
                $table->uuid('movement_number')->unique();

                // Reference to product
                $table->foreignId('product_id')->constrained()->onDelete('cascade');
                $table->foreignId('batch_id')->nullable()->constrained('purchase_batches')->nullOnDelete();

                // Movement details
                $table->string('movement_type'); // purchase, sale, adjustment, return, transfer, damage, expire
                $table->integer('quantity');
                $table->decimal('unit_cost', 10, 2)->nullable(); // Cost at time of movement

                // Reference
                $table->string('reference_type')->nullable(); // order, purchase_order, adjustment
                $table->unsignedBigInteger('reference_id')->nullable();

                // User who made the movement
                $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();

                // Notes
                $table->text('notes')->nullable();

                // Timestamps
                $table->timestamps();

                // Indexes
                $table->index(['product_id', 'movement_type']);
                $table->index(['product_id', 'created_at']);
                $table->index(['batch_id']);
                $table->index(['movement_number']);
            });
        }

        // Create capital_accounts table if not exists
        if (!Schema::hasTable('capital_accounts')) {
            Schema::create('capital_accounts', function (Blueprint $table) {
                $table->id();
                $table->foreignId('partner_id')->constrained('users')->onDelete('cascade');

                // Account information
                $table->string('account_type'); // partner, investor
                $table->decimal('opening_balance', 12, 2)->default(0);
                $table->decimal('current_balance', 12, 2)->default(0);

                // Notes
                $table->text('notes')->nullable();

                $table->timestamps();

                // Indexes
                $table->index(['partner_id', 'account_type']);
            });
        }

        // Create financial_transactions table if not exists
        if (!Schema::hasTable('financial_transactions')) {
            Schema::create('financial_transactions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('capital_account_id')->constrained()->onDelete('cascade');

                // Transaction details
                $table->string('transaction_type'); // credit, debit
                $table->decimal('amount', 12, 2);
                $table->date('transaction_date');

                // Description
                $table->string('description')->nullable();

                // Reference
                $table->string('reference_type')->nullable();
                $table->unsignedBigInteger('reference_id')->nullable();

                $table->timestamps();

                // Indexes
                $table->index(['capital_account_id', 'transaction_type']);
                $table->index(['capital_account_id', 'transaction_date']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('financial_transactions');
        Schema::dropIfExists('capital_accounts');
        Schema::dropIfExists('inventory_movements');
    }
};
