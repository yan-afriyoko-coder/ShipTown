<?php

namespace App\Modules\Api2cart\src\Jobs;

use App\Models\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class DetachNotSyncedTagIfNotAvailableOnlineJob implements ShouldQueue
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
        $query = Product::query()
            ->withAllTags('Not Synced')
            ->withoutAllTags(['Available Online']);

        $query->chunk(50, function (Collection $chunk) {
            $chunk->each(function (Product $product) {
                $product->detachTag('Not Synced');
            });
        });
    }
}
