<?php

namespace Tests\Feature\Api\Packlist;

use App\Models\Order;
use App\User;
use Laravel\Passport\Passport;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PacklistGetTest extends TestCase
{
    /**
     * @return void
     */
    public function testIfRequiredFieldsAreReturned()
    {
        Passport::actingAs(
            factory(User::class)->create()
        );

        $response = $this->get('api/packlist');

        $response->assertJsonStructure([
            "current_page",
            "data" => [
                "*" => [
                    "id",
                    "order_id",
                ]
            ],
            "total",
        ]);
    }

    /**
     * @return void
     */
    public function testBasicGet()
    {
        Passport::actingAs(
            factory(User::class)->create()
        );

        $response = $this->get('api/packlist');

        $response->assertStatus(200);
    }

    /**
     * @return void
     */
    public function testIfUnauthorizedIfNotLoggedIn()
    {
        $response = $this->get('api/packlist');

        $response->assertStatus(302);
    }
}
