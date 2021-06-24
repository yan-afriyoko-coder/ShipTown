<?php

namespace App\Modules\AutoStatusPaid\src;

use App\Events\Order\OrderUpdatedEvent;
use App\Modules\ModuleServiceProvider;

/**
 * Class EventServiceProvider
 * @package App\Providers
 */
class EventServiceProvider extends ModuleServiceProvider
{
    protected $listen = [
        OrderUpdatedEvent::class => [
            Listeners\OrderUpdatedEvent\ProcessingToPaidListener::class,
        ],
    ];
}
