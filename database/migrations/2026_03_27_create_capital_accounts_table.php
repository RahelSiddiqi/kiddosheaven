<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Tracks capital accounts for partners and investors
     */
    public function up(): void
    {
        Schema::create('capital_accounts', function (Blueprint $table) {
            $table->id();
            $table->uuid('account_number')->unique();

            // Account owner
            $table->string('owner_type'); // partner, investor
            $table->unsignedBigInteger('owner_id');

            // Account details
            $table->string('type'); // capital_contribution, purchase_contribution, profit_share, withdrawal
            $table->string('name');
            $table->decimal('balance', 12, 2)->default(0);
            $table->decimal('total_credited', 12, 2)->default(0);
            $table->decimal('total_debited', 12, 2)->default(0);

            // Profit sharing (if applicable)
            $table->decimal('profit_share_percentage', 5, 2)->nullable();
            $table->decimal('expense_share_percentage', 5, 2)->nullable();

            // Status
            $table->string('status')->default('active'); // active, closed, suspended

            $table->timestamps();

            // Indexes
            $table->index(['owner_type', 'owner_id']);
            $table->index(['status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('capital_accounts');
    }
};
