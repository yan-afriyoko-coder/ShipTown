<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPricingSourceWarehouseIdToModulesMagento2apiConnectionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('modules_magento2api_connections', function (Blueprint $table) {
            $table->integer('pricing_source_warehouse_id')->nullable()->after('inventory_source_warehouse_tag_id');
        });
    }
}
