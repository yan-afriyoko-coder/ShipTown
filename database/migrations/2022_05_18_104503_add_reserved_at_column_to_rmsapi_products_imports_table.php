<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddReservedAtColumnToRmsapiProductsImportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('rmsapi_products_imports', function (Blueprint $table) {
            $table->timestamp('reserved_at')->nullable()->after('batch_uuid');
        });
    }
}
