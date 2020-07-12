<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddForeingKeyToRmsapiProductImports extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('rmsapi_product_imports', function (Blueprint $table) {

            $table->foreign('product_id')
                ->on('products')
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
        Schema::table('rmsapi_product_imports', function (Blueprint $table) {
            $table->dropForeign(['product_id']);
            $table->dropIndex(config('database.connections.mysql.prefix').'rmsapi_product_imports_product_id_foreign');
        });
    }
}
