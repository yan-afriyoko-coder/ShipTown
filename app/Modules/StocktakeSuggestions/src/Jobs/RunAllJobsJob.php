<?php

namespace App\Modules\StocktakeSuggestions\src\Jobs;

use App\Models\Warehouse;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Bus;

class RunAllJobsJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;


    public function handle(): bool
    {
        BarcodeScannedToQuantityFieldJob::dispatch();
        NegativeInventoryJob::dispatch();
        NoMovementJob::dispatch();
        OutdatedCountsJob::dispatch();

        return true;
    }
}
