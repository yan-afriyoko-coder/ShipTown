<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameApi2cartModuleTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::rename('api2cart_connections', 'modules_api2cart_connections');
        Schema::rename('api2cart_order_imports', 'modules_api2cart_order_imports');
        Schema::rename('api2cart_product_links', 'modules_api2cart_product_links');
    }
}
