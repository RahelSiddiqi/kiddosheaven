<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add OTP columns to users table
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'otp_code')) {
                $table->string('otp_code', 6)->nullable()->after('phone');
            }
            if (! Schema::hasColumn('users', 'otp_expires_at')) {
                $table->timestamp('otp_expires_at')->nullable()->after('otp_code');
            }
            if (! Schema::hasColumn('users', 'phone_verified_at')) {
                $table->timestamp('phone_verified_at')->nullable()->after('otp_expires_at');
            }
            if (! Schema::hasColumn('users', 'role_id')) {
                $table->foreignId('role_id')->nullable()->constrained('roles')->nullOnDelete()->after('is_active');
            }
        });

        // Ensure the 'customer' role exists
        if (Schema::hasTable('roles')) {
            $customerRole = \DB::table('roles')->where('slug', 'customer')->first();
            if (! $customerRole) {
                \DB::table('roles')->insert([
                    'name'        => 'Customer',
                    'slug'        => 'customer',
                    'description' => 'Frontend store customer',
                    'is_default'  => false,
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ]);
            }
        }
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumnIfExists('otp_code');
            $table->dropColumnIfExists('otp_expires_at');
            $table->dropColumnIfExists('phone_verified_at');
        });
    }
};
