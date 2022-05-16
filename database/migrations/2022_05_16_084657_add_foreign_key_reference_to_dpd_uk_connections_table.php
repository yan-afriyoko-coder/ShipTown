<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForeignKeyReferenceToDpdUkConnectionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('modules_dpduk_connections', function (Blueprint $table) {
            $table->foreignId('collection_address_id')->nullable()->change();
            $table->foreign('collection_address_id')
                ->references('id')
                ->on('orders_addresses')
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
        Schema::table('dpd_uk_connections', function (Blueprint $table) {
            //
        });
    }
}
