<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateModulesDpdukConnectionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('modules_dpduk_connections', function (Blueprint $table) {
            $table->id();
            $table->string('username');
            $table->string('password');
            $table->string('account_number');
            $table->foreignId('collection_address_id');
            $table->string('geo_session')->nullable();
            $table->timestamps();
        });
    }
}
