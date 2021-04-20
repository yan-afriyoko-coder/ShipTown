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
 * Class RecalculateProductQuantityJob
 * @package App\Jobs\Products
 */
class RecalculateProductQuantityJob implements ShouldQueue
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
        $incorrectProductRecords = Queries::getProductsWithQuantityErrorsQuery()
            ->limit($this->maxPerJob)
            ->get();

        $incorrectProductRecords->each(function ($errorRecord) {
            Product::find($errorRecord->product_id)
                ->log('Incorrect quantity detected, recalculating')
                ->update([
                    'quantity' => $errorRecord->correct_inventory_quantity ?? 0
                ]);
        });

        if ($incorrectProductRecords->count() === $this->maxPerJob) {
            self::dispatch();
        }

        info('RecalculateProductQuantityJob finished', [
            'records_corrected_count' => $incorrectProductRecords->count()
        ]);
    }
}
