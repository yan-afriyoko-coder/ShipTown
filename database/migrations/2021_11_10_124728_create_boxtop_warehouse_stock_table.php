<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBoxtopWarehouseStockTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('modules_boxtop_warehouse_stock', function (Blueprint $table) {
            $table->id();
            $table->string('SKUGroup');
            $table->string('SKUNumber');
            $table->string('SKUName');
            $table->string('Attributes');
            $table->string('Warehouse');
            $table->float('WarehouseQuantity');
            $table->float('Allocated');
            $table->float('Available');
            $table->timestamps();
        });
    }
}
