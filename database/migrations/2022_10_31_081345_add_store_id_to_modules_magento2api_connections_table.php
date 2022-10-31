<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStoreIdToModulesMagento2apiConnectionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('modules_magento2api_connections', function (Blueprint $table) {
            $table->integer('magento_store_id')->nullable()->after('base_url');
        });
    }
}
