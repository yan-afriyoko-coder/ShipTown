<?php

namespace App\Jobs\Products;

use App\Helpers\Queries;
use App\Models\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

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
        $incorrectProductRecords = Queries::getProductsWithQuantityReservedErrorsQuery()
            ->limit(100) // for performance purposes
            ->get();

        $incorrectProductRecords->each(function ($errorRecord) {
            Product::find($errorRecord->product_id)
                ->log('Incorrect quantity reserved detected, recalculating')
                ->update([
                    'quantity_reserved' => $errorRecord->correct_inventory_quantity_reserved ?? 0
                ]);
        });

        info('Successfully ran RecalculateQuantityReservedJob', ['records_corrected_count' => $incorrectProductRecords->count()]);
    }
}
