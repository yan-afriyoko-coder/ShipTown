<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddQuantitiesColumnsToModulesRmsapiProductsImportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('modules_rmsapi_products_imports', function (Blueprint $table) {
            $table->dropColumn('quantity_on_hand');
            $table->dropColumn('quantity_on_order');
            $table->dropColumn('quantity_available');
            $table->dropColumn('quantity_committed');
        });

        Schema::table('modules_rmsapi_products_imports', function (Blueprint $table) {
            $table->decimal('quantity_on_hand')->nullable()->after('sku');
            $table->decimal('quantity_committed')->nullable()->after('quantity_on_hand');
            $table->decimal('quantity_available')->nullable()->after('quantity_committed');
            $table->decimal('quantity_on_order')->nullable()->after('quantity_available');
        });
    }
}
