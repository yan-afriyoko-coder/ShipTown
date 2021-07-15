<?php


namespace App\Modules\Api2cart\src\Listeners\OrderUpdatedEvent;

use App\Events\Order\OrderUpdatedEvent;
use App\Models\Order;
use App\Modules\Api2cart\src\Models\Api2cartConnection;

class SyncStatusListener
{
    public function handle(OrderUpdatedEvent $event)
    {
//        Api2cartConnection::
    }
}
