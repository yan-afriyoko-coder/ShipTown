<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLastMovementAtColumnToInventoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasColumn('inventory', 'last_movement_at')) {
            return;
        }

        Schema::table('inventory', function (Blueprint $table) {
            $table->dateTime('last_movement_at')->nullable()->after('restock_level');
        });
    }
}
