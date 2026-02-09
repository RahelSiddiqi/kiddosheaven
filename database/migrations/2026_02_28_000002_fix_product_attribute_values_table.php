<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
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
