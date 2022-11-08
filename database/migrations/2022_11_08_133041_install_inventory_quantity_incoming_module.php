<?php

use App\Modules\InventoryQuantityIncoming\src\InventoryQuantityIncomingServiceProvider;
use Illuminate\Database\Migrations\Migration;

class InstallInventoryQuantityIncomingModule extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        InventoryQuantityIncomingServiceProvider::installModule();
    }
}
