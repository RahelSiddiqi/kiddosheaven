<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Updates purchase_batches to link to specific variants
     */
    public function up(): void
    {
        Schema::table('purchase_batches', function (Blueprint $table) {
            // Link batch to specific variant
            $table->foreignId('product_variant_id')
                ->nullable()
                ->after('product_id')
                ->constrained()
                ->onDelete('cascade');

            // Add index for faster queries
            $table->index(['product_variant_id', 'remaining_quantity'], 'variant_stock_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchase_batches', function (Blueprint $table) {
            $table->dropForeign(['product_variant_id']);
            $table->dropIndex('variant_stock_idx');
            $table->dropColumn('product_variant_id');
        });
    }
};
