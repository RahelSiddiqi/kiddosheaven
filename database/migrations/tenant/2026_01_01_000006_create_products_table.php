<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('catalog_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('brand_id')->nullable()->constrained()->nullOnDelete();

            $table->string('name');
            $table->string('slug')->unique();
            $table->string('sku')->unique();
            $table->string('barcode')->nullable()->unique();

            $table->string('product_type')->default('physical'); // physical, digital, service
            $table->string('delivery_type')->default('standard'); // standard, express, digital

            // Pricing
            $table->decimal('price', 12, 2);
            $table->decimal('cost_price', 12, 2)->nullable();
            $table->decimal('discount_price', 12, 2)->nullable();
            $table->string('discount_type')->nullable(); // percentage, fixed
            $table->decimal('vat_rate', 5, 2)->default(0);
            $table->decimal('wholesale_price', 12, 2)->nullable();

            // Inventory
            $table->integer('stock_quantity')->default(0);
            $table->integer('low_stock_alert')->default(10);
            $table->string('stock_status')->default('in_stock'); // in_stock, out_of_stock, backorder
            $table->integer('sold_count')->default(0);

            // Dimensions & Weight
            $table->decimal('weight', 10, 2)->nullable();
            $table->decimal('length', 10, 2)->nullable();
            $table->decimal('width', 10, 2)->nullable();
            $table->decimal('height', 10, 2)->nullable();

            // Content
            $table->text('short_description')->nullable();
            $table->longText('description')->nullable();
            $table->json('features')->nullable();
            $table->text('care_instructions')->nullable();
            $table->text('ingredients')->nullable();
            $table->text('safety_warning')->nullable();

            // Media
            $table->json('images')->nullable();
            $table->string('primary_image')->nullable();
            $table->string('video_url')->nullable();

            // Flags & Settings
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_active')->default(true);
            $table->boolean('halal_certified')->default(false);
            $table->boolean('organic_certified')->default(false);

            // SEO
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->json('tags')->nullable();

            // Additional
            $table->json('custom_attributes')->nullable();
            $table->string('return_policy')->nullable();
            $table->string('warranty')->nullable();
            $table->string('manufacturer')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['catalog_id', 'is_active']);
            $table->index(['category_id', 'is_active']);
            $table->index(['brand_id']);
            $table->index(['slug']);
            $table->index(['is_featured', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
