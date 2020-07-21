<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddOrderIdToPicklistsTable extends Migration
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
            $table->bigInteger('orders_id')
                ->unsigned()
                ->nullable(true)
                ->after('id');
        });

        Schema::table('picklists', function (Blueprint $table) {
            $table->foreign('orders_id')
                ->on('orders')
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
            $table->dropForeign(['orders_id']);
            $table->dropIndex(config('database.connections.mysql.prefix').'picklists_orders_id_foreign');

        });

        Schema::table('picklists', function (Blueprint $table) {
            $table->dropColumn('orders_id');
        });
    }
}
