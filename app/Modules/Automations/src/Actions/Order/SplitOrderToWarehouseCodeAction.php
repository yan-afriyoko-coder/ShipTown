<?php

namespace App\Modules\Automations\src\Actions\Order;

use App\Events\Order\ActiveOrderCheckEvent;
use App\Events\Order\OrderCreatedEvent;
use App\Events\Order\OrderUpdatedEvent;
use App\Models\Warehouse;
use App\Modules\SplitOrder\src\SplitOrderService;
use Illuminate\Support\Facades\Log;

class SplitOrderToWarehouseCodeAction
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
     * @param $options
     */
    public function handle($options)
    {
        Log::debug('Automation Action', [
            'order_number' => $this->event->order->order_number,
            'class' => class_basename(self::class),
            '$options' => $options,
        ]);

        $optionsSeparated   = explode(',', $options);

        $splitOrderService = new SplitOrderService();

        $splitOrderService->split(
            $this->event->order->refresh(),
            Warehouse::whereCode($optionsSeparated[0])->first(),
            $optionsSeparated[1],
        );
    }
}
