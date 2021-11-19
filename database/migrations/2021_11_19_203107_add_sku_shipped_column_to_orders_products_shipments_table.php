<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSkuShippedColumnToOrdersProductsShipmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders_products_shipments', function (Blueprint $table) {
            $table->string('sku_shipped')->default('')->after('id');
        });
    }
}
