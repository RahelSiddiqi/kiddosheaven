<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * The tables that should have site_id added.
     */
    protected array $tables = [
        'categories',
        'brands',
        'products',
        'orders',
        'coupons',
        'flash_sales',
        'addresses',
        'cms_pages',
    ];

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add site_id column to each table
        foreach ($this->tables as $tableName) {
            if (Schema::hasTable($tableName) && !Schema::hasColumn($tableName, 'site_id')) {
                Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                    $table->unsignedBigInteger('site_id')->nullable()->after('id');
                    $table->foreign('site_id')->references('id')->on('sites')->nullOnDelete();
                });
            }
        }

        // Backfill: assign all existing rows to site_id = 1
        foreach ($this->tables as $tableName) {
            if (Schema::hasTable($tableName)) {
                DB::table($tableName)->whereNull('site_id')->update(['site_id' => 1]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        foreach ($this->tables as $tableName) {
            if (Schema::hasTable($tableName) && Schema::hasColumn($tableName, 'site_id')) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->dropForeign(['site_id']);
                    $table->dropColumn('site_id');
                });
            }
        }
    }
};
