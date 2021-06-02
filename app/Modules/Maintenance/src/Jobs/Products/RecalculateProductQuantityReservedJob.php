<?php

namespace App\Modules\Maintenance\src\Jobs\Products;

use App\Models\Inventory;
use App\Models\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Class RecalculateProductQuantityReservedJob
 * @package App\Jobs\Products
 */
class RecalculateProductQuantityReservedJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var int
     */
    private int $locationId = 999;

    /**
     * @var int
     */
    private int $maxPerJob = 200;

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $incorrectInventoryRecords = Inventory::query()->select([
            'product_id',
            DB::raw('max('. DB::getTablePrefix() .'products.quantity_reserved) as current_quantity_reserved'),
            DB::raw('sum('. DB::getTablePrefix() .'inventory.quantity_reserved) as expected_quantity_reserved'),
        ])
            ->leftJoin('products', 'products.id', '=', 'inventory.product_id')
            ->groupBy('product_id')
            ->having(DB::raw('current_quantity_reserved'), '!=', DB::raw('expected_quantity_reserved'))
            ->get();

        $incorrectInventoryRecords->each(function ($inventoryRecord) {
            $product = Product::where(['id' => $inventoryRecord->product_id])->first();
            $product->recalculateQuantityTotals();
        });

        info('RecalculateProductQuantityReservedJob finished', [
            'records_corrected_count' => $incorrectInventoryRecords->count()
        ]);
    }
}
