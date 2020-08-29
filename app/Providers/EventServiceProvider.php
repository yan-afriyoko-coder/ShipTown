<?php

namespace App\Providers;

use App\Events\OrderCreatedEvent;
use App\Events\OrderStatusChangedEvent;
use App\Events\PickPickedEvent;
use App\Events\PickRequestCreatedEvent;
use App\Listeners\AddToPicklistOnOrderCreatedEventListener;
use App\Listeners\OrderStatusChangedEvent\CreatePickRequestsListener;
use App\Listeners\OrderStatusChangedListener;
use App\Listeners\PickPickedEvent\MarkOrderProductsAsPickedListener;
use App\Listeners\PickRequestCreatedEvent\AddQuantityToPicklistListener;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        OrderCreatedEvent::class => [
            AddToPicklistOnOrderCreatedEventListener::class,
        ],
        OrderStatusChangedEvent::class => [
            OrderStatusChangedListener::class,
            CreatePickRequestsListener::class,
        ],
        PickRequestCreatedEvent::class => [
            AddQuantityToPicklistListener::class
        ],
        PickPickedEvent::class => [
            MarkOrderProductsAsPickedListener::class
        ]
    ];

    /**
     * The subscriber classes to register.
     *
     * @var array
     */
    protected $subscribe = [
        \App\Listeners\PublishSnsNotifications::class,
        \App\Listeners\UpdateQuantityReserved::class,
        \App\Listeners\Inventory\UpdateProductQuantity::class,
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
