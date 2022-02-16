<?php

namespace Tests\Feature\Routes\Api;

use App\Models\Inventory;
use App\Models\Product;
use App\User;
use Illuminate\Support\Facades\Event;
use Laravel\Passport\Passport;
use Tests\TestCase;

class InventoryRoutesTest extends TestCase
{
    public function testGetRouteUnauthorized()
    {
        $response = $this->get('/api/product/inventory');

        $response->assertStatus(302);
    }

    public function testGetRouteAuthorized()
    {
        Passport::actingAs(
            factory(User::class)->create()
        );

        $response = $this->get('/api/product/inventory');

        $response->assertStatus(200);
    }

    /**
     * @return void
     */
    public function testIfPostRouteIsProtected()
    {
        $response = $this->post('api/product/inventory');

        $response->assertStatus(302);
    }

    public function testIfCantPostWithoutData()
    {
        Passport::actingAs(
            factory(User::class)->create()
        );

        $response = $this->postJson('/api/product/inventory', []);

        $response->assertStatus(422);
    }

    public function testQuantityUpdate()
    {
        Event::fake();

        Passport::actingAs(
            factory(User::class)->create()
        );

        $product = factory(Product::class)->create();

        $inventory = factory(Inventory::class)->make();

        $update = [
            'sku'               => $product->sku,
            'location_id'       => 0,
            'quantity'          => $inventory->quantity,
            'quantity_reserved' => $inventory->quantity_reserved,
        ];

        $response = $this->postJson('/api/product/inventory', $update);

        $response->assertStatus(200);
    }
}
