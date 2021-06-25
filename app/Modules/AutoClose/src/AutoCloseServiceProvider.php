<?php

namespace App\Modules\AutoClose\src;

use App\Events\Order\OrderUpdatedEvent;
use App\Modules\BaseModuleServiceProvider;

/**
 * Class EventServiceProviderBase.
 */
class AutoCloseServiceProvider extends BaseModuleServiceProvider
{
    public bool $autoEnable = false;

    /**
    * @var string
    */
    public string $module_name = 'AutoClose Order';

    /**
     * @var string
     */
    public string $module_description = 'Automatically opens/closes order when status changed';

    /**
     * @var array
     */
    protected $listen = [
        OrderUpdatedEvent::class => [
            Listeners\OrderUpdatedEvent\DispatchOpenCloseOrderJobListener::class,
        ],
    ];
}
