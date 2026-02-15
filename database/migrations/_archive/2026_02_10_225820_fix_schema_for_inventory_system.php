<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // ──────────────────────────────────────────────────────────
        // 1. Add 'status' column to purchase_batches
        // ──────────────────────────────────────────────────────────
        Schema::table('purchase_batches', function (Blueprint $table) {
            $table->string('status', 30)->default('active')->after('quantity_reserved');
        });

        // Backfill status based on current quantities
        DB::statement("UPDATE purchase_batches SET status = CASE
            WHEN remaining_quantity = 0 THEN 'sold'
            WHEN remaining_quantity < quantity_received THEN 'partially_sold'
            ELSE 'active'
        END");

        // Add index for status-based filtering
        Schema::table('purchase_batches', function (Blueprint $table) {
            $table->index('status', 'purchase_batches_status_index');
        });

        // ──────────────────────────────────────────────────────────
        // 2. Fix partner_id FK on purchase_batches (users → partners)
        // ──────────────────────────────────────────────────────────
        if (Schema::hasTable('partners')) {
            Schema::table('purchase_batches', function (Blueprint $table) {
                $table->dropForeign('purchase_batches_partner_id_foreign');
            });
            Schema::table('purchase_batches', function (Blueprint $table) {
                $table->foreign('partner_id')
                      ->references('id')
                      ->on('partners')
                      ->nullOnDelete();
            });
        }

        // ──────────────────────────────────────────────────────────
        // 3. Add unique constraint on product_attribute_values
        //    (prevent duplicate "Red" for the same attribute)
        // ──────────────────────────────────────────────────────────

        // First remove duplicates — keep the lowest ID for each (attribute_id, value) pair
        DB::statement("
            DELETE pav1 FROM product_attribute_values pav1
            INNER JOIN product_attribute_values pav2
            WHERE pav1.id > pav2.id
              AND pav1.product_attribute_id = pav2.product_attribute_id
              AND pav1.value = pav2.value
        ");

        Schema::table('product_attribute_values', function (Blueprint $table) {
            $table->unique(['product_attribute_id', 'value'], 'pav_attr_value_unique');
        });

        // ──────────────────────────────────────────────────────────
        // 4. Add index on order_items for profit-per-product queries
        // ──────────────────────────────────────────────────────────
        Schema::table('order_items', function (Blueprint $table) {
            $table->index(['product_id', 'created_at'], 'order_items_product_created_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropIndex('order_items_product_created_index');
        });

        Schema::table('product_attribute_values', function (Blueprint $table) {
            $table->dropUnique('pav_attr_value_unique');
        });

        Schema::table('purchase_batches', function (Blueprint $table) {
            $table->dropForeign(['partner_id']);
            $table->foreign('partner_id')
                  ->references('id')
                  ->on('users')
                  ->nullOnDelete();
        });

        Schema::table('purchase_batches', function (Blueprint $table) {
            $table->dropIndex('purchase_batches_status_index');
            $table->dropColumn('status');
        });
    }
};
