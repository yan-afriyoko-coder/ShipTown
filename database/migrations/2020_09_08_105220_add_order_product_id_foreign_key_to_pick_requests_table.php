<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddOrderProductIdForeignKeyToPickRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pick_requests', function (Blueprint $table) {
            $table->foreign('order_product_id')
                ->references('id')
                ->on('order_products')
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
        Schema::table('pick_requests', function (Blueprint $table) {
            $table->dropForeign(['order_product_id']);
        });
    }
}
