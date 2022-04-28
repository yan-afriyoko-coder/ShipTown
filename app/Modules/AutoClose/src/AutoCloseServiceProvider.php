<?php

namespace App\Modules\AutoClose\src;

use App\Events\Order\OrderUpdatedEvent;
use App\Modules\BaseModuleServiceProvider;

/**
 * Class EventServiceProviderBase.
 */
class AutoCloseServiceProvider extends BaseModuleServiceProvider
{
    /**
     * @var string
     */
    public static string $module_name = 'AutoClose Order';

    /**
     * @var string
     */
    public static string $module_description = 'Automatically opens/closes order when status changed';

    /**
     * @var bool
     */
    public static bool $autoEnable = true;

    /**
     * @var array
     */
    protected $listen = [
        OrderUpdatedEvent::class => [
            Listeners\OrderUpdatedEvent\DispatchOpenCloseOrderJobListener::class,
        ],
    ];
}
