<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('inventory_movements', 'is_first_movement')) {
            Schema::dropColumns('inventory_movements', ['is_first_movement']);
        }

        if (Schema::hasColumn('inventory_movements', 'next_movement_id')) {
            Schema::dropColumns('inventory_movements', ['next_movement_id']);
        }

        if (Schema::hasColumn('inventory_movements', 'previous_movement_id')) {
            Schema::dropColumns('inventory_movements', ['previous_movement_id']);
        }
    }
};
