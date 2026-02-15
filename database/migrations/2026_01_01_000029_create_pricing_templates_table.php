<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pricing_templates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('catalog_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->string('type'); // markup, margin, fixed
            $table->decimal('value', 12, 2);
            $table->text('description')->nullable();
            $table->boolean('is_global')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pricing_templates');
    }
};
