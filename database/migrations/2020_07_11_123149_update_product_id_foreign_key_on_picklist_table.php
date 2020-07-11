<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateProductIdForeignKeyOnPicklistTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('picklists', function (Blueprint $table) {
            $table->dropForeign(['product_id']);
            $table->dropIndex(config('database.connections.mysql.prefix').'picklists_product_id_foreign');
        });

        Schema::table('picklists', function (Blueprint $table) {

            $table->unsignedBigInteger('product_id')
                ->nullable(true)
                ->change();

            $table->foreign('product_id')
                ->references('id')
                ->on('products')
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
        try {

            Schema::table('picklists', function (Blueprint $table) {
                $table->dropForeign(['product_id']);
                $table->dropIndex(config('database.connections.mysql.prefix').'picklists_product_id_foreign');
            });

        } catch (Exception $exception) {
            // we let it continue and just try to complete further actions
            // so we don't have to handle this error
            // run without try catch during debugging
        }


        Schema::table('picklists', function (Blueprint $table) {
            $table->unsignedBigInteger('product_id')
                ->nullable(true)
                ->change();

            $table->foreign('product_id')
                ->references('id')
                ->on('products')
                ->onDelete('cascade');
        });
    }
}
