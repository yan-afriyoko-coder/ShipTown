<?php

namespace App\Modules\Api2cart\src\Jobs;

use App\Models\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use romanzipp\QueueMonitor\Traits\IsMonitored;

class ResyncCheckFailedTaggedJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;
    use IsMonitored;

    /**ø
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Product::withAllTags(['CHECK FAILED'])
            ->each(function (Product $product) {
                activity()->withoutLogs(function () use ($product) {
                    $product->attachTag('Not Synced');
                });
            });
    }
}
