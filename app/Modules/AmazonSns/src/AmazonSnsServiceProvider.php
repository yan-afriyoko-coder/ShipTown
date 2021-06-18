<?php

namespace App\Modules\AmazonSns\src;

use App\Events\Order\OrderCreatedEvent;
use App\Events\Order\OrderUpdatedEvent;
use App\Events\Product\ProductCreatedEvent;
use App\Events\Product\ProductUpdatedEvent;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

/**
 * Class EventServiceProvider
 * @package App\Providers
 */
class AmazonSnsServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        ProductCreatedEvent::class => [
            Listeners\ProductCreatedEvent\PublishSnsNotificationListener::class,
        ],

        ProductUpdatedEvent::class => [
            Listeners\ProductUpdatedEvent\PublishSnsNotificationListener::class,
        ],

        OrderCreatedEvent::class => [
            Listeners\OrderCreatedEvent\PublishSnsNotificationListener::class,
        ],

        OrderUpdatedEvent::class => [
            Listeners\OrderUpdatedEvent\OrderUpdatedWebhookListener::class,
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
