<?php

namespace App\Modules\DataCollector\src\Jobs;

use App\Models\DataCollection;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Throwable;

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
            ->chunkById(1, function ($batch) {
                $batch->each(function (DataCollection $dataCollection) {
                    try {
                        /** @var Dispatchable $job */
                        $job = $dataCollection->currently_running_task;
                        $job::dispatch($dataCollection->getKey());
                    } catch (Exception $e) {
                        Log::error($e->getMessage());
                        report($e);
                    }
                });
            });

        Log::info('EnsureCorrectlyArchived job finished');
    }
}
