<?php

namespace App\Modules\InventoryMovementsStatistics\src\Listeners;

use App\Events\Product\ProductCreatedEvent;
use Illuminate\Support\Facades\DB;

class ProductCreatedEventListener
{
    public function handle(ProductCreatedEvent $event)
    {
        DB::statement('
            INSERT INTO inventory_movements_statistics (
                inventory_id, product_id, warehouse_id, warehouse_code, updated_at, created_at
            )
            SELECT
                inventory.id as inventory_id,
                inventory.product_id,
                inventory.warehouse_id,
                inventory.warehouse_code,
                now() as updated_at,
                now() as created_at
            FROM `inventory`
            LEFT JOIN inventory_movements_statistics ON inventory.id = inventory_movements_statistics.inventory_id
            WHERE inventory.product_id = ?
              AND inventory_movements_statistics.inventory_id IS NULL
        ', [$event->product->id]);
    }
}
