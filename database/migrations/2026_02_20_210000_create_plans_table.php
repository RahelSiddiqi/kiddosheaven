<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->string('name');                        // "Starter", "Growth", "Enterprise"
            $table->string('slug')->unique();              // "starter", "growth", "enterprise"
            $table->string('stripe_price_id')->nullable(); // Stripe Price ID
            $table->string('billing_period')->default('monthly'); // monthly | yearly
            $table->unsignedInteger('price_cents')->default(0);   // price in cents
            $table->json('features')->nullable();           // {"max_products": 100, "max_staff": 3, ...}
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('plans');
    }
};
