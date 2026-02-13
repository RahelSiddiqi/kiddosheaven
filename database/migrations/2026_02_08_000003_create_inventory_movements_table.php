<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Tracks all inventory movements (purchases, sales, adjustments, etc.)
     */
    public function up(): void
    {
        Schema::create('inventory_movements', function (Blueprint $table) {
            $table->id();
            $table->uuid('movement_number')->unique();

            // Reference to product
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->foreignId('batch_id')->nullable()->constrained('purchase_batches')->nullOnDelete();

            // Movement details
            $table->string('movement_type'); // purchase, sale, adjustment, return, transfer, damage, expire
            $table->integer('quantity');
            $table->decimal('unit_cost', 10, 2)->nullable(); // Cost at time of movement

            // Reference
            $table->string('reference_type')->nullable(); // order, purchase_order, adjustment
            $table->unsignedBigInteger('reference_id')->nullable();

            // User who made the movement
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();

            // Notes
            $table->text('notes')->nullable();

            // Timestamps
            $table->timestamps();

            // Indexes
            $table->index(['product_id', 'movement_type']);
            $table->index(['product_id', 'created_at']);
            $table->index(['batch_id']);
            $table->index(['movement_number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_movements');
    }
};
