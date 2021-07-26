<?php

namespace App\Modules\AutoStatusAwaitingPayment\src;

use App\Events\Order\ActiveOrderCheckEvent;
use App\Events\Order\OrderUpdatedEvent;
use App\Modules\BaseModuleServiceProvider;

/**
 * Class EventServiceProviderBase.
 */
class AutoStatusAwaitingPaymentServiceProvider extends BaseModuleServiceProvider
{
    /**
     * @var string
     */
    public static string $module_name = 'Auto awaiting_payment Status';

    /**
     * @var string
     */
    public static string $module_description = 'Automatically changes status from "processing" to "awaiting_payment" '.
        'if order has not been paid yet';

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
