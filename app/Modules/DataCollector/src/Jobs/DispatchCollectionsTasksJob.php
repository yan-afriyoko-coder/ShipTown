<?php

namespace App\Modules\DataCollector\src\Jobs;

use App\Models\DataCollection;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * Class SyncCheckFailedProductsJob.
 */
class DispatchCollectionsTasksJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function handle()
    {
        Log::debug('EnsureCorrectlyArchived job started');

        DataCollection::withTrashed()
            ->whereNotNull('currently_running_task')
            ->get()
            ->each(function (DataCollection $dataCollection) {
                $job = new $dataCollection->currently_running_task($dataCollection->getKey());
                dispatch($job);
            });

        Log::info('EnsureCorrectlyArchived job finished');
    }
}
