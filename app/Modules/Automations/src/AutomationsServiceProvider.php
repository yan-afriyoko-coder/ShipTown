<?php

namespace App\Modules\Automations\src;

use App\Events\Order\ActiveOrderCheckEvent;
use App\Events\Order\OrderCreatedEvent;
use App\Modules\BaseModuleServiceProvider;

class AutomationsServiceProvider extends BaseModuleServiceProvider
{
    /**
     * @var string
     */
    public static string $module_name = 'Automations';

    /**
     * @var string
     */
    public static string $module_description = 'Provides Order Automations';

    /**
     * @var bool
     */
    public bool $autoEnable = true;

    /**
     * @var array
     */
    protected $listen = [
        OrderCreatedEvent::class => [
            Listeners\OrderCreatedListener::class
        ],

        ActiveOrderCheckEvent::class => [

        ],
    ];
}
