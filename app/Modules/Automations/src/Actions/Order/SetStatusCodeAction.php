<?php

namespace App\Modules\Automations\src\Actions\Order;

use App\Events\Order\ActiveOrderCheckEvent;
use App\Events\Order\OrderCreatedEvent;
use App\Events\Order\OrderUpdatedEvent;
use Log;

class SetStatusCodeAction
{
    /**
    * @var ActiveOrderCheckEvent|OrderCreatedEvent|OrderUpdatedEvent
    */
    private $event;

    public function __construct($event)
    {
        $this->event = $event;
    }

    /**
     * @param $value
     */
    public function handle($value)
    {
        $order = $this->event->order;

        Log::debug('Set Status Code', [
            'order_number' => $this->event->order->order_number,
            'class' => class_basename(self::class),
            'new_status' => $value,
        ]);

        $order->update(['status_code' => $value]);
    }
}
