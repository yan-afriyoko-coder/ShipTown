<?php

namespace App\Modules\Webhooks\src;

use App\Events\DailyEvent;
use App\Events\HourlyEvent;
use App\Events\Inventory\InventoryUpdatedEvent;
use App\Events\InventoryMovementCreatedEvent;
use App\Events\Order\OrderCreatedEvent;
use App\Events\Order\OrderUpdatedEvent;
use App\Events\OrderProductShipmentCreatedEvent;
use App\Events\Product\ProductCreatedEvent;
use App\Events\Product\ProductUpdatedEvent;
use App\Events\SyncRequestedEvent;
use App\Modules\BaseModuleServiceProvider;
use Exception;
use Ramsey\Uuid\Guid\Guid;

/**
 * Class EventServiceProviderBase.
 */
class WebhooksServiceProviderBase extends BaseModuleServiceProvider
{
    /**
     * @var string
     */
    public static string $module_name = '.CORE - Webhooks';

    /**
     * @var string
     */
    public static string $module_description = 'Amazon SNS integration to provide webhooks';

    /**
     * @var string
     */
    public static string $settings_link = '/admin/settings/modules/webhooks/subscriptions';

    /**
     * @var bool
     */
    public static bool $autoEnable = false;

    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        SyncRequestedEvent::class => [
            Listeners\SyncRequestedEventListener::class,
        ],

        HourlyEvent::class => [
            Listeners\HourlyEventListener::class
        ],

        DailyEvent::class => [
            Listeners\DailyEventListener::class,
        ],

        OrderProductShipmentCreatedEvent::class => [
            Listeners\OrderProductShipmentCreatedListener::class,
        ],

        ProductCreatedEvent::class => [
            Listeners\ProductCreatedEvent\AttachAwaitingPublishTagListener::class,
        ],

        ProductUpdatedEvent::class => [
            Listeners\ProductUpdatedEvent\AttachAwaitingPublishTagListener::class,
        ],

        OrderCreatedEvent::class => [
            Listeners\OrderCreatedEvent\AttachAwaitingPublishTagListener::class,
        ],

        OrderUpdatedEvent::class => [
            Listeners\OrderUpdatedEvent\AttachAwaitingPublishTagListener::class,
        ],

        InventoryMovementCreatedEvent::class => [
            Listeners\InventoryMovementCreatedEventListener::class,
        ],

        InventoryUpdatedEvent::class => [
            Listeners\InventoryUpdatedEventListener::class,
        ]
    ];

    public function boot()
    {
        parent::boot();

        $this->publishes([__DIR__.'/../config/webhooks.php' => config_path('webhooks.php')], 'config');
        $this->mergeConfigFrom(__DIR__.'/../config/webhooks.php', 'webhooks');

        $this->loadViewsFrom(__DIR__.'/../resources/views', 'webhooks');
    }

    public static function enabling(): bool
    {
        $configuration = Services\SnsService::getConfiguration();

        if ($configuration->topic_arn === null) {
            $response = Services\SnsService::client()->createTopic(['Name' => Guid::uuid4()]);

            $topicArn = $response->toArray()['TopicArn'];

            $configuration->update(['topic_arn' => $topicArn]);
        }

        try {
            Services\SnsService::client()->listSubscriptionsByTopic(['TopicArn' => $configuration->topic_arn]);
        } catch (Exception $exception) {
            ray($exception);
            report($exception);
            return false;
        }


        return true;
    }
}
