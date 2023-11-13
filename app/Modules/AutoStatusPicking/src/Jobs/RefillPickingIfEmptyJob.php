<?php

namespace App\Modules\AutoStatusPicking\src\Jobs;

use App\Abstracts\UniqueJob;
use App\Models\AutoStatusPickingConfiguration;
use App\Models\Order;

class RefillPickingIfEmptyJob extends UniqueJob
{
    private mixed $maxAllowed;

    public function __construct()
    {
        $this->maxAllowed = AutoStatusPickingConfiguration::query()->firstOrCreate([], [])->max_batch_size;
    }

    public function handle()
    {
        if (Order::where(['status_code' => 'picking'])->count() > 0) {
            return;
        }

        do {
            $countBefore = Order::query()->whereIn('status_code', ['picking'])->count();

            RefillOldOrdersToPickingJob::dispatchSync();
            RefillPickingMissingStockJob::dispatchSync();
            RefillPickingByOldestJob::dispatchSync();

            $countAfter = Order::query()->whereIn('status_code', ['picking'])->count();
        } while (($countAfter < $this->maxAllowed) and ($countBefore !== $countAfter));
    }
}
