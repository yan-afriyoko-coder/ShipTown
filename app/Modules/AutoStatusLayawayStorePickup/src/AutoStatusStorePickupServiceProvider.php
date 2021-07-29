<?php

namespace App\Modules\AutoStatusLayawayStorePickup\src;

use App\Events\Order\ActiveOrderCheckEvent;
use App\Events\Order\OrderUpdatedEvent;
use App\Modules\BaseModuleServiceProvider;

class AutoStatusStorePickupServiceProvider extends BaseModuleServiceProvider
{
    public static string $module_name = 'Auto "layaway" status for Store Pickups';

    public static string $module_description = 'Automatically changes status from paid to layaway';

    /**
     * @var bool
     */
    public bool $autoEnable = false;

    protected $listen = [
        OrderUpdatedEvent::class => [
            Listeners\OrderUpdatedListener::class
        ],

        ActiveOrderCheckEvent::class => [
            Listeners\ActiveOrderCheckListener::class
        ]
    ];
}
