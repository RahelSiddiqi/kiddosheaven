<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * This table stores product-level attribute configuration.
     * Same attribute can be variant attribute for one product, normal attribute for another.
     */
    public function up(): void
    {
        Schema::create('product_attribute_configs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_attribute_id')->constrained()->onDelete('cascade');
            $table->enum('usage_type', ['variant', 'specification'])->default('specification');
            $table->boolean('is_visible')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            // One attribute can only be configured once per product
            $table->unique(['product_id', 'product_attribute_id'], 'product_attr_config_unique');

            // Indexes for faster queries
            $table->index(['product_id', 'usage_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_attribute_configs');
    }
};
