<?php

namespace App\Modules\Automations\src\Actions\Order;

use App\Events\Order\ActiveOrderCheckEvent;
use App\Events\Order\OrderCreatedEvent;
use App\Events\Order\OrderUpdatedEvent;
use Illuminate\Support\Facades\Log;

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
     * @param $new_status_code
     */
    public function handle($new_status_code)
    {
        $order = $this->event->order->refresh();

        if ($order->status_code === $new_status_code) {
            return;
        }

        Log::debug('Automation Action', [
            'order_number' => $this->event->order->order_number,
            'class' => class_basename(self::class),
            'new_status' => $new_status_code,
        ]);

        $order->update(['status_code' => $new_status_code]);
    }
}
