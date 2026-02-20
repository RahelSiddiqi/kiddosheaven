<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Coupon / discount tracking
            $table->foreignId('coupon_id')->nullable()->after('discount_amount')
                ->constrained('coupons')->nullOnDelete();
            $table->string('coupon_code')->nullable()->after('coupon_id');

            // Fulfillment
            $table->string('fulfillment_status')->default('unfulfilled')
                ->after('status');  // unfulfilled | partial | fulfilled | shipped | delivered
            $table->string('shipping_carrier')->nullable()->after('fulfillment_status');
            $table->string('shipping_tracking_number')->nullable()->after('shipping_carrier');
            $table->string('shipping_method_name')->nullable()->after('shipping_tracking_number');

            // Timestamps
            $table->timestamp('shipped_at')->nullable()->after('shipping_method_name');
            $table->timestamp('delivered_at')->nullable()->after('shipped_at');

            // Refund
            $table->decimal('refunded_amount', 12, 2)->default(0)->after('delivered_at');
            $table->string('refund_reason')->nullable()->after('refunded_amount');

            $table->index(['fulfillment_status']);
            $table->index(['coupon_id']);
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['coupon_id']);
            $table->dropColumn([
                'coupon_id', 'coupon_code',
                'fulfillment_status', 'shipping_carrier',
                'shipping_tracking_number', 'shipping_method_name',
                'shipped_at', 'delivered_at',
                'refunded_amount', 'refund_reason',
            ]);
        });
    }
};
