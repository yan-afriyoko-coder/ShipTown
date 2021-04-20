<?php

namespace App\Jobs\Products;

use App\Helpers\Queries;
use App\Models\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

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
        $incorrectProductRecords = Queries::getProductsWithQuantityReservedErrorsQuery()
            ->limit($this->maxPerJob) // for performance purposes
            ->get();

        $incorrectProductRecords->each(function ($errorRecord) {
            Product::find($errorRecord->product_id)
                ->log('Incorrect quantity reserved detected, recalculating')
                ->update([
                    'quantity_reserved' => $errorRecord->correct_inventory_quantity_reserved ?? 0
                ]);
        });

        info('RecalculateProductQuantityReservedJob finished', [
            'records_corrected_count' => $incorrectProductRecords->count()
        ]);
    }
}
