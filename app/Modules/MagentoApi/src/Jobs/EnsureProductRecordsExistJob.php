<?php

namespace App\Modules\MagentoApi\src\Jobs;

use App\Abstracts\UniqueJob;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Spatie\Tags\Tag;

/**
 * Class SyncCheckFailedProductsJob.
 */
class EnsureProductRecordsExistJob extends UniqueJob
{
    public function handle(): void
    {
        $tag = Tag::findOrCreate(['name' => 'Available Online']);

        DB::statement('
            INSERT INTO modules_magento2api_products (connection_id, product_id, product_price_id, created_at, updated_at)
            SELECT
                modules_magento2api_connections.id,
                taggables.taggable_id,
                products_prices.id,
                now(),
                now()
            FROM taggables
            INNER JOIN modules_magento2api_connections ON 1=1
            INNER JOIN products_prices ON products_prices.product_id = taggables.taggable_id
            WHERE taggables.tag_id = ?
            AND taggables.taggable_type = ?
            AND taggables.taggable_id NOT IN (
                SELECT modules_magento2api_products.product_id FROM modules_magento2api_products
            )
        ', [$tag->first()->getKey(), Product::class]);
    }
}
