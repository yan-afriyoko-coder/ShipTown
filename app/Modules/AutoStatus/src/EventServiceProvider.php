<?php

namespace App\Modules\AutoStatus\src;

use App\Events\Order\OrderUpdatedEvent;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

/**
 * Class EventServiceProviderBase
 * @package App\Providers
 */
class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        OrderUpdatedEvent::class => [
            Listeners\OrderUpdatedEvent\UpdateClosedAt::class,
        ],
    ];
}
