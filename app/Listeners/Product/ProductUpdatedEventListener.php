<?php

namespace App\Listeners\Product;

use App\Events\Product\UpdatedEvent;
use App\Jobs\Modules\Sns\PublishSnsNotificationJob;
use App\Services\Api2cartService;
use Exception;

class ProductUpdatedEventListener
{
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
        $this->publishSnsNotification($event);
        $this->ifOosSyncApi2cart($event);
    }

    /**
     * Handle the event.
     *
     * @param UpdatedEvent $event
     * @return void
     * @throws Exception
     */
    public function ifOosSyncApi2cart(UpdatedEvent $event)
    {
        $product = $event->getProduct();

        if ($product->quantity_available > 0) {
            return;
        }

        Api2cartService::syncProduct($product);
    }

    /**
     * Handle the event.
     *
     * @param UpdatedEvent $event
     * @return void
     */
    public function publishSnsNotification(UpdatedEvent $event)
    {
        PublishSnsNotificationJob::dispatch(
            'products_events',
            $event->getProduct()->toJson()
        );
    }
}
