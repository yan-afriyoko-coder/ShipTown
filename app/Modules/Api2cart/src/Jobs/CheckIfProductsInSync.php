<?php

namespace App\Modules\Api2cart\src\Jobs;

use App\Modules\Api2cart\src\Models\Api2cartProductLink;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * Class SyncProductJob.
 */
class CheckIfProductsInSync implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Api2cartProductLink::query()
            ->inRandomOrder()
            ->limit(100)
            ->each(function (Api2cartProductLink $productLink) {
                if ($productLink->isInSync()) {
                    return;
                }

                Log::warning('Api2cart product not in sync', ['sku' => $productLink->product->sku]);
            });
    }
}
