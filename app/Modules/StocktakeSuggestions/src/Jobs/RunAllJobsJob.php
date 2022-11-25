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
        // for all warehouses
        Warehouse::query()
            ->get('id')
            ->each(function (Warehouse $warehouse) {
                BarcodeScannedToQuantityFieldJob::dispatchNow($warehouse->getKey());
                NegativeWarehouseStockJob::dispatchNow($warehouse->getKey());
            });

        // for all wa
        Warehouse::query()
            ->whereIn('code', ['100', '99', 'MUL'])
            ->get('id')
            ->each(function (Warehouse $warehouse) {
                NegativeInventoryJob::dispatchNow($warehouse->getKey());
            });

        return true;
    }
}
