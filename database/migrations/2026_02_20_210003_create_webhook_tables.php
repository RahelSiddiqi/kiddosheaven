<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('webhook_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('site_id');
            $table->foreign('site_id')->references('id')->on('sites')->cascadeOnDelete();
            $table->string('url');                          // Endpoint URL to POST to
            $table->json('events');                         // ["order.created", "product.updated"]
            $table->string('secret', 64);                  // HMAC signing secret
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_triggered_at')->nullable();
            $table->timestamps();

            $table->index(['site_id', 'is_active']);
        });

        Schema::create('webhook_deliveries', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('webhook_subscription_id');
            $table->foreign('webhook_subscription_id')
                  ->references('id')->on('webhook_subscriptions')->cascadeOnDelete();
            $table->string('event');                       // "order.created"
            $table->json('payload');                       // event data
            $table->unsignedSmallInteger('http_status')->nullable();
            $table->text('response_body')->nullable();
            $table->unsignedTinyInteger('attempt')->default(1);
            $table->string('status')->default('pending');  // pending|delivered|failed
            $table->timestamp('delivered_at')->nullable();
            $table->timestamps();

            $table->index(['webhook_subscription_id', 'status']);
            $table->index('event');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('webhook_deliveries');
        Schema::dropIfExists('webhook_subscriptions');
    }
};
