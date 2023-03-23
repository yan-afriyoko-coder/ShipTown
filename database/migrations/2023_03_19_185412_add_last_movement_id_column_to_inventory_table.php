<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLastMovementIdColumnToInventoryTable extends Migration
{
    public function up()
    {
        Schema::table('inventory', function (Blueprint $table) {
            $table->unsignedBigInteger('last_movement_id')->nullable()->after('last_counted_at');
        });
    }
}
