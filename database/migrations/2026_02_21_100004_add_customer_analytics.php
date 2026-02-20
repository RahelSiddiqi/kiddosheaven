<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Enhance users table with customer analytics columns
        Schema::table('users', function (Blueprint $table) {
            $table->decimal('lifetime_value', 12, 2)->default(0)->after('remember_token');
            $table->unsignedInteger('total_orders')->default(0)->after('lifetime_value');
            $table->timestamp('last_order_at')->nullable()->after('total_orders');
            $table->string('customer_segment')->nullable()->after('last_order_at'); // vip, loyal, at_risk, new, lost
            $table->string('acquisition_source')->nullable()->after('customer_segment');
        });

        // Customer segments (admin-defined rules)
        Schema::create('customer_segments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('site_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->json('conditions');  // [{field, operator, value}]
            $table->string('condition_match')->default('all');  // all | any
            $table->unsignedInteger('customer_count')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['site_id', 'is_active']);
        });

        // Customer segment members (pivot)
        Schema::create('customer_segment_users', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_segment_id')->constrained('customer_segments')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamp('joined_at')->useCurrent();

            $table->unique(['customer_segment_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customer_segment_users');
        Schema::dropIfExists('customer_segments');

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'lifetime_value', 'total_orders', 'last_order_at',
                'customer_segment', 'acquisition_source',
            ]);
        });
    }
};
