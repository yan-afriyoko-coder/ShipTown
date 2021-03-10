<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddWarehouseIdColumnToApi2cartConnectionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('api2cart_connections', function (Blueprint $table) {
            $table->string('warehouse_id')->nullable(true)->after('magento_store_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('api2cart_connections', function (Blueprint $table) {
            //
        });
    }
}
