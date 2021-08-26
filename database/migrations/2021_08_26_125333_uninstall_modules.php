<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UninstallModules extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \App\Modules\AutoStatusLayawayStorePickup\src\AutoStatusStorePickupServiceProvider::uninstallModule();
        \App\Modules\AutoStatusPackingWarehouse\src\AutoPackingWarehouseServiceProvider::uninstallModule();
        \App\Modules\AutoStatusSingleLineOrders\src\AutoStatusSingleLineOrdersServiceProvider::uninstallModule();
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
