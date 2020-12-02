<?php

namespace App\Jobs\Products;

use App\Models\Inventory;
use App\Models\OrderProduct;
use App\Models\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Query\Builder;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class RecalculateQuantityReservedJob implements ShouldQueue
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
        $this->getProductsWithQuantityReservedErrorsQuery()
            ->limit(100) // for performance purposes
            ->each(function ($errorRecord) {
                Product::find($errorRecord->product_id)
                    ->log('Incorrect quantity reserved detected, recalculating')
                    ->update([
                        'quantity_reserved' => $errorRecord->correct_inventory_quantity_reserved ?? 0
                    ]);
            });
        info('Successfully ran RecalculateQuantityReservedJob');
    }

    /**
     * @return Product|\Illuminate\Database\Eloquent\Builder|Builder
     */
    private function getProductsWithQuantityReservedErrorsQuery()
    {
        // select all products
        // left join inventory to get actual quantity reserved
        // left join expected quantities reserved sub query
        // select only where expected not matching actual
        return Product::query()
            ->select([
                'products.id',
                'products.id as product_id',
                'products.quantity_reserved as actual_product_quantity_reserved',
                'inventory_totals.total_quantity_reserved as correct_inventory_quantity_reserved',
            ])
            ->leftJoinSub(
                $this->getInventoryTotalsByProductIdQuery(),
                'inventory_totals',
                'inventory_totals.product_id',
                '=',
                'products.id'
            )
            ->where(
                DB::raw('IFNULL(' . DB::getTablePrefix() . 'products.quantity_reserved, 0)'),
                '!=',
                DB::raw('IFNULL(`' . DB::getTablePrefix() . 'inventory_totals`.`total_quantity_reserved`, 0)')
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
                DB::raw('sum(quantity_reserved) as total_quantity_reserved')
            ])
            ->groupBy(['product_id']);
    }
}
