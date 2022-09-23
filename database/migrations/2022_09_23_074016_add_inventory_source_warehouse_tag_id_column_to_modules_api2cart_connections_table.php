<?php

use App\Modules\Api2cart\src\Models\Api2cartConnection;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Spatie\Tags\Tag;

class AddInventorySourceWarehouseTagIdColumnToModulesApi2cartConnectionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('modules_api2cart_connections', function (Blueprint $table) {
            $table->foreignId('inventory_source_warehouse_tag_id')->nullable()->after('inventory_source_warehouse_tag');
        });
    }
}
