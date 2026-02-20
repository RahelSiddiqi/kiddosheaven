<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sites', function (Blueprint $table) {
            if (!Schema::hasColumn('sites', 'subdomain')) {
                $table->string('subdomain')->nullable()->unique()->after('domain');
            }
            if (!Schema::hasColumn('sites', 'plan_id')) {
                $table->unsignedBigInteger('plan_id')->nullable()->after('subdomain');
                $table->foreign('plan_id')->references('id')->on('plans')->nullOnDelete();
            }
            if (!Schema::hasColumn('sites', 'owner_id')) {
                $table->unsignedBigInteger('owner_id')->nullable()->after('plan_id');
                $table->foreign('owner_id')->references('id')->on('users')->nullOnDelete();
            }
            if (!Schema::hasColumn('sites', 'trial_ends_at')) {
                $table->timestamp('trial_ends_at')->nullable()->after('owner_id');
            }
            if (!Schema::hasColumn('sites', 'is_default')) {
                $table->boolean('is_default')->default(false)->after('is_active');
            }
        });
    }

    public function down(): void
    {
        Schema::table('sites', function (Blueprint $table) {
            $table->dropForeign(['plan_id']);
            $table->dropForeign(['owner_id']);
            $table->dropColumn(['subdomain', 'plan_id', 'owner_id', 'trial_ends_at', 'is_default']);
        });
    }
};
