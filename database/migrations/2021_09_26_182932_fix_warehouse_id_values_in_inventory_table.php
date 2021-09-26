<?php

use App\Models\Inventory;
use App\Models\Warehouse;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class FixWarehouseIdValuesInInventoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        /** @var Inventory $distinctInventoryLocationIds */
        $distinctInventoryLocationIds = Inventory::select(['location_id'])->distinct()->get();

        $distinctInventoryLocationIds->each(function (Inventory $inventory) {
            $warehouse = Warehouse::query()->firstOrCreate([
                'code' => $inventory
            ],[
                'name' => ''
            ]);

            Inventory::withoutEvents(function () use ($warehouse) {
                Inventory::where(['location_id' => $warehouse->code])
                    ->update(['warehouse_id' => $warehouse->getKey()]);
            });
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
