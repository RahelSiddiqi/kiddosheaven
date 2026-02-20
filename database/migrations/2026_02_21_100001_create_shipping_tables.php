<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Shipping zones (geographical targeting)
        Schema::create('shipping_zones', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->json('countries')->nullable();   // ['BD', 'IN', 'PK']
            $table->json('regions')->nullable();     // State/division codes
            $table->boolean('is_rest_of_world')->default(false);
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->index(['is_active']);
        });

        // Shipping rates per zone
        Schema::create('shipping_rates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shipping_zone_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('type')->default('flat'); // flat | calculated | free | weight_based | price_based
            $table->decimal('price', 12, 2)->default(0);
            $table->decimal('free_above_amount', 12, 2)->nullable(); // free shipping above this order amount
            $table->decimal('min_order_amount', 12, 2)->nullable();
            $table->decimal('max_order_amount', 12, 2)->nullable();
            $table->decimal('min_weight', 10, 3)->nullable(); // kg
            $table->decimal('max_weight', 10, 3)->nullable();
            $table->string('carrier')->nullable();             // dhl, fedex, ups, local
            $table->integer('estimated_days_min')->nullable();
            $table->integer('estimated_days_max')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['shipping_zone_id', 'is_active']);
        });

        // Shipments — one order can have multiple shipments (partial fulfillment)
        Schema::create('shipments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('shipping_rate_id')->nullable()->constrained()->nullOnDelete();
            $table->string('tracking_number')->nullable();
            $table->string('carrier')->nullable();  // dhl, fedex, ups, steadfast, pathao, etc.
            $table->string('carrier_service')->nullable();
            $table->string('status')->default('pending');
            // pending | processing | picked_up | in_transit | out_for_delivery | delivered | failed | returned
            $table->decimal('weight', 10, 3)->nullable();
            $table->decimal('shipping_cost', 12, 2)->default(0);
            $table->string('tracking_url')->nullable();
            $table->text('notes')->nullable();
            $table->timestamp('shipped_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamp('estimated_delivery')->nullable();
            $table->timestamps();

            $table->index(['order_id', 'status']);
            $table->index(['tracking_number']);
        });

        // Shipment items — which order items are in this shipment
        Schema::create('shipment_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shipment_id')->constrained()->cascadeOnDelete();
            $table->foreignId('order_item_id')->constrained()->cascadeOnDelete();
            $table->integer('quantity');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shipment_items');
        Schema::dropIfExists('shipments');
        Schema::dropIfExists('shipping_rates');
        Schema::dropIfExists('shipping_zones');
    }
};
