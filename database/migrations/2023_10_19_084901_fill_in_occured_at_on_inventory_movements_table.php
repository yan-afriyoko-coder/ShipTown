<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('
            UPDATE inventory_movements
            SET occurred_at = created_at
            WHERE occurred_at IS NULL
        ');

        DB::statement('
            UPDATE inventory_movements
            SET occurred_at = date_sub(occurred_at, INTERVAL 1 HOUR)
            WHERE occurred_at > created_at
        ');
    }
};
