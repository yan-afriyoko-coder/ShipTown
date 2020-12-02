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
            // for performance purposes limit to 1000 products per job
            ->limit(1000)
            ->dd()
            ->each(function ($errorRecord) {
                $product = Product::find($errorRecord->product_id);
                activity()->on($product)->log('Incorrect quantity reserved detected, recalculating');
                $product->update([
                    'quantity_reserved' => $product->expected_inventory_quantity_reserved ?? 0
                ]);
            });
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
                'inventory_totals.total_quantity_reserved as expected_inventory_quantity_reserved',
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
