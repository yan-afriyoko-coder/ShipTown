<?php


namespace App\Modules\Api2cart\src\Listeners\OrderUpdatedEvent;

use App\Events\Order\OrderUpdatedEvent;
use App\Modules\Api2cart\src\Api\Orders;
use App\Modules\Api2cart\src\Exceptions\RequestException;
use App\Modules\Api2cart\src\Jobs\SyncOrderStatus;
use App\Modules\Api2cart\src\Models\Api2cartConnection;
use App\Modules\Api2cart\src\Models\Api2cartOrderImports;
use GuzzleHttp\Exception\GuzzleException;

class SyncStatusListener
{
    /**
     * @throws RequestException
     * @throws GuzzleException
     */
    public function handle(OrderUpdatedEvent $event)
    {
        if ($event->order->isAttributeNotChanged('status_code')) {
            return;
        }

        if (! $event->order->orderStatus->sync_ecommerce) {
            return;
        }

        SyncOrderStatus::dispatch($event->order);
    }
}
