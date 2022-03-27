<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateColumnsOnModulesApi2cartProductLinksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('modules_api2cart_product_links', function (Blueprint $table) {
            $table->foreignId('product_id')->change();
            $table->foreignId('api2cart_connection_id')->change();
        });

        Schema::table('modules_api2cart_product_links', function (Blueprint $table) {
            $table->foreign('product_id')
                ->references('id')
                ->on('products')
                ->onDelete('CASCADE');

            $table->foreign('api2cart_connection_id')
                ->references('id')
                ->on('modules_api2cart_connections')
                ->onDelete('CASCADE');
        });


    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
