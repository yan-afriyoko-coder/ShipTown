<?php

use App\Models\Order;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddOrderIdForeignKeyOnOrderProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     * @throws Exception
     */
    public function up()
    {
        App\Models\OrderProduct::withTrashed()
            ->whereNotIn('order_id', Order::all('id'))
            ->forceDelete();

        Schema::table('order_products', function (Blueprint $table) {
            $table->foreign('order_id')
                ->on('orders')
                ->references('id')
                ->onDelete('cascade');
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
            $table->dropForeign(['order_id']);
            $table->dropIndex(config('database.connections.mysql.prefix').'order_products_order_id_foreign');
        });
    }
}
