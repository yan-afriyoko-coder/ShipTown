<?php

namespace App\Modules\Automations\src\Conditions\Order;

use App\Events\Order\ActiveOrderCheckEvent;
use App\Events\Order\OrderCreatedEvent;
use App\Events\Order\OrderUpdatedEvent;
use App\Modules\Automations\src\Conditions\BaseCondition;
use App\Services\OrderService;
use Log;

/**
 *
 */
class CanNotFulfillFromLocationCondition extends BaseCondition
{
    /**
     * @var ActiveOrderCheckEvent|OrderCreatedEvent|OrderUpdatedEvent
     */
    protected $event;

    /**
     * @param $location_id
     * @return bool
     */
    public function isValid($location_id): bool
    {
        if ($location_id === '0') {
            $location_id = null;
        }

        $result = OrderService::canNotFulfill($this->event->order, $location_id);

        Log::debug('Validating condition', [
            'order_number' => $this->event->order->order_number,
            'location_id' => $location_id,
            'can_NOT_fulfill' => $result,
            'class' => self::class,
        ]);

        return $result;
    }
}
