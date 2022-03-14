<?php

use App\Models\Warehouse;
use App\Modules\Api2cart\src\Models\Api2cartConnection;
use Illuminate\Database\Migrations\Migration;

class AddAllWarehousesToApi2cartConnectionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Api2cartConnection::all('id')
            ->each(function (Api2cartConnection $api2cartConnection) {
                $api2cartConnection->update([
                    'inventory_warehouse_ids' => Warehouse::select('id')->get()->pluck('id')
                ]);
            });
    }
}
