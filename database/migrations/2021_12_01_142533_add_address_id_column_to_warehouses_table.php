<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAddressIdColumnToWarehousesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('warehouses', function (Blueprint $table) {
            $table->foreignId('address_id')->nullable()->after('name');

            $table->foreign('address_id')
                ->references('id')
                ->on('order_addresses')
                ->onDelete('CASCADE');
        });
    }
}
