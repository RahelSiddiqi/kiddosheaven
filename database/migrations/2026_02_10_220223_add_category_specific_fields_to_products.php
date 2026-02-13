<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'features')) {
                $table->text('features')->nullable()->after('description');
            }
            if (!Schema::hasColumn('products', 'care_instructions')) {
                $table->text('care_instructions')->nullable()->after('features');
            }
            if (!Schema::hasColumn('products', 'ingredients')) {
                $table->text('ingredients')->nullable()->after('care_instructions');
            }
            if (!Schema::hasColumn('products', 'safety_warning')) {
                $table->text('safety_warning')->nullable()->after('ingredients');
            }
            if (!Schema::hasColumn('products', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('is_featured');
            }
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['features', 'care_instructions', 'ingredients', 'safety_warning', 'is_active']);
        });
    }
};
