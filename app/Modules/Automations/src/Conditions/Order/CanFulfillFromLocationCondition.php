<?php

namespace App\Modules\Automations\src\Conditions\Order;

use App\Events\Order\ActiveOrderCheckEvent;
use App\Events\Order\OrderCreatedEvent;
use App\Events\Order\OrderUpdatedEvent;
use App\Services\OrderService;
use Log;

/**
 *
 */
class CanFulfillFromLocationCondition
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
     * @param $location_id
     * @return bool
     */
    public function isValid($location_id): bool
    {
        if ($location_id === '0') {
            $location_id = null;
        }

        $result = OrderService::canFulfill($this->event->order, $location_id);

        Log::debug('Validating condition', [
            'order_number' => $this->event->order->order_number,
            'location_id' => $location_id,
            'can_fulfill' => $result,
            'class' => self::class,
        ]);

        return $result;
    }
}
