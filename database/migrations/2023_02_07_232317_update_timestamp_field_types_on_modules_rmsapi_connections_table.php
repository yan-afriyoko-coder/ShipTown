<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateTimestampFieldTypesOnModulesRmsapiConnectionsTable extends Migration
{
    public function up()
    {
        Schema::table('modules_rmsapi_connections', function (Blueprint $table) {
            $table->unsignedBigInteger('sales_last_timestamp')->default(0)->change();
        });
    }
}
