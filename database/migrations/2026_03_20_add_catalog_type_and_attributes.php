<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add type field to catalogs if not exists
        if (!Schema::hasColumn('catalogs', 'type')) {
            Schema::table('catalogs', function (Blueprint $table) {
                $table->string('type')->default('general')->after('name')->nullable();
            });
        }

        // Add icon field if not exists
        if (!Schema::hasColumn('catalogs', 'icon')) {
            Schema::table('catalogs', function (Blueprint $table) {
                $table->string('icon')->nullable()->after('type');
            });
        }

        // Create catalog_attributes pivot table
        Schema::create('catalog_attributes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('catalog_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_attribute_id')->constrained()->onDelete('cascade');
            $table->integer('sort_order')->default(0);
            $table->boolean('is_required')->default(false);
            $table->timestamps();

            $table->unique(['catalog_id', 'product_attribute_id'], 'catalog_attr_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('catalog_attributes');

        Schema::table('catalogs', function (Blueprint $table) {
            $table->dropColumn(['type', 'icon']);
        });
    }
};
