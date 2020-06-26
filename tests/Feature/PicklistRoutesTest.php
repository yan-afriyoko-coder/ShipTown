<?php

namespace Tests\Feature;

use App\Models\Picklist;
use App\User;
use Doctrine\DBAL\Driver\IBMDB2\DB2Connection;
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
    public function test_get_route()
    {
        Passport::actingAs(
            factory(User::class)->create()
        );

        factory(Picklist::class)->create();

        $response = $this->json('GET', 'api/picklist');

        $response->assertStatus(200);

        $response->assertJsonStructure([
            "current_page",
            "data" => [
                "*" => [
                    "id",
                    "product_id",
                    "location_id",
                    "shelve_location",
                    "quantity_to_pick",
                ]
            ],
            "total",
        ]);

    }

    public function test_if_quantity_picked_is_deducted_from_quantity_to_pick()
    {

        Passport::actingAs(
            factory(User::class)->create()
        );

        // clear all picklist
        Picklist::query()->delete();

        $picklist = factory(Picklist::class)->create();

        $response = $this->json('POST', 'api/picklist/'.$picklist->id, [
            'quantity_picked' => $picklist->quantity_to_pick
        ]);

        $response->assertStatus(200);

        $this->assertFalse(
            Picklist::query()->where('quantity_to_pick', '>', 0)
                ->exists()
        );

    }
}
