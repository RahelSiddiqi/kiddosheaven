<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Links variants to their attribute values (e.g., this variant is Red + Small + 1kg)
     */
    public function up(): void
    {
        Schema::create('variant_attributes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_variant_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_attribute_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_attribute_value_id')->constrained()->onDelete('cascade');

            $table->timestamps();

            // Each variant can only have one value per attribute
            // e.g., can't be both "Red" and "Blue" for Color attribute
            $table->unique(['product_variant_id', 'product_attribute_id'], 'variant_attr_unique');

            // Indexes for fast queries
            $table->index(['product_attribute_id', 'product_attribute_value_id'], 'attr_value_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('variant_attributes');
    }
};
