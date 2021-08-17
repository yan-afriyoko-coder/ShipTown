<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersProductsShipmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders_products_shipments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_product_id');
            $table->decimal('quantity_shipped', 10);
            $table->foreignId('order_shipment_id')->nullable(true);
            $table->timestamps();
        });
    }
}
