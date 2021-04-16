<?php

namespace App\Services;

use App\Modules\Api2cart\src\Jobs\SyncProductJob;
use App\Models\Product;

class Api2cartService
{
    /**
     * @param Product $product
     */
    public static function dispatchSyncProductJob(Product $product): void
    {
        SyncProductJob::dispatch($product);
    }
}
