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
            ->limit(1)
            ->get()
            ->each(function (DataCollection $dataCollection) {
                if (! $dataCollection->records()->where('quantity_scanned', '!=', 0)->exists()) {
                    $dataCollection->update(['currently_running_task' => null]);
                    return;
                }

                try {
                    /** @var Dispatchable $job */
                    $job = $dataCollection->currently_running_task;
                    $job::dispatch($dataCollection->getKey());
                } catch (Exception $e) {
                    Log::error($e->getMessage());
                    report($e);
                    return;
                }
            });

        Log::info('EnsureCorrectlyArchived job finished');
    }
}
