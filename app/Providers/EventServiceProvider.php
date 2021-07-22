<?php

namespace App\Providers;

use App\Events\HourlyEvent;
use App\Events\Product\ProductCreatedEvent;
use App\Events\Product\ProductUpdatedEvent;
use App\Listeners\HourlyEvent\FireActiveOrderCheckEventsListener;
use App\Listeners\Product\ProductCreatedListener;
use App\Listeners\Product\ProductUpdatedListener;
use App\Models\Product;
use App\Observers\ProductObserver;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

/**
 * Class EventServiceProviderBase.
 */
class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        // App
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],

        HourlyEvent::class => [
            FireActiveOrderCheckEventsListener::class,
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
