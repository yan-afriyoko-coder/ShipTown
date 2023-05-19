<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPriceFieldNameColumnToRmsapiConnectionTable extends Migration
{
    public function up()
    {
        Schema::table('rmsapi_connection', function (Blueprint $table) {
            $table->string('price_field_name')->default('price');
        });
    }
}
