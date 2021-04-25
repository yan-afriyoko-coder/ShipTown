<?php

namespace App\Jobs\Inventory;

use App\Models\Inventory;
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
        $incorrectInventoryRecords = Inventory::whereLocationId($this->locationId)
            ->where('quantity_reserved', '!=', 0)
            ->limit($this->maxPerJob)
            ->get();

        $incorrectInventoryRecords->each(function (Inventory $incorrectInventoryRecord) {
            $incorrectInventoryRecord->log('Incorrect quantity reserved detected');
            $incorrectInventoryRecord->quantity_reserved = 0;
            $incorrectInventoryRecord->save();
        });

        info('RecalculateLocation999QuantityReservedJob finished', [
            'records_corrected_count' => $incorrectInventoryRecords->count()
        ]);
    }
}
