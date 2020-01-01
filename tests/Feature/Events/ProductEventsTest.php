<?php

namespace Tests\Feature\Events;


use App\Events\EventTypes;
use App\Events\ProductCreatedEvent;
use App\Events\ProductUpdatedEvent;
use App\Listeners\PublishSnsMessage;
use App\Models\Product;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Event;
use Mockery;
use Tests\Feature\AuthorizedUserTestCase;
use Tests\ModelSample;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProductEventsTest extends TestCase
{
    use AuthorizedUserTestCase;

    /**
     * Test ProductCreatedEvent
     */
    public function test_if_ProductCreatedEvent_is_dispatched_with_required_fields()
    {
        // Assign
        Event::fake();

        $product_data = [
            'sku' => 'test'
        ];

        $required_fields = [
            "sku",
            "name",
            "price",
            "sale_price",
            "sale_price_start_date",
            "sale_price_end_date",
            "quantity",
            "quantity_reserved",
            "quantity_available"
        ];


        // Act
        $product_new = new Product($product_data);

        $response = $this->json("POST", '/api/products', $product_data);


        // Assert
        $response->assertStatus(200);

        Event::assertDispatched(ProductCreatedEvent::class, function (ProductCreatedEvent $event) use ($required_fields) {
            return Arr::has($event->product->toArray(), $required_fields);
        });

    }
}
