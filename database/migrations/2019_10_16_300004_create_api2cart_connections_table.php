<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateApi2cartConnectionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('api2cart_connections')) {
            return;
        }

        Schema::create('api2cart_connections', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('location_id')->default('0');
            $table->string('type')->default('');
            $table->string('url')->default('');
            $table->char('prefix', 10)->default('');
            $table->string('bridge_api_key')->nullable();
            $table->unsignedBigInteger('magento_store_id')->nullable();
            $table->string('magento_warehouse_id')->nullable();
            $table->unsignedBigInteger('inventory_location_id')->nullable();
            $table->unsignedBigInteger('pricing_location_id')->nullable();
            $table->dateTime('last_synced_modified_at')->default('2020-01-01 00:00:00');
            $table->timestamps();
        });
    }
}
