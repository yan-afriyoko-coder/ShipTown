<?php

namespace App\Modules\AutoStatusPaid\src;

use App\Events\Order\ActiveOrderCheckEvent;
use App\Events\Order\OrderUpdatedEvent;
use App\Modules\BaseModuleServiceProvider;

/**
 * Class EventServiceProviderBase.
 */
class AutoStatusPaidServiceProvider extends BaseModuleServiceProvider
{
    /**
     * @var string
     */
    public string $module_name = 'Auto Paid Status';

    /**
     * @var string
     */
    public string $module_description = 'Automatically changes status ' .
        'from "processing" or "awaiting_payment" to "paid" if order has been paid';

    /**
     * @var bool
     */
    public bool $autoEnable = true;

    /**
     * @var array
     */
    protected $listen = [
        ActiveOrderCheckEvent::class => [
            Listeners\ActiveOrderCheckEvent\ProcessingToPaidListener::class,
        ],

        OrderUpdatedEvent::class => [
            Listeners\OrderUpdatedEvent\ProcessingToPaidListener::class,
        ],
    ];
}
