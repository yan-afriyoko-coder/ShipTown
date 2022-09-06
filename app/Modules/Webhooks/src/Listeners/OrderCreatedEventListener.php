<?php

namespace App\Modules\Webhooks\src\Listeners;

use App\Events\Order\OrderCreatedEvent;
use App\Models\Order;
use App\Modules\Webhooks\src\Models\PendingWebhook;

/**
 * Class AttachAwaitingPublishTagListener.
 */
class OrderCreatedEventListener
{
    /**
     * Handle the event.
     *
     * @param OrderCreatedEvent $event
     *
     * @return void
     */
    public function handle(OrderCreatedEvent $event)
    {
//        PendingWebhook::query()->firstOrCreate([
//            'model_class' => Order::class,
//            'model_id' => $event->order->getKey(),
//            'reserved_at' => null,
//            'published_at' => null,
//        ]);
    }
}
