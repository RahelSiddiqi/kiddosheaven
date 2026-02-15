<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('investors', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            $table->string('nid_or_passport')->nullable();

            $table->decimal('total_invested', 12, 2)->default(0);
            $table->decimal('current_value', 12, 2)->default(0);

            $table->string('type'); // individual, company, venture_capital, angel, institution
            $table->string('contact_person')->nullable();
            $table->string('contact_email')->nullable();
            $table->string('contact_phone')->nullable();

            $table->text('notes')->nullable();
            $table->string('status')->default('active'); // active, inactive

            $table->timestamps();
        });

        Schema::create('investments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('investor_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->decimal('amount', 12, 2);
            $table->string('type'); // inventory, equipment, property, marketing, research, expansion, working_capital, other
            $table->date('investment_date');
            $table->decimal('expected_return', 12, 2)->nullable();
            $table->decimal('actual_return', 12, 2)->nullable();
            $table->decimal('current_value', 12, 2)->nullable();
            $table->string('status')->default('active'); // active, completed, sold, pending
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['investor_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('investments');
        Schema::dropIfExists('investors');
    }
};
