<?php

namespace App\Services;

use App\Jobs\Modules\Api2cart\DisableProductJob;
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
}
