<?php

namespace App\Listeners\Product\Updated;

use App\Events\Product\UpdatedEvent;
use App\Services\Api2cartService;
use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SyncApi2cartIfOutOfStockListener implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param UpdatedEvent $event
     * @return void
     * @throws Exception
     */
    public function handle(UpdatedEvent $event)
    {
        $product = $event->getProduct();

        if ($product->quantity_available > 0) {
            return;
        }

        Api2cartService::syncProduct($product);
    }
}
