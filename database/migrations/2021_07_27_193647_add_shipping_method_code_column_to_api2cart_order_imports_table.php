<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddShippingMethodCodeColumnToApi2cartOrderImportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('api2cart_order_imports', function (Blueprint $table) {
            $table->string('shipping_method_name')->nullable(true)->after('api2cart_order_id');
            $table->string('shipping_method_code')->nullable(true)->after('shipping_method_name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('api2cart_order_imports', function (Blueprint $table) {
            //
        });
    }
}
