<?php

namespace App\Modules\Automations\src\Actions\Order;

use App\Events\Order\ActiveOrderCheckEvent;
use App\Events\Order\OrderCreatedEvent;
use Log;

class LogMessageAction
{
    /**
     * @var ActiveOrderCheckEvent|OrderCreatedEvent
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
        Log::debug('Adding order log message', [
            'class' => self::class,
            'order_number' => $this->event->order->order_number,
            'message' => $value,
        ]);

        $this->event->order->log($value);
    }
}
