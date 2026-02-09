<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add type column to catalogs table if it doesn't exist
        if (!Schema::hasColumn('catalogs', 'type')) {
            Schema::table('catalogs', function (Blueprint $table) {
                $table->string('type')->nullable()->after('name');
            });
        }

        // Add foreign key to catalogs table
        Schema::table('catalogs', function (Blueprint $table) {
            $table->foreign('type')->references('slug')->on('catalog_types')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('catalogs', function (Blueprint $table) {
            $table->dropForeign(['type']);
        });

        Schema::table('catalogs', function (Blueprint $table) {
            $table->dropColumn('type');
        });
    }
};
