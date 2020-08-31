<?php

namespace App\Providers;

use App\Events\Inventory\CreatedEvent as Inventory_CreatedEvent_Alias;
use App\Events\Inventory\DeletedEvent;
use App\Events\Inventory\UpdatedEvent;
use App\Events\Order\CreatedEvent as Order_CreatedEvent_Alias;
use App\Events\Order\StatusChangedEvent as Order_StatusChangedEvent_Alias;
use App\Events\OrderCreatedEvent;
use App\Events\OrderStatusChangedEvent;
use App\Events\PickPickedEvent;
use App\Events\PickQuantityRequiredChangedEvent as PickQuantity_RequiredChangedEvent_Alias;
use App\Events\PickRequestCreatedEvent as PickRequest_CreatedEvent_Alias;
use App\Events\PickUnpickedEvent;
use App\Listeners\Inventory\Created\AddToProductTotalQuantityListener;
use App\Listeners\Inventory\Deleted\DeductFromProductTotalQuantityListener;
use App\Listeners\Inventory\Updated\UpdateProductTotalQuantityListener;
use App\Listeners\Order\Created\AddToOldPicklistListener as AddToOldPicklistListener_OnOrderCreated;
use App\Listeners\Order\StatusChanged\AddToOldPicklistListener as AddToOldPicklistListener_OnStatusChanged;
use App\Listeners\Order\StatusChanged\CreatePickRequestsListener;
use App\Listeners\Order\StatusChanged\RemoveFromOldPicklistListener;
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

        // App
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],

        // Order
        Order_CreatedEvent_Alias::class => [
            AddToOldPicklistListener_OnOrderCreated::class,
        ],

        Order_StatusChangedEvent_Alias::class => [
            AddToOldPicklistListener_OnStatusChanged::class,
            RemoveFromOldPicklistListener::class,
            CreatePickRequestsListener::class,
        ],

        // Pick
        PickPickedEvent::class => [
            FillPickRequestsPickedQuantityListener::class,
        ],

        PickUnpickedEvent::class => [
            ClearPickRequestsQuantityPickedListener::class,
        ],

        PickQuantity_RequiredChangedEvent_Alias::class => [
            MovePickRequestToNewPickListener::class,
        ],

        // PickRequest
        PickRequest_CreatedEvent_Alias::class => [
            AddQuantityToPicklistListener::class,
        ],

        // Inventory
        Inventory_CreatedEvent_Alias::class => [
            AddToProductTotalQuantityListener::class,
        ],

        UpdatedEvent::class => [
            UpdateProductTotalQuantityListener::class,
        ],

        DeletedEvent::class => [
            DeductFromProductTotalQuantityListener::class,
        ],

        // Other

    ];

    /**
     * The subscriber classes to register.
     *
     * @var array
     */
    protected $subscribe = [
        \App\Listeners\PublishSnsNotifications::class,
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
