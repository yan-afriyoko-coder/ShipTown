<?php

namespace Tests\Feature\Http\Controllers\Api\ShippingLabelController;

use App\Abstracts\ShippingServiceAbstract;
use App\Models\Order;
use App\Models\ShippingService;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Tests\TestCase;

class TestShipmentService extends ShippingServiceAbstract
{
    public function ship(int $order_id): AnonymousResourceCollection
    {
        return AnonymousResourceCollection::collection(collect([]));
    }
}

class StoreTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function test_store_call_returns_ok()
    {
        $user = factory(User::class)->create();
        $order = factory(Order::class)->create();

        $shippingService = factory(ShippingService::class)->create([
            'code' => 'test_service',
            'service_provider_class' => TestShipmentService::class
        ]);

        $response = $this->actingAs($user, 'api')
            ->postJson(route('shipping-labels.store'), [
                'shipping_service_code' => 'test_service',
                'order_id' => $order->getKey(),
            ]);

        $response->assertOk();
        $response->assertJsonStructure([
            'meta',
            'links',
            'data' => [
                '*' => [
                    'id'
                ],
            ],
        ]);
    }
}
