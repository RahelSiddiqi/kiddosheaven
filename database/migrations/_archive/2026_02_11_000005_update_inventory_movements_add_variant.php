<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Updates inventory_movements to link to variants and inventory items
     */
    public function up(): void
    {
        Schema::table('inventory_movements', function (Blueprint $table) {
            // Link to variant
            $table->foreignId('product_variant_id')
                ->nullable()
                ->after('product_id')
                ->constrained()
                ->onDelete('cascade');

            // Link to inventory item (which batch/location)
            $table->foreignId('inventory_item_id')
                ->nullable()
                ->after('batch_id')
                ->constrained()
                ->onDelete('set null');

            // Add indexes
            $table->index(['product_variant_id', 'movement_type'], 'variant_movement_idx');
            $table->index('inventory_item_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inventory_movements', function (Blueprint $table) {
            $table->dropForeign(['product_variant_id']);
            $table->dropForeign(['inventory_item_id']);
            $table->dropIndex('variant_movement_idx');
            $table->dropIndex(['inventory_item_id']);
            $table->dropColumn(['product_variant_id', 'inventory_item_id']);
        });
    }
};
