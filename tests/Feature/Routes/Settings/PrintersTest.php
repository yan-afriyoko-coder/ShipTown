<?php

namespace Tests\Feature\Routes\Settings;

use App\User;
use Laravel\Passport\Passport;
use Tests\TestCase;

class PrintersTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testExample()
    {
        Passport::actingAs(
            factory(User::class)->create()
        );

        $response = $this->get('/api/settings/printers');

        $response->assertStatus(422);
    }
}
