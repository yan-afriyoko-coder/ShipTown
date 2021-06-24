<?php

namespace App\Modules\AutoStatusPaid\src;

use App\Events\Order\OrderUpdatedEvent;
use App\Modules\BaseModuleServiceProvider;

/**
 * Class EventServiceProviderBase
 * @package App\Providers
 */
class EventServiceProviderBase extends BaseModuleServiceProvider
{
    protected $listen = [
        OrderUpdatedEvent::class => [
            Listeners\OrderUpdatedEvent\ProcessingToPaidListener::class,
        ],
    ];
}
