<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Create category-related pivot/support tables.
     */
    public function up(): void
    {
        // 1. Create pricing_templates if missing
        if (!Schema::hasTable('pricing_templates')) {
            Schema::create('pricing_templates', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->text('description')->nullable();
                $table->string('strategy_type'); // percentage_markup, fixed_markup, tiered, attribute_based
                $table->json('config');
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        // 2. Create category_pricing_template pivot if missing
        if (!Schema::hasTable('category_pricing_template')) {
            Schema::create('category_pricing_template', function (Blueprint $table) {
                $table->id();
                $table->foreignId('category_id')->constrained()->cascadeOnDelete();
                $table->foreignId('pricing_template_id')->constrained()->cascadeOnDelete();
                $table->timestamps();
                $table->unique(['category_id', 'pricing_template_id']);
            });
        }

        // 3. Create category_attributes pivot if missing
        if (!Schema::hasTable('category_attributes')) {
            Schema::create('category_attributes', function (Blueprint $table) {
                $table->id();
                $table->foreignId('category_id')->constrained()->cascadeOnDelete();
                $table->foreignId('product_attribute_id')->constrained()->cascadeOnDelete();
                $table->boolean('is_required')->default(false);
                $table->integer('sort_order')->default(0);
                $table->timestamps();
                $table->unique(['category_id', 'product_attribute_id']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('category_attributes');
        Schema::dropIfExists('category_pricing_template');
        Schema::dropIfExists('pricing_templates');
    }
};
