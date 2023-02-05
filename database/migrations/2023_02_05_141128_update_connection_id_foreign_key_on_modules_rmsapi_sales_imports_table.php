<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateConnectionIdForeignKeyOnModulesRmsapiSalesImportsTable extends Migration
{
    public function up()
    {
        Schema::table('modules_rmsapi_sales_imports', function (Blueprint $table) {
            $table->dropForeign('modules_rmsapi_sales_imports_connection_id_foreign');
            $table->foreign('connection_id')
                ->references('id')
                ->on('modules_rmsapi_connections')
                ->onDelete('cascade');
        });
    }
}
