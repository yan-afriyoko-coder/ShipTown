<?php

namespace Tests\Feature\Modules\ScurriAnpost;

use App\Models\Order;
use App\Modules\ScurriAnpost\src\ScurriServiceProvider;
use App\User;
use Tests\TestCase;

class BasicModuleTest extends TestCase
{
    public function test_module_basic_functionality()
    {
        ScurriServiceProvider::enableModule();

        /** @var Order $order */
        $order = Order::factory()->create(['status_code' => 'anpost_courier', 'label_template' => 'anpost_3day']);

        $this->actingAs(User::factory()->create(), 'api');

        $response = $this->post('api/shipping-labels', [
            'order_id' => $order->getKey(),
            'shipping_service_code' => $order->label_template
        ]);

        ray($response->json());

        $response->assertSuccessful();
    }
}
