<?php

namespace App\Modules\Automations\src;

use App\Events\EveryHourEvent;
use App\Events\EveryTenMinutesEvent;
use App\Events\Order\OrderCreatedEvent;
use App\Events\Order\OrderUpdatedEvent;
use App\Events\OrderShipment\OrderShipmentCreatedEvent;
use App\Events\ShippingLabelCreatedEvent;
use App\Modules\Automations\src\Jobs\RunEnabledAutomationsJob;
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
        EveryTenMinutesEvent::class => [
            Listeners\EveryTenMinutesEventListener::class,
        ],

        EveryHourEvent::class => [
            Listeners\HourlyEventListener::class,
        ],

        OrderCreatedEvent::class => [
            Listeners\OrderCreatedListener::class,
        ],

        OrderUpdatedEvent::class => [
            Listeners\OrderUpdatedListener::class,
        ],

        ShippingLabelCreatedEvent::class => [
            Listeners\ShippingLabelCreatedListener::class
        ],

        OrderShipmentCreatedEvent::class => [
            Listeners\OrderShipmentCreatedEventListener::class,
        ],
    ];

    public static function enabling(): bool
    {
        RunEnabledAutomationsJob::dispatch();

        return parent::enabling();
    }

    public function boot()
    {
        parent::boot();

        $this->mergeConfigFrom(__DIR__.'/../config/automations.php', 'automations');
    }
}
