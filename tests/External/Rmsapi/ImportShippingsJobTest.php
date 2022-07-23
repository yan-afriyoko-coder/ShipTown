<?php

namespace Tests\External\Rmsapi;

use App\Modules\Rmsapi\src\Api\Client;
use App\Modules\Rmsapi\src\Models\RmsapiConnection;
use GuzzleHttp\Exception\GuzzleException;
use Tests\TestCase;

class ImportShippingsJobTest extends TestCase
{
    /**
     * @throws GuzzleException
     */
    public function test_endpoint()
    {
        $connection = factory(RmsapiConnection::class)->create([
            'location_id'  => env('TEST_RMSAPI_WAREHOUSE_CODE'),
            'url'          => env('TEST_RMSAPI_URL'),
            'username'     => env('TEST_RMSAPI_USERNAME'),
            'password'     => env('TEST_RMSAPI_PASSWORD'),
        ]);

        $response = Client::GET($connection, 'api/shippings');

        // todo $this->import(response)

        $this->assertTrue($response->isSuccess());
    }
}
