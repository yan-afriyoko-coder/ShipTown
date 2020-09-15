<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddConnectionIdColumnToApi2cartOrderImportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('api2cart_order_imports', function (Blueprint $table) {
            $table->bigInteger('connection_id')
                ->unsigned()
                ->nullable(true)
                ->after('id');

            $table->foreign('connection_id')
                ->references('id')
                ->on('api2cart_connections')
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
        Schema::table('api2cart_order_imports', function (Blueprint $table) {
            $table->dropForeign(['connection_id']);
            $table->dropColumn('connection_id');
        });
    }
}
