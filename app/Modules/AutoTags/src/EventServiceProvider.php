<?php

namespace App\Modules\AutoTags\src;

use App\Events\Inventory\InventoryUpdatedEvent;
use App\Events\Order\OrderCreatedEvent;
use App\Events\Order\OrderUpdatedEvent;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

/**
 * Class EventServiceProvider
 * @package App\Providers
 */
class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        InventoryUpdatedEvent::class => [
            Listeners\InventoryUpdatedEvent\ToggleProductOutOfStockTagListener::class,
            Listeners\InventoryUpdatedEvent\ToggleProductOversoldTagListener::class,
        ],

        OrderCreatedEvent::class => [
            Listeners\OrderCreatedEvent\ToggleOrderOutOfStockTagListener::class
        ],

        OrderUpdatedEvent::class => [
            Listeners\OrderUpdatedEvent\ToggleOrderOutOfStockTagListener::class
        ],
    ];
}
