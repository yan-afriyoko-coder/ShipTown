<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPreviousMovementIdColumnToInventoryMovementsTable extends Migration
{
    public function up()
    {
        Schema::table('inventory_movements', function (Blueprint $table) {
            $table->unsignedBigInteger('previous_movement_id')->nullable()->unique()->after('updated_at');
        });
    }
}
