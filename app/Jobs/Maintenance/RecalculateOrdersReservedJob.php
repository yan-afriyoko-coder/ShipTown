<?php

namespace App\Jobs\Maintenance;

use App\Models\Inventory;
use App\Models\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Spatie\Activitylog\Models\Activity;

class RecalculateOrdersReservedJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $locationId = 999;
        $prefix = config('database.connections.mysql.prefix');

        $productsWithIncorrectQuantity = DB::select('
            SELECT
              `'.$prefix.'products`.`id` as product_id,
              IFNULL(`'.$prefix.'inventory`.`quantity_reserved`, 0) as actual_quantity_reserved,
              (
                SELECT
                  SUM(`'.$prefix.'order_products`.`quantity_ordered` - quantity_shipped)
                FROM `'.$prefix.'order_products`
                WHERE `'.$prefix.'order_products`.`product_id` = `'.$prefix.'products`.`id`
              ) as expected_quantity_reserved

            FROM `'.$prefix.'products`
            LEFT JOIN `'.$prefix.'inventory` ON
              `'.$prefix.'inventory`.`product_id` = `'.$prefix.'products`.`id`
              AND `'.$prefix.'inventory`.`location_id` = '.$locationId.'

            WHERE
              (
                SELECT
                  SUM(quantity_ordered - quantity_shipped)
                FROM `'.$prefix.'order_products`
                WHERE `'.$prefix.'order_products`.`product_id` = `'.$prefix.'products`.`id`
              ) <> IFNULL(`'.$prefix.'inventory`.`quantity_reserved`, 0)
        ');

        foreach ($productsWithIncorrectQuantity as $record) {
            activity()->on(Product::find($record->product_id))
                ->log('Incorrect quantity on order detected');
            Inventory::query()->updateOrCreate([
                'product_id' => $record->product_id,
                'location_id' => $locationId,
            ], [
                'quantity_reserved' => $record->expected_quantity_reserved
            ]);
        }
    }
}
