<?php

use App\Models\Warehouse;
use App\Modules\Api2cart\src\Models\Api2cartConnection;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPricingSourceWarehouseIdColumnToApi2cartConnectionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (! Schema::hasColumn('modules_api2cart_connections', 'pricing_source_warehouse_id')) {
            Schema::table('modules_api2cart_connections', function (Blueprint $table) {
                $table->foreignId('pricing_source_warehouse_id')->nullable()->after('url');
            });
        }

        Api2cartConnection::all()->each(function (Api2cartConnection $conn) {
            $conn->pricing_source_warehouse_id = Warehouse::whereCode($conn->pricing_location_id)->first()->id;
        });
    }
}
