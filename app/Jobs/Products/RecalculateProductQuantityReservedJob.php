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
//        $incorrectProductRecords = Queries::getProductsWithQuantityReservedErrorsQuery()
//            ->limit($this->maxPerJob) // for performance purposes
//            ->get();

        $incorrectProductRecords = Product::where('quantity_reserved', '!=', 0)
            ->limit($this->maxPerJob) // for performance purposes
            ->get();

        $incorrectProductRecords->each(function ($incorrectProductRecord) {
            $incorrectProductRecord->log('Incorrect quantity reserved detected, recalculating');
            $incorrectProductRecord->quantity_reserved = 0;
            $incorrectProductRecord->save();
        });

        info('RecalculateProductQuantityReservedJob finished', [
            'records_corrected_count' => $incorrectProductRecords->count()
        ]);
    }
}
