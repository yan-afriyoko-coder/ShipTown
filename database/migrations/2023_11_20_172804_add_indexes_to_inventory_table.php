<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('inventory', function (Blueprint $table) {
            $table->index([DB::raw('last_sequence_number DESC')]);
            $table->index([DB::raw('last_movement_at DESC')]);
            $table->index([DB::raw('first_received_at DESC')]);
            $table->index([DB::raw('last_received_at DESC')]);
            $table->index([DB::raw('first_sold_at DESC')]);

            // Adds a descending index
//            DB::statement('ALTER TABLE `stuff` ADD INDEX `stuff_priority_index` (`priority` DESC)');
        });
    }
};
