<?php

namespace App\Services;

use App\Jobs\Modules\Api2cart\DisableProductJob;
use App\Jobs\Modules\Api2cart\SyncProductJob;
use App\Models\Product;
use Exception;

class Api2cartService
{
    /**
     * @param Product $product
     * @throws Exception
     */
    public static function disableProduct(Product $product): void
    {
        DisableProductJob::dispatch($product);
    }

    /**
     * @param Product $product
     */
    public static function dispatchSyncProductJob(Product $product): void
    {
        SyncProductJob::dispatch($product);
    }
}
