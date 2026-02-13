<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Add CHECK constraint on inventory_movements.movement_type
 * to restrict it to known values (the column is varchar(255) with no DB-level validation).
 */
return new class extends Migration
{
    public function up(): void
    {
        DB::statement("
            ALTER TABLE `inventory_movements`
            ADD CONSTRAINT `chk_movement_type`
            CHECK (`movement_type` IN ('purchase','sale','return','adjustment','transfer','damage','expire','expired'))
        ");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE `inventory_movements` DROP CHECK `chk_movement_type`");
    }
};
