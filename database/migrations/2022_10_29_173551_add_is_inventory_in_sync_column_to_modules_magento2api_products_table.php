<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsInventoryInSyncColumnToModulesMagento2apiProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('modules_magento2api_products', function (Blueprint $table) {
            $table->boolean('is_inventory_in_sync')->nullable()->after('product_id');
        });
    }
}
