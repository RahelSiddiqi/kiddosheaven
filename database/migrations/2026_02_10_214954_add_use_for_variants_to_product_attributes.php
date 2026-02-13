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
        Schema::table('product_attributes', function (Blueprint $table) {
            if (!Schema::hasColumn('product_attributes', 'use_for_variants')) {
                $table->boolean('use_for_variants')->default(false)->after('is_filterable');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_attributes', function (Blueprint $table) {
            if (Schema::hasColumn('product_attributes', 'use_for_variants')) {
                $table->dropColumn('use_for_variants');
            }
        });
    }
};
