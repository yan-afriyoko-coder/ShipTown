<?php

namespace App\Modules\AutoTags\src;

use App\Events\Inventory\InventoryUpdatedEvent;
use App\Events\Order\OrderCreatedEvent;
use App\Events\Order\OrderUpdatedEvent;
use App\Modules\AutoTags\src\Listeners\InventoryUpdatedEvent\ToggleProductOutOfStockTagListener;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

/**
 * Class EventServiceProvider
 * @package App\Providers
 */
class AutoTagsServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        InventoryUpdatedEvent::class => [
            ToggleProductOutOfStockTagListener::class,
        ],

        OrderCreatedEvent::class => [
            Listeners\OrderCreatedEvent\ToggleOrderOutOfStockTagListener::class
        ],

        OrderUpdatedEvent::class => [
            Listeners\OrderUpdatedEvent\ToggleOrderOutOfStockTagListener::class
        ],
    ];

    /**
     * The subscriber classes to register.
     *
     * @var array
     */
    protected $subscribe = [

    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }
}
