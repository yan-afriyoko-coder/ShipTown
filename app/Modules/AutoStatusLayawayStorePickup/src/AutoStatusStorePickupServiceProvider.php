<?php

namespace App\Modules\AutoStatusLayawayStorePickup\src;

use App\Events\Order\ActiveOrderCheckEvent;
use App\Events\Order\OrderCreatedEvent;
use App\Events\Order\OrderUpdatedEvent;
use App\Modules\BaseModuleServiceProvider;

/**
 * Class AutoStatusStorePickupServiceProvider
 * @package App\Modules\AutoStatusLayawayStorePickup\src
 */
class AutoStatusStorePickupServiceProvider extends BaseModuleServiceProvider
{
    /**
     * @var string
     */
    public static string $module_name = 'Auto "layaway" status for Store Pickups';

    /**
     * @var string
     */
    public static string $module_description = 'Automatically changes status from paid to layaway';

    /**
     * @var bool
     */
    public bool $autoEnable = false;

    protected $listen = [
        OrderCreatedEvent::class => [
            Listeners\OrderCreatedListener::class
        ],

        ActiveOrderCheckEvent::class => [
            Listeners\ActiveOrderCheckListener::class
        ]
    ];
}
