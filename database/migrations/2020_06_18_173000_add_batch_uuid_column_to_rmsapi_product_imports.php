<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddBatchUuidColumnToRmsapiProductImports extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('rmsapi_product_imports', function (Blueprint $table) {
            $table->uuid('batch_uuid')
                ->after('connection_id')
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
            $table->dropColumn('batch_uuid');
        });
    }
}
