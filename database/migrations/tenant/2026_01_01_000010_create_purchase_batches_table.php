<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('purchase_batches', function (Blueprint $table) {
            $table->id();
            $table->string('batch_number')->unique();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_variant_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('partner_id')->nullable()->constrained()->nullOnDelete();

            $table->decimal('unit_cost', 12, 2);
            $table->integer('quantity_received');
            $table->integer('remaining_quantity');
            $table->integer('quantity_reserved')->default(0);

            $table->string('status')->default('active'); // active, partially_sold, sold, expired, damaged
            $table->string('supplier')->nullable();
            $table->string('supplier_invoice_number')->nullable();

            $table->date('purchase_date');
            $table->date('manufacture_date')->nullable();
            $table->date('expiry_date')->nullable();

            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['product_id', 'status']);
            $table->index(['product_variant_id', 'status']);
            $table->index(['batch_number']);
            $table->index(['expiry_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchase_batches');
    }
};
