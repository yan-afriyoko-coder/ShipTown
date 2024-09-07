<?php

namespace App\Modules\MagentoApi\src\Listeners;

use App\Events\Product\ProductTagDetachedEvent;
use App\Modules\MagentoApi\src\Models\MagentoProduct;

class ProductTagDetachedEventListener
{
    public function handle(ProductTagDetachedEvent $event): void
    {
        if ($event->tag() === 'Available Online') {
            MagentoProduct::query()
                ->where(['product_id' => $event->product->id])
                ->delete();
        }
    }
}
