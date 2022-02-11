<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameSingularTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::rename('order_addresses', 'orders_addresses');
        Schema::rename('order_comments', 'orders_comments');
        Schema::rename('order_products', 'orders_products');
        Schema::rename('order_shipments', 'orders_shipments');
        Schema::rename('order_statuses', 'orders_statuses');
        Schema::rename('product_prices', 'products_prices');
    }
}
