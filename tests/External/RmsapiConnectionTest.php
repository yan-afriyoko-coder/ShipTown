<?php

namespace Tests\External;

use App\Models\RmsapiConnection;
use App\Modules\Rmsapi\src\Client as RmsapiClient;
use GuzzleHttp\Client;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RmsapiConnectionTest extends TestCase
{
    public function test_if_fetch_products() {

        $connection = RmsapiConnection::first();

        $response = RmsapiClient::GET($connection, 'api/products');

        $this->assertTrue($response->isSuccess());

    }
}
