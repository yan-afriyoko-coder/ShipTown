<?php

namespace App\Providers;

use App\Events\OrderCreatedEvent;
use App\Events\OrderStatusChangedEvent;
use App\Events\PickPickedEvent;
use App\Events\PickQuantityRequiredChangedEvent;
use App\Events\PickRequestCreatedEvent;
use App\Events\PickUnpickedEvent;
use App\Listeners\Order\Created\AddToOldPicklistListener as AddToOldPicklistListener_OnOrderCreated;
use App\Listeners\Order\StatusChanged\AddToOldPicklistListener as AddToOldPicklistListener_OnStatusChanged;
use App\Listeners\Order\StatusChanged\CreatePickRequestsListener;
use App\Listeners\Pick\Picked\FillPickRequestsPickedQuantityListener;
use App\Listeners\Pick\QuantityRequiredChanged\MovePickRequestToNewPickListener;
use App\Listeners\Pick\Unpicked\ClearPickRequestsQuantityPickedListener;
use App\Listeners\PickRequest\Created\AddQuantityToPicklistListener;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
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
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],

        OrderCreatedEvent::class => [
            AddToOldPicklistListener_OnOrderCreated::class,
        ],

        OrderStatusChangedEvent::class => [
            AddToOldPicklistListener_OnStatusChanged::class,
            CreatePickRequestsListener::class,
        ],

        PickRequestCreatedEvent::class => [
            AddQuantityToPicklistListener::class
        ],

        PickPickedEvent::class => [
            FillPickRequestsPickedQuantityListener::class
        ],

        PickUnpickedEvent::class => [
            ClearPickRequestsQuantityPickedListener::class
        ],

        PickQuantityRequiredChangedEvent::class => [
            MovePickRequestToNewPickListener::class
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
