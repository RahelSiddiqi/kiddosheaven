<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Guard against negative remaining_quantity and over-restoration
        DB::statement('ALTER TABLE purchase_batches ADD CONSTRAINT chk_remaining_qty_non_negative CHECK (remaining_quantity >= 0)');
        DB::statement('ALTER TABLE purchase_batches ADD CONSTRAINT chk_remaining_lte_received CHECK (remaining_quantity <= quantity_received)');
        DB::statement('ALTER TABLE purchase_batches ADD CONSTRAINT chk_reserved_non_negative CHECK (quantity_reserved >= 0)');
        DB::statement('ALTER TABLE purchase_batches ADD CONSTRAINT chk_reserved_lte_remaining CHECK (quantity_reserved <= remaining_quantity)');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE purchase_batches DROP CHECK chk_remaining_qty_non_negative');
        DB::statement('ALTER TABLE purchase_batches DROP CHECK chk_remaining_lte_received');
        DB::statement('ALTER TABLE purchase_batches DROP CHECK chk_reserved_non_negative');
        DB::statement('ALTER TABLE purchase_batches DROP CHECK chk_reserved_lte_remaining');
    }
};
