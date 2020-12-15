<?php

namespace App\Jobs\Inventory;

use App\Models\Inventory;
use App\Helpers\Queries;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class RecalculateLocation999QuantityReservedJob implements ShouldQueue
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
        $recordsCorrected = Queries::getProductsWithIncorrectQuantityReservedQuery($this->locationId)
            // for performance purposes limit to 1000 products per job
            ->limit(1000)
            ->get()
            ->each(function ($errorRecord) {
                Inventory::query()->firstOrCreate([
                        'product_id' => $errorRecord->product_id,
                        'location_id' => $this->locationId,
                    ])
                    ->log('Incorrect quantity reserved detected')
                    ->update([
                        'quantity_reserved' => $errorRecord->quantity_reserved_expected ?? 0
                    ]);
            });

        info('Successfully ran RecalculateLocation999QuantityReservedJob', ['records_corrected_count' => $recordsCorrected->count()]);
    }
}
