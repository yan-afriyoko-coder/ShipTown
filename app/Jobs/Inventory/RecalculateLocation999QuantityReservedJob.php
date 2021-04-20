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
        $incorrectInventoryRecords = Queries::getProductsWithIncorrectQuantityReservedQuery($this->locationId)
            // for performance purposes we will limit products per job
            ->limit($this->maxPerJob)
            ->get();

        $incorrectInventoryRecords->each(function ($errorRecord) {
            Inventory::query()->firstOrCreate([
                    'product_id' => $errorRecord->product_id,
                    'location_id' => $this->locationId,
                ])
                ->log('Incorrect quantity reserved detected')
                ->update([
                    'quantity_reserved' => $errorRecord->quantity_reserved_expected ?? 0
                ]);
        });

        info('RecalculateLocation999QuantityReservedJob finished', [
            'records_corrected_count' => $incorrectInventoryRecords->count()
        ]);
    }
}
