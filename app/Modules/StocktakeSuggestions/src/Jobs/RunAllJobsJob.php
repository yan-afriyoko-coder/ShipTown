<?php

namespace App\Modules\StocktakeSuggestions\src\Jobs;

use App\Models\Warehouse;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

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
                BarcodeScannedToQuantityFieldJob::dispatch($warehouse->getKey());
                NegativeWarehouseStockJob::dispatch($warehouse->getKey());
                OutdatedCountsJob::dispatch($warehouse->getKey());
            });

//        // for all warehouses
//        Warehouse::query()
//            ->whereIn('code', ['100', '99', 'MUL'])
//            ->get('id')
//            ->each(function (Warehouse $warehouse) {
//                NegativeInventoryJob::dispatch($warehouse->getKey());
//            });

        return true;
    }
}
