<?php

namespace Tests\Feature\Routes\Api;

use App\Models\Product;
use App\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Event;
use Laravel\Passport\Passport;
use Tests\TestCase;

class ProductEventsTest extends TestCase
{
    const REQUIRED_FIELDS = [
        'sku',
        'name',
        'price',
        'sale_price',
        'sale_price_start_date',
        'sale_price_end_date',
        'quantity',
        'quantity_reserved',
        'quantity_available',
    ];

    /**
     * Test ProductCreatedEvent.
     */
    public function testIfProductCreatedEventIsDispatchedWithRequiredFields()
    {
        Passport::actingAs(
            User::factory()->create()
        );

        // Assign
        Event::fake();

        $product_data = [
            'sku'   => 'test',
            'name'  => 'testName',
            'price' => 10,
        ];

        Product::query()->where('sku', '=', $product_data['sku'])->delete();

        // Act
        $response = $this->json('POST', '/api/products', $product_data);

        // Assert
        $response->assertStatus(200);

        Event::assertDispatched('eloquent.created: App\Models\Product', function ($event, Product $product) {
            return Arr::has($product->toArray(), self::REQUIRED_FIELDS);
        });
    }

    /**
     * Test ProductCreatedEvent.
     */
    public function testIfProductUpdatedEventIsDispatchedWithRequiredFields()
    {
        Passport::actingAs(
            User::factory()->create()
        );

        // Assign
        Event::fake();

        $product = Product::factory()->create();

        $product_update = [
            'sku'   => $product['sku'],
            'name'  => $product['name'],
            'price' => $product['price'] * 2,
        ];

        // Act
        $response_update = $this->json('POST', '/api/products', $product_update);

        // Assert
        $response_update->assertStatus(200);

        Event::assertDispatched('eloquent.updated: App\Models\Product', function ($event, Product $product) {
            return Arr::has($product->toArray(), self::REQUIRED_FIELDS);
        });
    }
}
