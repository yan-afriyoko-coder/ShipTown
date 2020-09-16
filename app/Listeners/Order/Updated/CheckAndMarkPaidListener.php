<?php

namespace App\Listeners\Order\Updated;

use App\Events\Order\UpdatedEvent;
use App\Models\Order;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class CheckAndMarkPaidListener
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
     */
    public function handle(UpdatedEvent $event)
    {
        if ($event->getOrder()->isNotStatusCode('processing')) {
            return;
        }

        $this->checkAndMarkPaid($event->getOrder());
    }

    /**
     * @param Order $order
     */
    public function checkAndMarkPaid(Order $order): void
    {
        if ($order->isPaid) {
            $order->update(['status_code' => 'paid']);
            info('CheckAndMarkPaid: set status to paid', ['order_number' => $order->order_number]);
        }
    }
}
