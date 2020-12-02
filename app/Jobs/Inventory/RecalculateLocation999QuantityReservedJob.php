<?php

namespace App\Jobs\Inventory;

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

class RecalculateLocation999QuantityReservedJob implements ShouldQueue
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
        $this->getQuantityReservedErrorsQuery()
            // for performance purposes limit to 1000 products per job
            ->limit(1000)
            ->each(function ($errorRecord) {
                activity()->on($errorRecord)->log('Incorrect quantity reserved detected');
                Inventory::query()->updateOrCreate([
                    'product_id' => $errorRecord->product_id,
                    'location_id' => $this->locationId,
                ], [
                    'quantity_reserved' => $errorRecord->quantity_reserved_expected ?? 0
                ]);
            });
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
                DB::raw($this->locationId . ' as location_id'),
                DB::raw('ceil(' . DB::getTablePrefix() . 'inventory.quantity_reserved) as quantity_reserved_actual'),
                'product_reserved_totals.total_quantity_to_ship as quantity_reserved_expected',
            ])
            ->leftJoin('inventory', function ($join) {
                $join->on('inventory.product_id', '=', 'products.id');
                $join->on('inventory.location_id', '=', DB::raw($this->locationId));
            })
            ->leftJoinSub(
                $this->getSumQuantityToShipByProductIdQuery(),
                'product_reserved_totals',
                'product_reserved_totals.product_id',
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
    private function getSumQuantityToShipByProductIdQuery()
    {
        return OrderProduct::query()
            ->whereStatusCodeIn(OrderStatus::getOpenStatuses())
            ->addSelect([
                'product_id',
                DB::raw('sum(quantity_to_ship) as total_quantity_to_ship'),
            ])
            ->groupBy(['product_id']);
    }
}
