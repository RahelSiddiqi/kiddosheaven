<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->text('description')->nullable();

            $table->string('type'); // percentage, fixed, shipping
            $table->decimal('value', 12, 2);

            $table->decimal('min_order_amount', 12, 2)->nullable();
            $table->decimal('max_discount', 12, 2)->nullable();

            $table->integer('usage_limit')->nullable();
            $table->integer('used_count')->default(0);

            $table->dateTime('valid_from')->nullable();
            $table->dateTime('valid_until')->nullable();

            $table->string('status')->default('active'); // active, inactive, expired
            $table->boolean('is_general')->default(true);
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();

            $table->timestamps();

            $table->index(['code', 'status']);
            $table->index(['valid_from', 'valid_until']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('coupons');
    }
};
