<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('product_attribute_values', function (Blueprint $table) {
            if (!Schema::hasColumn('product_attribute_values', 'display_value')) {
                $table->string('display_value', 255)->nullable()->after('value');
            }
            if (!Schema::hasColumn('product_attribute_values', 'color_code')) {
                $table->string('color_code', 7)->nullable()->after('display_value');
            }
        });
    }

    public function down(): void
    {
        Schema::table('product_attribute_values', function (Blueprint $table) {
            if (Schema::hasColumn('product_attribute_values', 'display_value')) {
                $table->dropColumn('display_value');
            }
            if (Schema::hasColumn('product_attribute_values', 'color_code')) {
                $table->dropColumn('color_code');
            }
        });
    }
};
