<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameRmsapiModuleTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::rename('rmsapi_connections', 'modules_rmsapi_connections');
        Schema::rename('rmsapi_product_imports', 'modules_rmsapi_products_imports');
    }
}
