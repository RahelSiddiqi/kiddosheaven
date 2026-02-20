<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── In-App Notifications ──────────────────────────────────
        Schema::create('app_notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('site_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('type');        // order_placed | order_shipped | low_stock | new_review | etc.
            $table->string('title');
            $table->text('body')->nullable();
            $table->string('icon')->nullable();
            $table->string('url')->nullable();
            $table->json('data')->nullable();
            $table->timestamp('read_at')->nullable();
            $table->boolean('is_admin')->default(true);
            $table->timestamps();

            $table->index(['user_id', 'read_at']);
            $table->index(['site_id', 'is_admin', 'created_at']);
        });

        // ── URL Redirects ─────────────────────────────────────────
        Schema::create('redirects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('site_id')->constrained()->cascadeOnDelete();
            $table->string('from_path');
            $table->string('to_path');
            $table->unsignedSmallInteger('type')->default(301);   // 301 | 302
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('hit_count')->default(0);
            $table->text('note')->nullable();
            $table->timestamps();

            $table->unique(['site_id', 'from_path']);
            $table->index(['site_id', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('redirects');
        Schema::dropIfExists('app_notifications');
    }
};
