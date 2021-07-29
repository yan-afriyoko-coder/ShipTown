<?php

namespace App\Modules\FireActiveOrderCheckEvent\src\Listeners;

use App\Events\HourlyEvent;
use App\Events\Order\ActiveOrderCheckEvent;
use App\Events\Order\OrderCreatedEvent;
use App\Jobs\FireActiveOrderCheckEventForAllActiveOrdersJob;
use App\Modules\FireActiveOrderCheckEvent\src\Jobs\FireActiveOrderCheckEventJob;

/**
 * Class SetPackingWebStatus.
 */
class HourlyEventListener
{
    /**
     * Handle the event.
     *
     * @param HourlyEvent $event
     *
     * @return void
     */
    public function handle(HourlyEvent $event)
    {
        FireActiveOrderCheckEventForAllActiveOrdersJob::dispatch();
    }
}
