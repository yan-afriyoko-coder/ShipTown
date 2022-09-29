<?php

namespace App\Modules\StocktakeSuggestions\src\Jobs;

use App\Models\Inventory;
use App\Models\StocktakeSuggestion;
use App\Models\Warehouse;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use romanzipp\QueueMonitor\Traits\IsMonitored;

class RunAllJobsJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;


    public function handle(): bool
    {
        Warehouse::query()
            ->get('id')
            ->each(function (Warehouse $warehouse) {
                NegativeInventoryJob::dispatchNow($warehouse->getKey());
                NeverCountedJob::dispatchNow($warehouse->getKey());
                BarcodeScannedToQuantityFieldJob::dispatchNow($warehouse->getKey());
                BelowMinus50InventoryJob::dispatchNow($warehouse->getKey());
                NegativeWarehouseStockJob::dispatchNow($warehouse->getKey());
            });

        return true;
    }
}
