<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Missing columns that controllers/services write to
            if (!Schema::hasColumn('orders', 'order_number')) {
                $table->string('order_number', 30)->nullable()->unique()->after('id');
            }
            if (!Schema::hasColumn('orders', 'subtotal')) {
                $table->unsignedInteger('subtotal')->default(0)->after('notes');
            }
            if (!Schema::hasColumn('orders', 'tax_amount')) {
                $table->unsignedInteger('tax_amount')->default(0)->after('subtotal');
            }
            if (!Schema::hasColumn('orders', 'status_notes')) {
                $table->text('status_notes')->nullable()->after('status');
            }
            if (!Schema::hasColumn('orders', 'cancellation_reason')) {
                $table->text('cancellation_reason')->nullable()->after('status_notes');
            }

            // Performance index for status queries
            $table->index('status');
            $table->index('created_at');
        });

        // Backfill order_number for existing orders
        $orders = \App\Models\Order::whereNull('order_number')->get();
        foreach ($orders as $order) {
            $order->update([
                'order_number' => 'ORD' . $order->created_at->format('Ymd') . str_pad($order->id, 4, '0', STR_PAD_LEFT),
            ]);
        }
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropIndex(['created_at']);
            $table->dropColumn(['order_number', 'subtotal', 'tax_amount', 'status_notes', 'cancellation_reason']);
        });
    }
};
