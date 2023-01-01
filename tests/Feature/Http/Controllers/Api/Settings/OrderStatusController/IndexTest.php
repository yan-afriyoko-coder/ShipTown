<?php

namespace Tests\Feature\Http\Controllers\Api\Settings\OrderStatusController;

use App\User;
use Laravel\Passport\Passport;
use Tests\TestCase;

class IndexTest extends TestCase
{
    /** @test */
    public function test_index_call_returns_ok()
    {
        Passport::actingAs(
            User::factory()->create()
        );

        $response = $this->get(route('api.settings.order-statuses.index'));

        $response->assertSuccessful();
    }
}
