<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateModulesRmsapiSalesImportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('modules_rmsapi_sales_imports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('connection_id')->constrained('modules_rmsapi_connections');
            $table->dateTime('reserved_at')->nullable();
            $table->dateTime('processed_at')->nullable();
            $table->json('raw_import');
            $table->timestamps();
        });
    }
}
