<?php

namespace Tests\External\DpdIreland;

use App\Models\Order;
use App\Models\OrderAddress;
use App\Modules\DpdIreland\Dpd;
use App\Modules\DpdIreland\src\Client;
use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class DpdIntegrationJobTest extends TestCase
{
    use RefreshDatabase, SeedConfiguration;

    /**
     * @test
     */
    public function if_env_variables_are_set()
    {
        $this->assertNotEmpty(config('dpd.token'), 'DPD_TOKEN is not set');
        $this->assertNotEmpty(config('dpd.user'), 'DPD_USER is not set');
        $this->assertNotEmpty(config('dpd.password'), 'DPD_PASSWORD is not set');
    }

    /**
     * @test
     */
    public function if_authenticates()
    {
        $auth = Client::getCachedAuthorization();
        $this->assertEquals('OK', $auth['authorization_response']['Status']);
    }

    /**
     * @test
     */
    public function if_record_id_matches()
    {
        $address = factory(OrderAddress::class)->create([
            'company' => 'DPD Test',
            'first_name' => 'DPD',
            'last_name' => 'Depot',
            'address1' => 'Athlone Business Park',
            'address2' => 'Dublin Road',
            'phone' => '0861230000',
            'city' => 'Athlone',
            'state_name' => 'Athlone',
            'postcode' => '1234XYZ',
            'country_code' => 'IRL',
        ]);

        $order = factory(Order::class)->create([
            'shipping_address_id' => $address->getKey(),
        ]);

        try {
            Dpd::shipOrder($order);
            $success = true;
        } catch (Exception $e) {
            $success = false;
            Log::error($e->getMessage());
            $this->assertTrue($success, $e->getMessage());
        }

        // we just want no exceptions
        $this->assertTrue($success);
    }
}
