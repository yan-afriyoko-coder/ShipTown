<?php

namespace App\Listeners\Product;

use App\Events\Product\ProductUpdatedEvent;
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
     * @param ProductUpdatedEvent $event
     * @return void
     * @throws Exception
     */
    public function handle(ProductUpdatedEvent $event)
    {
        $this->attachAutoTags($event);
    }

    /**
     * Handle the event.
     *
     * @param  ProductUpdatedEvent  $event
     * @return void
     */
    public function attachAutoTags(ProductUpdatedEvent $event)
    {
        $product = $event->getProduct();

        if ($product->isOutOfStock() && $product->doesNotHaveTags(['Out Of Stock'])) {
            $product->attachTag('Out Of Stock');
        }

        if ($product->isInStock() && $product->hasTags(['Out Of Stock'])) {
            $product->detachTag('Out Of Stock');
        }
    }
}
