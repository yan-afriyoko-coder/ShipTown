<?php

namespace App\Modules\Automations\src\Conditions\Order;

use App\Modules\Automations\src\Abstracts\BaseOrderConditionAbstract;
use App\Services\OrderService;
use Illuminate\Support\Facades\Log;

/**
 *
 */
class CanFulfillFromLocationCondition extends BaseOrderConditionAbstract
{
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

        Log::debug('Automation condition', [
            'order_number' => $this->event->order->order_number,
            'result' => $result,
            'class' => class_basename(self::class),
            'location_id' => $location_id,
        ]);

        return $result;
    }
}
