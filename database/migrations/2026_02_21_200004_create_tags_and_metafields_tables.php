<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── Tags ──────────────────────────────────────────────────
        Schema::create('tags', function (Blueprint $table) {
            $table->id();
            $table->foreignId('site_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('slug');
            $table->string('type')->default('general');  // product | order | customer | collection | general
            $table->string('color')->nullable();
            $table->timestamps();

            $table->unique(['site_id', 'slug', 'type']);
            $table->index(['site_id', 'type']);
        });

        // ── Polymorphic Taggables Pivot ────────────────────────────
        Schema::create('taggables', function (Blueprint $table) {
            $table->foreignId('tag_id')->constrained()->cascadeOnDelete();
            $table->morphs('taggable');   // taggable_type + taggable_id

            $table->primary(['tag_id', 'taggable_id', 'taggable_type']);
        });

        // ── Metafields ─────────────────────────────────────────────
        Schema::create('metafields', function (Blueprint $table) {
            $table->id();
            $table->foreignId('site_id')->constrained()->cascadeOnDelete();
            $table->morphs('owner');      // owner_type + owner_id
            $table->string('namespace');  // seo | specifications | shipping | custom
            $table->string('key');
            $table->text('value')->nullable();
            $table->string('value_type')->default('string');  // string | integer | float | boolean | json | date | url | color
            $table->text('description')->nullable();
            $table->timestamps();

            $table->unique(['owner_type', 'owner_id', 'namespace', 'key']);
            $table->index(['site_id', 'namespace']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('metafields');
        Schema::dropIfExists('taggables');
        Schema::dropIfExists('tags');
    }
};
