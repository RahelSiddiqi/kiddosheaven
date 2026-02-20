<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── Payment Gateways ──────────────────────────────────────
        Schema::create('payment_gateways', function (Blueprint $table) {
            $table->id();
            $table->foreignId('site_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('code');          // bkash | nagad | stripe | cod | sslcommerz | paypal
            $table->boolean('is_active')->default(false);
            $table->boolean('is_test_mode')->default(true);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->text('config')->nullable();   // encrypted JSON: keys, merchant id etc.
            $table->string('icon')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();

            $table->unique(['site_id', 'code']);
            $table->index(['site_id', 'is_active']);
        });

        // ── Payment Transactions ──────────────────────────────────
        Schema::create('payment_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('site_id')->constrained()->cascadeOnDelete();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->string('gateway_code');
            $table->string('transaction_id')->nullable();
            $table->string('gateway_reference')->nullable();
            $table->string('type')->default('charge');        // charge | refund | partial_refund | void
            $table->string('status')->default('pending');     // pending | success | failed | cancelled | refunded
            $table->decimal('amount', 12, 2);
            $table->string('currency', 10)->default('BDT');
            $table->decimal('fee_amount', 12, 2)->default(0);
            $table->decimal('net_amount', 12, 2)->default(0);
            $table->string('payment_method_type')->nullable(); // card | mobile_banking | internet_banking
            $table->string('last_four', 4)->nullable();
            $table->string('card_brand')->nullable();
            $table->string('phone')->nullable();
            $table->json('raw_response')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('refunded_at')->nullable();
            $table->timestamps();

            $table->index(['site_id', 'status']);
            $table->index(['order_id', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_transactions');
        Schema::dropIfExists('payment_gateways');
    }
};
