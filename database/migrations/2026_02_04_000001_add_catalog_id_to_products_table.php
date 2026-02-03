<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->unsignedBigInteger('catalog_id')->nullable()->after('id');
            $table->foreign('catalog_id')->references('id')->on('catalogs')->onDelete('set null');
            $table->dropColumn('category');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('category')->index()->after('id');
            $table->dropForeign(['catalog_id']);
            $table->dropColumn('catalog_id');
        });
    }
};
