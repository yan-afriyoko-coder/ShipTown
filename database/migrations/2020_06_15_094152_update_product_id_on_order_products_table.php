<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateProductIdOnOrderProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // we can do that because nothing is in production yet
        Schema::table('order_products', function (Blueprint $table) {
            $table->dropColumn('product_id');
        });

        Schema::table('order_products', function (Blueprint $table) {
            $table->unsignedInteger('product_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('order_products', function (Blueprint $table) {
            $table->string('product_id')->change();
        });
    }
}
