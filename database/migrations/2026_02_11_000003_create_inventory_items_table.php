<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Links variants to purchase batches for inventory tracking
     * Enables FIFO/LIFO and tracks which batch supplies which variant
     */
    public function up(): void
    {
        Schema::create('inventory_items', function (Blueprint $table) {
            $table->id();

            // References
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_variant_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('purchase_batch_id')->constrained()->onDelete('cascade');

            // Quantities
            $table->integer('quantity_on_hand')->default(0);
            $table->integer('quantity_reserved')->default(0); // Reserved for orders
            $table->integer('quantity_available')->virtualAs('quantity_on_hand - quantity_reserved');

            // Location (for multi-warehouse)
            $table->string('location')->default('main'); // warehouse, store, etc.
            $table->string('bin_number')->nullable(); // Physical location in warehouse
            $table->string('aisle')->nullable();
            $table->string('shelf')->nullable();

            // Cost tracking
            $table->decimal('unit_cost', 10, 2); // Inherited from batch
            $table->decimal('total_value', 10, 2)->virtualAs('quantity_on_hand * unit_cost');

            $table->timestamps();

            // Indexes for fast lookups
            $table->index(['product_variant_id', 'location']);
            $table->index(['purchase_batch_id']);
            $table->index(['quantity_on_hand']);
            $table->index(['location', 'bin_number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_items');
    }
};
