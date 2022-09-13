<?php

namespace App\Modules\Api2cart\src\Jobs;

use App\Modules\Api2cart\src\Models\Api2cartProductLink;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class VerifyIfProductsInSyncJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * Execute the job.
     *
     * @throws Exception
     *
     * @return void
     */
    public function handle()
    {
        $collection = Api2cartProductLink::query()
            ->whereNull('is_in_sync')
            ->limit(100)
            ->get();

        $collection->each(function (Api2cartProductLink $link) {
            $link->update(['is_in_sync' => $link->isInSync()]);
        });
    }
}
