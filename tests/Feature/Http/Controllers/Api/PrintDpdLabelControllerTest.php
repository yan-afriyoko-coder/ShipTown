<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Modules\Dpd\src\Dpd;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\Api\PrintOrderController
 */
class PrintDpdLabelControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function store_returns_an_ok_response()
    {
        $user = factory(User::class)->create();

        $response = $this->actingAs($user, 'api')->putJson('api/print/order/{order_number}/dpd_label', [
            // TODO: send request data
        ]);

        $response->assertOk();
        $response->assertJsonStructure([
            // TODO: compare expected response data
        ]);

        // TODO: perform additional assertions
    }

    /**
     * @test
     */
    public function if_authorization_is_cached()
    {
        $auth1 = Dpd::getCachedAuthorization();
        $auth2 = Dpd::getCachedAuthorization();

        $this->assertTrue($auth2['from_cache']);
        $this->assertEquals($auth1['authorization_response']['AccessToken'], $auth2['authorization_response']['AccessToken']);
    }

    // test cases...
}
