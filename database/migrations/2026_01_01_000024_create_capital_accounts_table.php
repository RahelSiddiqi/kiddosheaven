<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('capital_accounts', function (Blueprint $table) {
            $table->id();
            $table->string('account_number')->unique();
            $table->string('owner_type'); // Partner, Investor
            $table->unsignedBigInteger('owner_id');
            $table->string('type'); // capital_contribution, purchase_contribution, profit_share, withdrawal
            $table->string('name');
            $table->decimal('balance', 12, 2)->default(0);
            $table->decimal('total_credited', 12, 2)->default(0);
            $table->decimal('total_debited', 12, 2)->default(0);
            $table->decimal('profit_share_percentage', 5, 2)->nullable();
            $table->decimal('expense_share_percentage', 5, 2)->nullable();
            $table->string('status')->default('active'); // active, closed, suspended
            $table->timestamps();

            $table->index(['owner_type', 'owner_id']);
            $table->index(['status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('capital_accounts');
    }
};
