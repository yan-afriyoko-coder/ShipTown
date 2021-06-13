<?php

namespace App\Modules\AutoStatusSingleLineOrders\src;

use App\Events\Order\ActiveOrderCheckEvent;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

/**
 * Class EventServiceProvider
 * @package App\Providers
 */
class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        ActiveOrderCheckEvent::class => [
            Listeners\ActiveOrdersCheckEvent\SetStatusSingleLineOrders::class,
        ],
    ];
}
