<?php

namespace Tests\Feature\Routes\Api;

use App\Models\Inventory;
use App\Models\Product;
use App\Models\Warehouse;
use App\User;
use Laravel\Passport\Passport;
use Tests\TestCase;

class InventoryRoutesTest extends TestCase
{
    public function testGetRouteUnauthorized()
    {
        $response = $this->get(route('api.inventory.store'));

        $response->assertStatus(302);
    }

    public function testGetRouteAuthorized()
    {
        Passport::actingAs(
            User::factory()->create()
        );

        $response = $this->get(route('api.inventory.store'));

        $response->assertStatus(200);
    }

    /**
     * @return void
     */
    public function testIfPostRouteIsProtected()
    {
        $response = $this->post(route('api.inventory.store'));

        $response->assertStatus(302);
    }

    public function testIfCantPostWithoutData()
    {
        Passport::actingAs(
            User::factory()->create()
        );

        $response = $this->postJson(route('api.inventory.store'), []);

        $response->assertStatus(422);
    }

    public function testQuantityUpdate()
    {
        Passport::actingAs(
            User::factory()->create()
        );

        /** @var Warehouse $warehouse */
        $warehouse = Warehouse::factory()->create();

        /** @var Product $product */
        $product = Product::factory()->create();

        $inventory = Inventory::first();

        $update = [
            'id'                => $inventory->getKey(),
            'quantity'          => rand(100, 200),
            'quantity_reserved' => rand(10, 50),
        ];

        $response = $this->postJson(route('api.inventory.store'), $update);

        $response->assertStatus(200);
    }
}
