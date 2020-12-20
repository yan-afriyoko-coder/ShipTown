<?php

namespace App\Listeners\Product;

use App\Events\Product\UpdatedEvent;

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
     * @param  object  $event
     * @return void
     */
    public function handle(UpdatedEvent $event)
    {
        //
    }
}
