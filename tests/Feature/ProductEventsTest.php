<?php

namespace Tests\Feature;


use App\Events\EventTypes;
use App\Events\ProductCreatedEvent;
use App\Events\ProductUpdatedEvent;
use App\Listeners\PublishSnsMessage;
use App\Models\Product;
use App\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Event;
use Laravel\Passport\Passport;
use Mockery;
use Tests\Feature\AuthorizedUserTestCase;
use Tests\ModelSample;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProductEventsTest extends TestCase
{
    CONST REQUIRED_FIELDS = [
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

    /**
     * Test ProductCreatedEvent
     */
    public function test_if_ProductCreatedEvent_is_dispatched_with_required_fields()
    {
        Passport::actingAs(
            factory(User::class)->create()
        );

        // Assign
        Event::fake();

        $product_data = [
            'sku' => 'test'
        ];


        // Act
        $response = $this->json("POST", '/api/products', $product_data);


        // Assert
        $response->assertStatus(200);

        Event::assertDispatched('eloquent.created: App\Models\Product', function ($event, Product $product) {
            return Arr::has($product->toArray(), self::REQUIRED_FIELDS);
        });

    }


    /**
     * Test ProductCreatedEvent
     */
    public function test_if_ProductUpdatedEvent_is_dispatched_with_required_fields()
    {
        Passport::actingAs(
            factory(User::class)->create()
        );

        // Assign
        Event::fake();

        $product_new = [
            'sku' => 'test'
        ];
        $product_update = [
            'sku' => 'test',
            'price' => 1
        ];

        // Act
        $response_create = $this->json("POST", '/api/products', $product_new);
        $response_update = $this->json("POST", '/api/products', $product_update);

        // Assert
        $response_create->assertStatus(200);
        $response_update->assertStatus(200);

        Event::assertDispatched('eloquent.updated: App\Models\Product', function ($event, Product $product) {
            return Arr::has($product->toArray(), self::REQUIRED_FIELDS);
        });

    }
}
