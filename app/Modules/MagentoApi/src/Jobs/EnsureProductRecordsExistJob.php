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
            INSERT INTO modules_magento2api_products (connection_id, product_id, created_at, updated_at)
            SELECT
                modules_magento2api_connections.id,
                taggables.taggable_id,
                now(),
                now()
            FROM taggables
            LEFT JOIN modules_magento2api_connections ON 1=1
            WHERE taggables.tag_id = ?
            AND taggables.taggable_type = ?
            AND taggables.taggable_id NOT IN (
                SELECT product_id FROM modules_magento2api_products
            )
        ", [$tag->getKey(), 'App\\Models\\Product']);
    }
}
