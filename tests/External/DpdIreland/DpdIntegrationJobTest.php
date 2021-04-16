<?php

namespace Tests\External\DpdIreland;

use App\Models\Order;
use App\Modules\DpdIreland\Dpd;
use App\Modules\DpdIreland\src\Client;
use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DpdIntegrationJobTest extends TestCase
{
    use RefreshDatabase;

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
        $order = factory(Order::class, 10)->create();

        try {
            Dpd::shipOrder($order);
            $success = true;
        } catch (Exception $e) {
            $success = false;
        }

        // we just want no exceptions
        $this->assertTrue($success);
    }
}
