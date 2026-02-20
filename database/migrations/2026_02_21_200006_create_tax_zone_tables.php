<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── Tax Zones ─────────────────────────────────────────────
        Schema::create('tax_zones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('site_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->json('countries')->nullable();    // ["BD", "IN"] — ISO 3166
            $table->boolean('is_default')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['site_id', 'is_active']);
        });

        // ── Tax Rates (per zone) ──────────────────────────────────
        Schema::create('tax_rates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tax_zone_id')->constrained()->cascadeOnDelete();
            $table->string('name');                // "VAT", "Service Tax"
            $table->decimal('rate', 8, 4);         // 15.0000 = 15%
            $table->string('tax_class')->default('standard');   // standard | reduced | zero | exempt
            $table->boolean('is_compound')->default(false);
            $table->boolean('is_active')->default(true);
            $table->string('applies_to')->default('all');       // all | physical | digital | shipping
            $table->timestamps();

            $table->index(['tax_zone_id', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tax_rates');
        Schema::dropIfExists('tax_zones');
    }
};
