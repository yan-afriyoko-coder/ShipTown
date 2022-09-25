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
        Warehouse::query()->get()->each(function (Warehouse $warehouse) {
            NegativeInventoryJob::dispatchNow($warehouse);
            NeverCountedJob::dispatchNow($warehouse);
            BarcodeScannedToQuantityFieldJob::dispatchNow($warehouse);
        });

        return true;
    }
}
