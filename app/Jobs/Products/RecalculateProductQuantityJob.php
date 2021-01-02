<?php

namespace App\Jobs\Products;

use App\Helpers\Queries;
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

class RecalculateProductQuantityJob implements ShouldQueue
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
        $incorrectProductRecords = Queries::getProductsWithQuantityErrorsQuery()
            // for performance purposes limit to 1000 products per job
            ->limit(1000)
            ->get();

        $incorrectProductRecords->each(function ($errorRecord) {
            Product::find($errorRecord->product_id)
                ->log('Incorrect quantity detected, recalculating')
                ->update([
                    'quantity' => $errorRecord->correct_inventory_quantity ?? 0
                ]);
        });

        info('Successfully ran RecalculateProductQuantityJob', ['records_corrected_count' => $incorrectProductRecords->count()]);
    }
}
