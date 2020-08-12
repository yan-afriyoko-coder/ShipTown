<?php

namespace Tests\Feature\Api\Packlist;

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
    public function test_if_required_fields_are_returned()
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
                ]
            ],
            "total",
        ]);

    }

    /**
     * @return void
     */
    public function test_basic_get()
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
    public function test_if_unauthorized_if_not_logged_in()
    {
        $response = $this->get('api/packlist');

        $response->assertStatus(302);
    }
}
