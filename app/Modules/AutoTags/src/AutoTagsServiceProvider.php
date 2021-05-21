<?php

namespace App\Modules\AutoTags\src;

use App\Events\Inventory\InventoryUpdatedEvent;
use App\Modules\AutoTags\src\Listeners\InventoryUpdatedEvent\OutOfStockTagListener;
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
            OutOfStockTagListener::class,
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
