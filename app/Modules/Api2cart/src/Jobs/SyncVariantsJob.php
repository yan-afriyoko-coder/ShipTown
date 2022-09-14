<?php

namespace App\Modules\Api2cart\src\Jobs;

use App\Modules\Api2cart\src\Models\Api2cartProductLink;
use App\Modules\Api2cart\src\Models\Api2cartVariant;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use romanzipp\QueueMonitor\Traits\IsMonitored;

class SyncVariantsJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;
    use IsMonitored;

    /**
     * Execute the job.
     *
     * @throws Exception
     *
     * @return void
     */
    public function handle()
    {
        Api2cartVariant::query()
            ->where(['is_in_sync' => false])
            ->orderBy('updated_at', 'desc')
            ->chunk(10, function ($variants) {
                foreach ($variants as $variant) {
                    SyncVariant::dispatchNow($variant);
                }
            });
    }
}
