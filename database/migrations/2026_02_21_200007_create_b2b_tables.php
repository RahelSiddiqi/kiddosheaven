<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── Customer Groups ───────────────────────────────────────
        Schema::create('customer_groups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('site_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('slug');
            $table->text('description')->nullable();
            $table->decimal('discount_percent', 5, 2)->default(0);
            $table->boolean('is_default')->default(false);
            $table->boolean('is_active')->default(true);
            $table->decimal('min_order_amount', 12, 2)->nullable();
            $table->boolean('tax_exempt')->default(false);
            $table->boolean('can_view_prices')->default(true);
            $table->timestamps();

            $table->unique(['site_id', 'slug']);
            $table->index(['site_id', 'is_active']);
        });

        // ── Price Lists ───────────────────────────────────────────
        Schema::create('price_lists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('site_id')->constrained()->cascadeOnDelete();
            $table->foreignId('customer_group_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('currency', 10)->default('BDT');
            $table->boolean('is_active')->default(true);
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->timestamps();

            $table->index(['customer_group_id', 'is_active']);
        });

        // ── Price List Items ──────────────────────────────────────
        Schema::create('price_list_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('price_list_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_variant_id')->nullable()->constrained('product_variants')->nullOnDelete();
            $table->decimal('price', 12, 2);
            $table->decimal('compare_at_price', 12, 2)->nullable();
            $table->unsignedInteger('min_qty')->default(1);
            $table->timestamps();

            $table->index(['price_list_id', 'product_id']);
        });

        // ── Add customer_group_id to users ────────────────────────
        if (! Schema::hasColumn('users', 'customer_group_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->foreignId('customer_group_id')->nullable()->after('email')
                      ->constrained()->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('users', 'customer_group_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropForeign(['customer_group_id']);
                $table->dropColumn('customer_group_id');
            });
        }
        Schema::dropIfExists('price_list_items');
        Schema::dropIfExists('price_lists');
        Schema::dropIfExists('customer_groups');
    }
};
