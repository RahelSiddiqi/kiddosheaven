<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── Abandoned Carts ───────────────────────────────────────
        if (! Schema::hasTable('abandoned_carts')) {
            Schema::create('abandoned_carts', function (Blueprint $table) {
                $table->id();
                $table->foreignId('site_id')->constrained()->cascadeOnDelete();
                $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
                $table->string('email')->nullable()->index();
                $table->string('phone')->nullable();
                $table->string('token')->unique();
                $table->json('items');
                $table->decimal('subtotal', 12, 2)->default(0);
                $table->string('currency', 10)->default('BDT');
                $table->string('recovery_url')->nullable();
                $table->string('status')->default('active');       // active | recovered | expired
                $table->timestamp('reminder_1_sent_at')->nullable();
                $table->timestamp('reminder_2_sent_at')->nullable();
                $table->timestamp('reminder_3_sent_at')->nullable();
                $table->timestamp('recovered_at')->nullable();
                $table->foreignId('recovered_order_id')->nullable()->constrained('orders')->nullOnDelete();
                $table->timestamp('expires_at')->nullable();
                $table->timestamps();

                $table->index(['site_id', 'status']);
                $table->index(['site_id', 'created_at']);
            });
        }

        // ── Draft Orders ──────────────────────────────────────────
        if (! Schema::hasTable('draft_orders')) {
            Schema::create('draft_orders', function (Blueprint $table) {
                $table->id();
                $table->foreignId('site_id')->constrained()->cascadeOnDelete();
                $table->string('draft_number')->unique();
                $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
                $table->string('customer_name')->nullable();
                $table->string('customer_email')->nullable();
                $table->string('customer_phone')->nullable();
                $table->json('billing_address')->nullable();
                $table->json('shipping_address')->nullable();
                $table->json('line_items')->nullable();
                $table->decimal('subtotal', 12, 2)->default(0);
                $table->decimal('discount_amount', 12, 2)->default(0);
                $table->string('coupon_code')->nullable();
                $table->decimal('shipping_amount', 12, 2)->default(0);
                $table->decimal('tax_amount', 12, 2)->default(0);
                $table->decimal('total_amount', 12, 2)->default(0);
                $table->string('currency', 10)->default('BDT');
                $table->text('note')->nullable();
                $table->string('tags')->nullable();
                $table->string('payment_method')->default('cod');
                $table->string('status')->default('open');         // open | invoice_sent | completed | cancelled
                $table->string('invoice_url')->nullable();
                $table->timestamp('invoice_sent_at')->nullable();
                $table->foreignId('completed_order_id')->nullable()->constrained('orders')->nullOnDelete();
                $table->timestamp('expires_at')->nullable();
                $table->softDeletes();
                $table->timestamps();

                $table->index(['site_id', 'status']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('draft_orders');
        Schema::dropIfExists('abandoned_carts');
    }
};
