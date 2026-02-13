<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Updates order_items to track variants and COGS
     */
    public function up(): void
    {
        // Check and add columns one by one
        if (!Schema::hasColumn('order_items', 'product_variant_id')) {
            Schema::table('order_items', function (Blueprint $table) {
                $table->foreignId('product_variant_id')
                    ->nullable()
                    ->after('product_id')
                    ->constrained()
                    ->onDelete('set null');
                $table->index('product_variant_id');
            });
        }

        if (!Schema::hasColumn('order_items', 'unit_cost')) {
            Schema::table('order_items', function (Blueprint $table) {
                $table->decimal('unit_cost', 10, 2)
                    ->nullable()
                    ->after('unit_price')
                    ->comment('Cost at time of sale for COGS');
            });
        }

        if (!Schema::hasColumn('order_items', 'purchase_batch_id')) {
            Schema::table('order_items', function (Blueprint $table) {
                $table->foreignId('purchase_batch_id')
                    ->nullable()
                    ->after('unit_cost')
                    ->constrained()
                    ->onDelete('set null');
                $table->index('purchase_batch_id');
            });
        }

        if (!Schema::hasColumn('order_items', 'profit')) {
            Schema::table('order_items', function (Blueprint $table) {
                $table->decimal('profit', 10, 2)
                    ->nullable()
                    ->after('total_price')
                    ->virtualAs('(unit_price - COALESCE(unit_cost, 0)) * quantity');
            });
        }
    }    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropForeign(['product_variant_id']);
            $table->dropForeign(['purchase_batch_id']);
            $table->dropIndex(['product_variant_id']);
            $table->dropIndex(['purchase_batch_id']);
            $table->dropColumn(['product_variant_id', 'unit_cost', 'purchase_batch_id', 'profit']);
        });
    }
};
