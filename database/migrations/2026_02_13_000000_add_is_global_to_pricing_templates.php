<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('pricing_templates', 'is_global')) {
            Schema::table('pricing_templates', function (Blueprint $table) {
                $table->boolean('is_global')->default(false)->after('config');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('pricing_templates', 'is_global')) {
            Schema::table('pricing_templates', function (Blueprint $table) {
                $table->dropColumn('is_global');
            });
        }
    }
};
