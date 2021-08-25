<?php

use App\Models\Inventory;
use App\Models\Warehouse;
use Illuminate\Database\Migrations\Migration;

class ImportWarehousesFromInventoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $inventoryLocations = Inventory::select('location_id')->distinct()->get();

        $inventoryLocations->each(function (Inventory $inventory) {
            Warehouse::query()->firstOrCreate([
                'code' => $inventory->location_id
            ], [
                'name' => $inventory->location_id
            ]);
        });
    }
}
