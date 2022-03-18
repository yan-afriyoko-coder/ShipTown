<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddQuantityAvailableColumnToInventoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('inventory', function (Blueprint $table) {
            $table->decimal('quantity_available', 10)
                ->storedAs('quantity - quantity_reserved')
                ->comment('quantity - quantity_reserved')
                ->after('shelve_location');
        });
    }
}
