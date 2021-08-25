<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ImportWarehousesFromInventoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $inventoryLocations = \App\Models\Inventory::select('location_id')->distinct()->get();

        $inventoryLocations->each(function (\App\Models\Inventory $inventory) {
            \App\Models\Warehouse::query()->firstOrCreate([
                'code' => $inventory->location_id
            ], [
                'name' => $inventory->location_id
            ]);
        });

        ray($inventoryLocations);
        dd(1);
        Schema::table('inventory', function (Blueprint $table) {
            //
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('inventory', function (Blueprint $table) {
            //
        });
    }
}
