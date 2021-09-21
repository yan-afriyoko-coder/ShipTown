<?php

namespace Tests\External\Rmsapi;

use App\Models\RmsapiConnection;
use App\Modules\Rmsapi\src\Api\Client as RmsapiClient;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RmsapiConnectionTest extends TestCase
{
    use RefreshDatabase;

    public function testIfFetchesProducts()
    {
        $connection = factory(RmsapiConnection::class)->create();

        $response = RmsapiClient::GET($connection, 'api/products');

        $this->assertTrue($response->isSuccess());
    }
}
