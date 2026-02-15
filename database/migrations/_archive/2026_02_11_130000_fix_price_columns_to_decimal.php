<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Fix price type mismatch:
 *   order_items.unit_price & total_price were int unsigned → decimal(10,2)
 *   orders.total_amount was int unsigned → decimal(10,2)
 *   orders.subtotal was int unsigned → decimal(10,2)
 *
 * This ensures consistency with products.price (already decimal(10,2)).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->decimal('unit_price', 10, 2)->change();
            $table->decimal('total_price', 10, 2)->change();
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->decimal('total_amount', 10, 2)->change();
            $table->decimal('subtotal', 10, 2)->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->unsignedInteger('unit_price')->change();
            $table->unsignedInteger('total_price')->change();
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->unsignedInteger('total_amount')->change();
            $table->unsignedInteger('subtotal')->nullable()->change();
        });
    }
};
