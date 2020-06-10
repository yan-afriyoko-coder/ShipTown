<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRmsapiProductImportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rmsapi_product_imports', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->dateTime('when_processed')->nullable();
            $table->json('raw_import');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('rmsapi_product_imports');
    }
}
