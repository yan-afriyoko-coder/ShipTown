<?php

namespace App\Modules\Automations\src;

use App\Events\HourlyEvent;
use App\Events\Order\ActiveOrderCheckEvent;
use App\Events\Order\OrderCreatedEvent;
use App\Events\Order\OrderUpdatedEvent;
use App\Modules\BaseModuleServiceProvider;

class AutomationsServiceProvider extends BaseModuleServiceProvider
{
    /**
     * @var string
     */
    public static string $module_name = 'Automations - Active Orders';

    /**
     * @var string
     */
    public static string $module_description = 'Sporadically, when required, runs automations on active orders';

    /**
     * @var bool
     */
    public static bool $autoEnable = true;

    /**
     * @var array
     */
    protected $listen = [
        ActiveOrderCheckEvent::class => [
            Listeners\ActiveOrderCheckListener::class,
        ],

        OrderCreatedEvent::class => [
            Listeners\OrderCreatedListener::class,
        ],

        OrderUpdatedEvent::class => [
            Listeners\OrderUpdatedListener::class,
        ],

        HourlyEvent::class => [
            Listeners\HourlyEventListener::class,
        ]
    ];

    public function boot()
    {
        parent::boot();

        $this->mergeConfigFrom(__DIR__.'/../config/automations.php', 'automations');
    }
}
