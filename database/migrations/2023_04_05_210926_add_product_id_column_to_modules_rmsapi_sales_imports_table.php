<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddProductIdColumnToModulesRmsapiSalesImportsTable extends Migration
{
    public function up(): void
    {
        Schema::table('modules_rmsapi_sales_imports', function (Blueprint $table) {
            $table->foreignId('product_id')->nullable()->after('connection_id');
        });
    }
}
