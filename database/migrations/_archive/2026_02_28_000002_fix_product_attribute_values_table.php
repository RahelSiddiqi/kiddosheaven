<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Drop FK from variant_attributes first if table exists
        if (Schema::hasTable('variant_attributes')) {
            Schema::table('variant_attributes', function (Blueprint $table) {
                $table->dropForeign(['product_attribute_value_id']);
            });
        }

        // Drop the existing table and recreate with nullable product_id
        Schema::dropIfExists('product_attribute_values');

        Schema::create('product_attribute_values', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('product_attribute_id')->constrained()->onDelete('cascade');
            $table->string('value')->nullable();
            $table->decimal('price_modifier', 10, 2)->default(0);
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            // Allow null product_id for global attribute values
            $table->index(['product_id', 'product_attribute_id']);
        });

        // Recreate FK on variant_attributes
        if (Schema::hasTable('variant_attributes')) {
            Schema::table('variant_attributes', function (Blueprint $table) {
                $table->foreign('product_attribute_value_id')->references('id')->on('product_attribute_values')->onDelete('cascade');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('product_attribute_values');

        Schema::create('product_attribute_values', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_attribute_id')->constrained()->onDelete('cascade');
            $table->string('value')->nullable();
            $table->decimal('price_modifier', 10, 2)->default(0);
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->unique(['product_id', 'product_attribute_id']);
        });
    }
};
