<?php

namespace Tests\Feature\Api;

use App\Models\Inventory;
use App\Models\Order;
use App\Models\Product;
use App\User;
use Laravel\Passport\Passport;
use Tests\TestCase;

class OrdersShelfLocationsTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testIfMinShelfLocationSortAllowed()
    {
        Passport::actingAs(
            factory(User::class)->create()
        );

        Order::query()->forceDelete();
        Product::query()->forceDelete();
        Inventory::query()->forceDelete();

        $product = factory(Product::class, 100)
            ->create()
            ->each(function ($product) {
                factory(Inventory::class)->create([
                    'product_id'  => $product->getKey(),
                    'location_id' => 100,
                ]);
            });

        factory(Order::class, 5)
            ->with('orderProducts', 2)
            ->create();

        $response = $this->get('/api/orders?filter[inventory_source_location_id]=100&sort=min_shelf_location');

        $response->assertStatus(200);
    }
}
