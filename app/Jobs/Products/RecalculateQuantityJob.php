<?php

namespace App\Jobs\Products;

use App\Models\Inventory;
use App\Models\OrderProduct;
use App\Models\Product;
use DB;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class RecalculateQuantityJob implements ShouldQueue
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
            ->each(function ($errorRecord) {
                Product::find($errorRecord->product_id)
                    ->log('Incorrect quantity detected, recalculating')
                    ->update([
                        'quantity' => $errorRecord->correct_inventory_quantity ?? 0
                    ]);
            });
    }

    /**
     * @return Product|Builder
     */
    private function getProductsWithQuantityReservedErrorsQuery()
    {
        // select all products
        // left join inventory to get actual quantity
        // left join expected quantities sub query
        // select only where expected not matching actual
        return Product::query()
            ->select([
                'products.id',
                'products.id as product_id',
                'products.quantity as actual_product_quantity',
                'inventory_totals.total_quantity as correct_inventory_quantity',
            ])
            ->leftJoinSub(
                $this->getInventoryTotalsByProductIdQuery(),
                'inventory_totals',
                'inventory_totals.product_id',
                '=',
                'products.id'
            )
            ->where(
                DB::raw('IFNULL(' . DB::getTablePrefix() . 'products.quantity, 0)'),
                '!=',
                DB::raw('IFNULL(`' . DB::getTablePrefix() . 'inventory_totals`.`total_quantity`, 0)')
            );
    }

    /**
     * @return OrderProduct|Builder
     */
    private function getInventoryTotalsByProductIdQuery()
    {
        return Inventory::query()
            ->select([
                'product_id',
                DB::raw('sum(quantity) as total_quantity')
            ])
            ->groupBy(['product_id']);
    }
}
