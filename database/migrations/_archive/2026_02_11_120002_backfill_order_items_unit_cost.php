<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Backfill unit_cost on historical order_items that have NULL cost.
     * Uses the product's current average batch cost as a best-guess.
     */
    public function up(): void
    {
        // Skip backfill - no cost column in products table
        // This would need to be done manually or through a separate process
    }

    public function down(): void
    {
        // Cannot reliably reverse — the original NULL is the "true" state
    }
};
