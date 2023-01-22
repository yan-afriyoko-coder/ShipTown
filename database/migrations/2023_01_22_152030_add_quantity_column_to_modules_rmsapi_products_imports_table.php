<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddQuantityColumnToModulesRmsapiProductsImportsTable extends Migration
{
    public function up()
    {
        Schema::table('modules_rmsapi_products_imports', function (Blueprint $table) {
            $table->decimal('quantity_on_hand')->nullable();
            $table->decimal('quantity_on_order')->nullable();
            $table->decimal('quantity_available')->nullable();
            $table->decimal('quantity_committed')->nullable();
        });
    }
}
