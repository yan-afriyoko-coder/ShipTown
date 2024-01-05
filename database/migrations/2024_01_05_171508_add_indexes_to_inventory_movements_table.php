<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Adds a descending index
        DB::statement('ALTER TABLE `inventory_movements` ADD INDEX `occurred_at_sequence_number_index` (`occurred_at` DESC, `sequence_number` DESC)');
    }
};
