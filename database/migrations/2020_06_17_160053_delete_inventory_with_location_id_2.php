<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DeleteInventoryWithLocationId2 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // incorrect RMSAPI import was creating inventory records with location_id = 2
        // we need to clean database
        \App\Models\Inventory::query()
            ->where('location_id', '=', 2)
            ->delete();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
