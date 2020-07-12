<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSkuAndProductIdColumnToRmsapiProductImports extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('rmsapi_product_imports', function (Blueprint $table) {

            $table->unsignedBigInteger('product_id')
                ->after('when_processed')
                ->nullable(true);

            $table->string('sku')
                ->after('product_id')
                ->nullable(true);

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
            $table->dropColumn('product_id');
            $table->dropColumn('sku');
        });
    }
}
