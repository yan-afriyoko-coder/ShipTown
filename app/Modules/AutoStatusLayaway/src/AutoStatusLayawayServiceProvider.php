<?php

namespace App\Modules\AutoStatusLayaway\src;

use App\Events\Order\ActiveOrderCheckEvent;
use App\Events\Order\OrderUpdatedEvent;
use App\Modules\BaseModuleServiceProvider;

class AutoStatusLayawayServiceProvider extends BaseModuleServiceProvider
{
    public static string $module_name = 'Auto "layaway" status';

    public static string $module_description = 'Automatically changes status from paid to layaway ' .
        'if cannot fulfill from location 4';

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
