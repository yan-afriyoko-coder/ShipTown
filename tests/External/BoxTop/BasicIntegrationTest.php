<?php

namespace Tests\External\BoxTop;

use App\Modules\BoxTop\src\Api\ApiClient;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BasicIntegrationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     *
     * @return void
     * @throws GuzzleException
     */
    public function testExample()
    {
        $boxtop = new ApiClient();

        $apiResponse = $boxtop->getAllProducts();

        $this->assertEquals(200, $apiResponse->http_response->getStatusCode());
    }
}
