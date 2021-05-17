<?php

namespace App\Modules\Api2cart\src\Jobs;

use App\Models\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ResyncSyncErrorsTagged implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Product::withAllTags(['SYNC ERROR'])
            ->each(function (Product $product) {
                $product->attachTag('Not Synced');
                $product->detachTag('SYNC ERROR');
            });
    }
}
