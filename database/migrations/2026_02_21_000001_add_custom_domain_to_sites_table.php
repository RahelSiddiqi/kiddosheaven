<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('sites', function (Blueprint $table) {
            if (!Schema::hasColumn('sites', 'custom_domain')) {
                $table->string('custom_domain')->nullable()->unique()->after('subdomain');
            }

            if (!Schema::hasColumn('sites', 'domain_verified_at')) {
                $table->timestamp('domain_verified_at')->nullable()->after('custom_domain');
            }

            if (!Schema::hasColumn('sites', 'domain_txt_record')) {
                $table->string('domain_txt_record')->nullable()->after('domain_verified_at');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sites', function (Blueprint $table) {
            if (Schema::hasColumn('sites', 'custom_domain')) {
                $table->dropColumn('custom_domain');
            }

            if (Schema::hasColumn('sites', 'domain_verified_at')) {
                $table->dropColumn('domain_verified_at');
            }

            if (Schema::hasColumn('sites', 'domain_txt_record')) {
                $table->dropColumn('domain_txt_record');
            }
        });
    }
};
