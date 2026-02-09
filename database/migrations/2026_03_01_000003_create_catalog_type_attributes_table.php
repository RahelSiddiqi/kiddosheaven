<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Create pivot table for Catalog Type → Attributes (available pool)
        Schema::create('catalog_type_attributes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('catalog_type_id')->constrained('catalog_types')->onDelete('cascade');
            $table->foreignId('attribute_id')->constrained('product_attributes')->onDelete('cascade');
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->unique(['catalog_type_id', 'attribute_id'], 'catalog_type_attr_unique');
        });

        // Update catalog_attributes to reference the catalog's enabled attributes
        // This table links catalogs to the attributes they ENABLED from their type
        Schema::table('catalog_attributes', function (Blueprint $table) {
            // Add catalog_type_id for reference and validation
            $table->foreignId('catalog_type_id')->nullable()->after('catalog_id')->constrained('catalog_types')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('catalog_attributes', function (Blueprint $table) {
            $table->dropForeign(['catalog_type_id']);
            $table->dropColumn('catalog_type_id');
        });

        Schema::dropIfExists('catalog_type_attributes');
    }
};
