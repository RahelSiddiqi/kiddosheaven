<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('partner_calculations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('partner_id')->constrained()->onDelete('cascade');
            $table->decimal('total_sales', 12, 2)->default(0);
            $table->decimal('commission_amount', 10, 2)->default(0);
            $table->decimal('payment_amount', 10, 2)->default(0);
            $table->date('period_start');
            $table->date('period_end');
            $table->enum('status', ['pending', 'approved', 'paid'])->default('pending');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('partner_calculations');
    }
};
