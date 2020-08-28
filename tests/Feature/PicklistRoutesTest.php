<?php

namespace Tests\Feature;

use App\Models\Picklist;
use App\Models\Product;
use App\Models\ProductAlias;
use App\User;
use Doctrine\DBAL\Driver\IBMDB2\DB2Connection;
use Illuminate\Support\Facades\Event;
use Laravel\Passport\Passport;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PicklistRoutesTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testGetRoute()
    {
        Event::fake();

        Passport::actingAs(
            factory(User::class)->create()
        );

        $product = factory(Product::class)->create();

        factory(ProductAlias::class)->create([
            'product_id' => $product->getKey()
        ]);

        factory(Picklist::class)->create([
            'product_id' => $product->getKey()
        ]);

        $response = $this->json('GET', 'api/picklist?include=product.aliases,order');

        $response->assertStatus(200);

        $this->assertGreaterThan(0, $response->json('total'));

        $response->assertJsonStructure([
            "current_page",
            "data" => [
                "*" => [
                    "id",
                    "product_id",
                    "sku_ordered",
                    "name_ordered",
                    "quantity_requested",
                    "product" => [
                        'aliases'
                    ],
                    "order",
                ]
            ],
            "total",
        ]);
    }

    public function testIfQuantityPickedIsDeductedFromQuantityToPick()
    {
        Event::fake();

        Passport::actingAs(
            factory(User::class)->create()
        );

        // clear all picklist
        Picklist::query()->delete();

        $picklist = factory(Picklist::class)->create();

        $response = $this->json('POST', 'api/picklist/'.$picklist->id, [
            'quantity_picked' => $picklist->quantity_requested,
            'is_picked' => true,
        ]);

        $response->assertStatus(200);

        $this->assertFalse(
            Picklist::query()->whereRaw('quantity_requested <> quantity_picked')
                ->exists()
        );
    }
}
