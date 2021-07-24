<?php

namespace App\Modules\AutoStatusLayaway\src;

use App\Events\Order\ActiveOrderCheckEvent;
use App\Events\Order\OrderUpdatedEvent;
use App\Modules\BaseModuleServiceProvider;

class AutoStatusLayawayServiceProvider extends BaseModuleServiceProvider
{
    public string $module_name = 'Auto "layaway" status';

    public string $module_description = 'Automatically changes status from paid to layaway';

    /**
     * @var bool
     */
    public bool $autoEnable = false;

    protected $listen = [
        OrderUpdatedEvent::class => [
            Listeners\OrderUpdatedEvent\SetLayawayStatusListener::class
        ],

        ActiveOrderCheckEvent::class => [
            Listeners\ActiveOrderCheckEvent\SetLayawayStatusListener::class
        ]
    ];
}
