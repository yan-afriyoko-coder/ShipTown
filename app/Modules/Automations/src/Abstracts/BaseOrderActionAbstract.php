<?php

namespace App\Modules\Automations\src\Abstracts;

use App\Events\Order\OrderCreatedEvent;
use App\Events\Order\OrderUpdatedEvent;
use App\Models\Order;
use Illuminate\Support\Facades\Log;

/**
 * @property-read Order $order
 */
abstract class BaseOrderActionAbstract
{
    /**
    * @var OrderCreatedEvent|OrderUpdatedEvent
    */
    private $event;

    /**
     * @var Order
     */
    public Order $order;

    public function __construct($event)
    {
        $this->event = $event;
        $this->order = $event->order;
    }

    /**
     * @param string $options
     */
    public function handle(string $options = '')
    {
        Log::debug('automation.action.executing', [
            'order_number' => $this->event->order->order_number,
            'class' => class_basename(self::class),
            '$options' => $options,
        ]);
    }
}
