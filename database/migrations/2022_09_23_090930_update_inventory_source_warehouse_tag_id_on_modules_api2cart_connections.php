<?php

use App\Modules\Api2cart\src\Models\Api2cartConnection;
use Illuminate\Database\Migrations\Migration;
use Spatie\Tags\Tag;

class UpdateInventorySourceWarehouseTagIdOnModulesApi2cartConnections extends Migration
{
    public function up()
    {
        Api2cartConnection::all()
            ->each(function (Api2cartConnection $connection) {
                $tag = Tag::findFromString('magento_stock');

                $connection->inventory_source_warehouse_tag_id = $tag->getKey();
                $connection->save();
            });
    }
}
