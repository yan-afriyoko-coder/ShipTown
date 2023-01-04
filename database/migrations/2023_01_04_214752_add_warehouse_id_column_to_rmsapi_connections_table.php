<?php

use App\Models\Warehouse;
use App\Modules\Rmsapi\src\Models\RmsapiConnection;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddWarehouseIdColumnToRmsapiConnectionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('rmsapi_connections', function (Blueprint $table) {
            $table->foreignId('warehouse_id')->nullable()->after('id');

            $table->foreign('warehouse_id')
                ->references('id')
                ->on('warehouses');
        });

        RmsapiConnection::all()
            ->each(function (RmsapiConnection $conn) {
                $conn->warehouse_id = Warehouse::query()->where('code', $conn->location_id)->first()->getKey();
                $conn->save();
            });
    }
}
