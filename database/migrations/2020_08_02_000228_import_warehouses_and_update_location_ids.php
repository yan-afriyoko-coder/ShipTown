<?php

use App\Models\Inventory;
use App\Models\RmsapiConnection;
use App\Modules\Api2cart\src\Models\Api2cartConnection;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ImportWarehousesAndUpdateLocationIds extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        foreach(RmsapiConnection::all('location_id') as $connection) {
            $warehouse = \App\Models\Warehouse::query()->create([
                'code' => $connection->location_id,
                'name' => $connection->location_id
            ]);

            $connection->update([
               'location_id' => $warehouse->getKey()
            ]);

            Inventory::query()
                ->where('location_id','=',$connection->location_id)
                ->update([
                    'warehouse_id' => $warehouse->getKey()
                ]);
        };

        foreach (Api2cartConnection::all('location_id') as $connection) {
            $warehouse = \App\Models\Warehouse::query()->create([
                'code' => $connection->location_id,
                'name' => $connection->location_id
            ]);

            $connection->update([
                'location_id' => $warehouse->getKey()
            ]);

            Inventory::query()
                ->where('location_id','=',$connection->location_id)
                ->update([
                    'location_id' => $warehouse->getKey()
                ]);

        };
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
