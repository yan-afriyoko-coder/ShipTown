<?php

namespace App\Modules\Webhooks\src\Listeners;

use App\Events\DailyEvent;
use App\Models\Order;

/**
 * Class AttachAwaitingPublishTagListener.
 */
class DailyEventListener
{
    /**
     * Handle the event.
     *
     * @param DailyEvent $event
     *
     * @return void
     */
    public function handle(DailyEvent $event)
    {
        activity()->withoutLogs(function () use ($event) {
            $orders = Order::where('updated_at', '>', now()->subDay())->get();
            $orders->each(function (Order $order) {
                $order->attachTag(config('webhooks.tags.awaiting.name'));
            });
        });
    }
}
