<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DeleteInventoryWarehouseIdsColumnFromApi2cartConnecctionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('modules_api2cart_connections', function (Blueprint $table) {
            $table->dropColumn('inventory_warehouse_ids');
        });
    }
}
