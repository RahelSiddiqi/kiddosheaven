<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Creates proper relational table for product variants instead of JSON storage
     */
    public function up(): void
    {
        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');

            // Identification
            $table->string('sku')->unique();
            $table->string('barcode')->nullable()->unique();
            $table->string('name')->nullable(); // e.g., "Red - Small - 1kg"

            // Pricing
            $table->decimal('price', 10, 2);
            $table->decimal('cost_price', 10, 2)->nullable(); // Current average cost
            $table->decimal('compare_at_price', 10, 2)->nullable(); // Original price for discounts

            // Inventory
            $table->integer('stock_quantity')->default(0);
            $table->integer('reserved_quantity')->default(0); // Reserved for pending orders
            $table->integer('low_stock_alert')->nullable();

            // Physical attributes
            $table->decimal('weight', 8, 2)->nullable();
            $table->decimal('length', 8, 2)->nullable();
            $table->decimal('width', 8, 2)->nullable();
            $table->decimal('height', 8, 2)->nullable();

            // Media
            $table->string('image')->nullable(); // Variant-specific image

            // Flags
            $table->boolean('is_default')->default(false); // Default variant to show
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);

            $table->timestamps();

            // Indexes
            $table->index(['product_id', 'is_active']);
            $table->index(['product_id', 'is_default']);
            $table->index(['sku']);
            $table->index(['stock_quantity']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_variants');
    }
};
