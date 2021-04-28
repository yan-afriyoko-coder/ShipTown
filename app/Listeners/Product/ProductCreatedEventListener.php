<?php

namespace App\Listeners\Product;

use App\Events\Product\ProductCreatedEvent;
use App\Models\Inventory;
use App\Models\Warehouse;
use App\Modules\Sns\src\Jobs\PublishSnsNotificationJob;

class ProductCreatedEventListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param ProductCreatedEvent $event
     * @return void
     */
    public function handle(ProductCreatedEvent $event)
    {
        $this->insertInventoryRecords($event);
        $this->publishSnsNotification($event);
    }

    /**
     * Handle the event.
     *
     * @param ProductCreatedEvent $event
     * @return void
     */
    public function publishSnsNotification(ProductCreatedEvent $event)
    {
        PublishSnsNotificationJob::dispatch(
            'products_events',
            $event->getProduct()->toJson()
        );
    }

    /**
     * @param ProductCreatedEvent $event
     */
    private function insertInventoryRecords(ProductCreatedEvent $event): void
    {
        $product = $event->getProduct();

        $warehouse_ids = Warehouse::all('id')
            ->map(function ($warehouse) use ($product) {
                return [
                    'warehouse_id' => $warehouse->getKey(),
                    'product_id' => $product->getKey(),
                    'location_id' => $warehouse->getKey(),
                ];
            });

        Inventory::query()->insert($warehouse_ids->toArray());
    }
}
