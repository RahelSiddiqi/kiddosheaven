<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_variant_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('purchase_batch_id')->constrained()->cascadeOnDelete();

            $table->integer('quantity_on_hand')->default(0);
            $table->integer('quantity_reserved')->default(0);

            $table->string('location')->nullable();
            $table->string('bin_number')->nullable();
            $table->string('aisle')->nullable();
            $table->string('shelf')->nullable();

            $table->decimal('unit_cost', 12, 2);
            $table->timestamps();

            $table->index(['product_id']);
            $table->index(['product_variant_id']);
            $table->index(['purchase_batch_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_items');
    }
};
