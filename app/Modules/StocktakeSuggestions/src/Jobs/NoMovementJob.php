<?php

namespace App\Modules\StocktakeSuggestions\src\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class NoMovementJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    private int $warehouse_id;
    private string $reason;
    private int $points;

    public function __construct(int $warehouse_id)
    {
        $this->warehouse_id = $warehouse_id;

        $this->reason = 'quantity>100, price>5 and no movement for 7 days, scan product to verify barcode and stock';
        $this->points = 3;
    }

    public function handle()
    {
        DB::statement("
            INSERT INTO stocktake_suggestions (inventory_id, product_id, warehouse_id, points, reason, created_at, updated_at)
            SELECT inventory.id, inventory.product_id, inventory.warehouse_id, ? , ?, NOW(), NOW()
            FROM `inventory`

            LEFT JOIN products_prices
                ON products_prices.product_id = inventory.product_id
                AND products_prices.warehouse_id = inventory.warehouse_id

            WHERE inventory.warehouse_id = ?
            AND inventory.quantity != 0
            AND inventory.quantity_available > 100
            AND products_prices.price > 5
            AND DATEDIFF(now(), inventory.last_movement_at) > 7
            AND inventory.last_movement_at > inventory.last_counted_at
            AND NOT EXISTS (
                SELECT NULL
                FROM stocktake_suggestions
                WHERE stocktake_suggestions.inventory_id = inventory.id
                AND stocktake_suggestions.reason = ?
            )
        ", [$this->points, $this->reason, $this->warehouse_id, $this->reason]);
    }
}
