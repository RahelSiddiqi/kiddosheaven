<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Adds automatic tracking of how investments are being spent
     */
    public function up(): void
    {
        Schema::table('investments', function (Blueprint $table) {
            // Track how much of the investment has been spent on purchases
            $table->decimal('spent_amount', 12, 2)->default(0)->after('amount')
                ->comment('Amount spent on purchases from this investment');

            // Virtual column: available_balance = amount - spent_amount
            // This will be calculated in the model as an accessor

            // Add index for investor queries
            if (!Schema::hasColumn('investments', 'investor_id')) {
                $table->foreignId('investor_id')->nullable()->after('id')
                    ->constrained('investors')->nullOnDelete();
            }

            // Ensure investment_date has proper index
            $table->index('investment_date');
            $table->index(['investor_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('investments', function (Blueprint $table) {
            $table->dropIndex(['investment_date']);
            $table->dropIndex(['investor_id', 'status']);
            $table->dropColumn('spent_amount');
        });
    }
};
