<?php

namespace App\Listeners\Product\Updated;

use App\Events\Product\UpdatedEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class AttachTagsListener implements ShouldQueue
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
     * @param  UpdatedEvent  $event
     * @return void
     */
    public function handle(UpdatedEvent $event)
    {
        $product = $event->getProduct();

        if ($product->withAllTags(['Available Online'])) {
            $product->attachTag('Not Synced');
        }

        if ($product->quantity_available <= 0) {
            $product->attachTag('Out Of Stock');
        }

        if ($product->quantity_available > 0) {
            $product->detachTag('Out Of Stock');
        }
    }
}
