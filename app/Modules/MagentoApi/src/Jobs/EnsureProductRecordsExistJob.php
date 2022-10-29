<?php

namespace App\Modules\MagentoApi\src\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use romanzipp\QueueMonitor\Traits\IsMonitored;
use Spatie\Tags\Tag;

/**
 * Class SyncCheckFailedProductsJob.
 */
class EnsureProductRecordsExistJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;
    use IsMonitored;

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        /* @var Tag $tag */
        $tag = Tag::findFromString('Available Online');

        DB::statement("
            INSERT INTO modules_magento2api_products (product_id, created_at, updated_at)
            SELECT
                taggables.taggable_id,
                now(),
                now()
            FROM taggables
            WHERE taggables.tag_id = ?
            AND taggables.taggable_type = ?
            AND taggables.taggable_id NOT IN (
                SELECT product_id FROM modules_magento2api_products
            )
        ", [$tag->getKey(), 'App\\Models\\Product']);

//        $query = Product::withAllTags(['Available Online']);
//
//        $this->queueData([
//            'total_count' => $query->count(),
//        ]);
//
//        $totalCount = $query->count();
//
//        $chunkSize = 100;
//
//        $query->orderBy('quantity')
//            ->chunk($chunkSize, function (Collection $products) use ($totalCount, $chunkSize) {
//                $products->each(function (Product $product) {
//                    SyncProductStockJob::dispatch($product);
//                });
//            });
//
//        Log::info('Dispatched Sync MagentoApi Jobs', ['count' => $totalCount]);
    }
}
