<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

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
            SET occurred_at = created_at
            WHERE occurred_at IS NULL
        ');
    }
};
