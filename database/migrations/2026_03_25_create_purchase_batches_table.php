<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Tracks purchase batches for FIFO/LIFO inventory management
     */
    public function up(): void
    {
        Schema::create('purchase_batches', function (Blueprint $table) {
            $table->id();
            $table->uuid('batch_number')->unique();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->foreignId('partner_id')->nullable()->constrained('users')->nullOnDelete();

            // Batch cost information
            $table->decimal('unit_cost', 10, 2);
            $table->integer('quantity_received');
            $table->integer('remaining_quantity')->default(0);
            $table->integer('quantity_reserved')->default(0);

            // Batch identification
            $table->string('supplier')->nullable();
            $table->string('supplier_invoice_number')->nullable();
            $table->date('purchase_date')->nullable();
            $table->date('manufacture_date')->nullable();
            $table->date('expiry_date')->nullable();

            // Status
            $table->text('notes')->nullable();

            $table->timestamps();

            // Indexes
            $table->index(['product_id', 'remaining_quantity']);
            $table->index(['product_id', 'purchase_date']);
            $table->index(['batch_number']);
            $table->index(['expiry_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_batches');
    }
};
