<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddOrderProductsIdToPicklistsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \App\Models\Picklist::query()->delete();

        Schema::table('picklists', function (Blueprint $table) {
            $table->bigInteger('order_products_id')
                ->unsigned()
                ->nullable(true)
                ->after('id');
        });

        Schema::table('picklists', function (Blueprint $table) {
            $table->foreign('order_products_id')
                ->on('order_products')
                ->references('id')
                ->onDelete('SET NULL');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('picklists', function (Blueprint $table) {
            $table->dropForeign(['order_products_id']);
            $table->dropIndex(config('database.connections.mysql.prefix').'picklists_order_products_id_foreign');

        });

        Schema::table('picklists', function (Blueprint $table) {
            $table->dropColumn('order_products_id');
        });
    }
}
