<?php

use App\Modules\Api2cart\src\Models\Api2cartConnection;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddInventorySourceWarehouseTagToModulesApi2cartConnectionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('modules_api2cart_connections', function (Blueprint $table) {
            $table->string('inventory_source_warehouse_tag')->nullable()->after('url');
        });

        Api2cartConnection::query()->update(['inventory_source_warehouse_tag' => 'magento_stock']);
    }
}
