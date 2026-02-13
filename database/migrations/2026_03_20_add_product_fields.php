<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add new fields to products table if they don't exist
        if (!Schema::hasColumn('products', 'product_type')) {
            Schema::table('products', function (Blueprint $table) {
                $table->string('product_type')->default('simple')->after('category_id');
                $table->string('delivery_type')->default('instant')->after('product_type');
                $table->string('barcode')->nullable()->after('delivery_type');
                $table->string('discount_type')->default('percentage')->after('discount_price');
                $table->decimal('vat_rate', 5, 2)->default(0)->after('discount_type');
                $table->decimal('wholesale_price', 10, 2)->nullable()->after('vat_rate');
                $table->integer('low_stock_alert')->default(5)->after('stock_quantity');
                $table->string('stock_status')->default('in_stock')->after('low_stock_alert');
                $table->text('variants')->nullable()->after('custom_attributes');
                $table->boolean('halal_certified')->default(false)->after('variants');
                $table->boolean('organic_certified')->default(false)->after('halal_certified');
                $table->text('return_policy')->nullable()->after('organic_certified');
                $table->string('warranty')->nullable()->after('return_policy');
                $table->string('manufacturer')->nullable()->after('warranty');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn([
                'product_type',
                'delivery_type',
                'barcode',
                'discount_type',
                'vat_rate',
                'wholesale_price',
                'low_stock_alert',
                'stock_status',
                'variants',
                'halal_certified',
                'organic_certified',
                'return_policy',
                'warranty',
                'manufacturer',
            ]);
        });
    }
};
