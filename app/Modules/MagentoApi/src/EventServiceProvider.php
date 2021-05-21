<?php

namespace App\Modules\MagentoApi\src;

use App\Modules\MagentoApi\src\Listeners\ProductTagAttachedEvent\SyncWhenOutOfStockAttachedListener;
use App\Modules\MagentoApi\src\Listeners\ProductTagDetachedEvent\SyncWhenOutOfStockDetachedListener;
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
        // ProductTag
        \App\Events\Product\ProductTagAttachedEvent::class => [
            SyncWhenOutOfStockAttachedListener::class,
        ],

        \App\Events\Product\ProductTagDetachedEvent::class => [
            SyncWhenOutOfStockDetachedListener::class,
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
