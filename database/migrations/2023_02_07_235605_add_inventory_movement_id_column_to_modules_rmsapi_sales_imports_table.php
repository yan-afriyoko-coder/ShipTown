<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddInventoryMovementIdColumnToModulesRmsapiSalesImportsTable extends Migration
{
    public function up()
    {
        Schema::table('modules_rmsapi_sales_imports', function (Blueprint $table) {
            $table->unsignedBigInteger('inventory_movement_id')->nullable()->after('id');

            $table->foreign('inventory_movement_id')
                ->references('id')
                ->on('inventory_movements')
                ->onDelete('SET NULL');
        });
    }
}
