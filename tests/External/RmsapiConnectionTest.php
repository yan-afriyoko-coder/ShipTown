<?php

namespace Tests\External;

use App\Models\RmsapiConnection;
use GuzzleHttp\Client;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RmsapiConnectionTest extends TestCase
{
    public function test_if_fetch_products() {

        $connection = RmsapiConnection::query()->first();

        $guzzle = new Client([
            'base_uri' => $connection->url,
            'timeout' => 600,
            'exceptions' => false,
            'auth' => [
                $connection->username,
                \Crypt::decryptString($connection->password)
            ]
        ]);

        $response = $guzzle->get('api/products');

        $this->assertEquals($response->getStatusCode(), 200);

    }
}
