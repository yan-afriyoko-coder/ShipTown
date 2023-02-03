<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSalesLastTimestampColumnToModulesRmsapiConnectionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('modules_rmsapi_connections', function (Blueprint $table) {
            $table->integer('sales_last_timestamp')->default(0)->after('shippings_last_timestamp');
        });
    }
}
