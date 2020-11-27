<?php

namespace App\Jobs\Maintenance;

use App\Models\Inventory;
use App\Models\OrderProduct;
use App\Models\OrderStatus;
use App\Models\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Query\Builder;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class RecalculateProductQuantityReservedJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    /**
     * @var int
     */
    private $locationId;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->locationId = 999;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
//        $prefix = config('database.connections.mysql.prefix');
//
//        DB::statement('
//            UPDATE `'.$prefix.'products`
//
//            SET `'.$prefix.'products`.`quantity_reserved` = IFNULL(
//                (
//                    SELECT sum(`'.$prefix.'inventory`.`quantity_reserved`)
//                    FROM `'.$prefix.'inventory`
//                    WHERE `'.$prefix.'inventory`.`product_id` = `'.$prefix.'products`.`id`
//                        AND `'.$prefix.'inventory`.`deleted_at` IS NULL
//                ),
//                0
//            )
//        ');
//
//        info('Recalculated products total quantity reserved');
//
//        $this->getQuantityReservedErrorsQuery()
//            // for performance purposes limit to 1000 products per job
//            ->limit(1000)
//            ->each(function ($errorRecord) {
//                activity()->on($errorRecord)->log('Incorrect quantity reserved detected');
//                Inventory::query()->updateOrCreate([
//                    'product_id' => $errorRecord->product_id,
//                    'location_id' => $this->locationId,
//                ], [
//                    'quantity_reserved' => $errorRecord->quantity_reserved_expected ?? 0
//                ]);
//            });
    }

    /**
     * @return Product|\Illuminate\Database\Eloquent\Builder|Builder
     */
    private function getQuantityReservedErrorsQuery()
    {
        // select all products
        // left join specified $locationId inventory to get actual quantity reserved
        // left join expected quantities reserved sub query
        // select only where expected not matching actual
        return Product::query()
            ->select([
                'products.id as product_id',
                'products.quantity as product_quantity',
                'inventory_totals.total_quantity as inventory_quantity',
                'products.quantity_reserved as product_quantity_reserved',
                'inventory_totals.total_quantity_reserved as inventory_quantity_reserved',
            ])
            ->leftJoinSub(
                $this->getInventoryTotalsByProductIdQuery(),
                'inventory_totals',
                'inventory_totals.product_id',
                '=',
                'products.id'
            )
            ->where(
                DB::raw('IFNULL(' . DB::getTablePrefix() . 'inventory.quantity_reserved, 0)'),
                '!=',
                DB::raw('IFNULL(`' . DB::getTablePrefix() . 'product_reserved_totals`.`total_quantity_to_ship`, 0)')
            );
    }

    /**
     * @return OrderProduct|\Illuminate\Database\Eloquent\Builder|Builder
     */
    private function getInventoryTotalsByProductIdQuery()
    {
        return Inventory::query()
            ->select([
                'product_id',
                DB::raw('sum(quantity) as total_quantity'),
                DB::raw('sum(quantity_reserved) as total_quantity_reserved')
            ]);
    }
}
