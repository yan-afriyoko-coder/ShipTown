<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateModulesMagento2apiConnectionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('modules_magento2api_connections', function (Blueprint $table) {
            $table->id();
            $table->string('base_url');
            $table->integer('inventory_source_warehouse_tag_id')->nullable();
            $table->string('access_token_encrypted')->nullable();
            $table->timestamps();
        });
    }
}
