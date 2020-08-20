<?php

namespace Tests\External\Rmsapi;

use App\Models\RmsapiConnection;
use App\Modules\Rmsapi\src\Client as RmsapiClient;
use Tests\TestCase;

class RmsapiConnectionTest extends TestCase
{
    public function testIfFetchesProducts()
    {
        $connection = RmsapiConnection::first();

        $response = RmsapiClient::GET($connection, 'api/products');

        $this->assertTrue($response->isSuccess());
    }
}
