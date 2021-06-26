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
 * Class RecalculateProductQuantityJob.
 */
class RecalculateProductQuantityJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

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
            DB::raw('max('.DB::getTablePrefix().'products.quantity) as current_quantity'),
            DB::raw('sum('.DB::getTablePrefix().'inventory.quantity) as expected_quantity'),
        ])
            ->leftJoin('products', 'products.id', '=', 'inventory.product_id')
            ->groupBy('product_id')
            ->having(DB::raw('current_quantity'), '!=', DB::raw('expected_quantity'))
            ->get();

        $incorrectInventoryRecords->each(function ($inventoryRecord) {
            $product = Product::where(['id' => $inventoryRecord->product_id])->first();
            Log::warning('Incorrect product total quantity detected', [
                'sku' => $product->sku,
            ]);
            $product->recalculateQuantityTotals();
        });

        info('RecalculateProductQuantityJob finished', [
            'records_corrected_count' => $incorrectInventoryRecords->count(),
        ]);
    }
}
