<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('products')) {
            return;
        }

        // Null out orphaned category_id and brand_id so FK can be added
        if (Schema::hasTable('categories')) {
            DB::table('products')
                ->whereNotNull('category_id')
                ->whereNotIn('category_id', DB::table('categories')->select('id'))
                ->update(['category_id' => null]);
        }
        if (Schema::hasTable('brands')) {
            DB::table('products')
                ->whereNotNull('brand_id')
                ->whereNotIn('brand_id', DB::table('brands')->select('id'))
                ->update(['brand_id' => null]);
        }

        try {
            if (Schema::hasTable('categories')) {
                Schema::table('products', function (Blueprint $table) {
                    $table->foreign('category_id')->references('id')->on('categories')->nullOnDelete();
                });
            }
        } catch (\Throwable $e) {
            // FK may already exist
            if (strpos($e->getMessage(), 'Duplicate foreign key') === false && strpos($e->getMessage(), 'already exists') === false) {
                throw $e;
            }
        }

        try {
            if (Schema::hasTable('brands')) {
                Schema::table('products', function (Blueprint $table) {
                    $table->foreign('brand_id')->references('id')->on('brands')->nullOnDelete();
                });
            }
        } catch (\Throwable $e) {
            if (strpos($e->getMessage(), 'Duplicate foreign key') === false && strpos($e->getMessage(), 'already exists') === false) {
                throw $e;
            }
        }
    }

    public function down(): void
    {
        if (!Schema::hasTable('products')) {
            return;
        }

        try {
            Schema::table('products', function (Blueprint $table) {
                $table->dropForeign(['category_id']);
            });
        } catch (\Throwable $e) {
            // Ignore if FK does not exist
        }
        try {
            Schema::table('products', function (Blueprint $table) {
                $table->dropForeign(['brand_id']);
            });
        } catch (\Throwable $e) {
            // Ignore if FK does not exist
        }
    }
};
