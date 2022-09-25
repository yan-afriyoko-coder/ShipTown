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
                NegativeInventoryJob::dispatch($warehouse->getKey());
                NeverCountedJob::dispatch($warehouse->getKey());
                BarcodeScannedToQuantityFieldJob::dispatch($warehouse->getKey());
            });

        return true;
    }
}
