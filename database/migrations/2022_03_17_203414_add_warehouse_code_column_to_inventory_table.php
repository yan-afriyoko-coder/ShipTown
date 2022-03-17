<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddWarehouseCodeColumnToInventoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('inventory', function (Blueprint $table) {
            $table->string('warehouse_code')->default('')->after('location_id');
        });
    }
}
