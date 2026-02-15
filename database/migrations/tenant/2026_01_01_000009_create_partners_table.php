<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('partners', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type'); // supplier, affiliate, franchise, employee, service_provider, reseller
            $table->json('contact_info')->nullable();
            $table->json('bank_details')->nullable();
            $table->decimal('commission_rate', 5, 2)->nullable();
            $table->string('status')->default('active'); // active, inactive, suspended
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['type', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('partners');
    }
};
